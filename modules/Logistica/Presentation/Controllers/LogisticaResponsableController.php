<?php

namespace Modules\Logistica\Presentation\Controllers;

use Modules\Logistica\Infrastructure\Persistence\Eloquent\Responsable;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Illuminate\Http\Request;
use Modules\Logistica\Presentation\Controllers\LogisticaController;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;

class LogisticaResponsableController extends Controller
{
    public function responsables()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'RESPONSABLES',  'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $responsables =  Responsable::from('logistica_responsables as log_res')
                    ->select(
                        'log_res.id',
                        'log_res.responsable',
                        'log_res.abreviacion',
                        'log_res.agencia_id',
                        DB::raw("IFNULL(log_res.usuario_id,0) AS usuario_id"),
                        'log_res.encargado_agencia',
                        'log_res.descripcion',
                        'log_res.habilitado',
                        'ag.nombre as agencia',
                        'us.usuario'
                    )
                    ->join('agencias as ag', 'log_res.agencia_id', 'ag.id_agencia')
                    ->leftjoin('usuarios as us', 'log_res.usuario_id', 'us.dni')
                    ->get();

                $agencias = Agencia::orderBy('nombre', 'asc')->get();
                $usuarios = Usuario::select(
                    'dni',
                    'agencia_id',
                    'usuario',
                    'habilitado'
                )
                    ->orderBy('usuario', 'asc')
                    ->get();

                return Inertia::render(
                    'Logistica/Mantenimiento/responsables',
                    [
                        'responsables' => $responsables,
                        'agencias' => $agencias,
                        'usuarios' => $usuarios,
                    ]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function verificar(Request $request)
    {
        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $responsable = $request->responsable;
        $abreviacion = $request->abreviacion;
        $agencia_id = $request->agencia_id;

        $responsable_existe = Responsable::select('id')
            ->where([['responsable',  $responsable], ['agencia_id', $agencia_id]])
            ->orwhere([['abreviacion',  $abreviacion], ['agencia_id', $agencia_id]])
            ->get();

        if ($modo == "EDITAR") {
            if (count($responsable_existe) > 0) {
                if ($responsable_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($responsable_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $responsable = mb_strtoupper($request->responsable);
        $abreviacion = mb_strtoupper($request->abreviacion);
        $agencia_id = $request->agencia_id;
        $usuario_id = $request->usuario_id == 0 ? null : $request->usuario_id;
        $encargado_agencia = $request->encargado_agencia;

        $descripcion = (new LogisticaController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Responsable::create(array(
                'responsable' =>  $responsable,
                'abreviacion' =>  $abreviacion,
                'agencia_id' => $agencia_id,
                'usuario_id' => $usuario_id,
                'encargado_agencia' => $encargado_agencia,
                'descripcion' => $descripcion,
                'habilitado' => 1,
                'datos_creacion' => $datos_registro,
            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Responsable::where('id', $id)
                ->update([
                    'responsable' =>  $responsable,
                    'abreviacion' =>  $abreviacion,
                    'agencia_id' => $agencia_id,
                    'usuario_id' => $usuario_id,
                    'encargado_agencia' => $encargado_agencia,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('log.man.responsables');
    }

    public function filtrar_por_agencia(Request $request)
    {

        $agencia_id = $request->input('agencia_id');
        $encargado_agencia = filter_var($request->input('encargado_agencia'), FILTER_VALIDATE_BOOLEAN);

        $operador = '>=';
        $search = 0;

        if ($encargado_agencia) {
            $operador = '=';
            $search = $encargado_agencia;
        }

        $responsables = Responsable::from('logistica_responsables as log_res')
            ->select(
                'log_res.id',
                'log_res.responsable',
                'log_res.abreviacion',
                'log_res.agencia_id',
                'log_res.usuario_id',
                'log_res.encargado_agencia',
                'log_res.descripcion',
                'us.usuario'
            )
            ->join('usuarios as us', 'us.dni', 'log_res.usuario_id')
            ->where([
                ['log_res.agencia_id', $agencia_id],
                ['log_res.encargado_agencia', $operador, $search],
                ['log_res.habilitado', 1]
            ])->orderBy('us.usuario', 'asc')->get();

        return response()->json(['responsables' => $responsables]);
    }
}
