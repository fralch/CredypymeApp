<?php

namespace Modules\Logistica\Presentation\Controllers\Activos;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Logistica\Presentation\Controllers\LogisticaController;
use Modules\Gth\Presentation\Controllers\Usuarios\UsuarioController;

use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Tipo;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\VentaDetalle;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Venta;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Activo;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Estado;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Responsable;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Ubicacion;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ActivoVentaController extends Controller
{
    public function venta()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'VENTA', 'LOGISTICA_ACTIVOS');

            if ($band == 1) {
                // $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();
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

                return Inertia::render(
                    'Logistica/Activos/venta',
                    [
                        'responsables' => $responsables,
                        'ubicaciones' => $ubicaciones
                    ]
                );
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }

    public function vender(Request $request)
    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $agencia_id = $request->agencia_id;
        $comprador = $request->comprador;
        $canasta_venta = json_decode($request->canasta_venta);

        $venta = Venta::create(
            [
                'agencia_id' => $agencia_id,
                'comprador' => $comprador,
                'datos_creacion' => $datos_registro
            ]

        );

        $venta_id = $venta->id;

        if (!empty($_FILES['documento'])) {

            $path_name = pathinfo($_FILES['documento']['name']);
            $extension = "." . $path_name['extension'];
            $nombre_documento = "act_venta_" . $venta_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/logistica/activos/ventas';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
            move_uploaded_file($archivo, $ruta);

            Venta::where('id', $venta_id)
                ->update([
                    'documento' => $nombre_documento
                ]);
        }

        $estado = Estado::select('id')->where('estado', 'VENDIDO')->get()->last();
        $estado_id = $estado->id;

        foreach ($canasta_venta as $item) {
            VentaDetalle::create(
                [
                    'venta_id' => $venta_id,
                    'activo_id' => $item->id,
                    'valor_actual' => $item->valor_actual,
                    'valor_venta' => $item->valor_venta,
                    'datos_creacion' => $datos_registro
                ]
            );

            Activo::where('id', $item->id)
                ->update([
                    'estado_id' => $estado_id,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return redirect()->route('log.act.venta');
    }

    public function historial_ventas()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_VENTAS_LOCAL',  'LOGISTICA_ACTIVOS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_VENTAS_GENERAL',  'LOGISTICA_ACTIVOS');
            // }

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_VENTAS',  'LOGISTICA_ACTIVOS');

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

                return Inertia::render('Logistica/Activos/Historial/historial_ventas', []);
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

        $lista_ventas = Venta::from('activo_ventas as act_ven')

            ->select(
                'act_ven.id',
                'act_ven.agencia_id',
                'act_ven.comprador',
                'act_ven.documento',
                'act_ven.datos_creacion',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_registro'
            )
            ->join('agencias as ag', 'act_ven.agencia_id', 'ag.id_agencia')
            ->join('usuarios as us_1', DB::raw("SUBSTRING(act_ven.datos_creacion,42,8)"), 'us_1.dni')
            ->where([
                ['act_ven.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_ven.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_ven.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])->get();

        $lista_ventas_detalles = VentaDetalle::from('activo_ventas_detalles as act_ven_det')
            ->select(
                'act_ven_det.id',
                'act_ven_det.venta_id',
                'act_ven_det.activo_id',
                'act_ven_det.valor_actual',
                'act_ven_det.valor_venta',

                'act_ven.comprador',
                'act_ven.datos_creacion',

                'act_inv.codigo',
                'act_inv.descripcion',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_registro'
            )
            ->join('activo_ventas as act_ven', 'act_ven_det.venta_id', 'act_ven.id')
            ->join('activo_inventario as act_inv', 'act_ven_det.activo_id', 'act_inv.id')
            ->join('agencias as ag', 'act_ven.agencia_id', 'ag.id_agencia')
            ->join('usuarios as us_1', DB::raw("SUBSTRING(act_ven.datos_creacion,42,8)"), 'us_1.dni')
            ->where([
                ['act_ven.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_ven.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_ven.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])->get();

        return [
            'lista_ventas' => $lista_ventas,
            'lista_ventas_detalles' => $lista_ventas_detalles
        ];
    }
}
