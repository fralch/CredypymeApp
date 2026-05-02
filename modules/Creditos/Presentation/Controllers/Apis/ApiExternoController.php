<?php

namespace Modules\Creditos\Presentation\Controllers\Apis;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Departamento;
use Modules\General\Infrastructure\Persistence\Eloquent\Provincia;
use Modules\General\Infrastructure\Persistence\Eloquent\Distrito;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Carrito;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Banco;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Notificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CarritoDetalle;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Prenda;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Pariente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Aval;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoVoucher;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMeta;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMetaMovimiento;

use Illuminate\Support\Facades\DB;


use App\Http\Controllers\Controller;
use Modules\Creditos\Presentation\Controllers\Caja\CajaCobranzaController;
use Illuminate\Http\Request;

class ApiExternoController extends Controller
{

    public function buscar_externo(Request $request)
    {

        $agencia_id = (new CreditosController)->verificar_nulo($request->agencia_id);

        if ($agencia_id == null) {
            return [];
        }

        $conexion = 'master_' .  $agencia_id;

        $texto_buscar = $request->texto_buscar;
        $tipo_filtro = $request->tipo_filtro;

        $columna = "";
        $operator = 'like';
        $search = "%$texto_buscar%";

        if ($tipo_filtro == 'apellidos_nombres') {
            $columna = 'cli_reg.apellido_paterno, " ", cli_reg.apellido_materno, " ", cli_reg.nombres';
        } else if ($tipo_filtro == 'dni') {
            $columna = 'cli_reg.dni';
        } else if ($tipo_filtro == 'expediente') {
            $columna = 'cli_reg.codigo_expediente';
        }

        $cancelados_parcial = $request->cancelados_parcial;
        if ($cancelados_parcial == 'true') {
            $lista_estados = ['DESEMBOLSADO', 'CANCELADO PARCIAL'];
        } else {
            $lista_estados = ['DESEMBOLSADO'];
        }

        $estados = Estado::on($conexion)->select('id')->whereIn('estado', $lista_estados)->get();

        $lista_creditos =  Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.dias_atraso',

                'caj_des.monto',
                'caj_des.datos_creacion',

                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.cuota',
                'cre_apr.numero_credito',

                'cli_reg.id as cliente_id',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.numero_expediente',

                'age.nombre as agencia',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_registro',

                'cre_est.estado',
                'cre_tip.tipo'
            )
            ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('solucion_master.agencias as age', 'cre_pro.agencia_id', 'age.id_agencia')
            ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->leftjoin('solucion_master.usuarios as us_2', DB::raw("SUBSTR(cre_apr.datos_creacion,42,8)"), 'us_2.dni')
            ->join('credito_estados as cre_est', 'cre_apr.estado_id', 'cre_est.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->whereIn('cre_reg.estado_id', $estados)
            ->where(
                DB::raw("CONCAT($columna)"),
                $operator,
                $search,
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'lista_creditos' => $lista_creditos
        ], 200);
    }


    public function informacion_credito(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $credito_id = $request->input('credito_id');

        $conexion = 'master_' .  $agencia_id;
        $main_db = 'solucion_master';

        $datos_credito = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.agencia_id',
                'cre_reg.datos_creacion',
                'cre_reg.dias_atraso',
                'cre_reg.cuota_actual',
                'cre_reg.cuotas_pendientes',
                'cre_reg.capital_total',
                'cre_reg.capital_pagado',
                'cre_reg.interes_total',
                'cre_reg.interes_pagado',
                'cre_reg.redondeo_total',
                'cre_reg.redondeo_pagado',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.notificaciones_total',
                'cre_reg.notificaciones_pagado',
                'cre_reg.saldo_total',
                'cre_reg.cuotas_vencidas',
                'cre_reg.monto_vencido',
                'cre_reg.acumulado',
                'cre_reg.estado_id',
                'cre_reg.cobrador_id',
                'cre_reg.fecha_desembolso',

                'cre_apr.monto',
                'cre_apr.cuota',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.dias_gracia_ci',
                'cre_apr.dias_gracia_si',
                'cre_apr.es_especial',
                'cre_apr.mora_adicional',
                'cre_apr.pago_oficina',
                'cre_apr.tasa_interes',
                'cre_apr.numero_credito',

                'cre_prop.id as propuesta_id',
                'cre_prop.agencia_pariente',
                'cre_prop.pariente_id',
                'cre_prop.agencia_aval',
                'cre_prop.aval_id',
                'cre_prop.agencia_pariente_aval',
                'cre_prop.pariente_aval_id',
                'cre_prop.prendario',
                'cre_prop.prendas',

                'caj_des.modo_desembolso',

                'cli_reg.id as cliente_id',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.codigo_expediente',
                'cli_reg.asesor_id',

                'cre_sec.sector',
                'cre_pro.producto',
                'cre_tip.tipo',
                'cre_est.estado',

                'usu.usuario as usuario_asesor',
            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credito_propuestas as cre_prop', 'cre_apr.propuesta_id', 'cre_prop.id')
            ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
            ->join('credito_sectores as cre_sec', 'cre_apr.sector_id', 'cre_sec.id')
            ->join('credito_productos as cre_pro', 'cre_apr.producto_id', 'cre_pro.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('credito_estados as cre_est', 'cre_reg.estado_id', 'cre_est.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join("$main_db.usuarios as usu", 'cli_reg.asesor_id', 'usu.dni')
            ->where('cre_reg.id', $credito_id)
            ->get()->last();

        $notificaciones = Notificacion::on($conexion)->from('credito_notificaciones as cre_not')
            ->select(
                'cre_not.id',
                'cre_not.credito_id',
                'cre_not.tipo_id',
                'cre_not.monto',
                'cre_not.acumulado',
                'cre_not.numero_cuota',
                'cre_not.usuario_envio',
                'cre_not.descripcion_envio',
                'cre_not.estado',
                'cre_not.datos_creacion',

                'cre_not_tip.tipo',
                'cre_not_tip.nombre_archivo',

                'us_1.usuario as usuario_envio_usuario',
                'us_2.usuario as usuario_registro'
            )
            ->join('credito_notificaciones_tipos as cre_not_tip', 'cre_not.tipo_id', 'cre_not_tip.id')
            ->leftjoin("$main_db.usuarios as us_1", 'cre_not.usuario_envio', 'us_1.dni')
            ->join("$main_db.usuarios as us_2", DB::raw("SUBSTRING(cre_not.datos_creacion,42,8)"), 'us_2.dni')
            ->where('cre_not.credito_id', $credito_id)
            ->orderby('cre_not.id', 'asc')
            ->get();

        $datos_cuotas = Cuota::on($conexion)->where('credito_id', $credito_id)->get();

        return response()->json([
            'status' => 200,
            'datos_credito' => $datos_credito,
            'creditos_vinculados_a' => [],
            'creditos_vinculados_de' => [],
            'notificaciones' => $notificaciones,
            'datos_cuotas' => $datos_cuotas,
            'usuarios_cobradores' => [],
            'credito_adicionales' => [],
            'bancos' => [],
            'cancelado' => $datos_credito->saldo_total == 0 ? 1 : 0,
        ]);
    }


    public function pagar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $agencia_origen = $request->agencia_origen;

        $conexion = 'master_' .  $agencia_id;

        // Para obtener el id de la caja en la base de datos de ADMINISTRATIVA externa
        $agencia_caja = 5;
        $conexion_caja = 'master_' . $agencia_caja;
        $caja_id = Caja::on($conexion_caja)->where('dni', '9999999' . $agencia_origen)->value('id');

        $datos_registro = $request->datos_sesion;
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $credito_id = $request->credito_id;
        $datos_cobranza = json_decode($request->datos_cobranza);
        // dd($datos_cobranza);
        $asesor_id = $request->asesor_id;

        $datos_credito = Credito::on($conexion)->where('id', $credito_id)->get()->last();
        // Obteniendo los datos del crédito

        $acumulado = $datos_credito->acumulado;

        $capital_total = $datos_credito->capital_total;
        $capital_pagado = $datos_credito->capital_pagado;
        $interes_total = $datos_credito->interes_total;
        $interes_pagado = $datos_credito->interes_pagado;
        $redondeo_total = $datos_credito->redondeo_total;
        $redondeo_pagado = $datos_credito->redondeo_pagado;

        $mora_total = $datos_credito->mora_total;
        $mora_pagado = $datos_credito->mora_pagado;

        $notificaciones_total = $datos_credito->notificaciones_total;
        $notificaciones_pagado = $datos_credito->notificaciones_pagado;

        $cuota_actual = $datos_credito->cuota_actual;
        $cuotas_pendientes = $datos_credito->cuotas_pendientes;
        $cuotas_vencidas = $datos_credito->cuotas_vencidas;
        $monto_vencido = $datos_credito->monto_vencido;
        $estado_id = $datos_credito->estado_id;

        $datos_voucher = [];

        $voucher_cuota = (object)[
            'concepto' => 'Pago de cuotas ' . $cuota_actual . ' - ',
            'importe' => 0
        ];

        $voucher_cuota_adelanto = (object)[
            'concepto' => 'Adelanto de cuotas ',
            'importe' => 0
        ];

        $voucher_mora = (object)[
            'concepto' => 'Pago de moras',
            'importe' => 0
        ];

        $voucher_notificaciones = (object)[
            'concepto' => 'Pago de notificaciones',
            'importe' => 0
        ];

        $pago_banco = filter_var($datos_cobranza->pago_banco, FILTER_VALIDATE_BOOLEAN);
        $banco_id = (new CreditosController)->verificar_nulo($datos_cobranza->banco_id);
        $comentario = (new CreditosController)->verificar_nulo($datos_cobranza->comentario);

        if ($banco_id) {
            $banco = Banco::find($banco_id);
            if ($comentario) {
                $comentario = mb_strtoupper(trim($comentario));
                $comentario .= ' (' . $banco->banco . ')';
            } else {
                $comentario = '(' . $banco->banco . ')';
            }
        }

        if ($datos_cobranza->pago_cuota) {

            if ($datos_cobranza->forma_pago == 'por_cuota') {

                $cantidad_cuotas = intVal($datos_cobranza->pago_cuota_cantidad);

                $cuotas_pagar = Cuota::on($conexion)->where([
                    ['credito_id', $credito_id],
                    ['estado', '<>', 'C']
                ])->take($cantidad_cuotas)->get();

                foreach ($cuotas_pagar as $item) {

                    $numero_cuota = $item->numero_cuota;

                    $saldo_capital = $item->capital - $item->capital_pagado;
                    $saldo_interes = $item->interes - $item->interes_pagado;
                    $saldo_redondeo = $item->redondeo - $item->redondeo_pagado;

                    $monto_cuota = $saldo_capital + $saldo_interes + $saldo_redondeo;

                    $voucher_cuota->importe += $monto_cuota;

                    $acumulado += $monto_cuota;

                    // $capital_pagado += $saldo_capital;
                    // $interes_pagado += $saldo_interes;
                    // $redondeo_pagado += $saldo_redondeo;

                    $cuota_actual += 1;
                    $cuotas_pendientes -= 1;

                    if ($item->estado == 'V') {
                        $cuotas_vencidas -= 1;
                        $monto_vencido -= $monto_cuota;
                    }

                    Cuota::on($conexion)->where('id', $item->id)->update([
                        'acumulado' => $item->cuota,
                        'capital_pagado' => $item->capital,
                        'interes_pagado' => $item->interes,
                        'redondeo_pagado' => $item->redondeo,
                        'estado' => 'C',
                        'fecha_ultimo_pago' => $fecha_larga,
                        'datos_actualizacion' => $datos_registro,
                    ]);

                    PagoCuota::on($conexion)->create([
                        'credito_id' => $credito_id,
                        'numero_cuota' => $numero_cuota,
                        'agencia_caja' => $agencia_caja,
                        'caja_id' => $caja_id,
                        'monto' => $monto_cuota,
                        'capital_pagado' => $saldo_capital,
                        'interes_pagado' => $saldo_interes,
                        'redondeo_pagado' => $saldo_redondeo,

                        'comentario' => $comentario,
                        'pago_banco' => 0,

                        'asesor_id' => $asesor_id,
                        'fecha_pago' => $fecha_larga,
                        'datos_creacion' => $datos_registro,
                    ]);
                }

                $voucher_cuota->concepto .= $cuota_actual - 1;
            } else if ($datos_cobranza->forma_pago == 'por_monto') {
                $monto_pago = round(floatval($datos_cobranza->pago_monto), 2);

                $cuotas_pagar = Cuota::on($conexion)->where([
                    ['credito_id', $credito_id],
                    ['estado', '<>', 'C']
                ])->get();


                foreach ($cuotas_pagar as $item) {
                    if ($monto_pago > 0) {
                        $numero_cuota = $item->numero_cuota;


                        $saldo_capital = $item->capital - $item->capital_pagado;
                        $saldo_interes = $item->interes - $item->interes_pagado;
                        $saldo_redondeo = $item->redondeo - $item->redondeo_pagado;

                        $restante_cuota = round(floatval($saldo_capital + $saldo_interes + $saldo_redondeo), 2);

                        $capital_pago = 0;
                        $interes_pago = 0;
                        $redondeo_pago = 0;


                        if (round($monto_pago, 2) >= round($restante_cuota, 2)) {

                            $capital_pago = $saldo_capital;
                            $interes_pago = $saldo_interes;
                            $redondeo_pago = $saldo_redondeo;

                            $monto_cuota = $capital_pago + $interes_pago + $redondeo_pago;
                            $voucher_cuota->importe += $monto_cuota;

                            $monto_pago -= $monto_cuota;

                            $cuota_actual += 1;
                            $cuotas_pendientes -= 1;


                            if ($item->estado == 'V') {
                                $cuotas_vencidas -= 1;
                                $monto_vencido -= $monto_cuota;
                            }

                            Cuota::on($conexion)->where('id', $item->id)->update([
                                'acumulado' => $item->cuota,
                                'capital_pagado' => $item->capital,
                                'interes_pagado' => $item->interes,
                                'redondeo_pagado' => $item->redondeo,
                                'estado' => 'C',
                                'fecha_ultimo_pago' => $fecha_larga,
                                'datos_actualizacion' => $datos_registro,
                            ]);
                        } else {

                            if (round($monto_pago, 2) >= round($saldo_capital, 2)) {

                                $monto_cuota = $saldo_capital;

                                $capital_pago = $saldo_capital;
                                $monto_pago -= $saldo_capital;
                                if ($monto_pago >= $saldo_interes) {
                                    $monto_cuota += $saldo_interes;
                                    $interes_pago = $saldo_interes;
                                    $monto_pago -= $saldo_interes;
                                    if ($monto_pago >= $saldo_redondeo) {
                                        $monto_cuota += $saldo_redondeo;
                                        $redondeo_pago = $saldo_redondeo;
                                        $monto_pago -= $saldo_redondeo;
                                    } else {
                                        $monto_cuota += $monto_pago;
                                        $redondeo_pago = $monto_pago;
                                        $monto_pago = 0;
                                    }
                                } else {
                                    $monto_cuota += $monto_pago;
                                    $interes_pago = $monto_pago;
                                    $monto_pago = 0;
                                }
                            } else {
                                $monto_cuota = $monto_pago;
                                $capital_pago = $monto_pago;
                                $monto_pago = 0;
                            }

                            $voucher_cuota_adelanto->importe += $monto_cuota;

                            if ($item->estado == 'V') {
                                $monto_vencido -= $monto_cuota;
                            }

                            Cuota::on($conexion)->where('id', $item->id)->update([
                                'acumulado' => $item->acumulado  + $monto_cuota,
                                'capital_pagado' => $item->capital_pagado + $capital_pago,
                                'interes_pagado' => $item->interes_pagado + $interes_pago,
                                'redondeo_pagado' => $item->redondeo_pagado + $redondeo_pago,
                                'estado' => $item->estado,
                                'fecha_ultimo_pago' => $fecha_larga,
                                'datos_actualizacion' => $datos_registro,
                            ]);
                        }

                        PagoCuota::on($conexion)->create([
                            'credito_id' => $credito_id,
                            'numero_cuota' => $numero_cuota,
                            'agencia_caja' =>  $agencia_caja,
                            'caja_id' => $caja_id,
                            'monto' => $monto_cuota,
                            'capital_pagado' => $capital_pago,
                            'interes_pagado' => $interes_pago,
                            'redondeo_pagado' => $redondeo_pago,
                            'comentario' => $comentario,
                            'pago_banco' => 0,
                            'asesor_id' => $asesor_id,
                            'fecha_pago' => $fecha_larga,
                            'datos_creacion' => $datos_registro,
                        ]);

                        // $capital_pagado += $capital_pago;
                        // $interes_pagado += $interes_pago;
                        // $redondeo_pagado += $redondeo_pago;
                        $acumulado += $monto_cuota;
                    } else {
                        break;
                    };
                }
                $voucher_cuota->concepto .= $cuota_actual - 1;
                $voucher_cuota_adelanto->concepto .= $cuota_actual;
            }
            $fecha_corta_aplicacion = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
            $dias_atraso = (new CajaCobranzaController)->calcular_dias_atraso($credito_id, $agencia_id, $fecha_corta_aplicacion);

            if ($datos_cobranza->pago_mora) {
                $mora_pagado += $datos_cobranza->pago_mora_monto;
                $voucher_mora->importe += $datos_cobranza->pago_mora_monto;

                PagoMora::on($conexion)->create([
                    'credito_id' => $credito_id,
                    'agencia_caja' =>  $agencia_caja,
                    'caja_id' => $caja_id,
                    'monto' => $datos_cobranza->pago_mora_monto,
                    'comentario' => $comentario,
                    'pago_banco' => 0,
                    'asesor_id' => $asesor_id,
                    'fecha_pago' => $fecha_larga,
                    'datos_creacion' => $datos_registro
                ]);
            }

            if ($datos_cobranza->pago_notificaciones) {
                $notificaciones_pagado += $datos_cobranza->pago_notificaciones_monto;
                $voucher_notificaciones->importe += $datos_cobranza->pago_notificaciones_monto;

                $monto_notificacion = round(floatval($datos_cobranza->pago_notificaciones_monto), 2);

                $notificaciones_pagar = Notificacion::on($conexion)->where([
                    ['credito_id', $credito_id],
                    ['estado', 'P']
                ])->get();

                foreach ($notificaciones_pagar as $item) {
                    if (round($monto_notificacion, 2) > 0) {
                        $notificacion_id = $item->id;

                        $restante_notificacion = round(floatval($item->monto), 2) - round(floatval($item->acumulado), 2);


                        if ($monto_notificacion >= $restante_notificacion) {

                            Notificacion::on($conexion)->where('id', $notificacion_id)->update([
                                'acumulado' => DB::raw("monto"),
                                'estado' => 'C',
                                'datos_actualizacion' => $datos_registro,
                            ]);

                            PagoNotificacion::on($conexion)->create([
                                'notificacion_id' => $notificacion_id,
                                'agencia_caja' =>  $agencia_caja,
                                'caja_id' => $caja_id,
                                'monto' => $restante_notificacion,
                                'comentario' => $comentario,
                                'pago_banco' => 0,
                                'fecha_pago' => $fecha_larga,
                                'datos_creacion' => $datos_registro,
                            ]);
                        } else {
                            Notificacion::on($conexion)->where('id', $notificacion_id)->update([
                                'acumulado' => DB::raw("acumulado + $monto_notificacion"),
                                'datos_actualizacion' => $datos_registro,
                            ]);

                            PagoNotificacion::on($conexion)->create([
                                'notificacion_id' => $notificacion_id,
                                'agencia_caja' =>  $agencia_caja,
                                'caja_id' => $caja_id,
                                'monto' => $monto_notificacion,
                                'comentario' => $comentario,
                                'pago_banco' => 0,
                                'fecha_pago' => $fecha_larga,
                                'datos_creacion' => $datos_registro,
                            ]);
                        }

                        $monto_notificacion -= $restante_notificacion;
                    } else {
                        break;
                    };
                }
            }

            $datos_cuotas = Cuota::on($conexion)->where('credito_id', $credito_id)->get();

            $capital_pagado = $datos_cuotas->sum('capital_pagado');
            $interes_pagado = $datos_cuotas->sum('interes_pagado');
            $redondeo_pagado = $datos_cuotas->sum('redondeo_pagado');

            $saldo_total = ($capital_total - $capital_pagado) +
                ($interes_total - $interes_pagado) +
                ($redondeo_total - $redondeo_pagado) + ($mora_total - $mora_pagado) +
                ($notificaciones_total - $notificaciones_pagado);

            if (round($saldo_total, 2) < 0) {
                $saldo_total = 0;
            }

            $cuotas_pagar = Cuota::on($conexion)->where([
                ['credito_id', $credito_id],
                ['estado', '<>', 'C']
            ])->get();

            $cuotas_pagadas = Cuota::on($conexion)->where([
                ['credito_id', $credito_id],
                ['estado', 'C']
            ])->count();


            if (count($cuotas_pagar) == 0 && round($saldo_total, 2) > 0) {
                $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO PARCIAL')->get()->last();
                $estado_id = $estado->id;

                Credito::on($conexion)->where('id', $credito_id)->update([
                    'acumulado' => $acumulado,
                    'saldo_total' => $saldo_total,
                    'capital_pagado' => $capital_pagado,
                    'interes_pagado' => $interes_pagado,
                    'redondeo_pagado' => $redondeo_pagado,
                    'notificaciones_pagado' => $notificaciones_pagado,
                    'mora_pagado' => $mora_pagado,
                    'cuota_actual' => $cuotas_pagadas,
                    'cuotas_pendientes' => 0,
                    'cuotas_vencidas' => $cuotas_vencidas,
                    'monto_vencido' => $monto_vencido,
                    'estado_id' => $estado_id,
                    'dias_atraso' => $dias_atraso,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,
                ]);
            } else if (count($cuotas_pagar) == 0 && round($saldo_total, 2) == 0) {
                $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
                $estado_id = $estado->id;

                Credito::on($conexion)->where('id', $credito_id)->update([
                    'acumulado' => $capital_total + $interes_total + $redondeo_total,
                    'saldo_total' => 0,
                    'capital_pagado' => $capital_total,
                    'interes_pagado' => $interes_total,
                    'redondeo_pagado' => $redondeo_total,
                    'mora_pagado' => $mora_total,
                    'notificaciones_pagado' => $notificaciones_pagado,
                    'cuota_actual' => $cuotas_pagadas,
                    'cuotas_pendientes' => 0,
                    'cuotas_vencidas' => 0,
                    'monto_vencido' => 0,
                    'estado_id' => $estado_id,
                    'dias_atraso' => 0,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,
                ]);
            } else {
                Credito::on($conexion)->where('id', $credito_id)->update([
                    'acumulado' => $acumulado,
                    'saldo_total' => $saldo_total,
                    'capital_pagado' => $capital_pagado,
                    'interes_pagado' => $interes_pagado,
                    'redondeo_pagado' => $redondeo_pagado,
                    'mora_pagado' => $mora_pagado,
                    'notificaciones_pagado' => $notificaciones_pagado,
                    'cuota_actual' => $cuota_actual,
                    'cuotas_pendientes' => $cuotas_pendientes,
                    'cuotas_vencidas' => $cuotas_vencidas,
                    'monto_vencido' => $monto_vencido,
                    'estado_id' => $estado_id,
                    'dias_atraso' => $dias_atraso,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,
                ]);
            }
        } else if ($datos_cobranza->pago_mora) {

            $mora_pagado += $datos_cobranza->pago_mora_monto;
            $voucher_mora->importe += $datos_cobranza->pago_mora_monto;


            PagoMora::on($conexion)->create([
                'credito_id' => $credito_id,
                'agencia_caja' => $agencia_caja,
                'caja_id' => $caja_id,
                'monto' => $datos_cobranza->pago_mora_monto,
                'comentario' => $comentario,
                'pago_banco' => 0,
                'asesor_id' => $asesor_id,
                'fecha_pago' => $fecha_larga,
                'datos_creacion' => $datos_registro
            ]);


            if ($datos_cobranza->pago_notificaciones) {
                $notificaciones_pagado += $datos_cobranza->pago_notificaciones_monto;
                $voucher_notificaciones->importe += $datos_cobranza->pago_notificaciones_monto;

                $monto_notificacion = round(floatval($datos_cobranza->pago_notificaciones_monto), 2);

                $notificaciones_pagar = Notificacion::on($conexion)->where([
                    ['credito_id', $credito_id],
                    ['estado', 'P']
                ])->get();

                foreach ($notificaciones_pagar as $item) {
                    if (round($monto_notificacion, 2) > 0) {
                        $notificacion_id = $item->id;

                        $restante_notificacion = round(floatval($item->monto), 2) - round(floatval($item->acumulado), 2);


                        if (round($monto_notificacion, 2) >= round($restante_notificacion, 2)) {

                            Notificacion::on($conexion)->where('id', $notificacion_id)->update([
                                'acumulado' => DB::raw("monto"),
                                'estado' => 'C',
                                'datos_actualizacion' => $datos_registro,
                            ]);

                            PagoNotificacion::on($conexion)->create([
                                'notificacion_id' => $notificacion_id,
                                'agencia_caja' =>  $agencia_caja,
                                'caja_id' => $caja_id,
                                'monto' => $restante_notificacion,
                                'comentario' => $comentario,
                                'pago_banco' => 0,
                                'fecha_pago' => $fecha_larga,
                                'datos_creacion' => $datos_registro,
                            ]);
                        } else {
                            Notificacion::on($conexion)->where('id', $notificacion_id)->update([
                                'acumulado' => DB::raw("acumulado + $monto_notificacion"),
                                'datos_actualizacion' => $datos_registro,
                            ]);

                            PagoNotificacion::on($conexion)->create([
                                'notificacion_id' => $notificacion_id,
                                'agencia_caja' =>  $agencia_caja,
                                'caja_id' => $caja_id,
                                'monto' => $monto_notificacion,
                                'comentario' => $comentario,
                                'pago_banco' => 0,
                                'fecha_pago' => $fecha_larga,
                                'datos_creacion' => $datos_registro,
                            ]);
                        }

                        $monto_notificacion -= $restante_notificacion;
                    } else {
                        break;
                    };
                }
            }

            $saldo_total = ($capital_total - $capital_pagado) +
                ($interes_total - $interes_pagado) +
                ($redondeo_total - $redondeo_pagado) + ($mora_total - $mora_pagado) +
                ($notificaciones_total - $notificaciones_pagado);

            if ($saldo_total < 0) {
                $saldo_total = 0;
            }

            $cuotas_pagar = Cuota::on($conexion)->where([
                ['credito_id', $credito_id],
                ['estado', '<>', 'C']
            ])->get();

            $cuotas_pagadas = Cuota::on($conexion)->where([
                ['credito_id', $credito_id],
                ['estado', 'C']
            ])->count();

            if (count($cuotas_pagar) == 0 && $saldo_total > 0) {
                $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO PARCIAL')->get()->last();
                $estado_id = $estado->id;

                Credito::on($conexion)->where('id', $credito_id)->update([
                    'saldo_total' => $saldo_total,
                    'mora_pagado' => $mora_pagado,
                    'estado_id' => $estado_id,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,

                ]);
            } else if (count($cuotas_pagar) == 0 && $saldo_total == 0) {
                $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
                $estado_id = $estado->id;

                Credito::on($conexion)->where('id', $credito_id)->update([
                    'acumulado' => $acumulado,
                    'saldo_total' => 0,
                    'capital_pagado' => $capital_total,
                    'interes_pagado' => $interes_total,
                    'redondeo_pagado' => $redondeo_total,
                    'mora_pagado' => $mora_total,
                    'notificaciones_pagado' => $notificaciones_total,
                    'estado_id' => $estado_id,
                    'cuota_actual' => $cuotas_pagadas,
                    'cuotas_pendientes' => 0,
                    'cuotas_vencidas' => 0,
                    'monto_vencido' => 0,
                    'dias_atraso' => 0,
                    'dscto_mora_cancelado' => 0,
                    'dscto_notificaciones_cancelado' => 0,
                    'dscto_interes_cancelado' => 0,
                    'comentario_cancelado' => 'CANCELADO',
                    'documento_cancelado' => null,
                    'caja_cancelado_id' => $caja_id,
                    'fecha_hora_cancelado' => $fecha_larga,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,

                ]);
            } else {
                Credito::on($conexion)->where('id', $credito_id)->update([
                    'saldo_total' => $saldo_total,
                    'mora_pagado' => $mora_pagado,
                    'notificaciones_pagado' => $notificaciones_pagado,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro
                ]);
            }
        } else if ($datos_cobranza->pago_notificaciones) {

            $notificaciones_pagado += $datos_cobranza->pago_notificaciones_monto;
            $voucher_notificaciones->importe += $datos_cobranza->pago_notificaciones_monto;

            $monto_notificacion = round(floatval($datos_cobranza->pago_notificaciones_monto), 2);

            $notificaciones_pagar = Notificacion::on($conexion)->where([
                ['credito_id', $credito_id],
                ['estado', 'P']
            ])->get();


            foreach ($notificaciones_pagar as $item) {
                if (round($monto_notificacion, 2) > 0) {
                    $notificacion_id = $item->id;

                    $restante_notificacion = round(floatval($item->monto), 2) - round(floatval($item->acumulado), 2);

                    if (round($monto_notificacion, 2) >= round($restante_notificacion, 2)) {

                        Notificacion::on($conexion)->where('id', $notificacion_id)->update([
                            'acumulado' => DB::raw("monto"),
                            'estado' => 'C',
                            'datos_actualizacion' => $datos_registro,
                        ]);

                        PagoNotificacion::on($conexion)->create([
                            'notificacion_id' => $notificacion_id,
                            'agencia_caja' =>  $agencia_caja,
                            'caja_id' => $caja_id,
                            'monto' => $restante_notificacion,
                            'comentario' => $comentario,
                            'pago_banco' => 0,
                            'fecha_pago' => $fecha_larga,
                            'datos_creacion' => $datos_registro,
                        ]);
                    } else {
                        Notificacion::on($conexion)->where('id', $notificacion_id)->update([
                            'acumulado' => DB::raw("acumulado + $monto_notificacion"),
                            'datos_actualizacion' => $datos_registro,
                        ]);

                        PagoNotificacion::on($conexion)->create([
                            'notificacion_id' => $notificacion_id,
                            'agencia_caja' =>  $agencia_caja,
                            'caja_id' => $caja_id,
                            'monto' => $monto_notificacion,
                            'comentario' => $comentario,
                            'pago_banco' => 0,
                            'fecha_pago' => $fecha_larga,
                            'datos_creacion' => $datos_registro,
                        ]);
                    }

                    $monto_notificacion -= $restante_notificacion;
                } else {
                    break;
                };
            }

            $saldo_total = ($capital_total - $capital_pagado) +
                ($interes_total - $interes_pagado) +
                ($redondeo_total - $redondeo_pagado) + ($mora_total - $mora_pagado) +
                ($notificaciones_total - $notificaciones_pagado);

            if (round($saldo_total, 2) < 0) {
                $saldo_total = 0;
            }

            $cuotas_pagar = Cuota::on($conexion)->where([
                ['credito_id', $credito_id],
                ['estado', '<>', 'C']
            ])->get();

            $cuotas_pagadas = Cuota::on($conexion)->where([
                ['credito_id', $credito_id],
                ['estado', 'C']
            ])->count();

            if (count($cuotas_pagar) == 0 && round($saldo_total, 2) > 0) {
                $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO PARCIAL')->get()->last();
                $estado_id = $estado->id;

                Credito::on($conexion)->where('id', $credito_id)->update([
                    'saldo_total' => $saldo_total,
                    'notificaciones_pagado' => $notificaciones_pagado,
                    'estado_id' => $estado_id,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,

                ]);
            } else if (count($cuotas_pagar) == 0 && round($saldo_total, 2) == 0) {
                $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
                $estado_id = $estado->id;

                Credito::on($conexion)->where('id', $credito_id)->update([
                    'acumulado' => $acumulado,
                    'saldo_total' => 0,
                    'capital_pagado' => $capital_total,
                    'interes_pagado' => $interes_total,
                    'redondeo_pagado' => $redondeo_total,
                    'mora_pagado' => $mora_total,
                    'notificaciones_pagado' => $notificaciones_total,
                    'estado_id' => $estado_id,
                    'cuota_actual' => $cuotas_pagadas,
                    'cuotas_pendientes' => 0,
                    'cuotas_vencidas' => 0,
                    'monto_vencido' => 0,
                    'dias_atraso' => 0,
                    'dscto_mora_cancelado' => 0,
                    'dscto_notificaciones_cancelado' => 0,
                    'dscto_interes_cancelado' => 0,
                    'comentario_cancelado' => 'CANCELADO',
                    'documento_cancelado' => null,
                    'caja_cancelado_id' => $caja_id,
                    'fecha_hora_cancelado' => $fecha_larga,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,

                ]);
            } else {
                Credito::on($conexion)->where('id', $credito_id)->update([
                    'saldo_total' => $saldo_total,
                    'notificaciones_pagado' => $notificaciones_pagado,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro
                ]);
            }
        }

        $cancelado = 0;

        $voucher_cuota->importe = round($voucher_cuota->importe, 2);
        $voucher_cuota_adelanto->importe = round($voucher_cuota_adelanto->importe, 2);
        $voucher_mora->importe = round($voucher_mora->importe, 2);
        $voucher_notificaciones->importe = round($voucher_notificaciones->importe, 2);

        $voucher_cuota->importe > 0 ? $datos_voucher[] = $voucher_cuota : null;
        $voucher_cuota_adelanto->importe > 0 ? $datos_voucher[] = $voucher_cuota_adelanto : null;
        $voucher_mora->importe > 0 ? $datos_voucher[] = $voucher_mora : null;
        $voucher_notificaciones->importe > 0 ? $datos_voucher[] = $voucher_notificaciones : null;

        $voucher = json_encode($datos_voucher);

        PagoVoucher::on($conexion)->create([
            'credito_id' => $credito_id,
            'agencia_id' =>  $agencia_caja,
            'caja_id' => $caja_id,
            'datos_voucher' =>  $voucher,
            'datos_creacion' => $datos_registro,
            'numero_cuota' => $cuota_actual
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'Cobranza registrada con éxito',
            'cancelado' => $cancelado,
            'datos_voucher' => $datos_voucher
        ]);
    }

    public function cancelar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $agencia_origen = $request->agencia_origen;

        $conexion = 'master_' .  $agencia_id;

        $main_db = 'solucion_master';

        // Para obtener el id de la caja en la base de datos de ADMINISTRATIVA externa
        $agencia_caja = 5;
        $conexion_caja = 'master_' . $agencia_caja;
        $caja_id = Caja::on($conexion_caja)->where('dni', '9999999' . $agencia_origen)->value('id');


        $datos_registro = $request->datos_sesion;
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_id);
        $año = substr($fecha_larga, 0, 4);


        $credito_id = $request->credito_id;
        $datos_cancelacion = json_decode($request->datos_cancelacion);
        $dscto_moras = round(floatVal($datos_cancelacion->dscto_moras), 2);
        $dscto_notificaciones = round(floatVal($datos_cancelacion->dscto_notificaciones), 2);
        $dscto_interes = round(floatVal($datos_cancelacion->dscto_interes), 2);

        $total_descuento = $dscto_moras + $dscto_notificaciones + $dscto_interes;

        $asesor_id = $request->asesor_id;

        $datos_credito = Credito::on($conexion)->where('id', $credito_id)->get()->last();

        $capital_total = round(floatVal($datos_credito->capital_total), 2);
        $interes_total = round(floatVal($datos_credito->interes_total), 2);
        $redondeo_total = round(floatVal($datos_credito->redondeo_total), 2);
        $mora_total = round(floatVal($datos_credito->mora_total), 2);
        $notificaciones_total = round(floatVal($datos_credito->notificaciones_total), 2);
        $cuota_actual = intVal($datos_credito->cuota_actual);

        $mora_pendiente = $mora_total - round(floatVal($datos_credito->mora_pagado), 2);
        $mora_pago = $mora_pendiente;

        $notificaciones_pendiente = $notificaciones_total - round(floatVal($datos_credito->notificaciones_pagado), 2);
        $notificaciones_pago = $notificaciones_pendiente - $dscto_notificaciones;

        $datos_voucher = [];

        $voucher_cuota = (object)[
            'concepto' => 'Cancelación de cuotas',
            'importe' => 0
        ];
        $voucher_mora = (object)[
            'concepto' => 'Pago de moras',
            'importe' => $mora_pendiente,
        ];
        $voucher_notificaciones = (object)[
            'concepto' => 'Pago de notificaciones',
            'importe' =>  $notificaciones_pendiente,
        ];
        $voucher_descuento_interes = (object)[
            'concepto' => 'Dscto. interés',
            'importe' => $dscto_interes * -1
        ];
        $voucher_descuento_moras = (object)[
            'concepto' => 'Dscto. moras',
            'importe' => $dscto_moras * -1
        ];
        $voucher_descuento_notificaciones = (object)[
            'concepto' => 'Dscto. notificaciones',
            'importe' => $dscto_notificaciones * -1
        ];


        $pago_banco = filter_var($datos_cancelacion->pago_banco, FILTER_VALIDATE_BOOLEAN);
        $banco_id = (new CreditosController)->verificar_nulo($datos_cancelacion->banco_id);

        $comentario = 'CANCELACIÓN';
        $comentario_cancelado = (new CreditosController)->verificar_nulo($datos_cancelacion->comentario);

        if ($banco_id) {
            $banco = Banco::find($banco_id);
            $comentario .= ' (' . $banco->banco . ')';
        }

        if ($comentario_cancelado) {
            $comentario .= '-' . mb_strtoupper($comentario_cancelado);
        }

        $cuotas_pagar = Cuota::on($conexion)->where([
            ['credito_id', $credito_id],
            ['estado', '<>', 'C']
        ])->get();

        foreach ($cuotas_pagar as $item) {

            $numero_cuota = $item->numero_cuota;
            $saldo_capital = $item->capital - $item->capital_pagado;
            $saldo_interes = $item->interes - $item->interes_pagado;
            $saldo_redondeo = $item->redondeo - $item->redondeo_pagado;

            $monto_cuota = $saldo_capital + $saldo_interes + $saldo_redondeo;
            $voucher_cuota->importe += $monto_cuota;

            $cuota_actual = $item->numero_cuota;

            Cuota::on($conexion)->where('id', $item->id)->update([
                'acumulado' => $item->cuota,
                'capital_pagado' => $item->capital,
                'interes_pagado' => $item->interes,
                'redondeo_pagado' => $item->redondeo,
                'estado' => 'C',
                'fecha_ultimo_pago' => $fecha_larga,
                'datos_actualizacion' => $datos_registro,
            ]);

            PagoCuota::on($conexion)->create([
                'credito_id' => $credito_id,
                'numero_cuota' => $numero_cuota,
                'agencia_caja' => $agencia_caja,
                'caja_id' => $caja_id,
                'monto' => $monto_cuota,
                'capital_pagado' => $saldo_capital,
                'interes_pagado' => $saldo_interes,
                'redondeo_pagado' => $saldo_redondeo,
                'comentario' => $comentario,
                'pago_banco' => 0,
                'asesor_id' => $asesor_id,
                'fecha_pago' => $fecha_larga,
                'datos_creacion' => $datos_registro,
            ]);
        }

        $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
        $estado_id = $estado->id;

        $acumulado = $capital_total + $interes_total +
            $redondeo_total;


        $nombre_documento = null;

        if (round($total_descuento, 2) > 0) {

            $path_name = pathinfo($_FILES['documento']['name']);
            $extension = "." . $path_name['extension'];
            $nombre_documento =  $año . '_' . $credito_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/creditos/caja/cancelaciones/' . $agencia_id . '/' . $año;
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
            $calidad = 10;
            // move_uploaded_file($archivo, $ruta);
            (new CreditosController)->compressImage($archivo, $ruta, $calidad);
        }

        if ($mora_pago > 0) {
            PagoMora::on($conexion)->create([
                'credito_id' => $credito_id,
                'agencia_caja' => $agencia_caja,
                'caja_id' => $caja_id,
                'monto' => $mora_pago,
                'comentario' => $comentario,
                'pago_banco' => 0,
                'asesor_id' => $asesor_id,
                'fecha_pago' => $fecha_larga,
                'datos_creacion' => $datos_registro
            ]);
        }

        $notificaciones_pagar = Notificacion::on($conexion)->where([
            ['credito_id', $credito_id],
            ['estado', 'P']
        ])->get();

        foreach ($notificaciones_pagar as $item) {

            $notificacion_id = $item->id;

            $restante_notificacion = round(floatval($item->monto), 2) - round(floatval($item->acumulado), 2);

            Notificacion::on($conexion)->where('id', $notificacion_id)->update([
                'acumulado' => $item->monto,
                'estado' => 'C',
                'datos_actualizacion' => $datos_registro,
            ]);

            PagoNotificacion::on($conexion)->create([
                'notificacion_id' => $notificacion_id,
                'agencia_caja' => $agencia_caja,
                'caja_id' => $caja_id,
                'monto' => $restante_notificacion,
                'comentario' => $comentario,
                'pago_banco' => 0,
                'fecha_pago' => $fecha_larga,
                'datos_creacion' => $datos_registro,
            ]);
        }

        Credito::on($conexion)->where('id', $credito_id)->update([
            'acumulado' => $acumulado,
            'saldo_total' => 0,
            'capital_pagado' => $capital_total,
            'interes_pagado' => $interes_total,
            'redondeo_pagado' => $redondeo_total,
            'mora_pagado' => $mora_total,
            'notificaciones_pagado' => $notificaciones_total,
            'estado_id' => $estado_id,
            'cuota_actual' => $cuota_actual,
            'cuotas_pendientes' => 0,
            'cuotas_vencidas' => 0,
            'monto_vencido' => 0,
            'dias_atraso' => 0,
            'fecha_ultimo_pago' => $fecha_larga,
            'dscto_mora_cancelado' => $dscto_moras,
            'dscto_notificaciones_cancelado' => $dscto_notificaciones,
            'dscto_interes_cancelado' => $dscto_interes,
            'comentario_cancelado' =>  $comentario,
            'documento_cancelado' => $nombre_documento,
            'agencia_caja_cancelado' => $agencia_caja,
            'caja_cancelado_id' => $caja_id,
            'fecha_hora_cancelado' => $fecha_larga,
            'datos_actualizacion' => $datos_registro

        ]);

        $prendario = filter_var($request->prendario, FILTER_VALIDATE_BOOLEAN);

        if ($prendario) {
            $prendas = json_decode($request->prendas);

            foreach ($prendas as $value) {
                Prenda::on($conexion)->where('id', $value)->update([
                    'disponible' => 1,
                    'datos_actualizacion' => $datos_registro
                ]);
            }
        }

        $voucher_cuota->importe != 0 ? $datos_voucher[] = $voucher_cuota : null;
        $voucher_mora->importe != 0 ? $datos_voucher[] = $voucher_mora : null;
        $voucher_notificaciones->importe != 0 ? $datos_voucher[] = $voucher_notificaciones : null;
        $voucher_descuento_interes->importe != 0 ? $datos_voucher[] = $voucher_descuento_interes : null;
        $voucher_descuento_moras->importe != 0 ? $datos_voucher[] = $voucher_descuento_moras : null;
        $voucher_descuento_notificaciones->importe != 0 ? $datos_voucher[] = $voucher_descuento_notificaciones : null;

        $voucher = json_encode($datos_voucher);

        PagoVoucher::on($conexion)->create([
            'credito_id' => $credito_id,
            'agencia_id' => $agencia_caja,
            'caja_id' => $caja_id,
            'datos_voucher' =>  $voucher,
            'datos_creacion' => $datos_registro,
            'numero_cuota' => $cuota_actual

        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cancelación registrada con éxito',
            'cancelado' => 1,
            'datos_voucher' => $datos_voucher
        ]);
    }

    public function listar_pagos_cuotas(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $credito_id = $request->credito_id;

        $pagos = PagoCuota::on($conexion)->from('caja_pago_cuotas as caj_pag_cuo')
            ->select(
                'caj_pag_cuo.id',
                'caj_pag_cuo.agencia_caja',
                'caj_pag_cuo.caja_id',
                'caj_pag_cuo.monto',
                'caj_pag_cuo.numero_cuota',
                'caj_pag_cuo.numero_recibo',
                'caj_pag_cuo.comentario',
                'caj_pag_cuo.datos_creacion'
            )
            ->where('caj_pag_cuo.credito_id', $credito_id)
            ->get();

        foreach ($pagos as $item) {
            $conexion = 'master_' .  $item->agencia_caja;
            $datos_caja = Caja::on($conexion)->from('caja_registros as caj_reg')
                ->select('usu.usuario')
                ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                ->where('caj_reg.id', $item->caja_id)
                ->get()
                ->last();
            $item->usuario_caja = $datos_caja->usuario;
        }

        return response()->json([
            'status' => 'success',
            'pago_cuotas' => $pagos
        ], 200);
    }

    public function listar_pagos_moras(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $credito_id = $request->credito_id;

        $pagos =  PagoMora::on($conexion)->from('caja_pago_moras as caj_pag_mor')
            ->select(
                'caj_pag_mor.id',
                'caj_pag_mor.agencia_caja',
                'caj_pag_mor.caja_id',
                'caj_pag_mor.datos_creacion',
                'caj_pag_mor.monto',
                'caj_pag_mor.comentario'
            )
            ->where('caj_pag_mor.credito_id', $credito_id)
            ->get();

        foreach ($pagos as $item) {
            $conexion = 'master_' .  $item->agencia_caja;
            $datos_caja = Caja::on($conexion)->from('caja_registros as caj_reg')
                ->select('usu.usuario')
                ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                ->where('caj_reg.id', $item->caja_id)
                ->get()
                ->last();
            $item->usuario_caja = $datos_caja->usuario;
        }

        return response()->json([
            'status' => 'success',
            'pago_moras' => $pagos
        ], 200);
    }

    public function listar_pagos_notificaciones(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $credito_id = $request->credito_id;

        $pagos =  PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
            ->select(
                'caj_pag_not.id',
                'caj_pag_not.agencia_caja',
                'caj_pag_not.caja_id',
                'caj_pag_not.datos_creacion',
                'caj_pag_not.monto',
                'caj_pag_not.comentario'
            )
            ->join('credito_notificaciones as cre_not', 'caj_pag_not.notificacion_id', 'cre_not.id')
            ->where('cre_not.credito_id', $credito_id)
            ->get();

        foreach ($pagos as $item) {
            $conexion = 'master_' .  $item->agencia_caja;
            $datos_caja = Caja::on($conexion)->from('caja_registros as caj_reg')
                ->select('usu.usuario')
                ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                ->where('caj_reg.id', $item->caja_id)
                ->get()
                ->last();
            $item->usuario_caja = $datos_caja->usuario;
        }

        return response()->json([
            'status' => 'success',
            'pago_notificaciones' => $pagos
        ], 200);
    }


    public function buscar_inversiones(Request $request)
    {
        $agencia_id = (new CreditosController)->verificar_nulo($request->agencia_id);

        if ($agencia_id == null) {
            return [];
        }

        $conexion = 'master_' .  $agencia_id;
        $texto_buscar = $request->texto_buscar;

        $columna1 = 'cre_reg.apellido_paterno, " ", cre_reg.apellido_materno, " ", cre_reg.nombres';
        $columna2 = 'inv_pro_met.producto';
        $operator = 'like';
        $search = "%$texto_buscar%";


        $lista_inversiones = InversionMeta::on($conexion)->from('inversion_meta_registros as inv_met_reg')
            ->select(
                'cre_reg.id as cliente_id',
                'cre_reg.dni',
                'cre_reg.apellido_paterno',
                'cre_reg.apellido_materno',
                'cre_reg.nombres',

                'inv_met_reg.id',
                'inv_met_reg.fecha_apertura',
                'inv_met_reg.acumulado',

                'inv_pro_met.producto',
                DB::raw("'META' as tipo")

            )
            ->join('cliente_registros as cre_reg', 'cre_reg.id', 'inv_met_reg.cliente_id')
            ->join('inversion_productos_meta as inv_pro_met', 'inv_met_reg.producto_meta_id', 'inv_pro_met.id')
            ->where([
                ['inv_met_reg.fecha_cierre', null],
                [DB::raw("CONCAT($columna1)"), $operator, $search]
            ])
            ->orWhere([
                ['inv_met_reg.fecha_cierre', null],
                [$columna2, $operator, $search]
            ])
            ->get();


        return response()->json([
            'status' => 'success',
            'lista_inversiones' => $lista_inversiones
        ]);
    }

    public function listar_recursos_inversion(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $inversion_id = $request->inversion_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_inversion = InversionMeta::on($conexion)
            ->from('inversion_meta_registros as inv_met_reg')
            ->select(
                'cli_reg.id as cliente_id',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',

                'inv_met_reg.id as inversion_id',
                'inv_met_reg.valor_meta',
                'inv_met_reg.acumulado',
                'inv_met_reg.datos_creacion',
                'inv_met_reg.fecha_movimiento',
                'inv_met_reg.fecha_cierre',

                'inv_pro_met.producto',

                'usu.usuario as usuario_registro'
            )
            ->join('cliente_registros as cli_reg', 'cli_reg.id', 'inv_met_reg.cliente_id')
            ->join('inversion_productos_meta as inv_pro_met', 'inv_pro_met.id', 'inv_met_reg.producto_meta_id')
            ->join('solucion_master.usuarios as usu', DB::raw('SUBSTR(inv_met_reg.datos_creacion,42,8)'), 'usu.dni')
            ->where('inv_met_reg.id', $inversion_id)
            ->get()->last();


        $cerrado = 0;

        if ($datos_inversion->fecha_cierre != null) {
            $cerrado = 1;
        }

        $lista_movimientos = InversionMetaMovimiento::on($conexion)
            ->from('inversion_meta_movimientos as inv_met_mov')
            ->select(
                'inv_met_mov.id',
                'inv_met_mov.tipo',
                'inv_met_mov.monto',
                'inv_met_mov.comentario',
                'inv_met_mov.agencia_caja',
                'inv_met_mov.caja_id',
                DB::raw("SUBSTRING(inv_met_mov.datos_creacion,11,19) as fecha_registro"),
            )
            ->where('inv_met_mov.inversion_id', $inversion_id)
            ->get();


        $lista_movimientos = $lista_movimientos->map(function ($row) {


            $conexion_caja = 'master_' . $row['agencia_caja'];
            $caja_id = $row['caja_id'];

            $datos_caja = Caja::on($conexion_caja)->find($caja_id);
            $usuario_caja = Usuario::find($datos_caja->dni);

            $row['agencia_caja'] = (new CreditosController)->agencia_abreviacion($row['agencia_caja']);
            $row['usuario_caja'] = $usuario_caja->usuario;

            return $row;
        });

        $bancos = Banco::where([
            ['agencia_id', $agencia_id],
            ['habilitado', 1]
        ])
            ->orderBy('banco', 'asc')
            ->get();

        return response()->json(
            [
                'success' => 200,
                'datos_inversion' => $datos_inversion,
                'agencia_id' => intVal($agencia_id),
                'lista_movimientos' => $lista_movimientos,
                'bancos' => $bancos,
                'cerrado' => $cerrado
            ]
        );
    }

    public function abonar_inversion(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $agencia_origen = $request->agencia_origen;

        // Para obtener el id de la caja en la base de datos de ADMINISTRATIVA externa
        $agencia_caja = 5;
        $conexion_caja = 'master_' . $agencia_caja;
        $caja_id = Caja::on($conexion_caja)->where('dni', '9999999' . $agencia_origen)->value('id');

        $datos_registro = $request->datos_sesion;
        $fecha_movimiento = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $inversion_id = $request->inversion_id;

        $monto = $request->monto;
        $comentario =  (new CreditosController)->verificar_nulo($request->comentario);

        if ($comentario !== null) {
            $comentario = mb_strtoupper($comentario);
        }

        InversionMetaMovimiento::on($conexion)->create(
            [
                'inversion_id' =>   $inversion_id,
                'tipo' =>   'I',
                'monto' =>   $monto,
                'comentario' => $comentario,
                'agencia_caja' =>  $agencia_caja,
                'caja_id' =>   $caja_id,
                'datos_creacion' =>   $datos_registro
            ]
        );

        $inversion = InversionMeta::on($conexion)->find($inversion_id);

        $inversion->fecha_movimiento = $fecha_movimiento;
        $inversion->acumulado += floatval($monto);
        $inversion->datos_actualizacion = $datos_registro;
        $inversion->save();

        $datos_voucher = [
            'fecha_registro' => $fecha_movimiento,
            'comentario' => $comentario,
        ];

        return response()->json([
            'status' => 200,
            'message' => 'Operación REGISTRADA',
            'datos_voucher' => $datos_voucher
        ]);
    }

    public function buscar_cliente(Request $request)
    {

        $dni = $request->input('dni');

        $agencias = [2, 3, 5];

        $resultado = 'NO_RESTRINGIDO';
        $agencia = null;

        foreach ($agencias as $item) {
            $conexion = 'master_' .  $item;
            $main_db = 'solucion_master';

            $existe_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                ->select(
                    'cli_reg.id',
                    'cli_reg.dni',
                    'ag.nombre as agencia'
                )
                ->join($main_db . '.agencias as ag', 'ag.id_agencia', 'cli_reg.agencia_id')
                ->where('cli_reg.dni', $dni)
                ->get();


            if (count($existe_cliente) > 0) {

                // RESTRICCIÓN DE CRÉDITOS VIGENTES----------------------------------

                $estado_id = Estado::on($conexion)->where('estado', 'DESEMBOLSADO')->value('id');

                $creditos_vigentes = Credito::on($conexion)->where([
                    ['cliente_id', $existe_cliente[0]->id],
                    ['estado_id', $estado_id]
                ])->count();

                if ($creditos_vigentes == 0) {

                    // RESTRICCIÓN DE INACTIVIDAD----------------------------------
                    $ultimo_credito = Credito::on($conexion)
                        ->where('cliente_id', $existe_cliente[0]->id)
                        ->orderBy('id', 'desc')->first();

                    $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($item);
                    $fecha_actual = date('Y-m-d', strtotime($fecha_corta));

                    if ($ultimo_credito != null) {
                        $fecha_inactivo = $ultimo_credito->fecha_ultimo_pago;
                        $fecha_limite = date('Y-m-d', strtotime($fecha_inactivo . ' + 2 month'));

                        if ($fecha_actual < $fecha_limite) {
                            $resultado = 'RESTRINGIDO';
                            $agencia = $existe_cliente[0]->agencia;
                            break;
                        }
                    }
                } else {
                    $resultado = 'RESTRINGIDO';
                    $agencia = $existe_cliente[0]->agencia;
                    break;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'resultado' => $resultado,
            'agencia' => $agencia
        ]);
    }

    public function datos_cliente(Request $request)
    {

        $conexion = 'master_' .  $request->agencia_id;

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
            ->where('cli_reg.id', $request->cliente_id)
            ->get()->last();

        return response()->json([
            'status' => 200,
            'datos_cliente' => $datos_cliente
        ]);
    }

    public function verificar_cliente(Request $request)
    {
        $dni = $request->dni;

        $agencias = Agencia::all();

        $lista_clientes = [];
        foreach ($agencias as $item) {
            if ($item->id_agencia != 5) {
                $conexion = 'master_' . $item->id_agencia;

                $lista_clientes = Cliente::on($conexion)->from('cliente_registros as cli')
                    ->select(
                        'cli.id',
                        'cli.dni',

                        'cli.apellido_paterno',
                        'cli.apellido_materno',
                        'cli.nombres',
                        'cli.fecha_nacimiento',
                        'cli.estado_civil',
                        'cli.sexo',
                        'cli.hijos',
                        'cli.agencia_id',
                        'ag.nombre as agencia',
                        'cli.correo_electronico',

                        'cli.codigo_expediente',
                        DB::raw("IFNULL(cli.asesor_id,0) as asesor_id"),
                        'us.usuario as usuario_asesor',
                        DB::raw("IFNULL(cli.promotor_id,0) as promotor_id"),
                        'cli.central_riesgo',
                        'cli.canal_referencia',

                        'cli.monto_maximo',
                        'cli.notas',
                        'cli.reportar_equifax',

                        'cli.direccion',
                        'cli.departamento_id',
                        'dep.departamento',
                        'cli.provincia_id',
                        'pro.provincia',
                        'cli.distrito_id',
                        'dis.distrito',
                        'cli.referencia_direccion',
                        'cli.telefonos',

                        'cli.imagen_dni',
                        'cli.observaciones',
                    )
                    ->join('solucion_master.agencias as ag', 'ag.id_agencia', 'cli.agencia_id')
                    ->join('solucion_master.departamentos as dep', 'dep.id', 'cli.departamento_id')
                    ->join('solucion_master.provincias as pro', 'pro.id', 'cli.provincia_id')
                    ->join('solucion_master.distritos as dis', 'dis.id', 'cli.distrito_id')
                    ->leftjoin('solucion_master.usuarios as us', 'cli.asesor_id', 'us.dni')
                    ->where('cli.dni', $dni)
                    ->get();
                if (count($lista_clientes) > 0) {
                    break;
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'lista_clientes' => $lista_clientes
        ]);
    }

    public function buscar_parientes_avales(Request $request)
    {
        $tipo = $request->tipo;
        $agencia_busqueda = $request->agencia_id;
        $cliente_busqueda = $request->cliente_id;

        $agencias = Agencia::all();

        $lista = [];
        if ($tipo == 'PARIENTE') {
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
                        ['cli_par.pariente_id', $cliente_busqueda],
                        ['cli_par.agencia_pariente', $agencia_busqueda]
                    ])
                    ->get();

                foreach ($parientes as $item_1) {
                    $lista[] = $item_1;
                }
            }
        } else if ($tipo == 'AVAL') {


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
                        ['cli_ava.aval_id', $cliente_busqueda],
                        ['cli_ava.agencia_aval', $agencia_busqueda]
                    ])
                    ->get();

                foreach ($avales as $item_1) {
                    $lista[] = $item_1;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'clientes' => $lista
        ]);
    }

    public function datos_pariente_aval(Request $request)
    {
        $conexion_aval = 'master_' . $request->agencia_id;

        $datos_pariente_aval = Pariente::on($conexion_aval)->select(
            'id',
            'agencia_pariente',
            'pariente_id as pariente_aval_id',
            'parentesco'
        )->where([
            ['cliente_id', $request->cliente_id],
            ['vinculado', 1]
        ])->get()->last();

        return response()->json([
            'status' => 'success',
            'datos_pariente_aval' => $datos_pariente_aval
        ]);
    }
}
