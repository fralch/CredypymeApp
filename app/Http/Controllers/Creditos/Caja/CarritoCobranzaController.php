<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\General\PermisosController;

use App\Models\Gth\Usuarios\Usuario;

use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Negocio;
use App\Models\Creditos\Clientes\Prenda;

use App\Models\Creditos\Credito\Compromiso;
use App\Models\Creditos\Credito\Aprobacion;
use App\Models\Creditos\Credito\Notificacion;
use App\Models\Creditos\Credito\NotificacionTipo;
use App\Models\Creditos\Clientes\Pariente;

use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Credito\Carrito;
use App\Models\Creditos\Credito\CarritoDetalle;
use Illuminate\Support\Facades\DB;

use App\Models\Creditos\Caja\PagoCuota;
use App\Models\Creditos\Caja\PagoMora;
use App\Models\Creditos\Caja\PagoNotificacion;
use App\Models\Creditos\Caja\PagoVoucher;

use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\General\GeneralController;
use App\Http\Controllers\Apis\ApiSmsController;
use App\Http\Controllers\Apis\ApiWhatsAppController;

use App\Models\General\Feriado;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\General\Agencia;

use Inertia\Inertia;

use Illuminate\Support\Facades\Storage;

class CarritoCobranzaController extends Controller
{
    public function carrito_cobranzas()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CARRITO_COBRANZAS', 'CREDITOS_CAJA');
            if ($band == 1) {


                $agencia_id = $x['id_agencia'];

                return Inertia::render('Creditos/Caja/carrito_cobranzas', [
                    'agencia_id' => $agencia_id,

                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function carrito_cobranzas_buscar(request $request)
    {
        $fecha = $request->input('fecha');
        $agencia_carrito = $request->input('agencia_id');

        $conexion = 'master_' .  $agencia_carrito;

        $lista_carritos = Carrito::on($conexion)->from('credito_carritos as cre_car')
            ->select(
                'cre_car.id',
                'cre_car.usuario_id',
                'usu.usuario'
            )
            ->join('solucion_master.usuarios as usu', 'cre_car.usuario_id', 'usu.dni')
            ->where([
                [DB::raw("SUBSTR(cre_car.fecha_apertura,1,10)"), $fecha],
                ['cre_car.fecha_cierre', null],
                ['cre_car.pagado', 0]
            ])
            ->get();

        $agencias = Agencia::all();

        foreach ($lista_carritos as $item) {

            $pendientes_pago = 0;
            $total_pendiente_pago = 0;

            foreach ($agencias as $item_2) {
                $conexion_agencia = 'master_' .  $item_2->id_agencia;

                $cobranzas = CarritoDetalle::on($conexion_agencia)
                    ->where([
                        ['agencia_carrito', $agencia_carrito],
                        ['carrito_id', $item->id]
                    ])
                    ->get();

                $cantidad = $cobranzas->where('estado', 'C')->count();
                $total = $cobranzas->where('estado', 'C')->sum('total_cobro');

                $pendientes_pago += $cantidad;
                $total_pendiente_pago += $total;
            }

            $item->pendientes_pago = $pendientes_pago;
            $item->total_pendiente_pago = $total_pendiente_pago;
        }

        return ['lista_carritos' => $lista_carritos];
    }



    public function carrito_cobranzas_creditos(Request $request)
    {
        $carrito_id = $request->input('carrito_id');
        $agencia_carrito = $request->input('agencia_carrito');

        $agencias = Agencia::all();

        $lista_creditos = [];

        foreach ($agencias as $item) {
            $conexion_agencia = 'master_' . $item->id_agencia;

            $cobranzas = CarritoDetalle::on($conexion_agencia)
                ->select(
                    'id',
                    'credito_id',
                    'agencia_carrito',
                    'carrito_id',
                    'total_cobro'
                )
                ->where([
                    ['agencia_carrito', $agencia_carrito],
                    ['carrito_id', $carrito_id],
                    ['estado', 'C']
                ])
                ->get();

            foreach ($cobranzas as $item_1) {

                $datos_credito = Credito::on($conexion_agencia)->find($item_1->credito_id);
                $datos_cliente = Cliente::on($conexion_agencia)->find($datos_credito->cliente_id);

                $cliente = $datos_cliente->apellido_paterno . ' ' .
                    $datos_cliente->apellido_materno . ' ' .
                    $datos_cliente->nombres;

                $item_1->cliente = $cliente;
                $item_1->cliente_id = $datos_cliente->id;

                $asesor = Usuario::find($datos_credito->asesor_id);
                $item_1->usuario_asesor = $asesor->usuario;

                $item_1->agencia_id = $item->id_agencia;
                $item_1->agencia_credito = $item->nombre;




                $anulado = CarritoDetalle::on($conexion_agencia)->where([
                    ['credito_id', $item_1->credito_id],
                    ['carrito_id', $carrito_id]
                ])
                    ->where('estado', "A")->get()->count();

                $modificado = CarritoDetalle::on($conexion_agencia)->where([
                    ['credito_id', $item_1->credito_id],
                    ['carrito_id', $carrito_id],
                    ['datos_actualizacion', '!=', null]
                ])->get()->count();

                $observacion = "-";
                if ($modificado >= 1) {

                    $observacion = "MODIFICADO";
                }
                if ($anulado >= 1) {

                    $observacion = "ANULADO";
                }

                $item_1->observacion = $observacion;

                $lista_creditos[] = $item_1;
            }
        }

        return ['lista_creditos' => $lista_creditos];
    }


    public function carrito_cobranzas_pagar(Request $request)
    {
        // dd($request);
        $lista_creditos = json_decode($request->lista_creditos);
        $cantidad_total_creditos =  $request->cantidad_total_creditos;
        $agencia_carrito =   $request->agencia_carrito;
        $carrito_id =   $request->carrito_id;
        $caja_id = $request->caja_id;
        $agencia_caja =   $request->agencia_caja;

        $conexion_carrito = 'master_' .  $agencia_carrito;

        // Creando objeto con información para realizar el pago
        $pago = (object)[
            'caja_id' => $caja_id,
            'agencia_caja' => $agencia_caja
        ];

        $lista_vouchers = [];
        foreach ($lista_creditos as $item) {

            $conexion_credito = 'master_' . $item->agencia_id;

            $carrito_detalle = CarritoDetalle::on($conexion_credito)
                ->find($item->id);

            $pago->agencia_credito = $item->agencia_id;
            $pago->detalle_pago = $carrito_detalle;

            // Método para realizar el pago de la cobranza
            $voucher_pago  = $this->pagar_credito($pago);

            // Para verificar si el tipo de pago es cobranza o cancelacion
            $tipo_voucher = 'cobranza';
            $credito = Credito::on($conexion_credito)->find($carrito_detalle->credito_id);
            $aprobacion = Aprobacion::on($conexion_credito)->find($credito->aprobacion_id);

            if ($credito->estado_id == 4 || $credito->estado_id  == 5) {
                $tipo_voucher  = 'cancelacion';
            }

            $lista_vouchers[] = (object)[
                'agencia_credito' => $item->agencia_id,
                'credito_id' => $carrito_detalle->credito_id,
                'voucher_pago' => $voucher_pago,
                'tipo_voucher' => $tipo_voucher,
                'cantidad_cuotas' => $aprobacion->plazo,
                'cuota' => $aprobacion->cuota
            ];
        }

        // Para cerrar el carrito en caso se pague todo

        if ($cantidad_total_creditos == count($lista_creditos)) {

            $datos_registro = (new CreditosController)->datos_registro($agencia_carrito);
            $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_carrito);

            Carrito::on($conexion_carrito)->where('id', $carrito_id)->update([
                'pagado' => 1,
                'fecha_cierre' => $fecha_larga,
                'datos_actualizacion' => $datos_registro
            ]);
        }

        return [
            'lista_vouchers' => $lista_vouchers,
        ];
    }

    public function pagar_credito($pago)
    {
        $agencia_credito = $pago->agencia_credito;
        $detalle_pago = $pago->detalle_pago;
        $credito_id = $detalle_pago->credito_id;
        $caja_id = $pago->caja_id;
        $agencia_caja = $pago->agencia_caja;

        // INICIO DEL PROCESO --------------------------

        $conexion = 'master_' . $agencia_credito;

        $datos_registro = (new CreditosController)->datos_registro($agencia_credito);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_credito);
        $fecha_corta_aplicacion = (new CreditosController)->fecha_corta_aplicacion($agencia_credito);

        $datos_credito = Credito::on($conexion)->find($credito_id);

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
            'importe' => 0,
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

        $voucher_saldo_total = (object)[
            'concepto' => 'Saldo total',
            'importe' => 0
        ];

        // Si se ha seleccionado PAGO POR CUOTA o PAGO POR MONTO

        if ($detalle_pago->pago_por_cuota == 1 || $detalle_pago->pago_por_monto == 1) {


            if ($detalle_pago->pago_por_cuota == 1) {
                // Si se ha realiado PAGO POR CUOTA

                $cantidad_cuotas = intVal($detalle_pago->pago_cantidad_cuota);

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
                        'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                        'comentario' => "CARRITO",
                        'asesor_id' => $detalle_pago->asesor_id,
                        'fecha_pago' => $fecha_larga,
                        'datos_creacion' => $datos_registro,
                    ]);
                }

                $voucher_cuota->concepto .= $cuota_actual - 1;
            } else if ($detalle_pago->pago_por_monto == 1) {

                // Si se ha realizado PAGO POR MONTO

                $monto_pago = round(floatval($detalle_pago->pago_monto), 2);

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
                            'agencia_caja' => $agencia_caja,
                            'caja_id' => $caja_id,
                            'monto' => $monto_cuota,
                            'capital_pagado' => $capital_pago,
                            'interes_pagado' => $interes_pago,
                            'redondeo_pagado' => $redondeo_pago,
                            'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                            'comentario' => "CARRITO",
                            'asesor_id' => $detalle_pago->asesor_id,
                            'fecha_pago' => $fecha_larga,
                            'datos_creacion' => $datos_registro,
                        ]);

                        $acumulado += $monto_cuota;
                    } else {
                        break;
                    };
                }
                $voucher_cuota->concepto .= $cuota_actual - 1;
                $voucher_cuota_adelanto->concepto .= $cuota_actual;
            }

            // Calcular nuevo DIAS DE ATRASO después de realizar el PAGO
            $dias_atraso = (new CajaCobranzaController)->calcular_dias_atraso($credito_id, $agencia_credito, $fecha_corta_aplicacion);


            if ($detalle_pago->pago_mora == 1) {
                // Si además del pago, se ha realizado algún PAGO DE MORA

                $mora_pagado += $detalle_pago->pago_mora_monto;
                $voucher_mora->importe += $detalle_pago->pago_mora_monto;

                PagoMora::on($conexion)->create([
                    'credito_id' => $credito_id,
                    'agencia_caja' => $agencia_caja,
                    'caja_id' => $caja_id,
                    'monto' => $detalle_pago->pago_mora_monto,
                    'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                    'comentario' => "CARRITO",
                    'asesor_id' => $detalle_pago->asesor_id,
                    'fecha_pago' => $fecha_larga,
                    'datos_creacion' => $datos_registro
                ]);
            }

            if ($detalle_pago->pago_notificaciones == 1) {
                // Si además del pago, se ha realizado algún PAGO DE NOTIFICACIONES

                $notificaciones_pagado += $detalle_pago->pago_notificaciones_monto;
                $voucher_notificaciones->importe += $detalle_pago->pago_notificaciones_monto;

                $monto_notificacion = round(floatval($detalle_pago->pago_notificaciones_monto), 2);

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
                                'agencia_caja' => $agencia_caja,
                                'caja_id' => $caja_id,
                                'monto' => $restante_notificacion,
                                'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                                'comentario' => "CARRITO",
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
                                'agencia_caja' => $agencia_caja,
                                'caja_id' => $caja_id,
                                'monto' => $monto_notificacion,
                                'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                                'comentario' => "CARRITO",
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

            // Generar nueva información a registrar después del PAGO

            $datos_cuotas = Cuota::on($conexion)->where('credito_id', $credito_id)->get();

            $capital_pagado = $datos_cuotas->sum('capital_pagado');
            $interes_pagado = $datos_cuotas->sum('interes_pagado');
            $redondeo_pagado = $datos_cuotas->sum('redondeo_pagado');

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


            if (count($cuotas_pagar) == 0 && round($saldo_total, 2) > 0) {

                // Si ya no quedan cuotas por pagar pero el SALTO TOTAL aún no es 0

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

                // Si ya no quedan cuotas por pagar y el SALTO TOTAL es 0

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
                    'comentario_cancelado' => 'CARRITO',
                    'agencia_caja_cancelado' => $agencia_caja,
                    'caja_cancelado_id' => $caja_id,
                    'fecha_hora_cancelado' => $fecha_larga,
                ]);
            } else {

                // Si todavía quedan CUOTAS POR PAGAR

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
        } else if ($detalle_pago->pago_mora == 1) {

            // Si no se pagaron CUOTAS pero si MORAS

            $mora_pagado += $detalle_pago->pago_mora_monto;
            $voucher_mora->importe += $detalle_pago->pago_mora_monto;

            PagoMora::on($conexion)->create([
                'credito_id' => $credito_id,
                'agencia_caja' => $agencia_caja,
                'caja_id' => $caja_id,
                'monto' => $detalle_pago->pago_mora_monto,
                'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                'comentario' => "CARRITO",
                'asesor_id' => $detalle_pago->asesor_id,
                'fecha_pago' => $fecha_larga,
                'datos_creacion' => $datos_registro
            ]);

            if ($detalle_pago->pago_notificaciones == 1) {

                // Si además del pago de MORA, se pagó NOTIFICACIONES

                $notificaciones_pagado += $detalle_pago->pago_notificaciones_monto;
                $voucher_notificaciones->importe += $detalle_pago->pago_notificaciones_monto;

                $monto_notificacion = round(floatval($detalle_pago->pago_notificaciones_monto), 2);

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
                                'agencia_caja' => $agencia_caja,
                                'caja_id' => $caja_id,
                                'monto' => $restante_notificacion,
                                'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                                'comentario' => "CARRITO",
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
                                'agencia_caja' => $agencia_caja,
                                'caja_id' => $caja_id,
                                'monto' => $monto_notificacion,
                                'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                                'comentario' => "CARRITO",
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

            // Generar nueva información a registrar después del PAGO

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

                // Si ya no quedan cuotas por pagar pero el SALTO TOTAL aún no es 0

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

                // Si ya no quedan cuotas por pagar y el SALTO TOTAL es 0

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
                    'comentario_cancelado' => 'CARRITO',
                    'documento_cancelado' => null,
                    'caja_cancelado_id' => $caja_id,
                    'fecha_hora_cancelado' => $fecha_larga,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,
                    'agencia_caja_cancelado' => $agencia_caja,

                ]);
            } else {

                // Si todavía quedan CUOTAS POR PAGAR

                Credito::on($conexion)->where('id', $credito_id)->update([
                    'saldo_total' => $saldo_total,
                    'mora_pagado' => $mora_pagado,
                    'notificaciones_pagado' => $notificaciones_pagado,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro
                ]);
            }
        } else if ($detalle_pago->pago_notificaciones == 1) {

            // Si no se pagaron CUOTAS pero si NOTIFICACIONES

            $notificaciones_pagado += $detalle_pago->pago_notificaciones_monto;
            $voucher_notificaciones->importe += $detalle_pago->pago_notificaciones_monto;

            $monto_notificacion = round(floatval($detalle_pago->pago_notificaciones_monto), 2);

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
                            'agencia_caja' => $agencia_caja,
                            'caja_id' => $caja_id,
                            'monto' => $restante_notificacion,
                            'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                            'comentario' => "CARRITO",
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
                            'agencia_caja' => $agencia_caja,
                            'caja_id' => $caja_id,
                            'monto' => $monto_notificacion,
                            'usuario_cobrador' => $detalle_pago->usuario_cobrador,
                            'comentario' => "CARRITO",
                            'fecha_pago' => $fecha_larga,
                            'datos_creacion' => $datos_registro,
                        ]);
                    }

                    $monto_notificacion -= $restante_notificacion;
                } else {
                    break;
                };
            }

            // Generar nueva información a registrar después del PAGO

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

            if (count($cuotas_pagar) == 0 && round($saldo_total, 2) > 0) {

                // Si ya no quedan cuotas por pagar pero el SALTO TOTAL aún no es 0

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

                // Si ya no quedan cuotas por pagar y el SALTO TOTAL es 0

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
                    'comentario_cancelado' => 'CARRITO',
                    'documento_cancelado' => null,
                    'caja_cancelado_id' => $caja_id,
                    'fecha_hora_cancelado' => $fecha_larga,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro,
                    'agencia_caja_cancelado' => $agencia_caja,
                ]);
            } else {

                // Si todavía quedan CUOTAS POR PAGAR

                Credito::on($conexion)->where('id', $credito_id)->update([
                    'saldo_total' => $saldo_total,
                    'notificaciones_pagado' => $notificaciones_pagado,
                    'fecha_ultimo_pago' => $fecha_larga,
                    'datos_actualizacion' => $datos_registro
                ]);
            }
        }

        $credito = Credito::on($conexion)->find($credito_id);

        if ($credito && $credito->estado_id == 4) {

            $prendas = Credito::on($conexion)->from('credito_registros as cre_reg')->select(
                'prendas'
            )
                ->join('credito_aprobaciones as cre_apr', 'cre_apr.id', 'cre_reg.aprobacion_id')
                ->join('credito_propuestas as cre_pro', 'cre_pro.id', 'cre_apr.propuesta_id')
                ->where('cre_reg.id', $credito_id)
                ->first();

            if ($prendas && $prendas->prendas) {

                $prendas = json_decode($prendas->prendas);

                foreach ($prendas as $value) {
                    Prenda::on($conexion)->where('id', $value)->update([
                        'disponible' => 1,
                        'datos_actualizacion' => $datos_registro
                    ]);
                }
                // dd($estado_id);
            }

            // dd($prendas);

        }
        // Generando información de VOUCHER para almacenar en base de datos

        $voucher_cuota->importe = round($voucher_cuota->importe, 2);
        $voucher_cuota_adelanto->importe = round($voucher_cuota_adelanto->importe, 2);
        $voucher_mora->importe = round($voucher_mora->importe, 2);
        $voucher_notificaciones->importe = round($voucher_notificaciones->importe, 2);
        $voucher_saldo_total->importe = round($saldo_total, 2);

        $voucher_cuota->importe > 0 ? $datos_voucher[] = $voucher_cuota : null;
        $voucher_cuota_adelanto->importe > 0 ? $datos_voucher[] = $voucher_cuota_adelanto : null;
        $voucher_mora->importe > 0 ? $datos_voucher[] = $voucher_mora : null;
        $voucher_notificaciones->importe > 0 ? $datos_voucher[] = $voucher_notificaciones : null;
        $datos_voucher[] = $voucher_saldo_total;

        $voucher = json_encode($datos_voucher);

        $pago_voucher = PagoVoucher::on($conexion)->create([
            'credito_id' => $credito_id,
            'agencia_id' => $agencia_caja,
            'caja_id' => $caja_id,
            'datos_voucher' =>  $voucher,
            'datos_creacion' => $datos_registro,
            'numero_cuota' => $cuota_actual

        ]);
        foreach ($datos_voucher as $valor) {
            $valor->credito_id = $credito_id;
            $valor->cuota_actual = $cuota_actual;
        }

        // Actualizando información del crédito en el CARRITO

        CarritoDetalle::on($conexion)->where('id', $detalle_pago->id)->update([
            'estado' => 'P',
            'fecha_pago' => $fecha_larga,
            'agencia_caja' => $agencia_caja,
            'caja_pago' => $caja_id,
            'voucher_id' => $pago_voucher->id,
            'datos_actualizacion' => $datos_registro

        ]);

        // Verificar si se ENVIARÁ o no el COMPROBANTE

        // Se verifica primero si el servicio está activo

        if ($detalle_pago->modo_envio == 'SMS') {
            $servicio = 'servicio_sms';
        } else if ($detalle_pago->modo_envio == 'WHATSAPP') {
            $servicio = 'servicio_whatsapp';
        }
        $verificar_servicio = (new GeneralController)->verificar_servicio($servicio, $agencia_credito);
        $enviar_comprobante = $verificar_servicio['resultado'];

        if ($enviar_comprobante) {

            // ---- Número celular según entorno -----
            $enviroment = getenv('APP_ENV');
            if ($enviroment == 'production') {
                $telefono_principal = $detalle_pago->telefono_envio;
            } else if ($enviroment == 'development') {
                $telefono_principal = 955547121;
            }
            // ---------------------------------------

            $ticket = $detalle_pago->ticket;

            if ($detalle_pago->modo_envio == 'SMS') {

                $message = "EL TICKET: " . $ticket . " se ha pagado correctamente." . "\n" .
                    "¡Gracias por tu confianza!" . "\n" .
                    "---Credipyme---";

                $respuesta_envio = (new ApiSmsController)->single_send($telefono_principal, $message)->getContent();
            } else if ($detalle_pago->modo_envio == 'WHATSAPP') {

                $usuario_asesor = Usuario::find($datos_credito->asesor_id);
                $usuario_asesor = $usuario_asesor->usuario;

                $cliente = Cliente::on($conexion)->find($datos_credito->cliente_id);
                $cliente = $cliente->apellido_paterno . ' ' . $cliente->apellido_materno . ' ' . $cliente->nombres;

                $fecha_hora_negocio = $detalle_pago->created_at;

                $agencia_abreviacion = (new CreditosController)->agencia_abreviacion($agencia_caja);

                $agencia_credito_nombre = Agencia::find($agencia_credito);
                $agencia_credito_nombre = $agencia_credito_nombre->nombre;

                $importe = $detalle_pago->total_cobro;

                $cantidad_cuotas = Cuota::on($conexion)->where(
                    'credito_id',
                    $credito_id
                )->count();

                $credito = Credito::on($conexion)->find($credito_id);
                $aprobacion = Aprobacion::on($conexion)->find($credito->aprobacion_id);
                $estados =  Estado::on($conexion)
                    ->whereIn('estado', ['CANCELADO TOTAL', 'CANCELADO PARCIAL'])
                    ->pluck('id')
                    ->toArray();

                if (in_array($credito->estado_id, $estados)) {
                    $datos_cuota = 'Próx.: - Pend.: 0';
                    $tipo_voucher = 'cancelacion';
                } else {
                    $datos_cuota = 'Próx.:';
                    $datos_cuota .= $cuota_actual;
                    $datos_cuota .= ' Pend.:';
                    $datos_cuota .= $cantidad_cuotas - $cuota_actual + 1;
                    $tipo_voucher = 'cobranza';
                }

                $voucher = (object)[
                    'ticket' => $ticket,
                    'cliente' => $cliente,
                    'usuario_asesor' => $usuario_asesor,
                    'fecha_hora_negocio' => $fecha_hora_negocio,
                    'fecha_hora_caja' => $fecha_larga,
                    'datos_cuota' => $datos_cuota,
                    'detalle' => $voucher,
                    'importe' => $importe,
                    'agencia_caja' => $agencia_abreviacion,
                    'agencia_credito' => $agencia_credito_nombre,
                    'telefono_principal' => $telefono_principal,
                    'tipo_voucher' => $tipo_voucher
                ];

                $respuesta_envio = $this->enviar_voucher($voucher)->getContent();
            }

            $respuesta_envio = json_decode($respuesta_envio);

            if ($respuesta_envio->status == "success") {
                CarritoDetalle::on($conexion)->where('id', $detalle_pago->id)->update([
                    'envio_voucher' => 1,
                ]);
            }
        }

        return $datos_voucher;
    }

    public function enviar_voucher($datos_voucher)
    {
        $ticket = $datos_voucher->ticket;
        $cliente = $datos_voucher->cliente;
        $usuario_asesor = $datos_voucher->usuario_asesor;
        $fecha_hora_negocio = $datos_voucher->fecha_hora_negocio;
        $fecha_hora_caja = $datos_voucher->fecha_hora_caja;
        $datos_cuota = $datos_voucher->datos_cuota;
        $detalle = json_decode($datos_voucher->detalle);
        $importe = $datos_voucher->importe;
        $agencia_caja = $datos_voucher->agencia_caja;
        $agencia_credito = $datos_voucher->agencia_credito;

        $message = "EL TICKET: " . $ticket . " se ha pagado correctamente. ¡Gracias por tu preferencia!" . "
            " . "***Credipyme***";

        // ---- Número celular según entorno -----
        $enviroment = getenv('APP_ENV');
        if ($enviroment == 'production') {
            $phone_number = $datos_voucher->telefono_principal;
        } else if ($enviroment == 'development') {
            $phone_number = 955547121;
        }
        // ---------------------------------------

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/caja/reportes/vchCobranzaVirtual.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Rellenar Voucher ------------------------

        $sheet->setCellValue("A4", 'TICKET N° ' . $ticket);
        $sheet->setCellValue("A6", $cliente);
        $sheet->setCellValue("A8", mb_strtoupper($usuario_asesor));

        $sheet->setCellValue("B10", date("d/m/Y H:i:s", strtotime($fecha_hora_negocio)));
        $sheet->setCellValue("A11", 'Pago en of. ' . '(' . $agencia_caja . ')');
        $sheet->setCellValue("B11", date("d/m/Y H:i:s", strtotime($fecha_hora_caja)));

        $fila = 13;

        foreach ($detalle as $item_2) {
            if ($item_2->concepto != 'Saldo total') {
                $sheet->setCellValue('A' . $fila, $item_2->concepto);
                $sheet->setCellValue('C' . $fila, $item_2->importe);

                $rango =  'A' . $fila . ':' . 'B' . $fila;
                $sheet->mergeCells($rango);

                $fila += 1;
            } else {
                if ($item_2->importe == 0) {
                    $saldo_total = 'CANCELADO';
                } else {
                    $saldo_total = $item_2->importe;
                }
            }
        }

        $sheet->setCellValue('C17', floatval($importe));
        $sheet->setCellValue("C19", $datos_cuota);
        $sheet->setCellValue("C20", $saldo_total);

        $sheet->setCellValue('A' . 26, 'AGENCIA ' . $agencia_credito);

        // Generar nombre de voucher ------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('vchCobranzaVirtual', 5);

        // Guardar archivo Excel ------------------------
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = public_path('/temp_files/' . $nombre_archivo . '.xlsx');


        // Generar ubicación de imagen -------------------
        $path_img = public_path('temp_files/' .   $nombre_archivo . '.png');

        // Convertir plantilla Excel a Imagen -------------------
        $command = env('LIBREOFFICE') . " --headless --convert-to png $path_xlsx --outdir " . dirname($path_img);
        shell_exec($command);

        //  Enviar Imagen por WhatsApp -------------------

        $existe = Storage::disk('public')->exists('/temp_files/' .   $nombre_archivo . '.png');

        while (!$existe) {
            $command = env('LIBREOFFICE') . " --headless --convert-to png $path_xlsx --outdir " . dirname($path_img);
            shell_exec($command);

            $existe = Storage::disk('public')->exists('/temp_files/' .   $nombre_archivo . '.png');
        }

        return $enviar_ws = (new ApiWhatsAppController)->text_image_send(
            $phone_number,
            $message,
            $path_img
        );
    }
}
