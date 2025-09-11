<?php

namespace App\Http\Controllers\Creditos\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Pariente;

use App\Models\General\Agencia;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ParienteController extends Controller
{
    public function listar_parientes($cliente_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $lista = Pariente::on($conexion)->select(
            'id',
            'cliente_id',
            'pariente_id as pariente_aval_id',
            'vinculado',
            'parentesco',
            'agencia_pariente'
        )
            ->where('cliente_id', $cliente_id)
            ->get();

        foreach ($lista as $key => $item) {
            $conexion = 'master_' .  $item->agencia_pariente;

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
    public function listar_parientes_dependientes($cliente_id, $agencia_id)
    {
        $lista = [];

        $agencias = Agencia::all();

        foreach ($agencias as $item) {
            $conexion = 'master_' .  $item->id_agencia;
            $parientes = Pariente::on($conexion)->from('cliente_parientes as cli_par')
                ->select(
                    'cli_par.id',
                    'cli_par.cliente_id',
                    'cli_par.pariente_id as pariente_aval_id',
                    'cli_par.vinculado',
                    'cli_par.parentesco',
                    'cli_reg.dni',
                    'cli_reg.apellido_paterno',
                    'cli_reg.apellido_materno',
                    'cli_reg.nombres',
                    'cli_reg.codigo_expediente',
                    'cli_reg.direccion',

                    'dep.departamento',
                    'pro.provincia',
                    'dis.distrito',

                    'cli_reg.referencia_direccion',
                    'cli_par.datos_creacion',
                    'cli_par.datos_actualizacion',

                    'us_1.usuario as usuario_creacion',
                    'us_2.usuario as usuario_actualizacion'
                )
                ->join('cliente_registros as cli_reg', 'cli_reg.id', 'cli_par.cliente_id')
                ->join('solucion_master.departamentos as dep', 'dep.id', 'cli_reg.departamento_id')
                ->join('solucion_master.provincias as pro', 'pro.id', 'cli_reg.provincia_id')
                ->join('solucion_master.distritos as dis', 'dis.id', 'cli_reg.distrito_id')
                ->leftjoin('solucion_master.usuarios as us_1', DB::raw("SUBSTRING(cli_par.datos_creacion, 42, 8)"), 'us_1.dni')
                ->leftjoin('solucion_master.usuarios as us_2', DB::raw("SUBSTRING(cli_par.datos_actualizacion, 42, 8)"), 'us_2.dni')
                ->where([
                    ['cli_par.pariente_id', $cliente_id],
                    ['cli_par.agencia_pariente', $agencia_id]
                ])
                ->get();

            foreach ($parientes as $item_1) {
                $lista[] = $item_1;
            }
        }
        return $lista;
    }

    public function asignar_pariente(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $modo_asignacion = $request->modo_asignacion;

        if ($modo_asignacion == 'EXISTENTE') {
            $cliente_id = $request->cliente_id;
            $pariente_id = $request->cliente_vinculado_id;
            $agencia_pariente = $request->agencia_pariente;
            $parentesco = $request->parentesco;

            // Desvinculando todos los parientes
            Pariente::on($conexion)->where('cliente_id', $cliente_id)->update([
                'vinculado' => 0,
                'datos_actualizacion' => $datos_registro
            ]);

            Pariente::on($conexion)->create([
                'cliente_id' => $cliente_id,
                'agencia_pariente' => $agencia_pariente,
                'pariente_id' => $pariente_id,
                'vinculado' => 1,
                'parentesco' => $parentesco,
                'datos_creacion' => $datos_registro,
            ]);
        } else if ($modo_asignacion == 'NUEVO-EDITAR') {
            $datos_cliente = json_decode($request->datos_cliente);

            $id = $datos_cliente->id;
            $cliente_id = $datos_cliente->cliente_id;
            $cliente_vinculado_id = $datos_cliente->cliente_vinculado_id;
            $dni = $datos_cliente->dni;
            $apellido_paterno = mb_strtoupper($datos_cliente->apellido_paterno);
            $apellido_materno = mb_strtoupper($datos_cliente->apellido_materno);
            $nombres = mb_strtoupper($datos_cliente->nombres);

            $fecha_nacimiento = $datos_cliente->fecha_nacimiento;
            $estado_civil = $datos_cliente->estado_civil;
            $sexo = $datos_cliente->sexo;
            $parentesco = $datos_cliente->parentesco;
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
                $agencia_pariente = $datos_cliente->agencia_id;
                $conexion = 'master_' .  $agencia_pariente;
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

                $conexion = 'master_' .  $agencia_id;
                Pariente::on($conexion)->where('id', $id)->update([
                    'parentesco' => $parentesco,
                    'datos_actualizacion' => $datos_registro
                ]);
            } else {

                $conexion = 'master_' .  $agencia_id;
                // Trae el asesor y promotor del cliente titular
                $asesor_promotor = Cliente::on($conexion)->select('asesor_id', 'promotor_id')
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

                $pariente_id = $cliente_nuevo->id;

                // Desvinculando todos los parientes
                Pariente::on($conexion)->where('cliente_id', $cliente_id)->update([
                    'vinculado' => 0,
                    'datos_actualizacion' => $datos_registro
                ]);

                Pariente::on($conexion)->create([
                    'cliente_id' => $cliente_id,
                    'agencia_pariente' => $agencia_id,
                    'pariente_id' => $pariente_id,
                    'vinculado' => 1,
                    'parentesco' => $parentesco,
                    'datos_creacion' => $datos_registro
                ]);
            }
        }

        return redirect()->route('cli.listado_registro');
    }

    public function vincular_desvincular(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $id = $request->id;
        $cliente_id = $request->cliente_id;
        $vinculado = $request->vinculado == 'true' ? 0 : 1;

        // Desvinculando todos los parientes
        Pariente::on($conexion)->where('cliente_id', $cliente_id)->update([
            'vinculado' => 0,
            'datos_actualizacion' => $datos_registro
        ]);

        // Vinculando/Desvinculando el pariente seleccionado
        Pariente::on($conexion)->where('id', $id)->update([
            'vinculado' => $vinculado,
            'datos_actualizacion' => $datos_registro
        ]);

        return redirect()->route('cli.listado_registro');
    }
}
