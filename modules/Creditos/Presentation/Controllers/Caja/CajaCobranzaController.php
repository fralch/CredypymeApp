<?php

namespace Modules\Creditos\Presentation\Controllers\Caja;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoVoucher;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Pariente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Aval;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Prenda;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Carrito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CarritoDetalle;
use Modules\General\Infrastructure\Persistence\Eloquent\Feriado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Notificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Propuesta;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Producto;
use Modules\General\Infrastructure\Persistence\Eloquent\Banco;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\BancoMovimiento;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Session;

class CajaCobranzaController extends Controller
{

    public function cobranza($credito_id, $agencia_id)
    {
        $datos_voucher = null;
        $cancelado = 0;

        if (Session::has('datos_voucher')) {
            $datos_voucher = Session::get('datos_voucher');
            Session::forget('datos_voucher');
        }
        if (Session::has('cancelado')) {
            $cancelado = Session::get('cancelado');
            Session::forget('cancelado');
        }

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'COBRANZA', 'CREDITOS_CAJA');
            if ($band == 1) {
                $conexion = 'master_' .  $agencia_id;

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
                    ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
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
                    ->leftjoin('solucion_master.usuarios as us_1', 'cre_not.usuario_envio', 'us_1.dni')
                    ->join('solucion_master.usuarios as us_2', DB::raw("SUBSTRING(cre_not.datos_creacion,42,8)"), 'us_2.dni')
                    ->where('cre_not.credito_id', $credito_id)
                    ->orderby('cre_not.id', 'asc')
                    ->get();

                $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
                $estado_id = $estado->id;

                if ($datos_credito->estado_id == $estado_id) {
                    $cancelado = 1;
                }

                $datos_cuotas = Cuota::on($conexion)->where('credito_id', $credito_id)->get();

                $lista_cargos = [
                    'ASESOR DE NEGOCIOS',
                    'GERENTE DE CRÉDITOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ];

                $cargos = Cargo::whereIn('cargo', $lista_cargos)->get();

                $cargos_id = [];

                foreach ($cargos as $item) {
                    $cargos_id[] = $item->id;
                };

                $usuarios_cobradores = Usuario::select('dni', 'usuario')->where([
                    ['habilitado', 1],
                    ['agencia_id', $datos_credito->agencia_id]
                ])->whereIn('cargo_id', $cargos_id)->get();


                // Listar los créditos vinculados al cliente
                $agencia_pariente = $datos_credito->agencia_pariente;
                $pariente_id = $datos_credito->pariente_id;
                $agencia_aval = $datos_credito->agencia_aval;
                $aval_id = $datos_credito->aval_id;
                $agencia_pariente_aval = $datos_credito->agencia_pariente_aval;
                $pariente_aval_id = $datos_credito->pariente_aval_id;

                // Listar los créditos vinculados al cliente
                $parientes_avales = (array)[
                    'PARIENTE' => ($pariente_id == null ? null : (object)[
                        'agencia_id' => $agencia_pariente,
                        'cliente_id' => $pariente_id
                    ]),
                    'AVAL' => ($aval_id == null ? null : (object)[
                        'agencia_id' => $agencia_aval,
                        'cliente_id' => $aval_id
                    ]),
                    'PARIENTE_AVAL' => ($pariente_aval_id == null ? null : (object)[
                        'agencia_id' => $agencia_pariente_aval,
                        'cliente_id' => $pariente_aval_id
                    ])
                ];

                $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
                $estado_id = $estado->id;

                $creditos_vinculados_de = [];
                $creditos_vinculados_a = [];

                foreach ($parientes_avales as $key => $value) {

                    // Pariente,aval o pariente_aval del cliente

                    if ($value != null) {
                        if (in_array($value->agencia_id, [2, 3, 5])) {
                            $conexion_1 = 'master_' .  $value->agencia_id;
                            $credito_de = Credito::on($conexion_1)->from('credito_registros as cre_reg')
                                ->select(
                                    'cre_reg.id',
                                    'cre_reg.capital_total',

                                    'cli_reg.apellido_paterno',
                                    'cli_reg.apellido_materno',
                                    'cli_reg.nombres',
                                    'cli_reg.codigo_expediente',
                                    'cli_reg.agencia_id',

                                    'cre_apr.plazo',
                                    'cre_apr.periodo_pago',

                                    'cre_tip.tipo',

                                    'caj_des.datos_creacion',

                                    'usu.usuario as usuario_asesor',

                                    DB::raw("'$key' as vinculo")
                                )
                                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                                ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
                                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                                ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                                ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
                                ->where([
                                    ['cre_reg.cliente_id', $value->cliente_id],
                                    ['cre_reg.estado_id', $estado_id]
                                ])
                                ->get();

                            foreach ($credito_de as $item) {
                                $creditos_vinculados_de[] = $item;
                            }
                        }
                    }

                    // Cliente como pariente,aval o pariente_aval de otros

                    $columna_1 = 'agencia_pariente';
                    $columna_2 = 'pariente_id';

                    if ($key == 'AVAL') {
                        $columna_1 = 'agencia_aval';
                        $columna_2 = 'aval_id';
                    } elseif ($key == 'PARIENTE_AVAL') {
                        $columna_1 = 'agencia_pariente_aval';
                        $columna_2 = 'pariente_aval_id';
                    }

                    $agencias = Agencia::all();
                    foreach ($agencias as $item_1) {

                        if (in_array($item_1->id_agencia, [2, 3, 5])) {
                            $conexion_2 = 'master_' .  $item_1->id_agencia;

                            $credito_a = Credito::on($conexion_2)->from('credito_registros as cre_reg')
                                ->select(
                                    'cre_reg.id',
                                    'cre_reg.capital_total',

                                    'cli_reg.apellido_paterno',
                                    'cli_reg.apellido_materno',
                                    'cli_reg.nombres',
                                    'cli_reg.codigo_expediente',
                                    'cli_reg.agencia_id',

                                    'cre_apr.plazo',
                                    'cre_apr.periodo_pago',

                                    'cre_tip.tipo',

                                    'caj_des.datos_creacion',

                                    'usu.usuario as usuario_asesor',

                                    DB::raw("'$key' as vinculo")
                                )
                                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                                ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
                                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                                ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                                ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
                                ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
                                ->where([
                                    ["cre_pro.$columna_1", $agencia_id],
                                    ["cre_pro.$columna_2", $datos_credito->cliente_id],
                                    ['cre_reg.estado_id', $estado_id]
                                ])
                                ->get();

                            foreach ($credito_a as $item) {
                                $creditos_vinculados_a[] = $item;
                            }
                        }
                    }
                }

                $cliente = Credito::on($conexion)
                    ->select(
                        'cliente_id',
                    )
                    ->where([
                        ['id', $credito_id]
                    ])
                    ->get()->last();
                $cliente_id = $cliente->cliente_id;
                $credito_adicionales = Credito::on($conexion)->from('credito_registros as cre_reg')
                    ->select(
                        'cre_reg.id',
                        'cre_reg.capital_total',

                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cli_reg.codigo_expediente',
                        'cli_reg.agencia_id',

                        'cre_apr.plazo',
                        'cre_apr.periodo_pago',

                        'cre_tip.tipo',
                        'caj_des.datos_creacion',
                        'usu.usuario as usuario_asesor'

                    )
                    ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                    ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
                    ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                    ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                    ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
                    ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')

                    ->where([
                        ['cre_reg.id', '<>', $credito_id],
                        ['cre_reg.cliente_id', $cliente_id],
                        ['cre_reg.estado_id', $estado_id]
                    ])
                    ->get();

                $bancos = Banco::where([
                    ['agencia_id', $agencia_id],
                    ['habilitado', 1]
                ])
                    ->orderBy('banco', 'asc')
                    ->get();


                return Inertia::render('Creditos/Caja/cobranza', [
                    'agencia_id' => intval($agencia_id),
                    'credito_id' => intval($credito_id),
                    'cancelado' => $cancelado == null ? 0 : intVal($cancelado),
                    'datos_voucher' => $datos_voucher,
                    'datos_credito' => $datos_credito,
                    'creditos_vinculados_a' => $creditos_vinculados_a,
                    'creditos_vinculados_de' => $creditos_vinculados_de,
                    'notificaciones' => $notificaciones,
                    'datos_cuotas' => $datos_cuotas,
                    'usuarios_cobradores' => $usuarios_cobradores,
                    'credito_adicionales' => $credito_adicionales,
                    'bancos' => $bancos

                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function verificar_recibo(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $numero_recibo = trim($request->numero_recibo);

        $resultado = 'NO_EXISTE';

        $existe = PagoCuota::on($conexion)->where('numero_recibo', $numero_recibo)->get()->count();

        if ($existe == 0) {
            $existe = PagoMora::on($conexion)->where('numero_recibo', $numero_recibo)->get()->count();

            if ($existe == 0) {
                $existe = PagoNotificacion::on($conexion)->where('numero_recibo', $numero_recibo)->get()->count();

                if ($existe == 0) {
                    $resultado = 'NO_EXISTE';
                } else {
                    $resultado = 'EXISTE';
                }
            } else {
                $resultado = 'EXISTE';
            }
        } else {
            $resultado = 'EXISTE';
        }

        return $resultado;
    }

    public function pagar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $credito_id = $request->credito_id;
        $datos_cobranza = json_decode($request->datos_cobranza);
        // dd($datos_cobranza);
        $asesor_id = $request->asesor_id;
        $caja_id = $request->caja_id;

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
                        'agencia_caja' => session('id_agencia'),
                        'caja_id' => $caja_id,
                        'monto' => $monto_cuota,
                        'capital_pagado' => $saldo_capital,
                        'interes_pagado' => $saldo_interes,
                        'redondeo_pagado' => $saldo_redondeo,
                        'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                        'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                        'comentario' => $comentario,
                        'pago_banco' => $pago_banco,
                        'banco_id' => $banco_id,
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
                            'agencia_caja' => session('id_agencia'),
                            'caja_id' => $caja_id,
                            'monto' => $monto_cuota,
                            'capital_pagado' => $capital_pago,
                            'interes_pagado' => $interes_pago,
                            'redondeo_pagado' => $redondeo_pago,
                            'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                            'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                            'comentario' => $comentario,
                            'pago_banco' => $pago_banco,
                            'banco_id' => $banco_id,
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
            $dias_atraso = $this->calcular_dias_atraso($credito_id, $agencia_id, $fecha_corta_aplicacion);

            if ($datos_cobranza->pago_mora) {
                $mora_pagado += $datos_cobranza->pago_mora_monto;
                $voucher_mora->importe += $datos_cobranza->pago_mora_monto;

                PagoMora::on($conexion)->create([
                    'credito_id' => $credito_id,
                    'agencia_caja' => session('id_agencia'),
                    'caja_id' => $caja_id,
                    'monto' => $datos_cobranza->pago_mora_monto,
                    'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                    'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                    'comentario' => $comentario,
                    'pago_banco' => $pago_banco,
                    'banco_id' => $banco_id,
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
                                'agencia_caja' => session('id_agencia'),
                                'caja_id' => $caja_id,
                                'monto' => $restante_notificacion,
                                'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                                'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                                'comentario' => $comentario,
                                'pago_banco' => $pago_banco,
                                'banco_id' => $banco_id,
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
                                'agencia_caja' => session('id_agencia'),
                                'caja_id' => $caja_id,
                                'monto' => $monto_notificacion,
                                'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                                'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                                'comentario' => $comentario,
                                'pago_banco' => $pago_banco,
                                'banco_id' => $banco_id,
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
                'agencia_caja' => session('id_agencia'),
                'caja_id' => $caja_id,
                'monto' => $datos_cobranza->pago_mora_monto,
                'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                'comentario' => $comentario,
                'pago_banco' => $pago_banco,
                'banco_id' => $banco_id,
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
                                'agencia_caja' => session('id_agencia'),
                                'caja_id' => $caja_id,
                                'monto' => $restante_notificacion,
                                'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                                'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                                'comentario' => $comentario,
                                'pago_banco' => $pago_banco,
                                'banco_id' => $banco_id,
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
                                'agencia_caja' => session('id_agencia'),
                                'caja_id' => $caja_id,
                                'monto' => $monto_notificacion,
                                'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                                'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                                'comentario' => $comentario,
                                'pago_banco' => $pago_banco,
                                'banco_id' => $banco_id,
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
                            'agencia_caja' => session('id_agencia'),
                            'caja_id' => $caja_id,
                            'monto' => $restante_notificacion,
                            'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                            'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                            'comentario' => $comentario,
                            'pago_banco' => $pago_banco,
                            'banco_id' => $banco_id,
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
                            'agencia_caja' => session('id_agencia'),
                            'caja_id' => $caja_id,
                            'monto' => $monto_notificacion,
                            'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cobranza->usuario_cobrador),
                            'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cobranza->numero_recibo),
                            'comentario' => $comentario,
                            'pago_banco' => $pago_banco,
                            'banco_id' => $banco_id,
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
            'agencia_id' => session('id_agencia'),
            'caja_id' => $caja_id,
            'datos_voucher' =>  $voucher,
            'datos_creacion' => $datos_registro,
            'numero_cuota' => $cuota_actual
        ]);

        if ($pago_banco) {

            //   Registrar el movimiento del BANCO seleccionado
            $cliente = Cliente::on($conexion)->find($datos_credito->cliente_id);
            $cliente = $cliente->apellido_paterno . ' '
                . $cliente->apellido_materno . ' '
                . $cliente->nombres;

            $datos_movimiento = [
                'banco_id' => $banco_id,
                'tipo'  => 'I',
                'agencia_credito'  => $agencia_id,
                'credito_id'  => $credito_id,
                'operacion' => 'COBRANZA',
                'modo' => 'DE_CAJA',
                'monto' => $datos_cobranza->total_cobro,
                'fecha_movimiento' => $fecha_larga,
                'agencia_operacion' => session('id_agencia'),
                'caja_operacion' => $caja_id,
                'descripcion' => 'COBRANZA CLIENTE: ' . $cliente,
                'datos_creacion' => $datos_registro
            ];

            BancoMovimiento::on($conexion)->create($datos_movimiento);

            // Actualizar el ACUMULADO de la cuenta en BANCO

            $banco = Banco::find($banco_id);
            $banco->acumulado += $datos_cobranza->total_cobro;
            $banco->datos_actualizacion = $datos_registro;
            $banco->save();
        }

        Session::put('cancelado', $cancelado);
        Session::put('datos_voucher', $datos_voucher);

        return Redirect::route('caj.cobranza', [
            'credito_id' => $credito_id,
            'agencia_id' => $agencia_id
        ]);
    }

    public function cancelar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_id);
        $año = substr($fecha_larga, 0, 4);


        $credito_id = $request->credito_id;
        $datos_cancelacion = json_decode($request->datos_cancelacion);
        $dscto_moras = round(floatVal($datos_cancelacion->dscto_moras), 2);
        $dscto_notificaciones = round(floatVal($datos_cancelacion->dscto_notificaciones), 2);
        $dscto_interes = round(floatVal($datos_cancelacion->dscto_interes), 2);

        $total_descuento = $dscto_moras + $dscto_notificaciones + $dscto_interes;

        $asesor_id = $request->asesor_id;
        $caja_id = $request->caja_id;

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
                'agencia_caja' => session('id_agencia'),
                'caja_id' => $caja_id,
                'monto' => $monto_cuota,
                'capital_pagado' => $saldo_capital,
                'interes_pagado' => $saldo_interes,
                'redondeo_pagado' => $saldo_redondeo,
                'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cancelacion->usuario_cobrador),
                'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cancelacion->numero_recibo),
                'comentario' => $comentario,
                'pago_banco' => $pago_banco,
                'banco_id' => $banco_id,
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
                'agencia_caja' => session('id_agencia'),
                'caja_id' => $caja_id,
                'monto' => $mora_pago,
                'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cancelacion->usuario_cobrador),
                'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cancelacion->numero_recibo),
                'comentario' => $comentario,
                'pago_banco' => $pago_banco,
                'banco_id' => $banco_id,
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
                'agencia_caja' => session('id_agencia'),
                'caja_id' => $caja_id,
                'monto' => $restante_notificacion,
                'usuario_cobrador' => (new CreditosController)->verificar_nulo($datos_cancelacion->usuario_cobrador),
                'numero_recibo' => (new CreditosController)->verificar_nulo($datos_cancelacion->numero_recibo),
                'comentario' => $comentario,
                'pago_banco' => $pago_banco,
                'banco_id' => $banco_id,
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
            'agencia_caja_cancelado' => session('id_agencia'),
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
            'agencia_id' => session('id_agencia'),
            'caja_id' => $caja_id,
            'datos_voucher' =>  $voucher,
            'datos_creacion' => $datos_registro,
            'numero_cuota' => $cuota_actual

        ]);

        if ($pago_banco) {

            //   Registrar el movimiento del BANCO seleccionado
            $cliente = Cliente::on($conexion)->find($datos_credito->cliente_id);
            $cliente = $cliente->apellido_paterno . ' '
                . $cliente->apellido_materno . ' '
                . $cliente->nombres;

            $datos_movimiento = [
                'banco_id' => $banco_id,
                'tipo' => 'I',
                'operacion' => 'COBRANZA',
                'agencia_credito' => $agencia_id,
                'credito_id' => $credito_id,
                'monto' => $datos_cancelacion->total_cancelar,
                'modo' => 'DE_CAJA',
                'fecha_movimiento' => $fecha_larga,
                'agencia_operacion' => session('id_agencia'),
                'caja_operacion' => $caja_id,
                'descripcion' => 'COBRANZA CLIENTE: ' . $cliente,
                'datos_creacion' => $datos_registro
            ];

            BancoMovimiento::on($conexion)->create($datos_movimiento);

            // Actualizar el ACUMULADO de la cuenta en BANCO

            $banco = Banco::find($banco_id);
            $banco->acumulado += $datos_cancelacion->total_cancelar;
            $banco->datos_actualizacion = $datos_registro;
            $banco->save();
        }

        Session::put('cancelado', 1);
        Session::put('datos_voucher', $datos_voucher);

        return redirect()->route('caj.cobranza', [
            'credito_id' => $credito_id,
            'agencia_id' => $agencia_id
        ]);
    }

    public function calcular_dias_atraso($credito_id, $agencia_id, $fecha_calculo)
    {
        $conexion = 'master_' .  $agencia_id;

        $resultado = 0;

        $fecha_vencimiento = Cuota::on($conexion)->where([
            ['credito_id', $credito_id],
            ['estado', 'V']
        ])->take(1)->get();

        $cuotas_pagadas = Cuota::on($conexion)->where([
            ['credito_id', $credito_id],
            ['estado', 'C']
        ])->count();

        $datos_credito = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.dias_atraso',
                'cre_apr.periodo_pago',
                'cre_reg.agencia_id',
                'cre_apr.plazo'
            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->where('cre_reg.id', $credito_id)
            ->get()->last();

        $cuotas_vencidas = Cuota::on($conexion)->where([
            ['credito_id', $credito_id],
            ['estado', 'V']
        ])->count();

        $cuotas_pendientes = Cuota::on($conexion)->where([
            ['credito_id', $credito_id],
            ['estado', 'P']
        ])->count();

        if (count($fecha_vencimiento) > 0) {

            $periodo_pago = $datos_credito->periodo_pago;

            if ($periodo_pago == 'DIARIO' || $periodo_pago == 'MENSUAL') {
                $ultima_fecha_vencimiento = Cuota::on($conexion)->where([
                    ['credito_id', $credito_id],
                    ['estado', 'V']
                ])->orderBy('id', 'asc')->take(1)->get();
            } else {
                $ultima_fecha_vencimiento = Cuota::on($conexion)->where([
                    ['credito_id', $credito_id],
                    ['estado', 'V']
                ])->orderBy('id', 'desc')->take(1)->get();
            }

            $ultima_fecha_vencimiento = $ultima_fecha_vencimiento[0]->fecha_vencimiento;
            $ultima_fecha_vencimiento = date("Y-m-d", strtotime($ultima_fecha_vencimiento));

            // Calculando días de atraso-------------------

            $cantidad_feriados = 0;

            $cantidad_feriados = Feriado::whereBetween('fecha', [$ultima_fecha_vencimiento, $fecha_calculo])
                ->where('agencias', 'like', '%' . $agencia_id . '%')
                ->count(); //Si es feriado

            $fecha_vencimiento = $ultima_fecha_vencimiento;

            $dia_semana = date('N', strtotime($fecha_vencimiento)); //Si es domingo

            if ($dia_semana == 7) {
                $cantidad_feriados += 1;
            }

            while ($fecha_vencimiento != $fecha_calculo) {
                $fecha_vencimiento = date("Y-m-d", strtotime($fecha_vencimiento . "+ 1 days"));

                $dia_semana = date('N', strtotime($fecha_vencimiento)); //Si es domingo
                if ($dia_semana == 7) {
                    $cantidad_feriados += 1;
                }
            }

            // ---------------------------------------------

            $ultima_fecha_vencimiento = new \DateTime($ultima_fecha_vencimiento);

            $fecha_aplicacion_agencia = $fecha_calculo;
            $fecha_aplicacion_agencia = new \DateTime($fecha_aplicacion_agencia);

            $cantidad_dias = intval(($ultima_fecha_vencimiento->diff($fecha_aplicacion_agencia))->format('%a'));
            $cantidad_dias -= $cantidad_feriados;

            if ($periodo_pago == 'DIARIO' || $periodo_pago == 'MENSUAL') {

                $resultado = $cantidad_dias;
            } else {
                $resultado = ($cuotas_vencidas * 7) + $cantidad_dias - 1;
            }
        } else {
            $numero_cuotas = Cuota::on($conexion)->where([
                ['credito_id', $credito_id]
            ])->count();

            if ($cuotas_pagadas == $numero_cuotas) {
                $resultado = $datos_credito->dias_atraso;
            }
        }

        return $resultado;
    }

    public function verificar_carrito(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $credito_id = $request->input('credito_id');

        $conexion = 'master_' .  $agencia_id;

        $existe_en_carrito = CarritoDetalle::on($conexion)
            ->where([
                ['credito_id', $credito_id],
                ['estado', 'C']
            ])
            ->get()->last();

        $usuario_carrito = null;

        if ($existe_en_carrito != null) {
            $carrito_id = $existe_en_carrito->carrito_id;
            $agencia_carrito = $existe_en_carrito->agencia_carrito;
            $conexion_carrito = 'master_' .  $agencia_carrito;


            $carrito = Carrito::on($conexion_carrito)->find($carrito_id);
            $usuario = Usuario::find($carrito->usuario_id);

            $usuario_carrito = $usuario->usuario . ' (' . (new CreditosController)->agencia_abreviacion($agencia_carrito) . ')';
        }

        return response()->json([
            'usuario_carrito' => $usuario_carrito
        ]);
    }
}
