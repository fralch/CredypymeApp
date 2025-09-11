<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Http\Controllers\General\PermisosController;
use App\Models\General\Agencia;

use App\Models\Logistica\Suministros\Suministro;
use App\Models\Logistica\Suministros\Tipo;
use App\Models\Logistica\Suministros\AsignacionDetalle;
use App\Models\Logistica\Suministros\Devolucion;
use App\Models\Logistica\Suministros\DevolucionDetalle;
use App\Models\Logistica\Condicion;
use App\Models\Logistica\Estado;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuministroDevolucionController extends Controller
{

    public function devolucion()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'DEVOLUCION_LOCAL',  'LOGISTICA_SUMINISTROS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'DEVOLUCION_GENERAL',  'LOGISTICA_SUMINISTROS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'DEVOLUCION',  'LOGISTICA_SUMINISTROS');

            if ($band == 1) {

                $tipos = Tipo::select('id', 'tipo')
                    ->where('habilitado', 1)
                    ->orderby('tipo', 'asc')
                    ->get();

                $condiciones = Condicion::select('id', 'condicion')
                    ->where('habilitado', 1)
                    ->orderby('id', 'asc')
                    ->get();

                return Inertia::render(
                    'Logistica/Suministros/devolucion',
                    [
                        'tipos' => $tipos,
                        'condiciones' => $condiciones,
                    ]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function historial_devoluciones()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_DEVOLUCIONES_LOCAL',  'LOGISTICA_SUMINISTROS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_DEVOLUCIONES_GENERAL',  'LOGISTICA_SUMINISTROS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_DEVOLUCIONES',  'LOGISTICA_SUMINISTROS');

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

                return Inertia::render('Logistica/Suministros/Historial/historial_devoluciones', []);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function historial_bajas()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;


            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_BAJAS',  'LOGISTICA_SUMINISTROS');

            if ($band == 1) {



                return Inertia::render('Logistica/Suministros/Historial/historial_bajas', []);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar(Request $request)
    {
        $modo = $request->modo;

        if ($modo == 'devolucion') {
            return $this->buscar_devoluciones($request);
        }
        if ($modo == 'baja') {
            return $this->buscar_bajas($request);
        }
    }


    public function buscar_devoluciones(Request $request)

    {

        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $lista_devoluciones = Devolucion::from('suministro_devoluciones as sum_dev')
            ->select(
                'sum_dev.id',
                'sum_dev.agencia_id',
                'sum_dev.documento',
                'sum_dev.datos_creacion',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_registro'

            )
            ->join('agencias as ag', 'sum_dev.agencia_id', 'ag.id_agencia')
            ->join('usuarios as us_1', DB::raw("SUBSTRING(sum_dev.datos_creacion,42,8)"), 'us_1.dni')
            ->join('suministro_devoluciones_detalles as sum_dev_det', 'sum_dev_det.devolucion_id', 'sum_dev.id')
            ->where([
                ['sum_dev.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->where('sum_dev_det.tipo', 'NORMAL')
            ->distinct('sum_dev.id')
            ->get();

        $lista_devoluciones_detalles = DevolucionDetalle::from('suministro_devoluciones_detalles as sum_dev_det')
            ->select(
                'sum_dev_det.id',
                'sum_dev_det.devolucion_id',
                'sum_dev_det.asignacion_detalle_id',
                'sum_dev_det.condicion_id',
                'sum_dev_det.cantidad',
                'sum_dev_det.valor_unitario',
                DB::raw("'DEVOLUCIÓN' as tipo"),
                'sum_dev_det.datos_creacion',

                'sum_alm.codigo',
                'sum_alm.suministro',
                'log_con.condicion',
                'log_res.abreviacion',
                'log_med.medicion',
                'us_1.usuario as usuario_devolucion',

                'ag.nombre as agencia',
                'us_2.usuario as usuario_registro'

            )
            ->join('suministro_devoluciones as sum_dev', 'sum_dev_det.devolucion_id', 'sum_dev.id')
            ->join('suministro_asignaciones_detalles as sum_asi_det', 'sum_dev_det.asignacion_detalle_id', 'sum_asi_det.id')
            ->join('suministro_asignaciones as sum_asi', 'sum_asi_det.asignacion_id', 'sum_asi.id')
            ->join('suministro_almacen as sum_alm', 'sum_asi_det.suministro_id', 'sum_alm.id')
            ->join('logistica_mediciones as log_med', 'sum_alm.medicion_id', 'log_med.id')
            ->join('logistica_responsables as log_res', 'sum_asi.responsable_id', 'log_res.id')
            ->join('logistica_condiciones as log_con', 'sum_dev_det.condicion_id',  'log_con.id')
            ->join('agencias as ag', 'sum_dev.agencia_id', 'ag.id_agencia')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(sum_dev.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['sum_dev.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->where('sum_dev_det.tipo', 'NORMAL')
            ->get();
        // dd($request);

        return [
            'lista_devoluciones' => $lista_devoluciones,
            'lista_devoluciones_detalles' => $lista_devoluciones_detalles
        ];
    }
    public function buscar_bajas(Request $request)

    {
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $lista_devoluciones = Devolucion::from('suministro_devoluciones as sum_dev')
            ->select(
                'sum_dev.id',
                'sum_dev.agencia_id',
                'sum_dev.documento',
                'sum_dev.datos_creacion',
                'sum_dev.datos_creacion',
                'sum_dev_det.observacion',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_registro'

            )
            ->join('agencias as ag', 'sum_dev.agencia_id', 'ag.id_agencia')
            ->join('usuarios as us_1', DB::raw("SUBSTRING(sum_dev.datos_creacion,42,8)"), 'us_1.dni')
            ->join('suministro_devoluciones_detalles as sum_dev_det', 'sum_dev_det.devolucion_id', 'sum_dev.id')
            ->where([
                ['sum_dev.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->where('sum_dev_det.tipo', 'BAJA')
            ->distinct('sum_dev.id')
            ->get();


        $lista_devoluciones_detalles = DevolucionDetalle::from('suministro_devoluciones_detalles as sum_dev_det')
            ->select(
                'sum_dev_det.id',
                'sum_dev_det.devolucion_id',
                'sum_dev_det.asignacion_detalle_id',
                'sum_dev_det.condicion_id',
                'sum_dev_det.cantidad',
                'log_med.medicion',
                'sum_dev_det.valor_unitario',
                'sum_dev_det.tipo',
                'sum_dev_det.datos_creacion',

                'sum_alm.codigo',
                'sum_alm.suministro',
                'log_con.condicion',
                'log_res.abreviacion',
                'us_1.usuario as usuario_baja',


                'ag.nombre as agencia',
                'us_2.usuario as usuario_registro'

            )
            ->join('suministro_devoluciones as sum_dev', 'sum_dev_det.devolucion_id', 'sum_dev.id')
            ->join('suministro_asignaciones_detalles as sum_asi_det', 'sum_dev_det.asignacion_detalle_id', 'sum_asi_det.id')
            ->join('suministro_asignaciones as sum_asi', 'sum_asi_det.asignacion_id', 'sum_asi.id')
            ->join('suministro_almacen as sum_alm', 'sum_asi_det.suministro_id', 'sum_alm.id')
            ->join('logistica_mediciones as log_med', 'sum_alm.medicion_id', 'log_med.id')
            ->join('logistica_responsables as log_res', 'sum_asi.responsable_id', 'log_res.id')
            ->join('logistica_condiciones as log_con', 'sum_dev_det.condicion_id',  'log_con.id')
            ->join('agencias as ag', 'sum_dev.agencia_id', 'ag.id_agencia')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(sum_dev.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['sum_dev.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->where('sum_dev_det.tipo', 'BAJA')
            ->get();


        return [
            'lista_devoluciones' => $lista_devoluciones,
            'lista_devoluciones_detalles' => $lista_devoluciones_detalles
        ];
    }

    public function devolver(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $controller = (new LogisticaController);
        $observacion =  $controller->verificar_nulo(trim($request->observacion));
        if ($observacion) {
            $observacion = mb_strtoupper($observacion);
        }
        $modo = $request->modo;
        $canasta_devolucion = json_decode($request->canasta_devolucion);

        $datos_registro =  $controller->datos_registro();

        $devolucion = Devolucion::create(
            [
                'agencia_id' => $agencia_id,
                'datos_creacion' => $datos_registro,
            ]
        );

        $devolucion_id = $devolucion->id;

        if (!empty($_FILES['documento'])) {

            $path_name = pathinfo($_FILES['documento']['name']);
            $extension = "." . $path_name['extension'];
            $nombre_documento = "sum_devolucion_" . $devolucion_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/logistica/suministros/devoluciones';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
            move_uploaded_file($archivo, $ruta);

            Devolucion::where('id', $devolucion_id)
                ->update([
                    'documento' => $nombre_documento
                ]);
        }

        $estados_condicion = Estado::select('id')
            ->where('estado', 'DISPONIBLE')
            ->orWhere('estado', 'AGOTADO')->get();

        foreach ($canasta_devolucion as $item) {

            if ($modo == 'devolucion') {
                $suministro_existe = Suministro::select(
                    'id',
                    'cantidad',
                )
                    ->where([
                        ['suministro', $item->suministro],
                        ['tipo_id', $item->tipo_id],
                        ['condicion_id', $item->condicion_devolucion],
                        ['agencia_id', $agencia_id],
                        ['valor_unitario', $item->valor_devolucion],
                    ])
                    ->whereIn('estado_id', $estados_condicion)
                    ->get()->last();

                $estado_nuevo = Estado::select('id')
                    ->where('estado', 'DISPONIBLE')->get()->last();

                $estado_nuevo_id = $estado_nuevo->id;

                if ($suministro_existe == null) {

                    $codi_tipo = $item->codigo;
                    $abrev = substr($codi_tipo, 0, 3);

                    $ultimo_codigo = Suministro::select(DB::raw("COUNT(id) as ultimo"))
                        ->where('tipo_id', $item->tipo_id)->get()->last();

                    $ultimo_codigo = $ultimo_codigo->ultimo;

                    Suministro::create(
                        [
                            'agencia_id' => $item->agencia_id,
                            'codigo' => $abrev . (str_pad(strval(intval($ultimo_codigo) + 1), 6, "0", STR_PAD_LEFT)),
                            'suministro' => $item->suministro,
                            'tipo_id' => $item->tipo_id,
                            'proveedor_id' => $item->proveedor_id,
                            'condicion_id' => $item->condicion_devolucion,
                            'cantidad' => $item->cantidad_devolucion,
                            'medicion_id' => $item->medicion_id,
                            'valor_unitario' => $item->valor_devolucion,
                            'estado_id' => $estado_nuevo_id,
                            'datos_creacion' => $datos_registro
                        ]
                    );
                } else {

                    $ultimo_cantidad = $suministro_existe->cantidad;

                    $nueva_cantidad = floatVal($item->cantidad_devolucion) + floatVal($ultimo_cantidad);

                    Suministro::where('id', $suministro_existe->id)
                        ->update([
                            'cantidad' => $nueva_cantidad,
                            'estado_id' => $estado_nuevo_id,
                            'datos_actualizacion' => $datos_registro
                        ]);
                }
            }


            $estado_devuelto = Estado::select('id')
                ->where('estado', 'DEVUELTO')->get()->last();
            $estado_devuelto_id = $estado_devuelto->id;

            if ($item->cantidad_devolucion == $item->cantidad) {
                AsignacionDetalle::where('id', $item->id)
                    ->update([
                        'cantidad_restante' => 0,
                        'estado_id' => $estado_devuelto_id,
                        'datos_actualizacion' => $datos_registro
                    ]);
            }
            if ($item->cantidad_devolucion < $item->cantidad) {
                $restante = floatVal($item->cantidad) - floatVal($item->cantidad_devolucion);
                AsignacionDetalle::where('id', $item->id)
                    ->update([
                        'cantidad_restante' => $restante,
                        'datos_actualizacion' => $datos_registro
                    ]);
            }

            $datos_devolucion = [
                'devolucion_id' =>  $devolucion_id,
                'asignacion_detalle_id' =>  $item->id,
                'condicion_id' => $item->condicion_devolucion,
                'cantidad' => $item->cantidad_devolucion,
                'datos_creacion' => $datos_registro
            ];

            if ($modo == 'devolucion') {
                $datos_devolucion['valor_unitario'] = $item->valor_devolucion;
                $datos_devolucion['tipo'] = 'NORMAL';
            } else if ($modo == 'baja') {
                $datos_devolucion['valor_unitario'] = 0;
                $datos_devolucion['tipo'] = 'BAJA';
                $datos_devolucion['observacion'] = $observacion;
            }

            DevolucionDetalle::create(
                $datos_devolucion
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Registro exitoso'
        ], 200);
    }
}
