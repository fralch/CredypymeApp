<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\Creditos\Mantenimiento\FacturacionLimiteController;
use App\Http\Controllers\Creditos\Caja\CajaCobranzaController;

use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Caja\Desembolso;
use App\Models\Creditos\Credito\Records\CreditoRegistroRecord;
use App\Models\Creditos\Cuenta\Records\CuentaUsuarioRecord;
use App\Models\Creditos\Cuenta\Records\BancoRecord;
use App\Models\Creditos\Credito\Records\CreditoResumenRecord;
use App\Models\Creditos\Cuenta\CuentaUsuario;
use App\Models\Creditos\Caja\Transferencia as CajaTransferencia;
use App\Models\Creditos\Cuenta\Transferencia as CuentaTransferencia;
use App\Models\Creditos\Caja\Caja;
use App\Models\General\Cierre;
use App\Models\General\Feriado;
use App\Models\General\Banco;
use App\Models\Creditos\Credito\Propuesta;
use App\Models\Creditos\Credito\Aprobacion;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

use App\Models\General\Datos_aplicacion;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Ifsnop\Mysqldump\Mysqldump;

class CajaCierreDiaController extends Controller
{

    public function cerrar_dia($agencia_id)
    {
        // Sacar copia de Backup, sólo en PRODUCCIÓN
        $enviroment = getenv('APP_ENV');
        if ($enviroment == 'production') {
            $this->database_backup($agencia_id);
        }
        // ---------------------------------

        $conexion = 'master_' .  $agencia_id;
        $fecha_actual_corta = date((new CreditosController)->fecha_corta_aplicacion($agencia_id));
        $fecha_nueva_corta = date("Y-m-d", strtotime($fecha_actual_corta . "+ 1 day"));

        $modulo = Datos_aplicacion::on($conexion)->where('descripcion', 'FECHA_CREDITOS')->get()->last();
        $modulo_id = $modulo->id;

        $cierre_id = $this->registrar_cierre($modulo_id, $agencia_id);

        $es_feriado = Feriado::where([
            ['fecha', $fecha_nueva_corta],
            ['agencias', 'like', '%' . $agencia_id . '%']
        ])->get()->count();

        if ($es_feriado > 0) {
            $es_feriado = true;
        } else {
            $es_feriado = false;
        }

        $es_domingo = date('N', strtotime($fecha_nueva_corta));

        if ($es_domingo == 7) {
            $es_domingo = true;
        } else {
            $es_domingo = false;
        }
        // Guardar creditos_registros
        $this->guardar_creditos($agencia_id, $cierre_id);
        // Guardar cuenta_usuarios
        $this->guardar_cuentas($agencia_id, $fecha_actual_corta, $fecha_nueva_corta, $cierre_id);
        // Guardar bancos
        $this->guardar_bancos($agencia_id, $fecha_actual_corta, $fecha_nueva_corta, $cierre_id);

        if (!$es_feriado && !$es_domingo) {

            //Aumentar dias atraso
            $this->actualizar_dias_atraso($agencia_id, $fecha_nueva_corta);

            //Aumentar moras
            $this->actualizar_moras($agencia_id, $fecha_actual_corta);
        }

        //Actualizar saldo total
        $this->actualizar_saldos($agencia_id, $fecha_actual_corta);
        // Generar y guardar resumen de creditos por asesor
        $this->guardar_resumen($agencia_id, $cierre_id, $fecha_nueva_corta);

        (new FacturacionLimiteController)->generar_limite_diario($fecha_actual_corta, $fecha_nueva_corta, $agencia_id);

        Datos_aplicacion::on($conexion)->where('descripcion', 'FECHA_CREDITOS')
            ->update([
                'valor_fecha' => $fecha_nueva_corta,
                'datos_actualizacion' => (new CreditosController)->datos_registro($agencia_id)
            ]);

        return redirect()->route('cre.index');
    }


    public function registrar_cierre($modulo_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $cierre = Cierre::on($conexion)->create(['modulo_id' => $modulo_id, 'datos_creacion' => $datos_registro]);

        $cierre_id = $cierre->id;

        return $cierre_id;
    }
    public function guardar_creditos($agencia_id, $cierre_id)
    {
        $conexion_master = 'master_' .  $agencia_id;
        $conexion_records = 'records_' .  $agencia_id;
        $estados = Estado::on($conexion_master)->select('id')->whereIn('estado', ['DESEMBOLSADO', 'CANCELADO PARCIAL'])->get();

        $creditos = Credito::on($conexion_master)->select(
            'id as credito_id',
            'agencia_id',
            'capital_pagado',
            'interes_pagado',
            'redondeo_pagado',
            'mora_pagado',
            'notificaciones_pagado',
            'dscto_mora_cancelado',
            'dscto_notificaciones_cancelado',
            'dscto_interes_cancelado',
            'saldo_total',
            'dias_atraso',
            DB::raw("'$cierre_id' as cierre_id"),

        )
            ->whereIn('estado_id', $estados)
            ->get();

        $creditos = $creditos->toArray();
        foreach (array_chunk($creditos, 1000) as $row) {
            CreditoRegistroRecord::on($conexion_records)->insert($row);
        }

        return true;
    }
    public function guardar_cuentas($agencia_id, $fecha_actual_corta, $fecha_nueva_corta, $cierre_id)
    {
        $conexion_master = 'master_' .  $agencia_id;
        $conexion_records = 'records_' .  $agencia_id;

        $usuarios_cuenta = CuentaUsuario::on($conexion_master)
            ->select(
                'id',
                'monto'
            )
            ->where('con_cuenta', 1)
            ->get();

        foreach ($usuarios_cuenta as $item) {

            CuentaUsuarioRecord::on($conexion_records)->where([
                ['cuenta_id', $item->id],
                ['fecha_inicio', $fecha_actual_corta],
                ['fecha_cierre', null]
            ])
                ->update([
                    'monto_final' => $item->monto,
                    'fecha_cierre' => $fecha_actual_corta,
                    'cierre_id' => $cierre_id
                ]);
        }

        $usuarios_cuenta = CuentaUsuario::on($conexion_master)
            ->select(
                'id as cuenta_id',
                DB::raw("'$agencia_id' as agencia_id"),
                'monto as monto_inicial',
                DB::raw("'$fecha_nueva_corta' as fecha_inicio"),
                DB::raw("null as fecha_cierre"),
                DB::raw("null as cierre_id"),
            )

            ->where('con_cuenta', 1)
            ->get();

        $usuarios_cuenta = $usuarios_cuenta->toArray();

        CuentaUsuarioRecord::on($conexion_records)->insert($usuarios_cuenta);

        return true;
    }

    public function guardar_bancos($agencia_id, $fecha_actual_corta, $fecha_nueva_corta, $cierre_id)
    {
        $conexion_records = 'records_' .  $agencia_id;

        $bancos = Banco::select(
            'id',
            'acumulado'
        )
            ->where([
                ['agencia_id', $agencia_id],
                ['habilitado', 1]
            ])
            ->get();

        foreach ($bancos as $item) {

            BancoRecord::on($conexion_records)->where([
                ['banco_id', $item->id],
                ['fecha', $fecha_actual_corta]
            ])
                ->update([
                    'monto_final' => $item->acumulado,
                    'cierre_id' => $cierre_id
                ]);
        }

        $bancos = Banco::select(
            'id as banco_id',
            DB::raw("'$agencia_id' as agencia_id"),
            'acumulado as monto_inicial',
            DB::raw("'$fecha_nueva_corta' as fecha"),
            DB::raw("null as cierre_id"),
        )
            ->where([
                ['agencia_id', $agencia_id],
                ['habilitado', 1]
            ])
            ->get();

        $bancos = $bancos->toArray();

        BancoRecord::on($conexion_records)->insert($bancos);

        return true;
    }
    public function actualizar_dias_atraso($agencia_id, $fecha_nueva_corta)
    {
        $conexion = 'master_' .  $agencia_id;

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        Cuota::on($conexion)->from('credito_cuotas as cre_cuo')
            ->join('credito_registros as cre_reg', 'cre_cuo.credito_id', 'cre_reg.id')
            ->where([
                ['cre_reg.estado_id', $estado_id],
                ['cre_cuo.fecha_vencimiento', '<', $fecha_nueva_corta],
                ['cre_cuo.estado', '!=', 'C']
            ])->update([
                'cre_cuo.estado' => 'V',
                'cre_cuo.dias_atraso' => DB::raw("cre_cuo.dias_atraso + 1")
            ]);

        Credito::on($conexion)->from('credito_registros as cre_reg')->where(
            'cre_reg.estado_id',
            $estado_id
        )->update([
            'cre_reg.cuotas_vencidas' => DB::raw('(
                select count(cre_cuo.id)
                from credito_cuotas as cre_cuo
                where cre_reg.id = cre_cuo.credito_id and cre_cuo.estado = "V")'),

            'cre_reg.monto_vencido' => DB::raw('(
                select sum(cre_cuo.cuota - cre_cuo.acumulado )
                 from credito_cuotas as cre_cuo
                 where cre_reg.id = cre_cuo.credito_id and cre_cuo.estado = "V")')

        ]);

        $creditos = Credito::on($conexion)->select('id')->where('estado_id', $estado_id)->get();

        foreach ($creditos as $item) {

            Credito::on($conexion)->where(
                'id',
                $item->id
            )->update(['dias_atraso' => (new CajaCobranzaController)->calcular_dias_atraso($item->id, $agencia_id, $fecha_nueva_corta)]);
        }
    }
    public function actualizar_moras($agencia_id, $fecha_actual_corta)
    {
        $conexion = 'master_' .  $agencia_id;

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_apr.monto',
                'cre_apr.cuota',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.es_especial',
                'cre_apr.mora_adicional'
            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->where('cre_reg.estado_id', $estado_id)
            ->get();

        $es_domingo = date('N', strtotime($fecha_actual_corta));

        if ($es_domingo == 7) {
            $fecha_actual_corta = date("Y-m-d", strtotime($fecha_actual_corta . "- 1 days"));
        }

        $es_feriado = Feriado::where([
            ['fecha', $fecha_actual_corta],
            ['agencias', 'like', '%' . $agencia_id . '%']
        ])->get()->count();

        while ($es_feriado > 0) {
            $fecha_actual_corta = date("Y-m-d", strtotime($fecha_actual_corta . "- 1 days"));

            $es_domingo = date('N', strtotime($fecha_actual_corta));

            if ($es_domingo == 7) {
                $fecha_actual_corta = date("Y-m-d", strtotime($fecha_actual_corta . "- 1 days"));
            }

            $es_feriado = Feriado::where([
                ['fecha', $fecha_actual_corta],
                ['agencias', 'like', '%' . $agencia_id . '%']
            ])->get()->count();
        }

        foreach ($creditos as $item) {

            $monto_mora = 0;

            $fecha_vence_hoy = Cuota::on($conexion)->where([
                ['credito_id', $item->id],
                ['estado', 'V'],
                ['fecha_vencimiento', $fecha_actual_corta]
            ])->orderBy('fecha_vencimiento', 'desc')->get();

            if (count($fecha_vence_hoy) > 0) {

                switch ($item->periodo_pago) {
                    case 'DIARIO':
                        // En caso de CRÉDITO DIARIO, la MORA ADICIONAL reemplaza la mora por defecto;
                        if ($item->es_especial) {
                            $monto_mora = floatval($item->mora_adicional);
                        } else {
                            $monto_mora = 0.5;
                        }
                        break;
                    case 'SEMANAL':
                        $monto_mora = 3;
                        break;
                    case 'QUINCENAL':
                        // $monto_mora = round((($item->cuota * $item->plazo) - $item->monto) / ($item->plazo * 15), 1);
                        $monto_mora = 5;
                        break;
                    case 'PAGO_UNICO':
                        $monto_mora = round(($item->cuota - $item->monto) / $item->plazo, 1);
                        $monto_mora = round(($item->cuota - $item->monto) / $item->plazo, 1);
                        break;
                    case 'MENSUAL':
                        $monto_mora = 5;
                        break;
                }
            } else if ($item->es_especial) {

                $cuotas_vencidas = Cuota::on($conexion)->where([
                    ['credito_id', $item->id],
                    ['estado', 'V']

                ])->get();

                if (count($cuotas_vencidas) > 0) {
                    $monto_mora = floatval($item->mora_adicional);
                }
            } else if (
                $item->periodo_pago == 'QUINCENAL' ||
                $item->periodo_pago == 'PAGO_UNICO' ||
                $item->periodo_pago == 'MENSUAL'
            ) {

                $cuotas_vencidas = Cuota::on($conexion)->where([
                    ['credito_id', $item->id],
                    ['estado', 'V']

                ])->get();

                if (count($cuotas_vencidas) > 0) {

                    switch ($item->periodo_pago) {
                        case 'QUINCENAL':
                            // $monto_mora = round((($item->cuota * $item->plazo) - $item->monto) / ($item->plazo * 15), 1);
                            $monto_mora = 5;
                            break;
                        case 'PAGO_UNICO':
                            $monto_mora = round(($item->cuota - $item->monto) / $item->plazo, 1);
                            break;
                        case 'MENSUAL':
                            $monto_mora = 5;
                            break;
                    }
                }
            }

            Credito::on($conexion)
                ->where('id', $item->id)
                ->update(['mora_total' => DB::raw("mora_total + $monto_mora")]);
        }
    }
    public function actualizar_saldos($agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        Credito::on($conexion)->from('credito_registros as cre_reg')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->where('cre_reg.estado_id', $estado_id)
            ->update(['saldo_total' => DB::raw("
            (capital_total - capital_pagado) +
            (interes_total - interes_pagado) +
            (redondeo_total - redondeo_pagado) +
            (cre_reg.mora_total - cre_reg.mora_pagado) +
            (cre_reg.notificaciones_total - cre_reg.notificaciones_pagado)")]);
    }
    public function guardar_resumen($agencia_id, $cierre_id, $fecha_nueva_corta)
    {
        $conexion_master = 'master_' .  $agencia_id;
        $conexion_records = 'records_' .  $agencia_id;

        $estado = Estado::on($conexion_master)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $creditos = Credito::on($conexion_master)->from('credito_registros as cre_reg')->select(
            'cli_reg.asesor_id as asesor_id',
            DB::raw("'$agencia_id' as agencia_id"),
            DB::raw('(SELECT cre_cen_rie.id FROM credito_central_riesgo cre_cen_rie WHERE cre_reg.dias_atraso between cre_cen_rie.dias_desde AND cre_cen_rie.dias_hasta ) AS tipo_riesgo_id'),
            DB::raw("COUNT(cre_reg.id) as cantidad"),
            DB::raw("SUM(cre_reg.capital_total) as capital_total"),
            DB::raw("SUM(cre_reg.capital_total-cre_reg.capital_pagado) as saldo_capital"),
            DB::raw("SUM(cre_reg.saldo_total) as saldo_total"),

            DB::raw("AVG(cre_apr.tasa_interes) as tasa_promedio"),

            DB::raw("(SELECT (COUNT(cre_reg.id)/COUNT(cre_reg_1.id))*100
            FROM credito_registros cre_reg_1
            INNER JOIN cliente_registros cli_reg_1 on cre_reg_1.cliente_id=cli_reg_1.id
            WHERE cli_reg.asesor_id = cli_reg_1.asesor_id
            AND cre_reg_1.estado_id = $estado_id
            GROUP BY cli_reg_1.asesor_id
             ) AS porcentaje_tipo"),

            DB::raw("(
                select (COUNT(cre_reg.id)/sum(t.cantidad))*100
                from(
                SELECT
                cli_reg_1.asesor_id,
                (SELECT cre_cen_rie_2.id
                FROM credito_central_riesgo cre_cen_rie_2
                WHERE cre_reg_1.dias_atraso between cre_cen_rie_2.dias_desde AND cre_cen_rie_2.dias_hasta ) AS tipo_riesgo_id_1,
                count(cre_reg_1.id) as cantidad
                from
                credito_registros cre_reg_1
                inner join
                cliente_registros cli_reg_1 on cre_reg_1.cliente_id=cli_reg_1.id
                where cre_reg_1.estado_id = $estado_id
                group by cli_reg_1.asesor_id,tipo_riesgo_id_1)
                as t
                where t.tipo_riesgo_id_1 = (SELECT cre_cen_rie.id FROM credito_central_riesgo cre_cen_rie WHERE cre_reg.dias_atraso between cre_cen_rie.dias_desde AND cre_cen_rie.dias_hasta )
                group by t.tipo_riesgo_id_1

             ) AS porcentaje_cartera"),

            DB::raw("(
                select (SUM(cre_reg.capital_total-cre_reg.capital_pagado)/sum(t.saldo_capital_total))*100
                from(
                SELECT
                cli_reg_1.asesor_id,
                (SELECT cre_cen_rie_2.id
                FROM credito_central_riesgo cre_cen_rie_2
                WHERE cre_reg_1.dias_atraso between cre_cen_rie_2.dias_desde AND cre_cen_rie_2.dias_hasta ) AS tipo_riesgo_id_1,
                sum(cre_reg_1.capital_total-cre_reg_1.capital_pagado) as saldo_capital_total
                from
                credito_registros cre_reg_1
                inner join
                cliente_registros cli_reg_1 on cre_reg_1.cliente_id=cli_reg_1.id
                where cre_reg_1.estado_id = $estado_id
                group by cli_reg_1.asesor_id,tipo_riesgo_id_1)
                as t
                where t.tipo_riesgo_id_1 = (SELECT cre_cen_rie.id FROM credito_central_riesgo cre_cen_rie WHERE cre_reg.dias_atraso between cre_cen_rie.dias_desde AND cre_cen_rie.dias_hasta )
                group by t.tipo_riesgo_id_1
             ) AS porcentaje_saldo_total"),

            DB::raw("(
                select (SUM(cre_reg.capital_total-cre_reg.capital_pagado)/sum(t.saldo_capital_total))*100
                from(
                SELECT
                cli_reg_1.asesor_id,
                sum(cre_reg_1.capital_total-cre_reg_1.capital_pagado) as saldo_capital_total
                from
                credito_registros cre_reg_1
                inner join
                cliente_registros cli_reg_1 on cre_reg_1.cliente_id=cli_reg_1.id
                where cre_reg_1.estado_id = $estado_id
                group by cli_reg_1.asesor_id)
                as t
                where t.asesor_id = cli_reg.asesor_id
                group by t.asesor_id
             ) AS porcentaje_saldo_cartera"),

            DB::raw("(
                select t.cantidad_clientes
                from(
                SELECT
                cli_reg_1.asesor_id,
                (SELECT cre_cen_rie_2.id
                FROM credito_central_riesgo cre_cen_rie_2
                WHERE cre_reg_1.dias_atraso between cre_cen_rie_2.dias_desde AND cre_cen_rie_2.dias_hasta ) AS tipo_riesgo_id_1,
                count(distinct(cre_reg_1.cliente_id)) as cantidad_clientes

                from
                credito_registros cre_reg_1
                inner join
                cliente_registros cli_reg_1 on cre_reg_1.cliente_id=cli_reg_1.id
                where cre_reg_1.estado_id = $estado_id
                group by cli_reg_1.asesor_id,tipo_riesgo_id_1)
                as t
                where cli_reg.asesor_id = t.asesor_id and t.tipo_riesgo_id_1 = (SELECT cre_cen_rie.id FROM credito_central_riesgo cre_cen_rie WHERE cre_reg.dias_atraso between cre_cen_rie.dias_desde AND cre_cen_rie.dias_hasta )
             ) AS cantidad_clientes"),

            DB::raw("(
                select t.cantidad_clientes
                from(
                SELECT
                cli_reg_1.asesor_id,
                count(distinct(cre_reg_1.cliente_id)) as cantidad_clientes
                from
                credito_registros cre_reg_1
                inner join
                cliente_registros cli_reg_1 on cre_reg_1.cliente_id=cli_reg_1.id
                where cre_reg_1.estado_id = $estado_id
                group by cli_reg_1.asesor_id)
                as t
                where cli_reg.asesor_id = t.asesor_id) AS cantidad_clientes_activos"),
            DB::raw("'$fecha_nueva_corta' as fecha_cartera"),
            DB::raw("'$cierre_id' as cierre_id")
        )
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_apr.id', 'cre_reg.aprobacion_id')

            ->where('cre_reg.estado_id', $estado_id)
            ->groupBy('cli_reg.asesor_id', 'tipo_riesgo_id')
            ->orderBy('cli_reg.asesor_id', 'asc')
            ->orderBy('tipo_riesgo_id', 'asc')
            ->get();

        $creditos = $creditos->toArray();

        foreach (array_chunk($creditos, 1000) as $row) {
            CreditoResumenRecord::on($conexion_records)->insert($row);
        }

        return true;
    }

    public function verificar_cierre_dia($agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $transferencias_pendientes_caja = CajaTransferencia::on($conexion)->where('estado', 'PENDIENTE')->count('id');

        $transferencias_pendientes_cuenta = CuentaTransferencia::on($conexion)->where('estado', 'PENDIENTE')->count('id');

        $cajas_abiertas = Caja::on($conexion)->where('datos_cierre', null)->count('id');

        $cajas_abiertas_usuarios = Caja::on($conexion)->from('caja_registros as caj_reg')->select('us.usuario')->join('solucion_master.usuarios as us', 'caj_reg.dni', 'us.dni')->where('caj_reg.datos_cierre', null)->get();



        $estado = Estado::on($conexion)->where('estado', 'APROBADO')->get()->last();
        $estado_id = $estado->id;

        $aprobaciones_pendientes = Aprobacion::on($conexion)->from('credito_aprobaciones as cre_apr')
            ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->where('cre_apr.estado_id', $estado_id)->count('cre_apr.id');

        $desembolsos_no_facturados = Desembolso::on($conexion)->from('caja_desembolsos as caj_des')
            ->join('credito_registros as cre_reg', 'caj_des.credito_id', 'cre_reg.id')
            ->where([
                [DB::raw("SUBSTR(caj_des.datos_creacion,11,10)"), $fecha_actual],
                ['caj_des.emite_comprobante', 1],
                ['caj_des.facturado', 0]
            ])->count('caj_des.id');

        return [
            'transferencias_pendientes_caja' => $transferencias_pendientes_caja,
            'transferencias_pendientes_cuenta' => $transferencias_pendientes_cuenta,
            'cajas_abiertas' => $cajas_abiertas,
            'cajas_abiertas_usuarios' => $cajas_abiertas_usuarios,
            'aprobaciones_pendientes' => $aprobaciones_pendientes,
            'desembolsos_no_facturados' => $desembolsos_no_facturados
        ];
    }

    public function anular_aprobaciones($agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $estado_aprobado = Estado::on($conexion)->select('id')->where('estado', 'APROBADO')->get()->last();
        $estado_aprobado_id = $estado_aprobado->id;

        $estado_anulado = Estado::on($conexion)->where('estado', 'ANULADO')->get()->last();
        $estado_anulado_id = $estado_anulado->id;

        // Anulando PROPUESTAS
        Aprobacion::on($conexion)
            ->where('estado_id', $estado_aprobado_id)
            ->update([
                'estado_id' => $estado_anulado_id,
                'datos_actualizacion' => $datos_registro
            ]);

        return redirect()->route('caj.cierre_dia');
    }

    public function database_backup($agencia_id)
    {

        // Configuración de la base de datos MASTER
        $master_host = getenv('S_MASTER_HOST_' . $agencia_id);
        $master_port = getenv('S_MASTER_PORT_' . $agencia_id);
        $master_database = getenv('S_MASTER_DATABASE_' . $agencia_id);
        $master_username = getenv('S_MASTER_USERNAME_' . $agencia_id);
        $master_password = getenv('S_MASTER_PASSWORD_' . $agencia_id);

        // Configuración de la base de datos RECORDS
        $records_host = getenv('S_RECORDS_HOST_' . $agencia_id);
        $records_port = getenv('S_RECORDS_PORT_' . $agencia_id);
        $records_database = getenv('S_RECORDS_DATABASE_' . $agencia_id);
        $records_username = getenv('S_RECORDS_USERNAME_' . $agencia_id);
        $records_password = getenv('S_RECORDS_PASSWORD_' . $agencia_id);

        // Ruta donde se almacenará el archivo de respaldo
        $backup_path = storage_path('app/backups/');

        // Nombre del archivo de respaldo MASTER
        $backup_master = 'solucion_master_' . $agencia_id . '_' . date('Y-m-d_H-i-s') . '.sql';

        // Nombre del archivo de respaldo RECORDS
        $backup_records = 'solucion_records_' . $agencia_id . '_' . date('Y-m-d_H-i-s') . '.sql';

        // Ruta completa del archivo de respaldo
        $backup_path_master = $backup_path . $backup_master;
        $backup_path_records = $backup_path . $backup_records;

        try {
            // Proceso MASTER
            $dump = new Mysqldump("mysql:host=$master_host;port=$master_port;dbname=$master_database", $master_username, $master_password);
            $dump->start($backup_path_master);

            // Proceso RECORDS
            $dump = new Mysqldump("mysql:host=$records_host;port=$records_port;dbname=$records_database", $records_username, $records_password);
            $dump->start($backup_path_records);

            // Descargar el archivo de respaldo
            return response()->json(['message' => 'BACKUP SUCCESS'], 201);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir durante el respaldo
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
