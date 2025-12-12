<?php

namespace App\Http\Controllers\Creditos\Credito;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\General\PermisosController;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Negocio;
use App\Models\Creditos\Credito\Compromiso;
use App\Models\Creditos\Credito\Notificacion;
use App\Models\Creditos\Credito\NotificacionTipo;
use App\Models\Creditos\Clientes\Pariente;

use App\Http\Controllers\General\GeneralController;
use App\Http\Controllers\Creditos\CreditosController;
use App\Models\Creditos\Mantenimiento\Credito\Producto;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Mantenimiento\Credito\Tipo;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Carrito;
use App\Models\Creditos\Credito\CarritoDetalle;
use App\Models\Creditos\Auditoria\CarritoDetalleAUD;
use App\Models\Creditos\Credito\Propuesta;
use App\Models\Creditos\Credito\Aprobacion;

use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Apis\ApiSmsController;
use App\Http\Controllers\Apis\ApiWhatsAppController;

use App\Models\Creditos\Credito\Cuota;

use App\Models\General\Agencia;
use App\Models\Gth\Usuarios\Usuario;

use Inertia\Inertia;
use Carbon\Carbon;

class CarritoController extends Controller
{
    public function carrito_cobranza()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso(
                $x['usuario_dni'],
                'CARRITO',
                'CREDITOS_CREDITO'
            );
            if ($band == 1) {

                $usuario_id = $x['usuario_dni'];
                $agencia_id = $x['id_agencia'];

                $conexion = 'master_' . $agencia_id;
                $carrito = Carrito::on($conexion)->where([
                    ['usuario_id', $usuario_id],
                    ['pagado', 0],
                    ['fecha_cierre', null]
                ])->get()->last();

                if ($carrito != null) {
                    $fecha_carrito = Carbon::parse($carrito->fecha_apertura)->format('Y-m-d');
                } else {
                    $fecha_carrito = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
                }

                return Inertia::render('Creditos/Creditos/carrito', [

                    'usuario_id' => $usuario_id,
                    'agencia_id' => $agencia_id,
                    'fecha_carrito' => $fecha_carrito,

                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar(Request $request)
    {
        $fecha_carrito = $request->input('fecha_carrito');
        $usuario_id = $request->input('usuario_id');
        $agencia_carrito = $request->input('agencia_carrito');
        $carrito_id = $request->input('carrito_id');

        $conexion_carrito = 'master_' .  $agencia_carrito;

        $estado = Estado::on($conexion_carrito)
            ->select('id')
            ->where('estado', 'DESEMBOLSADO')
            ->get()->last();

        $estado_id = $estado->id;

        $condition = [
            ['cre_reg.estado_id', $estado_id],
            ['cre_apr.pago_oficina', 0],
            ['cre_reg.asesor_id', $usuario_id]
        ];

        $rango_cartera_id = Credito::on($conexion_carrito)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.agencia_id as agencia_credito'
            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->where($condition)->get();


        $agencias = Agencia::all();

        $rango_cobranzas = [];
        foreach ($agencias as $value) {

            $conexion_credito = 'master_' . $value->id_agencia;
            $cobranzas = CarritoDetalle::on($conexion_credito)->from('credito_carrito_detalles as cre_car_det')
                ->select(
                    'cre_car_det.credito_id as id',
                    DB::raw("$value->id_agencia as agencia_credito")
                )
                ->where([
                    ['cre_car_det.agencia_carrito', $agencia_carrito],
                    ['cre_car_det.carrito_id', $carrito_id],
                    ['cre_car_det.estado', 'C']
                ])
                ->get();

            foreach ($cobranzas as $cobranza) {
                $rango_cobranzas[] = $cobranza;
            }
        }

        // Para separar los créditos ya cobrados en carrito de la cartera por cobrar

        if (count($rango_cobranzas) > 0) {
            // Convertir los arrays en cadenas JSON para comparación
            function array_to_json($array)
            {
                $array = json_decode(json_encode($array));
                return array_map('json_encode', $array);
            }

            $rango_cobranzas = array_to_json($rango_cobranzas);
            $rango_cartera_id = array_to_json($rango_cartera_id);

            $cartera_sin_cobranzas = array_diff($rango_cartera_id, $rango_cobranzas);
            $cartera_sin_cobranzas = array_map('json_decode', $cartera_sin_cobranzas);

            $rango_cobranzas = array_map('json_decode', $rango_cobranzas);
            $cartera_sin_cobranzas_id = collect($cartera_sin_cobranzas)->pluck('id');
        } else {
            $cartera_sin_cobranzas_id = collect($rango_cartera_id)->pluck('id');
        }

        $cartera_cobrar = Credito::on($conexion_carrito)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.cliente_id',
                'cre_reg.agencia_id',
                'cre_reg.asesor_id',
                'cre_reg.aprobacion_id',
                'cre_reg.dias_atraso',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.notificaciones_total',
                'cre_reg.notificaciones_pagado',

                'cre_cuo.numero_cuota',
                'cre_cuo.fecha_vencimiento',
                'cre_cuo.estado',
                'cre_cuo.cuota',
                'cre_cuo.acumulado'

            )
            ->join('credito_cuotas as cre_cuo', 'cre_cuo.credito_id', 'cre_reg.id')
            ->whereIn('cre_reg.id', $cartera_sin_cobranzas_id)
            ->where('cre_cuo.fecha_vencimiento', '<=', $fecha_carrito)
            ->whereIn('cre_cuo.estado', ['P', 'V'])
            ->get();

        if (count($rango_cobranzas) != 0) {

            $cartera_carrito = [];

            $estado_parcial = Estado::on($conexion_carrito)
                ->select('id')
                ->where('estado', 'CANCELADO PARCIAL')
                ->get()->last();

            foreach ($rango_cobranzas as  $item) {

                $conexion_cobro = 'master_' . $item->agencia_credito;

                $credito = Credito::on($conexion_cobro)->find($item->id);

                $estado_cuotas = ['P', 'V'];

                if ($credito->estado_id == $estado_parcial->id) {
                    $estado_cuotas = ['C'];
                }

                $detalle_credito = Credito::on($conexion_cobro)->from('credito_registros as cre_reg')
                    ->select(
                        'cre_reg.id',
                        'cre_reg.cliente_id',
                        'cre_reg.agencia_id',
                        'cre_reg.asesor_id',
                        'cre_reg.aprobacion_id',
                        'cre_reg.dias_atraso',
                        'cre_reg.mora_total',
                        'cre_reg.mora_pagado',
                        'cre_reg.notificaciones_total',
                        'cre_reg.notificaciones_pagado',

                        'cre_cuo.numero_cuota',
                        'cre_cuo.fecha_vencimiento',
                        'cre_cuo.estado',
                        'cre_cuo.cuota',
                        'cre_cuo.acumulado'

                    )
                    ->join('credito_cuotas as cre_cuo', 'cre_cuo.credito_id', 'cre_reg.id')
                    ->where('cre_reg.id', $item->id)
                    ->whereIn('cre_cuo.estado', $estado_cuotas);

                if ($credito->estado_id == $estado_parcial->id) {
                    $detalle_credito = $detalle_credito->get()->last();
                    $cartera_carrito[] = $detalle_credito;
                } else {
                    $detalle_credito = $detalle_credito->get();
                    foreach ($detalle_credito as $item_1) {
                        $cartera_carrito[] = $item_1;
                    }
                }
            }

            $lista_creditos = collect($cartera_cobrar)->merge(collect($cartera_carrito))->sortByDesc('dias_atraso');
            $cobros_realizados = true;
        } else {
            $lista_creditos = collect($cartera_cobrar)->sortByDesc('dias_atraso')->values();
            $cobros_realizados = false;
        }

        $lista_creditos = $lista_creditos->groupBy('id', 'agencia_id')
            ->map(function ($row) use ($carrito_id, $fecha_carrito) {

                $row = $row->values();

                // Verifica si hay cuotas pendientes con fechas anteriores o iguales a la fecha del carrito

                if (count($row->where('fecha_vencimiento', '<=', $fecha_carrito)) > 0) {

                    // Si hay entonces las filtra
                    $row = $row->where('fecha_vencimiento', '<=', $fecha_carrito);

                    if ($row->min('numero_cuota') == $row->max('numero_cuota')) {
                        $numero_cuota = $row->max('numero_cuota');
                    } else {
                        $numero_cuota = $row->min('numero_cuota') . ' - ' . $row->max('numero_cuota');
                    }

                    $saldo_cuota  = round($row->sum('cuota') - $row->sum('acumulado'), 2);
                } else {
                    $numero_cuota = null;
                    $saldo_cuota = 0;
                }

                // Obtener la información del cobro en carrito

                $conexion = 'master_' . $row[0]->agencia_id;
                $carrito_detalle = CarritoDetalle::on($conexion)->where([
                    ['carrito_id', $carrito_id],
                    ['credito_id', $row[0]->id],
                    ['estado', 'C'],
                ])->get()->last();

                $asesor = Usuario::find($row[0]->asesor_id);
                $agencia = Agencia::find($row[0]->agencia_id);

                $conexion = 'master_' . $row[0]->agencia_id;
                $cliente = Cliente::on($conexion)->find($row[0]->cliente_id);

                return (object)[
                    'id' => $row[0]->id,
                    'cliente_id' =>  $row[0]->cliente_id,
                    'cliente' =>  $cliente->apellido_paterno . ' ' .
                        $cliente->apellido_materno . ' ' .
                        $cliente->nombres,
                    'agencia_id' =>  $row[0]->agencia_id,
                    'agencia' =>  $agencia->nombre,
                    'asesor_id' =>  $row[0]->asesor_id,
                    'asesor' =>  $asesor->usuario,
                    'dias_atraso' =>  $row->min('dias_atraso'),
                    'saldo_cuota' => $saldo_cuota,
                    'saldo_mora' => round($row[0]->mora_total - $row[0]->mora_pagado, 2),
                    'saldo_notificaciones' =>  round($row[0]->notificaciones_total - $row[0]->notificaciones_pagado, 2),
                    'numero_cuota' =>  $numero_cuota,
                    'carrito_detalle_id' =>  $carrito_detalle != null ? $carrito_detalle->id : null,
                    'total_cobro' =>  $carrito_detalle != null ? $carrito_detalle->total_cobro : 0,
                ];

                return $row;
            });

        $lista_creditos = $lista_creditos->values();

        return [
            'lista_creditos' => $lista_creditos,
            'cobros_realizados' => $cobros_realizados
        ];
    }

    public function aperturar(request $request)
    {
        $usuario_id = $request->usuario_id;
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;

        $carrito_abierto = Carrito::on($conexion)->where([
            ['usuario_id', $usuario_id],
            ['pagado', 0]
        ])->get()->last();

        if ($carrito_abierto != null) {

            return response()->json([
                'status' => 'error',
                'message' => 'CARRITO YA APERTURADO',
                'data' => ['id' => $carrito_abierto->id]
            ], 422);
        } else {
            $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_id);
            $datos_registro = (new CreditosController)->datos_registro($agencia_id);

            $carrito = Carrito::on($conexion)->create([
                'usuario_id' => $usuario_id,
                'fecha_apertura' => $fecha_larga,
                'pagado' => 'ok',
                'anulado' => 0,
                'datos_creacion' => $datos_registro,

            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'CARRITO APERTURADO',
                'data' => $carrito->id
            ], 201);
        }
    }

    public function cerrar_carrito(request $request)
    {

        $response = new \stdClass();


        $carrito_id = $request->carrito_id;
        $agencia_id = $request->agencia_id;
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $conexion = 'master_' .  $agencia_id;

        Carrito::on($conexion)->where('id', $carrito_id)->update([
            'pagado' => 1,
            'fecha_cierre' => $fecha_larga,
            'datos_actualizacion' => $datos_registro
        ]);

        $response->success = true;
        return $response;
    }

    public function buscar(Request $request)
    {
        $conexion = 'master_' .  $request->input('agencia_id');
        $texto_buscar = $request->input('texto_buscar');
        $tipo_filtro = $request->input('tipo_filtro');
        $cancelados_parcial = filter_var($request->input('cancelados_parcial'), FILTER_VALIDATE_BOOLEAN);

        $columna = "";
        $operator = 'like';
        $search = "%$texto_buscar%";

        if ($tipo_filtro == 'apellidos_nombres') {
            $columna = 'cli_reg.apellido_paterno, " ", cli_reg.apellido_materno, " ", cli_reg.nombres';
        } else if ($tipo_filtro == 'dni') {
            $columna = 'cli_reg.dni';
        }

        if ($cancelados_parcial) {
            $lista_estados = ['DESEMBOLSADO', 'CANCELADO PARCIAL'];
        } else {
            $lista_estados = ['DESEMBOLSADO'];
        }

        $estados = Estado::on($conexion)->select('id')->whereIn('estado', $lista_estados)->get();

        $creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.aprobacion_id',
                'cre_reg.cliente_id',
                'cre_reg.asesor_id',
                'cre_reg.cuota_actual',
                'cre_reg.capital_total',
                'cre_reg.dias_atraso',

                'cli_reg.dni',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.agencia_id',
            )
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->whereIn('cre_reg.estado_id', $estados)
            ->where(
                DB::raw("CONCAT($columna)"),
                $operator,
                $search,
            )
            ->orderBy('cli_reg.apellido_paterno', 'asc')
            ->orderBy('cli_reg.apellido_materno', 'asc')
            ->orderBy('cli_reg.nombres', 'asc')
            ->get();


        $creditos = $creditos->map(function ($row, $index) use ($conexion) {

            $aprobacion = Aprobacion::on($conexion)->find($row->aprobacion_id);
            $tipo = Tipo::on($conexion)->find($aprobacion->tipo_id);
            $agencia = Agencia::find($row->agencia_id);
            $asesor = Usuario::find($row->asesor_id);

            return [
                'id' => $row->id,
                'index' => $index,
                'agencia_id' => $row->agencia_id,
                'agencia' => $agencia->nombre,
                'cliente_id' => $row->cliente_id,
                'cliente' => $row->apellido_paterno . ' ' .
                    $row->apellido_materno . ' ' .
                    $row->nombres,
                'asesor_id' => $row->asesor_id,
                'asesor' => $asesor->usuario,
                'capital_total' => $row->capital_total,
                'plazo_periodo' => round($aprobacion->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($aprobacion->periodo_pago),
                'cuota_actual' => $row->cuota_actual,
                'tipo' => $tipo->tipo,
                'dias_atraso' => $row->dias_atraso . ' d',
            ];
        });

        return ['creditos' => $creditos];
    }

    public function agregar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $credito_id = $request->credito_id;

        $conexion = 'master_' .  $agencia_id;

        $fecha_agencia = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $credito = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.cliente_id',
                'cre_reg.agencia_id',
                'cre_reg.asesor_id',
                'cre_reg.aprobacion_id',
                'cre_reg.dias_atraso',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.notificaciones_total',
                'cre_reg.notificaciones_pagado',
                'cre_reg.cuota_actual',
                'cre_reg.cuotas_vencidas',
                'cre_reg.monto_vencido'
            )
            ->where([
                ['cre_reg.id', $credito_id]
            ])
            ->get();

        $credito = $credito->map(function ($row) {

            $numero_cuota = $row->cuota_actual;

            if ($row->cuotas_vencidas > 1) {

                $ultima_cuota_vencida = $row->cuota_actual + $row->cuotas_vencidas - 1;
                $numero_cuota = $row->cuota_actual . ' - ' . $ultima_cuota_vencida;
            }

            $asesor = Usuario::find($row->asesor_id);
            $agencia = Agencia::find($row->agencia_id);

            $conexion = 'master_' . $row->agencia_id;
            $cliente = Cliente::on($conexion)->find($row->cliente_id);

            return (object)[
                'id' => $row->id,
                'cliente_id' =>  $row->cliente_id,
                'cliente' =>  $cliente->apellido_paterno . ' ' .
                    $cliente->apellido_materno . ' ' .
                    $cliente->nombres,
                'agencia_id' =>  $row->agencia_id,
                'agencia' =>  $agencia->nombre,
                'asesor_id' =>  $row->asesor_id,
                'asesor' =>  $asesor->usuario,
                'dias_atraso' =>  $row->dias_atraso,
                'saldo_cuota' =>  $row->monto_vencido,
                'saldo_mora' => round($row->mora_total - $row->mora_pagado, 2),
                'saldo_notificaciones' =>  round($row->notificaciones_total - $row->notificaciones_pagado, 2),
                'numero_cuota' =>  $numero_cuota,
                'carrito_detalle_id' =>   null,
                'total_cobro' =>  0,
            ];
        });

        return ['credito' => $credito[0]];
    }

    public function detalle(Request $request)
    {
        $agencia_credito = $request->input('agencia_credito');
        $credito_id = $request->input('credito_id');
        $carrito_detalle_id = $request->input('carrito_detalle_id');

        $conexion = 'master_' .  $agencia_credito;

        $datos_credito = Credito::on($conexion)
            ->select(
                'id',
                'agencia_id',
                'aprobacion_id',
                'cliente_id',
                'agencia_id',
                'datos_creacion',
                'dias_atraso',
                'cuota_actual',
                'cuotas_pendientes',
                'capital_total',
                'capital_pagado',
                'interes_total',
                'interes_pagado',
                'redondeo_total',
                'redondeo_pagado',
                'mora_total',
                'mora_pagado',
                'notificaciones_total',
                'notificaciones_pagado',
                'saldo_total',
                'cuotas_vencidas',
                'monto_vencido',
                'acumulado',
                'fecha_ultimo_pago',
                'fecha_vencimiento'
            )
            ->where('id', $credito_id)
            ->get();

        $datos_credito = $datos_credito->map(function ($row) {

            $conexion = 'master_' . $row->agencia_id;

            $aprobacion = Aprobacion::on($conexion)->find($row->aprobacion_id);
            $propuesta = Propuesta::on($conexion)->find($aprobacion->propuesta_id);
            $producto = Producto::on($conexion)->find($propuesta->producto_id);
            $tipo = Tipo::on($conexion)->find($propuesta->tipo_id);
            $estado = Estado::on($conexion)->find($propuesta->estado_id);
            $cliente = Cliente::on($conexion)->find($row->cliente_id);

            $result = $row;

            $result['monto'] = $aprobacion->monto;
            $result['cuota'] = $aprobacion->cuota;
            $result['plazo'] = $aprobacion->plazo;
            $result['periodo_pago'] = $aprobacion->periodo_pago;
            $result['pago_oficina'] = $aprobacion->pago_oficina;
            $result['tasa_interes'] = $aprobacion->tasa_interes;

            $result['producto'] = $producto->producto;
            $result['tipo'] = $tipo->tipo;
            $result['estado'] = $estado->estado;

            $result['cliente'] = $cliente->apellido_paterno . ' ' .
                $cliente->apellido_materno . ' ' .
                $cliente->nombres;

            $result['dni'] = $cliente->dni;
            $result['codigo_expediente'] = $cliente->codigo_expediente;
            $result['telefonos'] = $cliente->telefonos;

            return $result->toArray();
        });

        $datos_cuotas = Cuota::on($conexion)->where('credito_id', $credito_id)->get();

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

        $carrito_detalle = null;

        if ($carrito_detalle_id != null) {
            $carrito_detalle = CarritoDetalle::on($conexion)
                ->select(
                    'id',
                    'carrito_id',
                    'credito_id',
                    'pago_por_cuota',
                    'pago_cantidad_cuota',
                    'pago_cuota_monto',
                    'pago_por_monto',
                    'pago_monto',
                    'pago_mora',
                    'pago_mora_monto',
                    'pago_notificaciones',
                    'pago_notificaciones_monto',
                    'estado',
                    'total_cobro',
                    'telefono_envio',
                    'modo_envio',
                    'created_at'
                )
                ->where('id', $carrito_detalle_id)
                ->get()->last();
        }

        // dd(json_encode([
        //     'datos_credito' => $datos_credito[0],
        //     'datos_cuotas' => $datos_cuotas,
        //     'notificaciones' => $notificaciones,
        //     'carrito_detalle' => $carrito_detalle,
        // ]));

        return [
            'datos_credito' => $datos_credito[0],
            'datos_cuotas' => $datos_cuotas,
            'notificaciones' => $notificaciones,
            'carrito_detalle' => $carrito_detalle,
        ];
    }



    public function pagar(Request $request)
    {

        $datos_cobranza = json_decode($request->datos_cobranza);
        $agencia_carrito = $request->agencia_carrito;
        $agencia_credito = $request->agencia_credito;
        $datos_registro = (new CreditosController)->datos_registro($agencia_carrito);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_credito);

        $conexion_credito = 'master_' .  $agencia_credito;

        $credito_id = $request->credito_id;
        $carrito_id = $request->carrito_id;
        $cliente_id = $request->cliente_id;

        $modo = $request->modo;

        // ---- Número celular según entorno -----
        $enviroment = getenv('APP_ENV');
        if ($enviroment == 'production') {
            $telefono_principal = $request->telefono_principal;
        } else if ($enviroment == 'development') {
            $telefono_principal = 955547121;
        }
        // ---------------------------------------

        $modo_envio = $request->modo_envio;

        // Para verificar si el servicio está activo

        if ($modo_envio == 'SMS') {
            $servicio = 'servicio_sms';
        } else if ($modo_envio == 'WHATSAPP') {
            $servicio = 'servicio_whatsapp';
        }
        $verificar_servicio = (new GeneralController)->verificar_servicio($servicio, $agencia_credito);
        $enviar_comprobante = $verificar_servicio['resultado'];
        // -----------------------------------------------

        $pago_cuota = $datos_cobranza->pago_cuota;
        $pago_cantidad_cuota = $datos_cobranza->pago_cuota_cantidad;
        $pago_cuota_monto = $datos_cobranza->pago_cuota_monto;
        $pago_monto = $datos_cobranza->pago_monto;
        $pago_mora = $datos_cobranza->pago_mora;
        $pago_mora_monto = $datos_cobranza->pago_mora_monto;
        $pago_notificaciones = $datos_cobranza->pago_notificaciones;
        $pago_notificaciones_monto = $datos_cobranza->pago_notificaciones_monto;
        $total_cobro = $datos_cobranza->total_cobro;
        $forma_pago = $datos_cobranza->forma_pago;

        $pago_por_cuota = 0;
        $pago_por_monto = 0;

        if ($pago_cuota == true) {

            if ($forma_pago == 'por_cuota') {

                $pago_por_cuota = 1;
            }

            if ($forma_pago == 'por_monto') {

                $pago_por_monto = 1;
            }
        }

        if ($modo == 'NUEVO') {

            $id = CarritoDetalle::on($conexion_credito)->insertGetId([
                'agencia_carrito' => $agencia_carrito,
                'carrito_id' => $carrito_id,
                'credito_id' => $credito_id,
                'pago_por_cuota' => $pago_por_cuota,
                'pago_cantidad_cuota' => $pago_cantidad_cuota,
                'pago_cuota_monto' => $pago_cuota_monto,
                'pago_por_monto' => $pago_por_monto,
                'pago_monto' => $pago_monto,
                'pago_mora' => $pago_mora,
                'pago_mora_monto' => $pago_mora_monto,
                'pago_notificaciones' => $pago_notificaciones,
                'pago_notificaciones_monto' => $pago_notificaciones_monto,
                'total_cobro' => $total_cobro,
                'fecha_cobro' => $fecha_larga,
                'modo_envio' => $modo_envio,
                'telefono_envio' => $telefono_principal,
                'datos_creacion' => $datos_registro,
                'created_at' => now(),
            ]);


            $mensaje = "Se ha generado el Ticket de Cobranza: " . "*" . $agencia_credito . "-" . $cliente_id . "-" . $credito_id . "-" . $id . "*";
            CarritoDetalle::on($conexion_credito)->find($id)->update([
                'ticket' => $agencia_credito . "-" . $cliente_id . "-" . $credito_id . "-" . $id

            ]);
        }

        if ($modo == 'EDITAR') {

            $carrito_detalle_id = $request->carrito_detalle_id;

            CarritoDetalle::on($conexion_credito)->find($carrito_detalle_id)->update([
                'pago_por_cuota' => $pago_por_cuota,
                'pago_cantidad_cuota' => $pago_cantidad_cuota,
                'pago_cuota_monto' => $pago_cuota_monto,
                'pago_por_monto' => $pago_por_monto,
                'pago_monto' => $pago_monto,
                'pago_mora' => $pago_mora,
                'pago_mora_monto' => $pago_mora_monto,
                'pago_notificaciones' => $pago_notificaciones,
                'pago_notificaciones_monto' => $pago_notificaciones_monto,
                'total_cobro' => $total_cobro,
                'fecha_cobro' => $fecha_larga,
                'modo_envio' => $modo_envio,
                'telefono_envio' => $telefono_principal,
                'datos_actualizacion' => $datos_registro,
            ]);

            $mensaje = "Su Ticket de Cobranza: " . "*" . $agencia_credito . "-" . $cliente_id . "-" . $credito_id . "-" . $carrito_detalle_id . " se ha MODIFICADO" . "*";

            $carrito_detalle_aud = $request->carrito_detalle_aud;

            CarritoDetalleAUD::on($conexion_credito)->create([
                'registro_id' => $carrito_detalle_id,
                'historial' => $carrito_detalle_aud,
                'fecha_registro' => $fecha_larga,
                'datos_creacion' => $datos_registro,

            ]);
        }

        if ($pago_por_cuota == 1) {

            $concat_cuota = "_Cuotas(x" . $pago_cantidad_cuota . "): S/ " . $pago_cuota_monto . "_";
            $mensaje = $mensaje . "\n" . $concat_cuota;
        }

        if ($pago_por_monto == 1) {

            $concat_monto = "_Monto: S/ " . $pago_monto . "_";
            $mensaje = $mensaje . "\n" . $concat_monto;
        }

        if ($pago_mora == 1) {

            $concat_mora = "_Moras: S/ " . $pago_mora_monto . "_";
            $mensaje = $mensaje . "\n" . $concat_mora;
        }
        if ($pago_notificaciones == 1) {

            $concat_notif = "_Notif: S/ " . $pago_notificaciones_monto . "_";
            $mensaje = $mensaje . "\n" . $concat_notif;
        }

        $mensaje = $mensaje . "\n" .
            "*Total Cobro: S/* " . "*" . $total_cobro . "*" . "
        **Credipyme Huanca**";


        // if ($enviar_comprobante) {
        //     if ($modo_envio == 'SMS') {

        //         $mensaje = str_replace('_', '', $mensaje);
        //         $mensaje = str_replace('**', '--', $mensaje);
        //         $mensaje = str_replace('*', '', $mensaje);

        //         $respuesta_envio = (new ApiSmsController)->single_send($telefono_principal, $mensaje);
        //     } else if ($modo_envio == 'WHATSAPP') {
        //         $respuesta_envio = (new ApiWhatsAppController)->text_send($telefono_principal, $mensaje);
        //     }
        // }

        return "ENVIADO";
    }

    public function verificar(Request $request)
    {
        $resultado = null;

        $agencia_carrito = $request->agencia_carrito;
        $carrito_id = $request->carrito_id;
        $agencia_credito = $request->agencia_credito;
        $credito_id = $request->credito_id;


        $conexion_carrito = 'master_' .  $agencia_carrito;
        $conexion_credito = 'master_' .  $agencia_credito;

        $carrito_cerrado = Carrito::on($conexion_carrito)
            ->where('id', $carrito_id)
            ->where('pagado', '1')
            ->get();

        if (count($carrito_cerrado) != 0) {

            $resultado = "CARRITO_CERRADO";
        } else {

            $pendientes_pago = CarritoDetalle::on($conexion_credito)
                ->where([
                    ['credito_id', $credito_id],
                    ['agencia_carrito', '!=', $agencia_carrito],
                    ['carrito_id', '!=', $carrito_id],
                    ['estado', 'C']
                ])
                ->count();

            if ($pendientes_pago > 0) {
                $resultado = "PENDIENTE_PAGO";
            } else {
                $resultado = "DISPONIBLE";
            }
        }

        return $resultado;
    }

    public function verificar_tiempo(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $carrito_detalle_id = $request->carrito_detalle_id;

        $carrito_detalle = CarritoDetalle::on($conexion)
            ->select(
                'created_at'
            )
            ->where('id', $carrito_detalle_id)
            ->get()->last();


        $fecha_creacion = $carrito_detalle->created_at;

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $fecha1 = new \DateTime($fecha_creacion);
        $fecha2 = new \DateTime($fechaActual);


        $diferencia = $fecha1->diff($fecha2);

        $minutos_totales = $diferencia->days * 24 * 60 + $diferencia->h * 60 + $diferencia->i;

        // Límite de tiempo para EDITAR cobranza

        if ($minutos_totales <= 5) {

            $validacion = true;
        } else {
            $validacion = false;
        }

        return $validacion;
    }


    public function anular(Request $request)
    {
        $carrito_detalle_id = $request->carrito_detalle_id;
        $motivo_anulacion = $request->motivo_anulacion;
        $agencia_carrito = $request->agencia_carrito;
        $agencia_credito = $request->agencia_credito;
        $credito_id = $request->credito_id;
        $cliente_id = $request->cliente_id;

        // ---- Número celular según entorno -----
        $enviroment = getenv('APP_ENV');
        if ($enviroment == 'production') {
            $telefono_principal = $request->telefono_principal;
        } else if ($enviroment == 'development') {
            $telefono_principal = 955547121;
        }
        // ---------------------------------------


        $conexion_credito = 'master_' .  $agencia_credito;

        $motivo_anulacion = mb_strtoupper($request->motivo_anulacion);

        $datos_registro = (new CreditosController)->datos_registro($agencia_credito);
        $carrito_detalle = CarritoDetalle::on($conexion_credito)->find($carrito_detalle_id);

        $modo_envio = $carrito_detalle->modo_envio;
        $telefono_envio = $carrito_detalle->telefono_envio;

        $carrito_detalle->estado = 'A';
        $carrito_detalle->motivo_anulacion = $motivo_anulacion;
        $carrito_detalle->datos_actualizacion = $datos_registro;
        $carrito_detalle->save();

        $mensaje = "Su Ticket de Cobranza: " . "*" . $agencia_credito . "-" . $cliente_id . "-" . $credito_id . "-" . $carrito_detalle_id . " se ha ANULADO*";
        $mensaje = $mensaje . "\n" . "**Credipyme Huanca**";

        // Para verificar si el servicio está activo

        if ($modo_envio == 'SMS') {
            $servicio = 'servicio_sms';
        } else if ($modo_envio == 'WHATSAPP') {
            $servicio = 'servicio_whatsapp';
        }
        $verificar_servicio = (new GeneralController)->verificar_servicio($servicio, $agencia_credito);
        $enviar_comprobante = $verificar_servicio['resultado'];
        // -----------------------------------------------

        // if ($enviar_comprobante) {
        //     if ($modo_envio == 'SMS') {

        //         $mensaje = str_replace('_', '', $mensaje);
        //         $mensaje = str_replace('**', '--', $mensaje);
        //         $mensaje = str_replace('*', '', $mensaje);

        //         $respuesta_envio = (new ApiSmsController)->single_send($telefono_envio, $mensaje);
        //     } else if ($modo_envio == 'WHATSAPP') {
        //         $respuesta_envio = (new ApiWhatsAppController)->text_send($telefono_envio, $mensaje);
        //     }
        // }

        return "EXITO";
    }

    public function actualizar_telefono(Request $request)
    {

        try {
            DB::beginTransaction();

            $agencia_id = $request->agencia_id;
            $cliente_id = $request->cliente_id;
            $nuevo_telefono = $request->nuevo_telefono;

            $conexion = 'master_' . $agencia_id;

            $datos_registro = (new CreditosController)->datos_registro($agencia_id);
            $fecha_larga_aplicacion = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

            $cliente = Cliente::on($conexion)->find($cliente_id);
            $telefonos = json_decode($cliente->telefonos);

            $telefonos->t1 = $nuevo_telefono;
            $telefonos->n1 = 'TITULAR - MODIFICADO EN CAMPO ' . '(' . $fecha_larga_aplicacion . ')';

            $cliente->telefonos = json_encode($telefonos);
            $cliente->datos_actualizacion = $datos_registro;
            $cliente->save();

            DB::commit();
            return response()->json(['message' => 'success'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verificar_carritos($agencia_id)
    {

        $conexion = 'master_' .  $agencia_id;

        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $carrito_abierto_usuarios = Carrito::on($conexion)->from('credito_carritos as cre_car')
            ->select('us.usuario')
            ->join('solucion_master.usuarios as us', 'cre_car.usuario_id', 'us.dni')
            ->where(DB::raw("SUBSTR(cre_car.fecha_apertura,1,10)"), $fecha_actual)
            ->where('cre_car.fecha_cierre', null)
            ->where('cre_car.pagado', 0)
            ->get();

        // dd($carritos_abiertos);
        return [
            'carrito_abierto_usuarios' => $carrito_abierto_usuarios,

        ];
    }
}
