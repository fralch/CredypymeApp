<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Models\General\Agencia;
use App\Models\Logistica\Responsable;
use App\Models\Logistica\Estado;
use App\Models\Logistica\Suministros\Tipo;
use App\Models\Logistica\Suministros\Suministro;
use App\Models\Logistica\Suministros\Asignacion;
use App\Models\Logistica\Suministros\AsignacionDetalle;
use App\Models\Logistica\Suministros\Devolucion;
use App\Models\Logistica\Suministros\DevolucionDetalle;
use Illuminate\Http\Request;

use Inertia\Inertia;

use Illuminate\Support\Facades\DB;

class SuministroAsignacionController extends Controller
{
    //--------------------------FUNCIONES QUE DEVUELVEN UNA VISTA-----------------------------
    public function asignacion()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASIGNACION_LOCAL',  'LOGISTICA_SUMINISTROS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASIGNACION_GENERAL',  'LOGISTICA_SUMINISTROS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASIGNACION',  'LOGISTICA_SUMINISTROS');
            if ($band == 1) {

                $tipos = Tipo::select('id', 'tipo')
                    ->where('habilitado', 1)
                    ->orderby('tipo', 'asc')
                    ->get();


                return Inertia::render(
                    'Logistica/Suministros/asignacion',
                    [
                        'tipos' => $tipos,
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

    public function mis_suministros()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_SUMINISTROS',  'LOGISTICA_SUMINISTROS');

            if ($band == 1) {

                $responsables = Responsable::from('logistica_responsables as log_res')
                    ->select(
                        'log_res.id',
                        'log_res.responsable',
                        'log_res.abreviacion',
                        'log_res.agencia_id',
                        'log_res.usuario_id',
                        'log_res.encargado_agencia',
                        'log_res.descripcion',
                        'us.usuario'
                    )
                    ->join('usuarios as us', 'us.dni', 'log_res.usuario_id')
                    ->where([
                        ['log_res.usuario_id', session('usuario_dni')]
                    ])->orderBy('us.usuario', 'asc')->get();

                $tipos = Tipo::select('id', 'tipo')
                    ->where('habilitado', 1)
                    ->orderby('tipo', 'asc')
                    ->get();

                $estados = Estado::select('id', 'estado')
                    ->where('habilitado',  1)
                    ->whereIn('estado', ['ASIGNADO', 'DEVUELTO'])
                    ->orderby('estado', 'asc')
                    ->get();

                return Inertia::render(
                    'Logistica/Suministros/mis_suministros',
                    [
                        'responsables' => $responsables,
                        'tipos' => $tipos,
                        'estados' => $estados
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
    public function historial_asignaciones()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ASIGNACIONES_LOCAL',  'LOGISTICA_SUMINISTROS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ASIGNACIONES_GENERAL',  'LOGISTICA_SUMINISTROS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ASIGNACIONES',  'LOGISTICA_SUMINISTROS');

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

                return Inertia::render('Logistica/Suministros/Historial/historial_asignaciones', []);
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

        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $lista_asignaciones = Asignacion::from('suministro_asignaciones as sum_asi')
            ->select(
                'sum_asi.id',
                'sum_asi.agencia_id',
                'sum_asi.responsable_id',
                'sum_asi.documento',
                'sum_asi.datos_creacion',

                'ag.nombre as agencia',
                'log_res.abreviacion',
                'us_1.usuario as usuario_asignado',
                'us_2.usuario as usuario_asignador',
            )
            ->join('agencias as ag', 'sum_asi.agencia_id', 'ag.id_agencia')
            ->join('logistica_responsables as log_res', 'sum_asi.responsable_id', 'log_res.id')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(sum_asi.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['sum_asi.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_asi.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_asi.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();

        $lista_asignaciones_detalles = AsignacionDetalle::from('suministro_asignaciones_detalles as sum_asi_det')
            ->select(
                'sum_asi_det.id',
                'sum_asi_det.asignacion_id',
                'sum_asi_det.suministro_id',
                'sum_asi_det.condicion_id',
                'sum_asi_det.cantidad',
                'sum_asi_det.cantidad_restante',
                'sum_asi_det.valor_unitario',
                'sum_asi_det.estado_id',
                'sum_asi_det.datos_creacion',

                'sum_alm.codigo',
                'sum_alm.suministro',
                'sum_tip.tipo',
                'log_con.condicion',
                'log_est.estado',

                'ag.nombre as agencia',
                'log_res.abreviacion',
                'us_1.usuario as usuario_asignado',
                'us_2.usuario as usuario_asignador'
            )
            ->join('suministro_asignaciones as sum_asi', 'sum_asi_det.asignacion_id', 'sum_asi.id')
            ->join('suministro_almacen as sum_alm', 'sum_asi_det.suministro_id', 'sum_alm.id')
            ->join('suministro_tipos as sum_tip', 'sum_alm.tipo_id', 'sum_tip.id')
            ->join('logistica_condiciones as log_con', 'sum_asi_det.condicion_id',  'log_con.id')
            ->join('logistica_estados as log_est', 'sum_asi_det.estado_id',  'log_est.id')
            ->join('agencias as ag', 'sum_asi.agencia_id', 'ag.id_agencia')
            ->join('logistica_responsables as log_res', 'sum_asi.responsable_id', 'log_res.id')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(sum_asi.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['sum_asi.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_asi.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_asi.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();

        return [
            'lista_asignaciones' => $lista_asignaciones,
            'lista_asignaciones_detalles' => $lista_asignaciones_detalles
        ];
    }

    // --------------------------FUNCIONES QUE NO DEVUELVEN VISTAS------------------------------
    public function asignar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $responsable_id = $request->responsable_id;
        $canasta_asignacion = json_decode($request->canasta_asignacion);

        $datos_registro = (new LogisticaController)->datos_registro();

        $asignacion = Asignacion::create(
            [
                'agencia_id' => $agencia_id,
                'responsable_id' => $responsable_id,
                'datos_creacion' => $datos_registro
            ]
        );

        $asignacion_id = $asignacion->id;

        if (!empty($_FILES['documento'])) {

            $path_name = pathinfo($_FILES['documento']['name']);
            $extension = "." . $path_name['extension'];
            $nombre_documento = "sum_asignacion_" . $asignacion_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/logistica/suministros/asignaciones';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
            move_uploaded_file($archivo, $ruta);

            Asignacion::where('id', $asignacion_id)
                ->update([
                    'documento' => $nombre_documento
                ]);
        }

        foreach ($canasta_asignacion as $item) {
            $clasificacion = $item->clasificacion;

            // Mapear clasificación a estado
            $estado_nombre = $clasificacion === 'SUMINISTRO' ? 'ASIGNADO' : 'DEVUELTO';

            // Obtener ID del estado solo una vez
            $estado_id = Estado::where('estado', $estado_nombre)->value('id');

            $asignacion_detalle = AsignacionDetalle::create(
                [
                    'asignacion_id' => $asignacion_id,
                    'suministro_id' => $item->id,
                    'condicion_id' => $item->condicion_id,
                    'cantidad' => $item->cantidad_asignar,
                    'cantidad_restante' => $item->cantidad_asignar,
                    'valor_unitario' => $item->valor_unitario,
                    'estado_id' => $estado_id,
                    'datos_creacion' => $datos_registro
                ]
            );

            if ($clasificacion === 'GASTO') {

                $devolucion = Devolucion::create(
                    [
                        'agencia_id' => $agencia_id,
                        'datos_creacion' => $datos_registro,
                    ]
                );

                $datos_devolucion = [
                    'devolucion_id' =>  $devolucion->id,
                    'asignacion_detalle_id' =>  $asignacion_detalle->id,
                    'condicion_id' => $item->condicion_id,
                    'cantidad' => $item->cantidad_asignar,
                    'datos_creacion' => $datos_registro,
                    'valor_unitario' => 0,
                    'tipo' => 'BAJA',
                    'observacion' => 'BAJA POR CLASIFICACIÓN GASTO',
                ];

                DevolucionDetalle::create(
                    $datos_devolucion
                );
            }

            $cantidad_actual = Suministro::select('id', 'cantidad')->where('id', $item->id)->get()->last();

            $nueva_cantidad = floatval($cantidad_actual->cantidad) - floatval($item->cantidad_asignar);

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
            'message' => 'Suministros ASIGNADOS.'
        ], 200);
    }

    public function filtrar_por_responsable(Request $request)
    {
        $responsable_id = $request->input('responsable_id');
        $estado = $request->input('estado');

        $operador = '>';
        $estado_id = 0;

        if ($estado != 'TODOS') {
            $operador = '=';

            $estado_id = Estado::select('id')->where('estado', $estado)->get()->last();
            $estado_id = $estado_id->id;
        };

        $lista_suministros = AsignacionDetalle::from('suministro_asignaciones_detalles as sum_asi_det')
            ->select(
                'sum_asi_det.id',
                'sum_asi_det.asignacion_id',
                'sum_asi_det.suministro_id',
                'sum_asi_det.condicion_id',

                // 'sum_asi_det.cantidad',

                'sum_asi_det.valor_unitario',
                'sum_asi_det.estado_id',
                'sum_asi_det.cantidad_restante as cantidad',
                'sum_asi_det.valor_unitario',


                'sum_asi.agencia_id',
                'sum_asi.responsable_id',
                'sum_asi.datos_creacion',
                'age.nombre as agencia',
                'sum_alm.codigo',
                'sum_alm.suministro',
                'sum_alm.tipo_id',
                'sum_alm.proveedor_id',
                'sum_alm.medicion_id',
                'sum_tip.tipo',
                'log_res.responsable',
                'log_con.condicion',
                'log_med.medicion',
                'log_med.escala',
                'log_est.estado',
                'usu_1.usuario as usuario_responsable',
                'usu_2.usuario as usuario_asignador',

                'sum_asi_det.cantidad_restante as cantidad_devolucion',
                'sum_asi_det.condicion_id as condicion_devolucion',
                'sum_asi_det.valor_unitario as valor_devolucion',
            )
            ->join('suministro_asignaciones as sum_asi', 'sum_asi_det.asignacion_id', 'sum_asi.id')
            ->join('agencias as age', 'sum_asi.agencia_id', 'age.id_agencia')
            ->join('suministro_almacen as sum_alm', 'sum_asi_det.suministro_id', 'sum_alm.id')

            ->leftjoin('logistica_mediciones as log_med', 'sum_alm.medicion_id',  'log_med.id')

            ->join('suministro_tipos as sum_tip', 'sum_alm.tipo_id', 'sum_tip.id')
            ->join('logistica_responsables as log_res', 'sum_asi.responsable_id', 'log_res.id')
            ->join('logistica_condiciones as log_con', 'sum_asi_det.condicion_id', 'log_con.id')
            ->join('logistica_estados as log_est', 'sum_asi_det.estado_id', 'log_est.id')
            ->join('usuarios as usu_1', 'log_res.usuario_id', 'usu_1.dni')
            ->join('usuarios as usu_2', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sum_asi.datos_creacion, '$.usuario'))"), 'usu_2.dni')
            ->orderBy(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sum_asi.datos_creacion, '$.fecha'))"), 'desc')
            ->where([['sum_asi.responsable_id', $responsable_id], ['sum_asi_det.estado_id', $operador, $estado_id]])
            ->get();


        return response()->json(['lista_suministros' => $lista_suministros]);
    }
}
