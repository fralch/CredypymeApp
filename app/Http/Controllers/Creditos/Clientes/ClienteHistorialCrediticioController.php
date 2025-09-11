<?php

namespace App\Http\Controllers\Creditos\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Aprobacion;
use App\Models\Creditos\Credito\Cuota;

use App\Models\Creditos\Credito\Compromiso;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ClienteHistorialCrediticioController extends Controller
{
    public function historial_crediticio($cliente_id, $agencia_id)
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

        $usuarios = Usuario::where([
            ['habilitado', 1],
            ['agencia_id', $datos_cliente->agencia_id]
        ])->get();

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
                'cre_apr.codigo_seguimiento_2',
                'cre_apr.tasa_interes',

                'caj_des.modo_desembolso',
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

                'cre_pro.agencia_pariente',
                'cre_pro.pariente_id',
                'cre_pro.agencia_aval',
                'cre_pro.aval_id',
                'cre_pro.agencia_pariente_aval',
                'cre_pro.pariente_aval_id',

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
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('credito_productos as cre_prod', 'cre_apr.producto_id', 'cre_prod.id')
            ->leftjoin('credito_subproductos as cre_sub', 'cre_apr.subproducto_id', 'cre_sub.id')
            ->join('credito_estados as cre_est', 'cre_reg.estado_id', 'cre_est.id')
            ->leftjoin('solucion_master.usuarios as us_1', 'cre_reg.asesor_id', 'us_1.dni')
            ->leftjoin('solucion_master.usuarios as us_2', 'cre_reg.cobrador_id', 'us_2.dni')
            ->where('cre_reg.cliente_id', $cliente_id)
            ->orderBy('cre_reg.fecha_desembolso', 'asc')
            ->get();

        $datos_creditos = $datos_creditos->map(function ($row) {


            if ($row['pariente_id'] != null && $row['agencia_pariente'] != null) {

                if (in_array($row['agencia_pariente'], [2, 3])) {
                    $conexion_pariente = 'master_' . $row['agencia_pariente'];
                    $datos_pariente = Cliente::on($conexion_pariente)
                        ->select(
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres',
                            'dni',
                            'direccion'
                        )
                        ->where('id',  $row['pariente_id'])->get()->last();


                    if ($datos_pariente != null) {

                        $row['apellido_paterno_pariente'] = $datos_pariente->apellido_paterno;
                        $row['apellido_materno_pariente'] = $datos_pariente->apellido_materno;
                        $row['nombres_pariente'] = $datos_pariente->nombres;
                        $row['dni_pariente'] = $datos_pariente->dni;
                        $row['direccion_pariente'] = $datos_pariente->direccion;
                    }
                }
            }

            if ($row['aval_id'] != null && $row['agencia_aval'] != null) {

                if (in_array($row['agencia_aval'], [2, 3])) {
                    $conexion_aval = 'master_' . $row['agencia_aval'];
                    $datos_aval = Cliente::on($conexion_aval)
                        ->select(
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres',
                            'dni',
                            'direccion'
                        )
                        ->where('id',  $row['aval_id'])->get()->last();

                    if ($datos_aval != null) {
                        $row['apellido_paterno_aval'] = $datos_aval->apellido_paterno;
                        $row['apellido_materno_aval'] = $datos_aval->apellido_materno;
                        $row['nombres_aval'] = $datos_aval->nombres;
                        $row['dni_aval'] = $datos_aval->dni;
                        $row['direccion_aval'] = $datos_aval->direccion;
                    }
                }
            }

            if ($row['pariente_aval_id'] != null && $row['agencia_pariente_aval'] != null) {

                if (in_array($row['agencia_pariente_aval'], [2, 3])) {

                    $conexion_pariente_aval = 'master_' . $row['agencia_pariente_aval'];
                    $datos_pariente_aval = Cliente::on($conexion_pariente_aval)
                        ->select(
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres',
                            'dni',
                            'direccion'
                        )
                        ->where('id',  $row['pariente_aval_id'])->get()->last();

                    if ($datos_pariente_aval != null) {
                        $row['apellido_paterno_pariente_aval'] = $datos_pariente_aval->apellido_paterno;
                        $row['apellido_materno_pariente_aval'] = $datos_pariente_aval->apellido_materno;
                        $row['nombres_pariente_aval'] = $datos_pariente_aval->nombres;
                        $row['dni_pariente_aval'] = $datos_pariente_aval->dni;
                        $row['direccion_pariente_aval'] = $datos_pariente_aval->direccion;
                    }
                }
            }


            return $row;
        });

        return  [
            'agencia_id' => intVal($agencia_id),
            'cliente_id' => intVal($cliente_id),
            'datos_cliente' => $datos_cliente,
            'datos_creditos' => $datos_creditos,
            'usuarios' => $usuarios
        ];
    }
    public function listar_detalle(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $credito_id = $request->credito_id;

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


        $compromisos = Compromiso::on($conexion)->from('credito_compromisos as cre_com')
            ->select(
                'cre_com.id',
                'cre_com.credito_id',
                'cre_com.compromiso',
                'cre_com.fecha_hora_visita',
                'cre_com.fecha_vencimiento',
                'cre_com.datos_creacion',

                'us_1.usuario as usuario_registro',
                'car_1.cargo'
            )
            ->where('cre_com.credito_id', $credito_id)
            ->join('solucion_master.usuarios as us_1', DB::raw("SUBSTRING(cre_com.datos_creacion,42,8)"), 'us_1.dni')
            ->leftjoin('solucion_master.cargos as car_1', 'car_1.id', 'us_1.cargo_id')
            ->orderby('cre_com.id', 'desc')
            ->get();

        return [
            'cuotas_pagos' => $cuotas_pagos,
            'compromisos' => $compromisos
        ];
    }
    public function calificar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $frmCalificacionLugar = json_decode($request->frmCalificacionLugar);
        $credito_id = $frmCalificacionLugar->credito_id;
        $calificacion = $frmCalificacionLugar->calificacion;
        $forma_pago = $frmCalificacionLugar->forma_pago;
        $usuario_cobrador = $frmCalificacionLugar->usuario_cobrador;

        $credito = tap(Credito::on($conexion)->where('id', $credito_id))->update([
            'calificacion' => $calificacion,
            'datos_actualizacion' => $datos_registro
        ])->first();

        if ($forma_pago == 'OFICINA') {
            Aprobacion::on($conexion)->where('id', $credito->aprobacion_id)
                ->update([
                    'pago_oficina' => 1,
                    'datos_actualizacion' => $datos_registro
                ]);
        } else if ($forma_pago == 'NEGOCIO') {
            Aprobacion::on($conexion)->where('id', $credito->aprobacion_id)
                ->update([
                    'pago_oficina' => 0,
                    'datos_actualizacion' => $datos_registro
                ]);

            Credito::on($conexion)->where('id', $credito_id)->update([
                'cobrador_id' => $usuario_cobrador,
                'datos_actualizacion' => $datos_registro
            ]);
        }

        return true;
    }

    public function historial_crediticio_modal(Request $request)
    {
        $cliente_id = $request->cliente_id;
        $agencia_id = $request->agencia_id;

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

        $usuarios = Usuario::where([
            ['habilitado', 1],
            ['agencia_id', $datos_cliente->agencia_id]
        ])->get();

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
            ->get();

        return  [
            'agencia_id' => intVal($agencia_id),
            'cliente_id' => intVal($cliente_id),
            'datos_cliente' => $datos_cliente,
            'datos_creditos' => $datos_creditos,
            'usuarios' => $usuarios
        ];
    }
}
