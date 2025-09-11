<?php

namespace App\Http\Controllers\Logistica\Activos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Models\General\Agencia;

use App\Models\Logistica\Responsable;
use App\Models\Logistica\Activos\Ubicacion;
use App\Models\Logistica\Activos\Tipo;
use App\Models\Logistica\Activos\Activo;
use App\Models\Logistica\Activos\Asignacion;
use App\Models\Logistica\Activos\AsignacionDetalle;
use Illuminate\Http\Request;

use Inertia\Inertia;

use Illuminate\Support\Facades\DB;

class ActivoAsignacionController extends Controller
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
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASIGNACION_LOCAL',  'LOGISTICA_ACTIVOS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASIGNACION_GENERAL',  'LOGISTICA_ACTIVOS');
            // }

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASIGNACION',  'LOGISTICA_ACTIVOS');

            if ($band == 1) {

                // if ($tipo_modulo == 'LOCAL') {
                //     $agencias = Agencia::select('id_agencia', 'nombre')
                //         ->where('id_agencia', session('id_agencia'))->get();
                // } else if ($tipo_modulo == 'GENERAL') {
                //     $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();
                // }

                $responsables = Responsable::from('logistica_responsables as log_res')
                    ->select(
                        'log_res.id',
                        'log_res.responsable',
                        'log_res.abreviacion',
                        'log_res.agencia_id',
                        'log_res.usuario_id',
                        'log_res.encargado_agencia',

                        'us.usuario'
                    )
                    ->join('usuarios as us', 'log_res.usuario_id', 'us.dni')
                    ->where('log_res.habilitado', 1)
                    ->get();

                $ubicaciones = Ubicacion::from('activo_ubicaciones as act_ubi')
                    ->select(
                        'act_ubi.id',
                        'act_ubi.ubicacion',
                        'act_ubi.abreviacion',
                        'act_ubi.agencia_id',
                        'ag.nombre as agencia'
                    )
                    ->join('agencias as ag', 'act_ubi.agencia_id', 'ag.id_agencia')
                    ->where('act_ubi.habilitado', 1)
                    ->get();

                $tipos = Tipo::select('id', 'tipo')
                    ->where('habilitado', 1)
                    ->orderby('tipo', 'asc')
                    ->get();


                return Inertia::render(
                    'Logistica/Activos/asignacion',
                    [
                        'responsables' => $responsables,
                        'ubicaciones' => $ubicaciones,
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

    public function mis_activos()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_ACTIVOS',  'LOGISTICA_ACTIVOS');

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

                return Inertia::render(
                    'Logistica/Activos/mis_activos',
                    [
                        'responsables' => $responsables,
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
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ASIGNACIONES_LOCAL',  'LOGISTICA_ACTIVOS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ASIGNACIONES_GENERAL',  'LOGISTICA_ACTIVOS');
            // }

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ASIGNACIONES','LOGISTICA_ACTIVOS');


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

                return Inertia::render('Logistica/Activos/Historial/historial_asignaciones', [
                ]);
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

        $lista_asignaciones = Asignacion::from('activo_asignaciones as act_asi')
            ->select(
                'act_asi.id',
                'act_asi.agencia_id',
                'act_asi.responsable_id',
                'act_asi.documento',
                'act_asi.datos_creacion',

                'ag.nombre as agencia',
                'log_res.abreviacion',
                'us_1.usuario as usuario_asignado',
                'us_2.usuario as usuario_asignador',
            )
            ->join('agencias as ag', 'act_asi.agencia_id', 'ag.id_agencia')
            ->join('logistica_responsables as log_res', 'act_asi.responsable_id', 'log_res.id')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(act_asi.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['act_asi.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_asi.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_asi.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();



        $lista_asignaciones_detalles = AsignacionDetalle::from('activo_asignaciones_detalles as act_asi_det')
            ->select(
                'act_asi_det.id',
                'act_asi_det.asignacion_id',
                'act_asi_det.activo_id',
                'act_asi_det.condicion_id',

                'act_asi.datos_creacion',

                'act_inv.codigo',
                'act_inv.descripcion',
                'act_inv.valor_actual',

                'log_con.condicion',

                'ag.nombre as agencia',
                'log_res.abreviacion',
                'us_1.usuario as usuario_asignado',
                'us_2.usuario as usuario_asignador'
            )
            ->join('activo_asignaciones as act_asi', 'act_asi_det.asignacion_id', 'act_asi.id')
            ->join('activo_inventario as act_inv', 'act_asi_det.activo_id', 'act_inv.id')
            ->join('logistica_condiciones as log_con', 'act_asi_det.condicion_id',  'log_con.id')
            ->join('agencias as ag', 'act_asi.agencia_id', 'ag.id_agencia')
            ->join('logistica_responsables as log_res', 'act_asi.responsable_id', 'log_res.id')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(act_asi.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['act_asi.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_asi.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_asi.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
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
        $datos_registro = (new LogisticaController)->datos_registro();

        $tipo_modulo = $request->tipo_modulo;
        $agencia_id = $request->agencia_id;
        $responsable_id = $request->responsable_id;
        $ubicacion_id = $request->ubicacion_id;
        $canasta_asignacion = json_decode($request->canasta_asignacion);

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
            $nombre_documento = "act_asignacion_" . $asignacion_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/logistica/activos/asignaciones';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
            move_uploaded_file($archivo, $ruta);

            Asignacion::where('id', $asignacion_id)
                ->update([
                    'documento' => $nombre_documento
                ]);
        }

        foreach ($canasta_asignacion as $item) {

            AsignacionDetalle::create(
                [
                    'asignacion_id' => $asignacion_id,
                    'activo_id' => $item->id,
                    'condicion_id' => $item->condicion_id,
                    'datos_creacion' => $datos_registro
                ]
            );

            Activo::where('id', $item->id)
                ->update([
                    'codigo' => $item->nuevo_codigo,
                    'responsable_id' => $responsable_id,
                    'ubicacion_id' => $ubicacion_id,
                    'condicion_id' => $item->condicion_id,
                    'datos_actualizacion' => $datos_registro
                ]);
        }
        return redirect()->route('log.act.asignacion', $tipo_modulo);
    }

    public function filtrar_por_responsable($responsable_id)
    {

        return Activo::from('activo_inventario as act_inv')
            ->select(
                'act_inv.id',
                'act_inv.codigo',
                'act_inv.descripcion',
                'act_inv.cantidad',

                'log_res.abreviacion as abreviacion_responsable',
                'usu_1.usuario as usuario_responsable',
                'act_ubi.abreviacion as abreviacion_ubicacion',
                'ag.nombre as agencia_ubicacion',
                'log_con.condicion',

                DB::raw("if(act_asi_det.datos_creacion is null,act_inv.datos_creacion,act_asi_det.datos_creacion) as datos_creacion"),
                DB::raw("if(usu_2.usuario is null,usu_3.usuario,usu_2.usuario) as usuario_asignador")

            )->leftjoin('activo_asignaciones_detalles as act_asi_det', 'act_inv.id', 'act_asi_det.activo_id')
            ->join('logistica_responsables as log_res', 'act_inv.responsable_id', 'log_res.id')
            ->join('usuarios as usu_1', 'log_res.usuario_id', 'usu_1.dni')
            ->join('activo_ubicaciones as act_ubi', 'act_inv.ubicacion_id', 'act_ubi.id')
            ->join('agencias as ag', 'act_ubi.agencia_id', 'ag.id_agencia')
            ->join('logistica_condiciones as log_con', 'act_inv.condicion_id', 'log_con.id')
            ->leftjoin('usuarios as usu_2', DB::raw("SUBSTRING(act_asi_det.datos_creacion,42,8)"), 'usu_2.dni')
            ->leftjoin('usuarios as usu_3', DB::raw("SUBSTRING(act_inv.datos_creacion,42,8)"), 'usu_3.dni')
            ->where('act_inv.responsable_id', $responsable_id)
            ->get();
    }
}
