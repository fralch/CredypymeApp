<?php

namespace App\Http\Controllers\Apis;

use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\General\Agencia;
use App\Models\General\Departamento;
use App\Models\General\Provincia;
use App\Models\General\Distrito;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiClienteController extends Controller
{

    public function acceder($dni, $clave)
    {
        $agencias = Agencia::all();

        foreach ($agencias as $item) {
            $conexion = 'master_' . $item->id_agencia;

            $datos_cliente = Cliente::on($conexion)->select(
                'id',
                'nombres',
                'apellido_paterno',
                'apellido_materno',
                'agencia_id'
            )->where([
                ['dni', $dni],
                ['dni', $clave]
            ])->get()->last();

            if ($datos_cliente != null) {
                break;
            }
        }

        $datos_cliente = $datos_cliente == null ? 'NO_AUTORIZADO' : $datos_cliente;
        return $datos_cliente;
    }

    public function datos_personales($agencia_id, $cliente_id)
    {
        $conexion = 'master_' . $agencia_id;

        $datos_personales = Cliente::on($conexion)->from('cliente_registros as cli_reg')
            ->select(
                'cli_reg.dni',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.agencia_id',
                'cli_reg.fecha_nacimiento',
                'cli_reg.estado_civil',
                'cli_reg.hijos',
                'cli_reg.sexo',
                'cli_reg.departamento_id',
                'cli_reg.provincia_id',
                'cli_reg.distrito_id',
                'cli_reg.direccion',
                'cli_reg.referencia_direccion',
                'cli_reg.telefonos',
                'cli_reg.correo_electronico',

                'age.nombre as agencia',
                'dep.departamento',
                'pro.provincia',
                'dis.distrito'

            )->where('cli_reg.id', $cliente_id)
            ->join('solucion_master.agencias as age', 'cli_reg.agencia_id', 'age.id_agencia')
            ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
            ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id', 'pro.id')
            ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
            ->get()
            ->last();
        $distritos = Distrito::orderBy('distrito', 'asc')->get();
        $provincias = Provincia::orderBy('provincia', 'asc')->get();
        $departamentos = Departamento::orderBy('departamento', 'asc')->get();

        return [
            'datos_personales' => $datos_personales,
            'distritos' => $distritos,
            'provincias' => $provincias,
            'departamentos' => $departamentos
        ];
    }

    public function actualizar(Request $request)
    {

        $response = new \stdClass;

        $frmDatosCliente = json_decode($request->frmDatosCliente);
        $agencia_id = $frmDatosCliente->agencia_id;
        $conexion = 'master_' . $agencia_id;

        $cliente_id = $request->cliente_id;

        $fecha_nacimiento = $frmDatosCliente->fecha_nacimiento;
        $estado_civil = $frmDatosCliente->estado_civil;
        $sexo = $frmDatosCliente->sexo;
        $hijos = $frmDatosCliente->hijos;
        $correo_electronico = $frmDatosCliente->correo_electronico;
        $direccion = mb_strtoupper($frmDatosCliente->direccion);
        $departamento_id = $frmDatosCliente->departamento_id;
        $provincia_id = $frmDatosCliente->provincia_id;
        $distrito_id = $frmDatosCliente->distrito_id;
        $referencia_direccion = mb_strtoupper($frmDatosCliente->referencia_direccion);
        $telefonos = json_encode($frmDatosCliente->telefonos);

        Cliente::on($conexion)->where('id', $cliente_id)->update([
            'fecha_nacimiento' => $fecha_nacimiento,
            'estado_civil' => $estado_civil,
            'sexo' => $sexo,
            'hijos' => $hijos,
            'correo_electronico' => $correo_electronico,
            'direccion' => $direccion,
            'departamento_id' => $departamento_id,
            'provincia_id' => $provincia_id,
            'distrito_id' => $distrito_id,
            'referencia_direccion' => $referencia_direccion,
            'telefonos' => $telefonos
        ]);

        $response->success = true;

        return $response;
    }

    public function historial_creditos($agencia_id, $cliente_id)
    {
        $conexion = 'master_' .  $agencia_id;
        $datos_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
            ->select(
                'cli_reg.id',
                'cli_reg.agencia_id',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.codigo_expediente',
                'cli_reg.calificacion',
                'cli_reg.datos_creacion',

                'us.usuario as usuario_asesor'
            )
            ->leftjoin('solucion_master.usuarios as us', 'cli_reg.asesor_id', 'us.dni')
            ->where('cli_reg.id', $cliente_id)
            ->get()->last();

        $datos_creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.calificacion',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.notificaciones_total',
                'cre_reg.notificaciones_pagado',
                'cre_reg.fecha_vencimiento',

                'cre_apr.monto',
                'cre_apr.plazo',
                'cre_apr.cuota',
                'cre_apr.periodo_pago',
                'cre_apr.pago_oficina',
                'cre_apr.codigo_seguimiento',
                'cre_apr.tasa_interes',

                'caj_des.datos_creacion',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.codigo_expediente',
                'cli_reg.direccion',

                'dis.distrito',
                'pro.provincia',
                'dep.departamento',

                'cli_reg_2.apellido_paterno as apellido_paterno_pariente',
                'cli_reg_2.apellido_materno as apellido_materno_pariente',
                'cli_reg_2.nombres as nombres_pariente',
                'cli_reg_2.dni as dni_pariente',
                'cli_reg_2.direccion as direccion_pariente',

                'cli_reg_3.apellido_paterno as apellido_paterno_aval',
                'cli_reg_3.apellido_materno as apellido_materno_aval',
                'cli_reg_3.nombres as nombres_aval',
                'cli_reg_3.dni as dni_aval',
                'cli_reg_3.direccion as direccion_aval',

                'cli_reg_4.apellido_paterno as apellido_paterno_pariente_aval',
                'cli_reg_4.apellido_materno as apellido_materno_pariente_aval',
                'cli_reg_4.nombres as nombres_pariente_aval',
                'cli_reg_4.dni as dni_pariente_aval',
                'cli_reg_4.direccion as direccion_pariente_aval',

                'cre_tip.tipo',
                'cre_est.estado',
                'cre_prod.producto',
                'cre_sub.subproducto',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_cobrador'

            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
            ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id', 'pro.id')
            ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
            ->leftjoin('cliente_parientes as cli_par_1', 'cre_pro.pariente_id', 'cli_par_1.id')
            ->leftjoin('cliente_avales as cli_ava', 'cre_pro.aval_id', 'cli_ava.id')
            ->leftjoin('cliente_parientes as cli_par_2', 'cre_pro.pariente_aval_id', 'cli_par_2.id')
            ->leftjoin('cliente_registros as cli_reg_2', 'cli_par_1.pariente_id', 'cli_reg_2.id')
            ->leftjoin('cliente_registros as cli_reg_3', 'cli_ava.aval_id', 'cli_reg_3.id')
            ->leftjoin('cliente_registros as cli_reg_4', 'cli_par_2.pariente_id', 'cli_reg_4.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('credito_productos as cre_prod', 'cre_apr.producto_id', 'cre_prod.id')
            ->leftjoin('credito_subproductos as cre_sub', 'cre_apr.subproducto_id', 'cre_sub.id')
            ->join('credito_estados as cre_est', 'cre_reg.estado_id', 'cre_est.id')
            ->leftjoin('solucion_master.usuarios as us_1', 'cre_reg.asesor_id', 'us_1.dni')
            ->leftjoin('solucion_master.usuarios as us_2', 'cre_reg.cobrador_id', 'us_2.dni')
            ->where('cre_reg.cliente_id', $cliente_id)
            ->orderBy('cre_reg.fecha_desembolso', 'asc')
            ->get();

        return  [
            'datos_cliente' => $datos_cliente,
            'datos_creditos' => $datos_creditos
        ];
    }

    public function detalle_pagos($agencia_id, $credito_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $cuotas_pagos = Cuota::on($conexion)->from('credito_cuotas')
            ->select(
                'credito_id',
                'numero_cuota',
                'fecha_vencimiento',
                'fecha_ultimo_pago',
                'estado',
                'capital',
                'interes',
                'redondeo',
                'cuota',
                'acumulado',
                'dias_atraso',
            )
            ->where('credito_id', $credito_id)->get();

        return [
            'cuotas_pagos' => $cuotas_pagos
        ];
    }

}
