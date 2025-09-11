<?php

namespace App\Http\Controllers\Logistica\Activos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Models\Logistica\Estado;
use App\Models\Logistica\Responsable;

use App\Models\General\Agencia;
use App\Models\Logistica\Activos\Activo;
use App\Models\Logistica\Activos\Envio;
use App\Models\Logistica\Activos\EnvioDetalle;
use App\Models\Logistica\Activos\Ubicacion;

use Inertia\Inertia;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Env;

class ActivoEnvioController extends Controller
{
    public function historial_envios()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ENVIOS_LOCAL',  'LOGISTICA_ACTIVOS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ENVIOS_GENERAL',  'LOGISTICA_ACTIVOS');
            // }

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_ENVIOS', 'LOGISTICA_ACTIVOS');
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

                return Inertia::render('Logistica/Activos/Historial/historial_envios', [
                    //'tipo_modulo' => $tipo_modulo,
                   // 'agencias' => $agencias,

                ]);
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
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ENVIOS_RECEPCIONES',  'LOGISTICA_ACTIVOS');
            if ($band == 1) {
                return Inertia::render(
                    'Logistica/Activos/envios_recepciones',

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

        $datos_registro = (new LogisticaController)->datos_registro();

        $tipo_modulo = $request->tipo_modulo;
        $agencia_envio = $request->agencia_envio;
        $agencia_recepcion = $request->agencia_recepcion;
        $responsable_recepcion = $request->responsable_recepcion;
        $ubicacion_recepcion = $request->ubicacion_recepcion;

        $canasta_envio = json_decode($request->canasta_envio);
        $situacion = 'PENDIENTE';

        $envio = Envio::create(
            [
                'agencia_envio' => $agencia_envio,
                'agencia_recepcion' => $agencia_recepcion,
                'responsable_recepcion' => $responsable_recepcion,
                'ubicacion_recepcion' => $ubicacion_recepcion,
                'situacion' => $situacion,
                'datos_creacion' => $datos_registro
            ]
        );

        $envio_id = $envio->id;

        $path_name = pathinfo($_FILES['documento_envio']['name']);
        $extension = "." . $path_name['extension'];
        $nombre_documento = "act_envio_" . $envio_id . $extension;
        $archivo = $_FILES['documento_envio']['tmp_name'];
        $ruta = '/imagenes_server/logistica/activos/envios_recepciones';
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
        move_uploaded_file($archivo, $ruta);

        Envio::where('id', $envio_id)
            ->update([
                'documento_envio' => $nombre_documento
            ]);

        $estado = Estado::select('id')->where('estado', 'NO_DISPONIBLE')->get()->last();
        $estado_id = $estado->id;

        foreach ($canasta_envio as $item) {

            EnvioDetalle::create(
                [
                    'envio_id' => $envio_id,
                    'activo_id' => $item->id,
                    'cantidad' => $item->cantidad,
                    'valor_actual' => $item->valor_actual,
                    'situacion' => $situacion,
                    'datos_creacion' => $datos_registro
                ]
            );

            Activo::where('id', $item->id)->update([
                'estado_id' => $estado_id,
                'datos_actualizacion' => $datos_registro
            ]);
        }

        return redirect()->route('log.act.inventario', $tipo_modulo);
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

        $envios_recepciones = Envio::from('activo_envios as act_env')
            ->select(
                DB::raw("IF(SUBSTRING(act_env.datos_creacion,42,8)$operador$usuario_dni,'ENVÍO','RECEPCIÓN') as tipo"),
                'act_env.id',
                'act_env.agencia_envio',
                'act_env.agencia_recepcion',
                'act_env.responsable_recepcion',
                'act_env.ubicacion_recepcion',
                'act_env.documento_envio',
                'act_env.documento_recepcion',
                'act_env.situacion',
                'act_env.datos_creacion',

                'ag_1.nombre as nombre_agencia_envio',
                'ag_2.nombre as nombre_agencia_recepcion',

                'us_1.usuario as usuario_envio',
                'log_res.abreviacion as abreviacion_responsable',
                'act_ubi.abreviacion as abreviacion_ubicacion',
                'us_2.usuario as usuario_recepcion'
            )
            ->join('agencias as ag_1', 'act_env.agencia_envio', 'ag_1.id_agencia')
            ->join('agencias as ag_2', 'act_env.agencia_recepcion', 'ag_2.id_agencia')
            ->join('logistica_responsables as log_res', 'act_env.responsable_recepcion', 'log_res.id')
            ->join('activo_ubicaciones as act_ubi', 'act_env.ubicacion_recepcion', 'act_ubi.id')
            ->join('usuarios as us_1', DB::raw("SUBSTRING(act_env.datos_creacion,42,8)"), 'us_1.dni')
            ->join('usuarios as us_2', 'log_res.usuario_id', 'us_2.dni')
            ->where([
                [DB::raw("SUBSTRING(act_env.datos_creacion,42,8)"), $operador, $usuario_dni],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->orWhere([
                ['log_res.usuario_id', $operador, $usuario_dni],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();


        $envios_recepciones_detalle = EnvioDetalle::from('activo_envios_detalles as act_env_det')
            ->select(
                'act_env_det.id',
                'act_env_det.envio_id',
                'act_env_det.activo_id',
                'act_env_det.cantidad',
                'act_env_det.valor_actual',
                'act_env_det.situacion',

                'act_env.agencia_envio',
                'act_env.agencia_recepcion',

                'act_inv.codigo',
                'act_inv.descripcion',
                'act_inv.valor_actual',
                'act_inv.condicion_id',

                'act_nom.nombre as nombre',
                'act_tip.abreviacion as abreviacion_tipo',

                'log_con.condicion',
            )
            ->join('activo_envios as act_env', 'act_env_det.envio_id', 'act_env.id')
            ->join('activo_inventario as act_inv', 'act_env_det.activo_id', 'act_inv.id')
            ->join('logistica_condiciones as log_con', 'act_inv.condicion_id', 'log_con.id')
            ->join('logistica_responsables as log_res', 'act_env.responsable_recepcion', 'log_res.id')
            ->join('activo_tipos as act_tip', 'act_inv.tipo_id', 'act_tip.id')
            ->join('activo_nombres as act_nom', 'act_inv.nombre_id', 'act_nom.id')
            ->where([
                [DB::raw("SUBSTRING(act_env.datos_creacion,42,8)"), $operador, $usuario_dni],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->orWhere([
                ['log_res.usuario_id', $operador, $usuario_dni],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();


        return [
            'envios_recepciones' => $envios_recepciones,
            'envios_recepciones_detalle' => $envios_recepciones_detalle
        ];
    }

    public function confirmar(Request $request)
    {

        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $envio_id = $request->envio_id;

        $canasta_recepcion = json_decode($request->canasta_recepcion);

        $cantidad_enviados = EnvioDetalle::select('id')->where('envio_id', $envio_id)
            ->get();
        $cantidad_confirmados = EnvioDetalle::select('id')
            ->where([['envio_id', $envio_id], ['situacion', 'CONFIRMADO']])->get();
        $cantidad_rechazados = EnvioDetalle::select('id')
            ->where([['envio_id', $envio_id], ['situacion', 'RECHAZADO']])->get();

        $estado = Estado::select('id')->where('estado', 'DISPONIBLE')->get()->last();

        if ($modo == 'CONFIRMAR') {
            $agencia_recepcion = $request->agencia_recepcion;
            $responsable_recepcion = $request->responsable_recepcion;
            $ubicacion_recepcion = $request->ubicacion_recepcion;

            foreach ($canasta_recepcion as $item) {

                EnvioDetalle::where('id', $item->id)->update([
                    'situacion' => 'CONFIRMADO',
                    'datos_actualizacion' => $datos_registro
                ]);

                $nuevo_responsable = Responsable::select('abreviacion')->where('id', $responsable_recepcion)->get()->last();
                $nueva_ubicacion = Ubicacion::select('abreviacion')->where('id', $ubicacion_recepcion)->get()->last();

                $nuevo_codigo = $item->abreviacion_tipo . '-' .
                    $nuevo_responsable->abreviacion . '-' .
                    $nueva_ubicacion->abreviacion . '-' .
                    $item->activo_id . '-' . $item->nombre;

                Activo::where('id', $item->activo_id)->update([
                    'codigo' => $nuevo_codigo,
                    'agencia_id' => $agencia_recepcion,
                    'responsable_id' => $responsable_recepcion,
                    'ubicacion_id' => $ubicacion_recepcion,
                    'estado_id' => $estado->id,
                    'datos_actualizacion' => $datos_registro
                ]);
            }

            if ((count($canasta_recepcion)  +
                count($cantidad_confirmados) +
                count($cantidad_rechazados)) == count($cantidad_enviados)) {
                $situacion_nueva = 'CONFIRMADO';
            } else {
                $situacion_nueva = 'PENDIENTE';
            }

            $path_name = pathinfo($_FILES['documento_recepcion']['name']);
            $extension = "." . $path_name['extension'];
            $nombre_documento = "act_recepcion_" . $envio_id . $extension;
            $archivo = $_FILES['documento_recepcion']['tmp_name'];
            $ruta = '/imagenes_server/logistica/activos/envios_recepciones';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
            move_uploaded_file($archivo, $ruta);

            Envio::where('id', $envio_id)
                ->update([
                    'situacion' => $situacion_nueva,
                    'documento_recepcion' => $nombre_documento,
                    'datos_actualizacion' => $datos_registro
                ]);
        } else if ($modo == 'RECHAZAR') {

            foreach ($canasta_recepcion as $item) {

                EnvioDetalle::where('id', $item->id)->update([
                    'situacion' => 'RECHAZADO',
                    'datos_actualizacion' => $datos_registro
                ]);

                Activo::where('id', $item->activo_id)
                    ->update([
                        'estado_id' => $estado->id,
                        'datos_actualizacion' => $datos_registro
                    ]);
            }

            if ((count($canasta_recepcion)  +
                count($cantidad_confirmados) +
                count($cantidad_rechazados)) == count($cantidad_enviados)) {
                if ((count($canasta_recepcion) + count($cantidad_rechazados)) == count($cantidad_enviados)) {
                    $situacion_nueva = 'RECHAZADO';
                } else {
                    $situacion_nueva = 'CONFIRMADO';
                }
            } else {
                $situacion_nueva = 'PENDIENTE';
            }

            Envio::where('id', $envio_id)
                ->update([
                    'situacion' => $situacion_nueva,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return redirect()->route('log.act.envios_recepciones');
    }

    public function buscar(Request $request)
    {

        $agencia_id  = $request->agencia_id;
        $fecha_desde  = $request->fecha_desde;
        $fecha_hasta  = $request->fecha_hasta;

        $lista_envios = Envio::from('activo_envios as act_env')
            ->select(
                'act_env.id',
                'act_env.agencia_envio',
                'act_env.agencia_recepcion',
                'act_env.responsable_recepcion',
                'act_env.ubicacion_recepcion',
                'act_env.documento_envio',
                'act_env.documento_recepcion',
                'act_env.situacion',
                'act_env.datos_creacion',
                'act_env.datos_actualizacion',

                'ag_1.nombre as nombre_agencia_envio',
                'ag_2.nombre as nombre_agencia_recepcion',

                'log_res.abreviacion as abreviacion_responsable',
                'act_ubi.abreviacion as abreviacion_ubicacion',

                'us_1.usuario as usuario_responsable',
                'us_2.usuario as  usuario_envio'
            )
            ->join('agencias as ag_1', 'act_env.agencia_envio', 'ag_1.id_agencia')
            ->join('agencias as ag_2', 'act_env.agencia_recepcion', 'ag_2.id_agencia')
            ->join('logistica_responsables as log_res', 'act_env.responsable_recepcion', 'log_res.id')
            ->join('activo_ubicaciones as act_ubi', 'act_env.ubicacion_recepcion', 'act_ubi.id')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(act_env.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['act_env.agencia_envio', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();

        $lista_envios_detalles = EnvioDetalle::from('activo_envios_detalles as act_env_det')
            ->select(

                'act_env_det.id',
                'act_env_det.envio_id',
                'act_env_det.activo_id',
                'act_env_det.cantidad',
                'act_env_det.valor_actual',
                'act_env_det.situacion',
                'act_env_det.datos_creacion',
                'act_env_det.datos_actualizacion',

                'act_env.agencia_envio',
                'act_env.agencia_recepcion',
                'act_env.responsable_recepcion',

                'act_inv.codigo',
                'act_inv.descripcion',

                'ag_1.nombre as nombre_agencia_envio',
                'ag_2.nombre as nombre_agencia_recepcion',

                'log_res.abreviacion as abreviacion_responsable',
                'act_ubi.abreviacion as abreviacion_ubicacion',

                'us_1.usuario as usuario_responsable',
                'us_2.usuario as  usuario_envio'
            )
            ->join('activo_envios as act_env', 'act_env_det.envio_id', 'act_env.id')
            ->join('activo_inventario as act_inv', 'act_env_det.activo_id', 'act_inv.id')
            ->join('agencias as ag_1', 'act_env.agencia_envio', 'ag_1.id_agencia')
            ->join('agencias as ag_2', 'act_env.agencia_recepcion', 'ag_2.id_agencia')
            ->join('logistica_responsables as log_res', 'act_env.responsable_recepcion', 'log_res.id')
            ->join('activo_ubicaciones as act_ubi', 'act_env.ubicacion_recepcion', 'act_ubi.id')
            ->join('usuarios as us_1', 'log_res.usuario_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(act_env.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['act_env.agencia_envio', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_env.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();

        return [
            'lista_envios' => $lista_envios,
            'lista_envios_detalles' => $lista_envios_detalles
        ];
    }
}
