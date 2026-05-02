<?php

namespace Modules\Creditos\Presentation\Controllers\Credito;

use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Creditos\Presentation\Controllers\Caja\CarritoCobranzaController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Carrito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CarritoDetalle;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Aprobacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Auditoria\CarritoDetalleAUD;
use Modules\Creditos\Presentation\Controllers\Apis\ApiSmsController;


use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoVoucher;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class CarritoSeguimientoController extends Controller
{

    public function carrito_seguimiento()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CARRITO_SEGUIMIENTO', 'CREDITOS_CREDITO');
            if ($band == 1) {


                return Inertia::render('Creditos/Creditos/carrito_seguimiento', []);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function carrito_seguimiento_buscar(Request $request)
    {
        $fecha = $request->input('fecha');
        $agencia_id = $request->input('agencia_id');

        $conexion = 'master_' .  $agencia_id;

        $condition = [[DB::raw("SUBSTR(cre_car.fecha_apertura,1,10)"), $fecha]];

        $lista_carrito_seguimiento = Carrito::on($conexion)
            ->from('credito_carritos as cre_car')
            ->select(
                'cre_car.id',
                'cre_car.usuario_id',
                'usu.usuario',

                DB::raw("SUBSTR(cre_car.fecha_apertura,12,9) as hora_apertura"),
                DB::raw("SUBSTR(cre_car.fecha_cierre,12,9) as hora_cierre"),

            )
            ->join('solucion_master.usuarios as usu', 'cre_car.usuario_id', 'usu.dni')
            ->where($condition)
            ->get();

        $lista_carrito_seguimiento = $lista_carrito_seguimiento->map(function ($row, $index) use (
            $fecha,
            $agencia_id
        ) {

            $total_cobrados = 0;
            $total_pagados = 0;

            $agencias = Agencia::all();

            // Busca en todas las agencias, los créditos cobrados con el carrito
            foreach ($agencias as $item) {

                $conexion_agencia = 'master_' . $item->id_agencia;

                $carrito_detalle = CarritoDetalle::on($conexion_agencia)->where([
                    ['carrito_id', $row['id']],
                    ['agencia_carrito', $agencia_id]
                ])->get();

                $cobrados = $carrito_detalle->whereNotIn('estado', ['A'])->count();
                $pagados = $carrito_detalle->where('estado', 'P')->count();

                $total_cobrados += $cobrados;
                $total_pagados += $pagados;
            }

            return (object)[
                'id' => $row['id'],
                'usuario_id' => $row['usuario_id'],
                'usuario' => $row['usuario'],
                'hora_apertura' => date("h:i:s A", strtotime($row['hora_apertura'])),
                'hora_cierre' => $row['hora_cierre'] != null ? date("h:i:s A", strtotime($row['hora_cierre'])) : null,
                'total_cobrados' => $total_cobrados,
                'total_pagados' => $total_pagados,
                'fecha' => $fecha,
                'agencia_id' => $agencia_id,

            ];
        });

        return ['lista_carrito_seguimiento' => $lista_carrito_seguimiento];
    }
    public function carrito_seguimiento_detalle(request $request)
    {
        $carrito_id = $request->input('carrito_id');
        $agencia_carrito = $request->input('agencia_carrito');
        $usuario_id = $request->input('usuario_id');
        $fecha = $request->input('fecha');
        $conexion_carrito = 'master_' .  $agencia_carrito;

        $condition_1 =  [
            ['cre_cuo.estado', 'P'],
            ['cre_cuo.fecha_vencimiento', $fecha]
        ];

        $condition_2 = [
            ['cre_reg.dias_atraso', '>', 0]
        ];

        $estado = Estado::on($conexion_carrito)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $condition = [
            ['cre_reg.estado_id', $estado_id],
            ['cre_reg.asesor_id', $usuario_id],
            ['cre_apr.pago_oficina', 0]
        ];

        $rango_pendientes = Credito::on($conexion_carrito)->from('credito_registros as cre_reg')
            ->select('cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->where($condition)->get();

        $agencias = Agencia::all();

        $rango_cobrados = [];
        foreach ($agencias as $item) {
            $conexion_credito = 'master_' . $item->id_agencia;

            $creditos = CarritoDetalle::on($conexion_credito)->from('credito_carrito_detalles as cre_car_det')
                ->select(
                    'cre_car_det.id as id',
                    DB::raw("$item->id_agencia as agencia_credito")
                )
                ->where([
                    ['cre_car_det.carrito_id', $carrito_id],
                    ['agencia_carrito', $agencia_carrito]
                ])
                ->get();

            foreach ($creditos as $item_1) {
                $rango_cobrados[] = $item_1;
            }
        }

        $lista_creditos_pendientes = [];
        $lista_creditos_cobrados = [];

        if (count($rango_pendientes) != 0) {


            $rango_pendientes = $rango_pendientes->map(function ($row, $index) use (
                $conexion_carrito,
                $carrito_id
            ) {

                $random = CarritoDetalle::on($conexion_carrito)->where('credito_id', $row['id'])->where('carrito_id', $carrito_id)->get()->last();

                if ($random == null) {
                    return ['id' => $row->id];
                } else {

                    if ($random['estado'] == "A") {

                        return ['id' => $row->id];
                    }
                }
            });

            if (count($rango_pendientes) != 0) {

                $creditos_vigentes = Cuota::on($conexion_carrito)
                    ->from('credito_cuotas as cre_cuo')
                    ->select(
                        'cre_reg.id',

                    )
                    ->join('credito_registros as cre_reg', 'cre_cuo.credito_id', 'cre_reg.id')
                    ->whereIn('cre_cuo.credito_id', $rango_pendientes)
                    ->where($condition_1)
                    ->groupBy('cre_cuo.credito_id')->get()->toArray();


                $creditos_vencidos = Credito::on($conexion_carrito)
                    ->from('credito_registros as cre_reg')
                    ->select(
                        'cre_reg.id',
                    )
                    ->whereIn('cre_reg.id', $rango_pendientes)
                    ->where($condition_2)
                    ->groupBy('cre_reg.id')->get()->toArray();

                $creditos_vigentes = array_column($creditos_vigentes, 'id');
                $creditos_vencidos = array_column($creditos_vencidos, 'id');

                $union = array_merge($creditos_vigentes, $creditos_vencidos);
                $rango = array_unique($union);

                if (count($rango) != 0) {
                    $lista_creditos_pendientes = Credito::on($conexion_carrito)
                        ->from('credito_registros as cre_reg')
                        ->select(
                            'cre_reg.id',
                            'cre_reg.asesor_id',
                            'cre_reg.agencia_id as agencia_credito',
                            'cli_reg.apellido_paterno',
                            'cli_reg.apellido_materno',
                            'cli_reg.nombres',
                            DB::raw("0 as pago_cuota_monto"),
                            DB::raw("NULL as observacion"),
                            DB::raw("0 as pago_mora_monto"),
                            DB::raw("0 as pago_notificaciones_monto"),
                            DB::raw("0 as hora_cobro"),
                            DB::raw("0 as hora_pago"),
                            DB::raw("0 as total_cobro"),
                            DB::raw("0 as total_pago"),
                            DB::raw("0 as total_cobro_real")
                        )
                        ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id',  'cli_reg.id')
                        ->whereIn('cre_reg.id', $rango)
                        ->orderBy('apellido_paterno', 'asc')
                        ->get();
                }
            }
        }
        if (count($rango_cobrados) > 0) {

            $detalle_cobrados = [];
            foreach ($rango_cobrados as $item) {
                $conexion_credito  = 'master_' . $item->agencia_credito;

                $detalle = CarritoDetalle::on($conexion_credito)->from('credito_carrito_detalles as cre_car_det')
                    ->select(
                        'cre_reg.id',
                        'cre_reg.asesor_id',
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cre_car_det.id as carrito_detalle_id',
                        'cre_car_det.estado',
                        'cre_car_det.datos_actualizacion',
                        'cre_car_det.pago_cuota_monto',
                        'cre_car_det.pago_monto',
                        'cre_car_det.pago_mora_monto',
                        'cre_car_det.pago_notificaciones_monto',
                        'cre_car_det.total_cobro',
                        'cre_car_det.fecha_cobro as hora_cobro',
                        'cre_car_det.agencia_caja',
                        'cre_car_det.caja_pago',
                        'cre_car_det.voucher_id',
                        'cre_car_det.modo_envio',
                        'cre_car_det.envio_voucher',
                        'cre_car_det.fecha_pago as hora_pago',
                        'cre_car_det.ticket',
                        'cre_car_det.telefono_envio',

                        DB::raw("$item->agencia_credito as agencia_credito")

                    )
                    ->join('credito_registros as cre_reg', 'cre_reg.id',  'cre_car_det.credito_id')
                    ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id',  'cli_reg.id')
                    ->where('cre_car_det.id', $item->id)
                    ->get();

                foreach ($detalle as $item) {
                    if ($item->estado == "P") {

                        $conexion2 = 'master_' .  $item->agencia_caja;

                        $datos_caja = Caja::on($conexion2)->from('caja_registros as caj_reg')
                            ->select('usu.usuario')
                            ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                            ->where('caj_reg.id', $item->caja_pago)
                            ->get()
                            ->last();

                        $item->usuario_caja = $datos_caja->usuario;
                    }

                    $datos_asesor = Usuario::find($item->asesor_id);
                    $item->usuario_asesor = $datos_asesor->usuario;

                    $detalle_cobrados[] = $item;
                }
            }

            $detalle_cobrados = collect($detalle_cobrados)->sortBy('hora_cobro')->values();

            $lista_creditos_cobrados = $detalle_cobrados->map(function ($row) use ($carrito_id) {

                $agencia_credito = $row['agencia_credito'];
                $conexion_credito = 'master_' . $agencia_credito;

                $agencia_caja = "-";

                if ($row['estado'] == "A") {

                    $estado = "ANULADO";
                }
                if ($row['estado'] == "C" && $row['datos_actualizacion'] != null) {

                    $estado = "MODIFICADO";
                }
                if ($row['estado'] == "P") {


                    $credito_pagado = CarritoDetalleAUD::on($conexion_credito)
                        ->where('registro_id', $row['carrito_detalle_id'])
                        ->get()->count();

                    if ($credito_pagado > 0) {

                        $estado = "PAGADO_MODIFICADO";
                    } else {
                        $estado = "PAGADO";
                    }
                }
                if ($row['estado'] == "C" && $row['datos_actualizacion'] == null) {

                    $estado = "COBRADO";
                }

                if ($row['usuario_caja'] && $row['agencia_caja'] != null) {

                    if ($row['agencia_caja'] == 1) {

                        $agencia_caja = "(TAM)";
                    }
                    if ($row['agencia_caja'] == 2) {

                        $agencia_caja = "(HYO)";
                    }
                    if ($row['agencia_caja'] == 3) {

                        $agencia_caja = "(PAM)";
                    }
                    if ($row['agencia_caja'] == 4) {

                        $agencia_caja = "(HVCA)";
                    }
                    if ($row['agencia_caja'] == 5) {

                        $agencia_caja = "(ADM)";
                    }
                    if ($row['agencia_caja'] == 6) {

                        $agencia_caja = "(CHI)";
                    }
                }

                $agencia_cobranza = Agencia::find($agencia_credito);
                $cliente = $row['apellido_paterno'] . ' ' .
                    $row['apellido_materno'] . ' ' .
                    $row['nombres'];


                return [
                    'id' => $row['id'],
                    'carrito_detalle_id' => $row['carrito_detalle_id'],
                    'observacion' => $estado,
                    'total_cobro' => $estado == "ANULADO" ? 0 : $row['total_cobro'],
                    'total_pago' =>  $estado == "PAGADO" || $estado == "PAGADO_MODIFICADO" ? $row['total_cobro'] : 0,
                    'agencia_credito' => $agencia_cobranza->nombre,
                    'cliente' => $cliente,
                    'usuario_asesor' => $row['usuario_asesor'],
                    'pago_cuota_monto' => $estado == "ANULADO"  ? 0 : ($row['pago_cuota_monto'] != 0 ? $row['pago_cuota_monto'] : ($row['pago_monto'] != 0 ? $row['pago_monto'] : 0)),
                    'pago_mora_monto' => $row['pago_mora_monto'],
                    'pago_notificaciones_monto' => $row['pago_notificaciones_monto'],
                    'hora_cobro' => date("h:i:s A", strtotime($row['hora_cobro'])),
                    'hora_pago' => $row['hora_pago'] != null ? date("h:i:s A", strtotime($row['hora_pago'])) : null,
                    'usuario_caja' => $agencia_caja != '-' ? $row['usuario_caja'] . " " . $agencia_caja : "-",
                    'voucher_id' => $row['voucher_id'],
                    'modo_envio' => $row['modo_envio'],
                    'envio_voucher' => $row['envio_voucher'],
                    'total_cobro_real' => $row['total_cobro'],
                    'ticket' => $row['ticket'],
                    'telefono_envio' => $row['telefono_envio'],
                    'carrito_id' => $carrito_id,
                    'agencia_caja' => $row['agencia_caja'],
                    'agencia_id' => $row['agencia_credito'],
                ];
            });

            $lista_creditos_cobrados = collect($lista_creditos_cobrados)->toArray();
        }

        if (!empty($lista_creditos_pendientes)) {
            $lista_creditos_pendientes = $lista_creditos_pendientes->map(function ($row) use ($carrito_id) {

                $agencia = Agencia::find($row['agencia_credito']);
                $datos_asesor = Usuario::find($row['asesor_id']);

                $cliente = $row['apellido_paterno'] . ' ' .
                    $row['apellido_materno'] . ' ' .
                    $row['nombres'];

                return [
                    'id' => $row['id'],
                    'carrito_detalle_id' => null,
                    'observacion' => null,
                    'total_cobro' => 0,
                    'total_pago' =>  0,
                    'agencia_credito' => $agencia->nombre,
                    'cliente' => $cliente,
                    'usuario_asesor' => $datos_asesor->usuario,
                    'pago_cuota_monto' => 0,
                    'pago_mora_monto' => 0,
                    'pago_notificaciones_monto' => 0,
                    'hora_cobro' => null,
                    'hora_pago' => null,
                    'usuario_caja' => null,
                    'voucher_id' => null,
                    'modo_envio' => null,
                    'envio_voucher' => null,
                    'total_cobro_real' => null,
                    'ticket' => null,
                    'telefono_envio' => null,
                    'carrito_id' => $carrito_id,
                    'agencia_caja' => null,
                    'agencia_id' => $row['agencia_credito'],
                ];
            });


            $lista_creditos = array_merge(
                $lista_creditos_cobrados,
                $lista_creditos_pendientes->toArray()
            );
        } else {
            $lista_creditos = $lista_creditos_cobrados;
        }

        return ['lista_creditos' => $lista_creditos];
    }

    public function carrito_seguimiento_modificacion(Request $request)
    {
        $carrito_detalle_id = $request->input('carrito_detalle_id');
        $agencia_credito = $request->input('agencia_credito');

        $conexion = 'master_' .  $agencia_credito;

        $lista_credito_modificado = CarritoDetalleAUD::on($conexion)
            ->select('registro_id', 'historial', 'fecha_registro')
            ->where('registro_id', $carrito_detalle_id)
            ->get();

        return ['lista_credito_modificado' => $lista_credito_modificado];
    }

    public function carrito_seguimiento_anulacion(Request $request)
    {

        $agencia_credito = $request->input('agencia_credito');
        $carrito_detalle_id = $request->input('carrito_detalle_id');

        $conexion = 'master_' .  $agencia_credito;

        $credito = CarritoDetalle::on($conexion)
            ->select(
                DB::raw("SUBSTR(datos_actualizacion,11,10) as fecha_anulacion"),
                DB::raw("SUBSTR(datos_actualizacion,22,8) as hora_anulacion"),
                'motivo_anulacion'
            )
            ->where('id', $carrito_detalle_id)
            ->get()->last();

        $lista_datos_anulado = (object)[

            'fecha_anulacion' => $credito->fecha_anulacion . " " . date("h:i:s A", strtotime($credito->hora_anulacion)),
            'motivo_anulacion' => $credito->motivo_anulacion,
        ];

        return ['lista_datos_anulado' => $lista_datos_anulado];
    }

    public function carrito_seguimiento_comprobante(Request $request)
    {

        $response = new \stdClass();

        $telefono_envio = $request->telefono_envio;
        $ticket = $request->ticket;
        $carrito_detalle_id = $request->carrito_detalle_id;
        $modo_envio = $request->modo_envio;
        $agencia_credito = $request->agencia_credito;
        $agencia_caja = $request->agencia_caja;

        $conexion_credito = 'master_' .  $agencia_credito;

        if ($modo_envio == 'SMS') {
            $message = "EL TICKET: " . $ticket . " se ha pagado correctamente." . "\n" .
                "¡Gracias por tu confianza!" . "\n" .
                "---Credipyme Huanca---";

            $respuesta_envio = (new ApiSmsController)->single_send($telefono_envio, $message)->getContent();
        } else if ($modo_envio == 'WHATSAPP') {

            $cliente = $request->cliente;
            $credito_id = $request->credito_id;

            $datos_credito = Credito::on($conexion_credito)->find($credito_id);
            $datos_aprobacion = Aprobacion::on($conexion_credito)->find($datos_credito->aprobacion_id);
            $usuario_asesor = Usuario::find($datos_credito->asesor_id);

            $usuario_asesor = $usuario_asesor->usuario;

            $fecha_cobro = $request->fecha_cobro;
            $fecha_pago = $request->fecha_pago;
            $importe = $request->total_cobro;
            $voucher_id = $request->voucher_id;

            $pago_voucher = PagoVoucher::on($conexion_credito)->find($voucher_id);
            $cuota_actual = $pago_voucher->numero_cuota;

            $cantidad_cuotas = Cuota::on($conexion_credito)->where(
                'credito_id',
                $credito_id
            )->count();

            $estados =  Estado::on($conexion_credito)
                ->whereIn('estado', ['CANCELADO TOTAL', 'CANCELADO PARCIAL'])
                ->pluck('id')
                ->toArray();

            if (in_array($datos_credito->estado_id, $estados)) {
                $datos_cuota = 'Próx.: - Pend.: 0';
                $tipo_voucher = 'cancelacion';
            } else {
                $datos_cuota = 'Próx.:';
                $datos_cuota .= $cuota_actual;
                $datos_cuota .= ' Pend.:';
                $datos_cuota .= $cantidad_cuotas - $cuota_actual + 1;
                $tipo_voucher = 'cobranza';
            }

            $agencia_abreviacion = (new CreditosController)->agencia_abreviacion($agencia_caja);

            $agencia_credito_nombre = Agencia::find($agencia_credito);
            $agencia_credito_nombre = $agencia_credito_nombre->nombre;

            $datos_voucher = (object)[
                'ticket' => $ticket,
                'cliente' => $cliente,
                'usuario_asesor' => $usuario_asesor,
                'fecha_hora_negocio' => $fecha_cobro,
                'fecha_hora_caja' => $fecha_pago,
                'cuota' => $datos_aprobacion->cuota,
                'datos_cuota' => $datos_cuota,
                'detalle' => $pago_voucher->datos_voucher,
                'importe' => $importe,
                'agencia_caja' => $agencia_abreviacion,
                'agencia_credito' => $agencia_credito_nombre,
                'telefono_principal' => $telefono_envio,
                'tipo_voucher' => $tipo_voucher
            ];

            $respuesta_envio = (new CarritoCobranzaController)->enviar_voucher($datos_voucher)->getContent();
        }

        $respuesta_envio = json_decode($respuesta_envio);

        if ($respuesta_envio->status == "success") {
            CarritoDetalle::on($conexion_credito)
                ->where('id', $carrito_detalle_id)
                ->update([
                    'envio_voucher' => 1,
                ]);
        }

        $response->success = true;

        return $response;
    }
    public function listar_sin_voucher(Request $request)
    {
        $agencia_id = $request->input('agencia_id');

        $conexion_agencia = 'master_' . $agencia_id;

        $rango = CarritoDetalle::on($conexion_agencia)->where([
            ['fecha_pago', '!=', null],
            ['envio_voucher', 0],
        ])->get();

        $lista_sin_voucher = $rango->map(function ($row) use ($agencia_id, $conexion_agencia) {

            $credito = Credito::on($conexion_agencia)->find($row->credito_id);
            $cliente = Cliente::on($conexion_agencia)->find($credito->cliente_id);
            $asesor = Usuario::find($credito->asesor_id);
            $agencia_carrito = Agencia::find($row['agencia_carrito']);
            $agencia_credito = Agencia::find($agencia_id);

            $conexion_carrito = 'master_' . $row['agencia_carrito'];
            $carrito = Carrito::on($conexion_carrito)->find($row['carrito_id']);
            $usuario_carrito = Usuario::find($carrito->usuario_id);

            $conexion_caja = 'master_' . $row['agencia_caja'];
            $caja = Caja::on($conexion_caja)->find($row['caja_pago']);
            $usuario_caja = Usuario::find($caja->dni);

            $cliente = $cliente->apellido_paterno . ' ' .
                $cliente->apellido_materno . ' ' .
                $cliente->nombres;

            if ($row['agencia_caja'] == 1) {

                $agencia_caja = "(TAM)";
            }
            if ($row['agencia_caja'] == 2) {

                $agencia_caja = "(HYO)";
            }
            if ($row['agencia_caja'] == 3) {

                $agencia_caja = "(PAM)";
            }
            if ($row['agencia_caja'] == 4) {

                $agencia_caja = "(HVCA)";
            }
            if ($row['agencia_caja'] == 5) {

                $agencia_caja = "(ADM)";
            }
            if ($row['agencia_caja'] == 6) {

                $agencia_caja = "(CHI)";
            }



            return [
                'id' => $row['id'],
                'credito_id' => $row['credito_id'],
                'total_cobro' =>  $row['total_cobro'],
                'total_pago' =>  $row['total_cobro'],
                'nombre_agencia_carrito' => $agencia_carrito->nombre,
                'nombre_agencia_credito' => $agencia_credito->nombre,
                'cliente' => $cliente,
                'usuario_carrito' => $usuario_carrito->usuario,
                'usuario_asesor' => $asesor->usuario,
                'pago_cuota_monto' => ($row['pago_cuota_monto'] != 0 ? $row['pago_cuota_monto'] : ($row['pago_monto'] != 0 ? $row['pago_monto'] : 0)),
                'pago_mora_monto' => $row['pago_mora_monto'],
                'pago_notificaciones_monto' => $row['pago_notificaciones_monto'],
                // 'fecha_cobro' => date("d/m/Y h:i:s", strtotime($row['fecha_cobro'])),
                'fecha_cobro' => $row['fecha_cobro'],
                // 'fecha_pago' =>  date("d/m/Y h:i:s", strtotime($row['fecha_pago'])),
                'fecha_pago' =>  $row['fecha_pago'],
                'usuario_caja' => $usuario_caja->usuario . " " . $agencia_caja,
                'voucher_id' => $row['voucher_id'],
                'modo_envio' => $row['modo_envio'],
                'envio_voucher' => $row['envio_voucher'],
                'ticket' => $row['ticket'],
                'telefono_envio' => $row['telefono_envio'],
                'carrito_id' => $row['carrito_id'],
                'agencia_caja' => $row['agencia_caja'],
                'agencia_credito' =>  $agencia_id,
            ];
        });

        return ['lista_sin_voucher' => $lista_sin_voucher];
    }
}
