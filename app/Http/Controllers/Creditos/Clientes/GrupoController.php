<?php

namespace App\Http\Controllers\Creditos\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;
use App\Models\General\Agencia;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Clientes\Grupo;
use App\Models\Creditos\Clientes\GrupoCliente;
use App\Models\Creditos\Clientes\Cliente;


use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    protected $main_db;

    public function __construct()
    {
        $this->main_db = env('S_MASTER_DATABASE');
    }

    public function listar_datos(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $lista_asesores = Usuario::select('usuario', 'dni', 'agencia_id')
            ->whereIn(
                'cargo_id',
                [2, 8, 15]
            )
            ->where('habilitado', 1)
            ->where('usuario_real', 1)
            ->where('agencia_id', $agencia_id)
            ->orderBy('usuario', 'asc')->get();


        return response()->json([
            'success' => true,
            'lista_asesores' => $lista_asesores,
        ], 200);
    }

    public function listar_grupos(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' .  $agencia_id;

        $lista_grupos = Grupo::on($conexion)
            ->from('grupos as gru')
            ->select(
                'gru.id',
                'gru.nombre',
                'gru.asesor_id',
                'gru.habilitado',

                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(gru.datos_creacion, '$.fecha')) as fecha_creacion"),
                'usu.usuario as usuario_asesor'
            )
            ->join($this->main_db . '.usuarios as usu', 'gru.asesor_id', 'usu.dni')
            ->groupBy('gru.id')
            ->orderBy('nombre', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'lista_grupos' => $lista_grupos,
        ], 200);
    }
    public function buscar_clientes(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $texto_buscar = $request->input('texto_buscar'); {

            $search = "%$texto_buscar%";
            $filtro_nombre = "cli.apellido_paterno,' ', cli.apellido_materno,' ', cli.nombres";


            $conexion = 'master_' .  $agencia_id;
            $lista_clientes = Cliente::on($conexion)->from('cliente_registros as cli')
                ->select(
                    'cli.id as cliente_id',
                    'cli.dni',

                    DB::raw("CONCAT(cli.apellido_paterno,' ',cli.apellido_materno,' ',cli.nombres) as cliente"),
                    DB::raw("null as cargo")
                )
                ->where([
                    [DB::raw("CONCAT($filtro_nombre)"), 'like', $search]
                ])
                ->orWhere(
                    'cli.dni',
                    'like',
                    $search
                )
                ->orderBy('cli.apellido_paterno', 'asc')
                ->get();

            // dd($lista_clientes);

            return response()->json([
                'success' => true,
                'message' => '¡Listo!',
                'lista_clientes' => $lista_clientes
            ]);
        }
    }
    public function listar_grupo_clientes(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' .  $agencia_id;
        $grupo_id = $request->input('grupo_id');

        $lista_grupo_clientes = GrupoCliente::on($conexion)
            ->from('grupo_clientes as gru_cli')
            ->select(
                'gru_cli.grupo_id',
                'gru_cli.cliente_id',
                'gru_cli.responsable',
                DB::raw("CONCAT(cli_reg.apellido_paterno,' ',cli_reg.apellido_materno,' ',cli_reg.nombres) as cliente"),
            )
            ->where('gru_cli.grupo_id', $grupo_id)
            ->join('cliente_registros as cli_reg', 'cli_reg.id', 'gru_cli.cliente_id')
            ->orderBy('grupo_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'lista_grupo_clientes' => $lista_grupo_clientes,
        ], 200);
    }


    public function verificar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' .  $agencia_id;

        $nombre = $request->input('nombre');
        $grupo_id = $request->input('grupo_id');
        $modo = $request->input('modo');

        $nombre = mb_strtoupper(trim($nombre));

        $resultado = false;
        $grupo = Grupo::on($conexion)->where('nombre', $nombre)->first();

        if ($modo == "EDITAR") {

            if ($grupo !== null) {

                if ($grupo->id == $grupo_id) {
                    $resultado = false;
                } else {
                    $resultado = true;
                }
            }
        } else {
            if ($grupo !== null) {
                $resultado = true;
            }
        }

        return response()->json([
            'success' => true,
            'existe' => $resultado
        ], 200);
    }

    public function guardar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $frmDatosGrupo = json_decode($request->frmDatosGrupo);

        $nombre = mb_strtoupper(trim($frmDatosGrupo->nombre));
        $lista_grupo_clientes = $frmDatosGrupo->lista_grupo_clientes;
        $asesor_id = $frmDatosGrupo->asesor_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $modo = $request->modo;

        if ($modo == 'NUEVO') {

            $grupo = Grupo::on($conexion)->create(array(
                'agencia_id' => $agencia_id,
                'nombre' => $nombre,
                'asesor_id' => $asesor_id,
                'datos_creacion' => $datos_registro
            ));

            $id = $grupo->id;

            foreach ($lista_grupo_clientes as $item) {

                GrupoCliente::on($conexion)->create(array(
                    'grupo_id' => $id,
                    'cliente_id' => $item->cliente_id,
                    'responsable' => $item->responsable ?? null,
                    'datos_creacion' => $datos_registro,

                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Grupo registrado con éxito'
            ], 200);
        } else if ($modo == 'EDITAR') {
            $grupo_id = $frmDatosGrupo->grupo_id;


            Grupo::on($conexion)->where('id', $grupo_id)
                ->update([
                    'nombre' => $nombre,
                    'asesor_id' => $asesor_id,
                    'datos_actualizacion' => $datos_registro,

                ]);

            GrupoCliente::on($conexion)
                ->where('grupo_id', $grupo_id)
                ->delete();

            foreach ($lista_grupo_clientes as $item) {
                GrupoCliente::on($conexion)->create([
                    'grupo_id' => $grupo_id,
                    'cliente_id' => $item->cliente_id,
                    'responsable' => $item->responsable ?? null,
                    'datos_creacion' => $datos_registro,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Grupo actualizado con éxito'
            ], 200);
        }
    }
}
