<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Models\Logistica\Estado;
use App\Models\General\Agencia;
use App\Models\Logistica\Suministros\Suministro;
use App\Models\Logistica\Suministros\Envio;
use App\Models\Logistica\Suministros\EnvioDetalle;

use Inertia\Inertia;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SuministroEnvioController extends Controller
{
    public function historial_envios()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ENVIOS_LOCAL',  'LOGISTICA_SUMINISTROS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ENVIOS_GENERAL',  'LOGISTICA_SUMINISTROS');
            // }

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ENVIOS',  'LOGISTICA_SUMINISTROS');

            if ($band == 1) {

                // if ($tipo_modulo == 'LOCAL') {
                //     $agencias = Agencia::select(
                //         'id_agencia',
                //         'nombre as nombre',
                //     )
                //         ->where('id_agencia', session('id_agencia'))
                //         ->get();
                // } else {
                //     $agencias = Agencia::select(
                //         'id_agencia',
                //         'nombre'
                //     )
                //         ->orderby('nombre', 'asc')
                //         ->get();
                // }

                return Inertia::render('Logistica/Suministros/Historial/historial_envios', []);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function envios_recepciones()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ENVIOS_RECEPCIONES',  'LOGISTICA_SUMINISTROS');
            if ($band == 1) {
                return Inertia::render(
                    'Logistica/Suministros/envios_recepciones',

                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function enviar(Request $request)
    {

        $agencia_envio = $request->agencia_envio;
        $agencia_recepcion = $request->agencia_recepcion;
        $responsable_recepcion = $request->responsable_recepcion;

        $canasta_envio = json_decode($request->canasta_envio);

        $datos_registro = (new LogisticaController)->datos_registro();

        $situacion = 'PENDIENTE';

        $envio = Envio::create(
            [
                'agencia_envio' => $agencia_envio,
                'agencia_recepcion' => $agencia_recepcion,
                'responsable_recepcion' => $responsable_recepcion,
                'situacion' => $situacion,
                'datos_creacion' => $datos_registro
            ]


        );

        $envio_id = $envio->id;

        $path_name = pathinfo($_FILES['documento_envio']['name']);
        $extension = "." . $path_name['extension'];
        $nombre_documento = "sum_envio_" . $envio_id . $extension;
        $archivo = $_FILES['documento_envio']['tmp_name'];
        $ruta = '/imagenes_server/logistica/suministros/envios_recepciones';
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
        move_uploaded_file($archivo, $ruta);

        Envio::where('id', $envio_id)
            ->update([
                'documento_envio' => $nombre_documento
            ]);

        foreach ($canasta_envio as $item) {

            EnvioDetalle::create(
                [
                    'envio_id' => $envio_id,
                    'suministro_id' => $item->id,
                    'cantidad' => $item->cantidad_envio,
                    'valor_unitario' => $item->valor_unitario,
                    'situacion' => $situacion,
                    'datos_creacion' => $datos_registro
                ]
            );

            $cantidad_actual = Suministro::select('id', 'cantidad')->where('id', $item->id)->get()->last();

            $nueva_cantidad = floatval($cantidad_actual->cantidad) - floatval($item->cantidad_envio);

            if ($nueva_cantidad == 0) {
                $estado = Estado::select('id')->where('estado', 'AGOTADO')->get()->last();
                $estado_id = $estado->id;
            } else {
                $estado_id = $item->estado_id;
            }

            Suministro::where('id', $item->id)
                ->update([
                    'cantidad' => $nueva_cantidad,
                    'estado_id' => $estado_id,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Suministros ENVIADOS.'
        ], 200);
    }

    public function listar_recursos()
    {
        $fecha_actual = (new LogisticaController)->fecha_corta_aplicacion();

        $fecha = Carbon::parse($fecha_actual);
        $primer_dia  = $fecha->firstOfMonth()->toDateString();

        return response()->json([
            'fecha_desde' => $primer_dia,
            'fecha_hasta' => $fecha_actual
        ]);
    }

    public function filtrar_por_fecha(Request $request)
    {
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $operador = '>';
        $usuario_dni = 0;
        if ($request->usuario == 'MI_USUARIO') {
            $operador = '=';
            $usuario_dni = session('usuario_dni');
        }

        $envios_recepciones = Envio::from('suministro_envios as sum_env')
            ->select(
                DB::raw("IF(SUBSTRING(sum_env.datos_creacion,42,8)$operador$usuario_dni,'ENVÍO','RECEPCIÓN') as tipo"),
                'sum_env.id',
                'sum_env.agencia_envio',
                'sum_env.agencia_recepcion',
                'sum_env.responsable_recepcion',
                'sum_env.documento_envio',
                'sum_env.documento_recepcion',
                'sum_env.situacion',
                'sum_env.datos_creacion',

                'ag_1.nombre as nombre_agencia_envio',
                'ag_2.nombre as nombre_agencia_recepcion',

                'us_1.usuario as usuario_envio',
                'log_res.abreviacion',
                'us_2.usuario as usuario_recepcion'
            )
            ->join('agencias as ag_1', 'sum_env.agencia_envio', 'ag_1.id_agencia')
            ->join('agencias as ag_2', 'sum_env.agencia_recepcion', 'ag_2.id_agencia')
            ->join('logistica_responsables as log_res', 'sum_env.responsable_recepcion', 'log_res.id')
            ->join('usuarios as us_1', DB::raw("SUBSTRING(sum_env.datos_creacion,42,8)"), 'us_1.dni')
            ->join('usuarios as us_2', 'log_res.usuario_id', 'us_2.dni')
            ->where([
                [DB::raw("SUBSTRING(sum_env.datos_creacion,42,8)"), $operador, $usuario_dni],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->orWhere([
                ['log_res.usuario_id', $operador, $usuario_dni],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->orderBy('sum_env.id', 'desc')
            ->get();


        // $envios_recepciones_detalle = EnvioDetalle::from('suministro_envios_detalles as sum_env_det')
        //     ->select(
        //         'sum_env_det.id',
        //         'sum_env_det.envio_id',
        //         'sum_env_det.suministro_id',
        //         'sum_env_det.cantidad',
        //         'sum_env_det.valor_unitario',
        //         'sum_env_det.situacion',

        //         'sum_env.agencia_envio',
        //         'sum_env.agencia_recepcion',

        //         'sum_alm.codigo',
        //         'sum_alm.suministro',
        //         'sum_alm.tipo_id',
        //         'sum_alm.proveedor_id',
        //         'sum_alm.condicion_id',
        //         'sum_alm.medicion_id',

        //         'sum_tip.tipo',
        //         'log_con.condicion',
        //     )
        //     ->join('suministro_envios as sum_env', 'sum_env_det.envio_id', 'sum_env.id')
        //     ->join('suministro_almacen as sum_alm', 'sum_env_det.suministro_id', 'sum_alm.id')
        //     ->join('suministro_tipos as sum_tip', 'sum_alm.tipo_id', 'sum_tip.id')
        //     ->join('logistica_condiciones as log_con', 'sum_alm.condicion_id', 'log_con.id')
        //     ->join('logistica_responsables as log_res', 'sum_env.responsable_recepcion', 'log_res.id')
        //     ->where([
        //         [DB::raw("SUBSTRING(sum_env.datos_creacion,42,8)"), $operador, $usuario_dni],
        //         [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
        //         [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
        //     ])
        //     ->orWhere([
        //         ['log_res.usuario_id', $operador, $usuario_dni],
        //         [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
        //         [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
        //     ])
        //     ->get();

        return  response()->json(['envios_recepciones' => $envios_recepciones]);
    }

    public function detalle(Request $request)

    {

        $envio_id = $request->envio_id;

        $envio_detalle = EnvioDetalle::from('suministro_envios_detalles as sum_env_det')
            ->select(
                'sum_env_det.id',
                'sum_env_det.envio_id',
                'sum_env_det.suministro_id',
                'sum_env_det.cantidad',
                'sum_env_det.valor_unitario',
                'sum_env_det.situacion',

                'sum_env.agencia_envio',
                'sum_env.agencia_recepcion',

                'sum_alm.codigo',
                'sum_alm.suministro',
                'sum_alm.tipo_id',
                'sum_alm.proveedor_id',
                'sum_alm.condicion_id',
                'sum_alm.medicion_id',

                'sum_tip.tipo',
                'log_con.condicion',
            )
            ->join('suministro_envios as sum_env', 'sum_env_det.envio_id', 'sum_env.id')
            ->join('suministro_almacen as sum_alm', 'sum_env_det.suministro_id', 'sum_alm.id')
            ->join('suministro_tipos as sum_tip', 'sum_alm.tipo_id', 'sum_tip.id')
            ->join('logistica_condiciones as log_con', 'sum_alm.condicion_id', 'log_con.id')
            ->join('logistica_responsables as log_res', 'sum_env.responsable_recepcion', 'log_res.id')
            ->where([
                ['sum_env.id', $envio_id]
            ])
            ->get();

        return response()->json(['envio_detalle' => $envio_detalle]);
    }

    public function confirmar(Request $request)
    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $envio_id = $request->envio_id;
        $canasta_recepcion = json_decode($request->canasta_recepcion);

        $estado_nuevo = Estado::select('id')
            ->where('estado', 'DISPONIBLE')->get()->last();

        $estado_nuevo_id = $estado_nuevo->id;

        // dd(count($canasta_recepcion));

        if ($modo == 'CONFIRMAR') {
            $estados_existe = Estado::select('id')->whereIn('estado', ['DISPONIBLE', 'AGOTADO'])->get();

            foreach ($canasta_recepcion as $item) {

                $suministro_existe = Suministro::select(
                    'id',
                    'cantidad',
                )
                    ->where([
                        ['suministro', $item->suministro],
                        ['tipo_id', $item->tipo_id],
                        ['condicion_id', $item->condicion_id],
                        ['agencia_id', $item->agencia_recepcion],
                        ['valor_unitario', $item->valor_unitario],
                    ])
                    ->whereIn('estado_id', $estados_existe)
                    ->get()->last();


                if ($suministro_existe == null) {
                    // Si NO EXISTE el suministro, se CREA uno nuevo

                    $codigo_tipo = $item->codigo;
                    $abrev = substr($codigo_tipo, 0, 3);

                    $ultimo_codigo = Suministro::select(DB::raw("COUNT(id) as ultimo"))
                        ->where('tipo_id', $item->tipo_id)->get()->last();

                    $ultimo_codigo = $ultimo_codigo->ultimo;
                    $codigo = $abrev . (str_pad(strval(intval($ultimo_codigo) + 1), 6, "0", STR_PAD_LEFT));

                    Suministro::create(
                        [
                            'agencia_id' => $item->agencia_recepcion,
                            'codigo' => $codigo,
                            'suministro' => $item->suministro,
                            'tipo_id' => $item->tipo_id,
                            'proveedor_id' => $item->proveedor_id,
                            'condicion_id' => $item->condicion_id,
                            'medicion_id' => $item->medicion_id,
                            'cantidad' => $item->cantidad,
                            'valor_unitario' => $item->valor_unitario,
                            'estado_id' => $estado_nuevo_id,
                            'datos_creacion' => $datos_registro
                        ]
                    );
                } else {
                    // Si  EXISTE el suministro, se ACTUALIZA su STOCK
                    $ultimo_cantidad = $suministro_existe->cantidad;

                    $nueva_cantidad = floatVal($item->cantidad) + floatVal($ultimo_cantidad);

                    Suministro::where('id', $suministro_existe->id)
                        ->update([
                            'cantidad' => $nueva_cantidad,
                            'estado_id' => $estado_nuevo_id,
                            'datos_actualizacion' => $datos_registro
                        ]);
                }
                // Actualiza la SITUACIÓN a RECHAZADO
                EnvioDetalle::where('id', $item->id)->update([
                    'situacion' => 'CONFIRMADO',
                    'datos_actualizacion' => $datos_registro
                ]);
            }

            // Guardar el DOCUMENTO de RECEPCIÓN
            $path_name = pathinfo($_FILES['documento_recepcion']['name']);
            $extension = "." . $path_name['extension'];
            $nombre_documento = "sum_recepcion_" . $envio_id . $extension;
            $archivo = $_FILES['documento_recepcion']['tmp_name'];
            $ruta = '/imagenes_server/logistica/suministros/envios_recepciones';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
            move_uploaded_file($archivo, $ruta);

            // Crear el MENSAJE de respuesta
            $mensaje = 'Suministros CONFIRMADOS.';
        } else if ($modo == 'RECHAZAR') {

            foreach ($canasta_recepcion as $item) {

                $suministro  = Suministro::select('cantidad')
                    ->where('id', $item->suministro_id)
                    ->get()
                    ->last();

                $nueva_cantidad = floatval($suministro->cantidad) + floatval($item->cantidad);

                // Devuelve la CANTIDAD al ALMACÉN de ORIGEN
                Suministro::where('id', $item->suministro_id)
                    ->update([
                        'cantidad' => $nueva_cantidad,
                        'estado_id' => $estado_nuevo_id,
                        'datos_actualizacion' => $datos_registro
                    ]);

                // Actualiza la SITUACIÓN a RECHAZADO
                EnvioDetalle::where('id', $item->id)->update([
                    'situacion' => 'RECHAZADO',
                    'datos_actualizacion' => $datos_registro
                ]);
            }

            // Crear el MENSAJE de respuesta
            $mensaje = 'Suministros RECHAZADOS.';
        }

        // Actualizando SITUACIÓN del ENVÍO de acuerdo a la cantidad de suministros que quedan
        $envios = EnvioDetalle::select('situacion')->where('envio_id', $envio_id)->pluck('situacion')->toArray();

        $situacion_nueva = 'PENDIENTE';
        if (!in_array('PENDIENTE', $envios)) {
            if (in_array('CONFIRMADO', $envios)) {
                $situacion_nueva = 'CONFIRMADO';
            } else {
                $situacion_nueva = 'RECHAZADO';
            }
        }

        Envio::where('id', $envio_id)
            ->update([
                'situacion' => $situacion_nueva,
                'datos_actualizacion' => $datos_registro,
            ]);

        // Retorna la RESPUESTA
        return response()->json([
            'success' => true,
            'message' => $mensaje
        ], 200);
    }

    public function buscar(Request $request)
    {

        $agencia_id  = $request->agencia_id;
        $fecha_desde  = $request->fecha_desde;
        $fecha_hasta  = $request->fecha_hasta;

        $lista_envios = Envio::from('suministro_envios as sum_env')
            ->select(
                'sum_env.id',
                'sum_env.agencia_envio',
                'sum_env.agencia_recepcion',
                'sum_env.responsable_recepcion',
                'sum_env.documento_envio',
                'sum_env.documento_recepcion',
                'sum_env.situacion',
                'sum_env.datos_creacion',
                'sum_env.datos_actualizacion',

                'ag_1.nombre as nombre_agencia_envio',
                'ag_2.nombre as nombre_agencia_recepcion',

                'log_res.abreviacion',

                'us_1.usuario as usuario_recepcion',
                'us_2.usuario as  usuario_envio'
            )
            ->join('agencias as ag_1', 'sum_env.agencia_envio', 'ag_1.id_agencia')
            ->join('agencias as ag_2', 'sum_env.agencia_recepcion', 'ag_2.id_agencia')
            ->join('logistica_responsables as log_res', 'sum_env.responsable_recepcion', 'log_res.id')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(sum_env.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['sum_env.agencia_envio', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();

        $lista_envios_detalles = EnvioDetalle::from('suministro_envios_detalles as sum_env_det')
            ->select(

                'sum_env_det.id',
                'sum_env_det.envio_id',
                'sum_env_det.suministro_id',
                'sum_env_det.cantidad',
                'sum_env_det.valor_unitario',
                'sum_env_det.situacion',
                'sum_env_det.datos_creacion',
                'sum_env_det.datos_actualizacion',

                'sum_env.agencia_envio',
                'sum_env.agencia_recepcion',
                'sum_env.responsable_recepcion',


                'sum_alm.suministro',
                'sum_tip.tipo',

                'ag_1.nombre as nombre_agencia_envio',
                'ag_2.nombre as nombre_agencia_recepcion',

                'log_res.abreviacion',

                'us_1.usuario as usuario_recepcion',
                'us_2.usuario as  usuario_envio'
            )
            ->join('suministro_envios as sum_env', 'sum_env_det.envio_id', 'sum_env.id')
            ->join('suministro_almacen as sum_alm', 'sum_env_det.suministro_id', 'sum_alm.id')
            ->join('suministro_tipos as sum_tip', 'sum_alm.tipo_id', 'sum_tip.id')
            ->join('agencias as ag_1', 'sum_env.agencia_envio', 'ag_1.id_agencia')
            ->join('agencias as ag_2', 'sum_env.agencia_recepcion', 'ag_2.id_agencia')
            ->join('logistica_responsables as log_res', 'sum_env.responsable_recepcion', 'log_res.id')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(sum_env.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['sum_env.agencia_envio', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();

        return [
            'lista_envios' => $lista_envios,
            'lista_envios_detalles' => $lista_envios_detalles
        ];
    }
}
