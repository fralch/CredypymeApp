<?php

namespace App\Http\Controllers\Creditos\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Aval;
use App\Models\General\Agencia;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

define('API_AVA_URL',  getenv('VITE_S_API_EXTERNA'));

class AvalController extends Controller
{
    public function listar_avales($cliente_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $lista = Aval::on($conexion)->select(
            'id',
            'cliente_id',
            'aval_id as pariente_aval_id',
            'vinculado',
            'agencia_aval'
        )
            ->where('cliente_id', $cliente_id)
            ->get();

        foreach ($lista as $key => $item) {

            if (in_array($item->agencia_pariente, [2, 3, 5])) {
                $conexion = 'master_' .  $item->agencia_aval;

                $datos_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                    ->select(
                        'cli_reg.dni',
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cli_reg.fecha_nacimiento',
                        'cli_reg.estado_civil',
                        'cli_reg.sexo',
                        'cli_reg.agencia_id',
                        'cli_reg.codigo_expediente',
                        'cli_reg.asesor_id',
                        'cli_reg.promotor_id',
                        'cli_reg.central_riesgo',
                        'cli_reg.canal_referencia',
                        'cli_reg.monto_maximo',
                        'cli_reg.notas',
                        'cli_reg.reportar_equifax',
                        'cli_reg.direccion',
                        'cli_reg.departamento_id',
                        'cli_reg.provincia_id',
                        'cli_reg.distrito_id',
                        'cli_reg.referencia_direccion',
                        'cli_reg.telefonos',

                        'ag.nombre as agencia',

                        'dep.departamento',
                        'pro.provincia',
                        'dis.distrito'
                    )
                    ->join('solucion_master.agencias as ag', 'ag.id_agencia', 'cli_reg.agencia_id')
                    ->join('solucion_master.departamentos as dep', 'dep.id', 'cli_reg.departamento_id')
                    ->join('solucion_master.provincias as pro', 'pro.id', 'cli_reg.provincia_id')
                    ->join('solucion_master.distritos as dis', 'dis.id', 'cli_reg.distrito_id')
                    ->where('cli_reg.id', $item->pariente_aval_id)
                    ->get()->last();
            } else {

                $datos_aval =
                    [
                        'agencia_id' => $item->agencia_aval,
                        'cliente_id' => $item->pariente_aval_id
                    ];

                $response = Http::get(API_AVA_URL . "/api/cli/listado_externa/datos_cliente", $datos_aval);


                if ($response->successful()) {

                    $response = $response->json();

                    $datos_cliente = collect($response['datos_cliente']);
                } else {
                    $datos_cliente = null;
                }
            }


            if ($datos_cliente != null) {

                $datos_cliente = $datos_cliente->toArray();
                foreach ($datos_cliente as $key_1 => $value) {
                    $item->$key_1 = $value;
                }
            } else {
                unset($lista[$key]);
            }
        }

        $lista = $lista->values();

        return $lista;
    }

    public function listar_avales_dependientes($cliente_id, $agencia_id)
    {

        $lista = [];

        $agencias = Agencia::all();

        foreach ($agencias as $item) {
            $conexion = 'master_' .  $item->id_agencia;
            $avales = Aval::on($conexion)->from('cliente_avales as cli_ava')
                ->select(
                    'cli_ava.id',
                    'cli_ava.cliente_id',
                    'cli_ava.aval_id as pariente_aval_id',
                    'cli_ava.vinculado',
                    'cli_reg.dni',
                    'cli_reg.apellido_paterno',
                    'cli_reg.apellido_materno',
                    'cli_reg.nombres',
                    'cli_reg.codigo_expediente',
                    'cli_reg.direccion',
                    'cli_reg.referencia_direccion',

                    'dep.departamento',
                    'pro.provincia',
                    'dis.distrito',

                    'cli_ava.datos_creacion',
                    'cli_ava.datos_actualizacion',

                    'us_1.usuario as usuario_creacion',
                    'us_2.usuario as usuario_actualizacion'
                )
                ->join('cliente_registros as cli_reg', 'cli_reg.id', 'cli_ava.cliente_id')
                ->join('solucion_master.departamentos as dep', 'dep.id', 'cli_reg.departamento_id')
                ->join('solucion_master.provincias as pro', 'pro.id', 'cli_reg.provincia_id')
                ->join('solucion_master.distritos as dis', 'dis.id', 'cli_reg.distrito_id')
                ->leftjoin('solucion_master.usuarios as us_1', DB::raw("SUBSTRING(cli_ava.datos_creacion, 42, 8)"), 'us_1.dni')
                ->leftjoin('solucion_master.usuarios as us_2', DB::raw("SUBSTRING(cli_ava.datos_actualizacion, 42, 8)"), 'us_2.dni')
                ->where([
                    ['cli_ava.aval_id', $cliente_id],
                    ['cli_ava.agencia_aval', $agencia_id]
                ])
                ->get();

            foreach ($avales as $item_1) {
                $lista[] = $item_1;
            }
        }


        $datos_titular =
            [
                'tipo' => 'AVAL',
                'agencia_id' => $agencia_id,
                'cliente_id' => $cliente_id
            ];

        $response = Http::get(API_AVA_URL . "/api/cli/listado_externa/buscar_parientes_avales", $datos_titular);


        if ($response->successful()) {

            $response = $response->json();

            $avales = collect($response['clientes']);
        } else {
            $avales = [];
        }

        foreach ($avales as $item_2) {
            $lista[] = $item_2;
        }


        return $lista;
    }

    public function asignar_aval(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));
        $tipo_modulo = $request->tipo_modulo;
        $modo_asignacion = $request->modo_asignacion;

        if ($modo_asignacion == 'EXISTENTE') {
            $cliente_id = $request->cliente_id;
            $aval_id = $request->cliente_vinculado_id;
            $agencia_aval = $request->agencia_aval;

            // Desvinculando todos los avales
            Aval::on($conexion)->where('cliente_id', $cliente_id)->update([
                'vinculado' => 0,
                'datos_actualizacion' => $datos_registro
            ]);

            Aval::on($conexion)->create([
                'cliente_id' => $cliente_id,
                'agencia_aval' => $agencia_aval,
                'aval_id' => $aval_id,
                'vinculado' => 1,
                'datos_creacion' => $datos_registro,
            ]);
        } else if ($modo_asignacion == 'NUEVO-EDITAR') {
            $datos_cliente = json_decode($request->datos_cliente);

            $cliente_id = $datos_cliente->cliente_id;
            $cliente_vinculado_id = $datos_cliente->cliente_vinculado_id;
            $dni = $datos_cliente->dni;
            $apellido_paterno = mb_strtoupper($datos_cliente->apellido_paterno);
            $apellido_materno = mb_strtoupper($datos_cliente->apellido_materno);
            $nombres = mb_strtoupper($datos_cliente->nombres);

            $fecha_nacimiento = $datos_cliente->fecha_nacimiento;
            $estado_civil = $datos_cliente->estado_civil;
            $sexo = $datos_cliente->sexo;
            $agencia_id = $request->agencia_id;

            $central_riesgo = $datos_cliente->central_riesgo;
            $notas = $datos_cliente->notas;

            $direccion = mb_strtoupper($datos_cliente->direccion);
            $departamento_id = $datos_cliente->departamento_id;
            $provincia_id = $datos_cliente->provincia_id;
            $distrito_id = $datos_cliente->distrito_id;
            $referencia_direccion = mb_strtoupper($datos_cliente->referencia_direccion);
            $telefonos = json_encode($datos_cliente->telefonos);

            if ($cliente_vinculado_id != 0) {
                $agencia_aval = $datos_cliente->agencia_id;
                $conexion = 'master_' .  $agencia_aval;

                Cliente::on($conexion)->where('id', $cliente_vinculado_id)->update([
                    'dni' => $dni,
                    'apellido_paterno' => $apellido_paterno,
                    'apellido_materno' => $apellido_materno,
                    'nombres' => $nombres,
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'estado_civil' => $estado_civil,
                    'sexo' => $sexo,

                    'central_riesgo' => $central_riesgo,
                    'notas' => $notas,

                    'direccion' => $direccion,
                    'departamento_id' => $departamento_id,
                    'provincia_id' => $provincia_id,
                    'distrito_id' => $distrito_id,
                    'referencia_direccion' => $referencia_direccion,

                    'telefonos' => $telefonos,
                    'datos_actualizacion' => $datos_registro
                ]);
            } else {

                $conexion = 'master_' .  $agencia_id;
                $asesor_promotor = Cliente::on($conexion)
                    ->select('asesor_id', 'promotor_id')
                    ->where('id', $cliente_id)->first();

                $cliente_nuevo = Cliente::on($conexion)->create([
                    'dni' => $dni,
                    'agencia_id' => $agencia_id,
                    'apellido_paterno' => $apellido_paterno,
                    'apellido_materno' => $apellido_materno,
                    'nombres' => $nombres,
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'estado_civil' => $estado_civil,
                    'sexo' => $sexo,

                    'asesor_id' => $asesor_promotor->asesor_id,
                    'promotor_id' => $asesor_promotor->promotor_id,
                    'central_riesgo' => $central_riesgo,
                    'canal_referencia' => 0,

                    'monto_maximo' => 0,
                    'notas' => $notas,

                    'direccion' => $direccion,
                    'departamento_id' => $departamento_id,
                    'provincia_id' => $provincia_id,
                    'distrito_id' => $distrito_id,
                    'referencia_direccion' => $referencia_direccion,

                    'telefonos' => $telefonos,
                    'datos_creacion' => $datos_registro
                ]);

                $aval_id = $cliente_nuevo->id;

                // Desvinculando todos los avales
                Aval::on($conexion)->where('cliente_id', $cliente_id)->update([
                    'vinculado' => 0,
                    'datos_actualizacion' => $datos_registro
                ]);
                Aval::on($conexion)->create([
                    'cliente_id' => $cliente_id,
                    'agencia_aval' => $agencia_id,
                    'aval_id' => $aval_id,
                    'vinculado' => 1,
                    'datos_creacion' => $datos_registro
                ]);
            }
        }

        return redirect()->route('cli.listado_registro', $tipo_modulo);
    }

    public function vincular_desvincular(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $tipo_modulo = $request->tipo_modulo;
        $id = $request->id;
        $cliente_id = $request->cliente_id;
        $vinculado = $request->vinculado == 'true' ? 0 : 1;


        // Desvinculando todos los avales
        Aval::on($conexion)->where('cliente_id', $cliente_id)->update([
            'vinculado' => 0,
            'datos_actualizacion' => $datos_registro
        ]);

        // Vinculando/Desvinculando el pariente seleccionado
        Aval::on($conexion)->where('id', $id)->update([
            'vinculado' => $vinculado,
            'datos_actualizacion' => $datos_registro
        ]);

        return redirect()->route('cli.listado_registro', $tipo_modulo);
    }
}
