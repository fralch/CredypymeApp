<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Http\Controllers\Gth\Usuarios\UsuarioController;

use App\Models\Logistica\Suministros\Tipo;
use App\Models\Logistica\Suministros\VentaDetalle;
use App\Models\Logistica\Suministros\Venta;
use App\Models\General\Agencia;
use App\Models\Logistica\Suministros\Suministro;
use App\Models\Logistica\Estado;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SuministroVentaController extends Controller
{
    public function venta()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'VENTA', 'LOGISTICA_SUMINISTROS');

            if ($band == 1) {

                $tipos = Tipo::select('id', 'tipo')
                    ->where('habilitado', 1)
                    ->orderby('tipo', 'asc')
                    ->get();


                return Inertia::render(
                    'Logistica/Suministros/venta',
                    [
                        'tipos' => $tipos,
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
        $agencia_id = $request->agencia_id;
        $comprador = $request->comprador;
        $canasta_venta = json_decode($request->canasta_venta);

        $datos_registro = (new LogisticaController)->datos_registro();

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
            $nombre_documento = "sum_venta_" . $venta_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/logistica/suministros/ventas';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
            move_uploaded_file($archivo, $ruta);

            Venta::where('id', $venta_id)
                ->update([
                    'documento' => $nombre_documento
                ]);
        }

        foreach ($canasta_venta as $item) {
            VentaDetalle::create(
                [
                    'venta_id' => $venta_id,
                    'suministro_id' => $item->id,
                    'condicion_id' => $item->condicion_id,
                    'cantidad' => $item->cantidad_vender,
                    'valor_unitario' => $item->valor_venta,
                    'datos_creacion' => $datos_registro
                ]

            );

            $nueva_cantidad = floatVal($item->cantidad) - floatVal($item->cantidad_vender);

            if ($nueva_cantidad == 0) {
                $estado = Estado::select('id')->where('estado', 'AGOTADO')->get()->last();
            } else {
                $estado = Estado::select('id')->where('estado', 'DISPONIBLE')->get()->last();
            }

            Suministro::where('id', $item->id)
                ->update([
                    'cantidad' => $nueva_cantidad,
                    'estado_id' => $estado->id,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Suministro(s) VENDIDOS'
        ], 200);
    }

    public function historial_ventas()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_VENTAS_LOCAL',  'LOGISTICA_SUMINISTROS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_VENTAS_GENERAL',  'LOGISTICA_SUMINISTROS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_VENTAS',  'LOGISTICA_SUMINISTROS');


            if ($band == 1) {

                return Inertia::render('Logistica/Suministros/Historial/historial_ventas', []);
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

        $lista_ventas = Venta::from('suministro_ventas as sum_ven')

            ->select(
                'sum_ven.id',
                'sum_ven.agencia_id',
                'sum_ven.comprador',
                'sum_ven.documento',
                'sum_ven.datos_creacion',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_registro'
            )
            ->join('agencias as ag', 'sum_ven.agencia_id', 'ag.id_agencia')
            ->join('usuarios as us_1', DB::raw("SUBSTRING(sum_ven.datos_creacion,42,8)"), 'us_1.dni')
            ->where([
                ['sum_ven.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_ven.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_ven.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])->get();

        $lista_ventas_detalles = VentaDetalle::from('suministro_ventas_detalles as sum_ven_det')
            ->select(
                'sum_ven_det.id',
                'sum_ven_det.venta_id',
                'sum_ven_det.suministro_id',
                'sum_ven_det.condicion_id',
                'sum_ven_det.cantidad',
                'log_med.medicion',
                'sum_ven_det.valor_unitario as valor_venta',
                'sum_ven_det.datos_creacion',

                'sum_ven.comprador',

                'sum_alm.codigo',
                'sum_alm.suministro',
                'sum_alm.valor_unitario',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_registro'
            )
            ->join('suministro_ventas as sum_ven', 'sum_ven_det.venta_id', 'sum_ven.id')
            ->join('suministro_almacen as sum_alm', 'sum_ven_det.suministro_id', 'sum_alm.id')
            ->join('logistica_mediciones as log_med', 'sum_alm.medicion_id', 'log_med.id')
            ->join('agencias as ag', 'sum_ven.agencia_id', 'ag.id_agencia')
            ->join('usuarios as us_1', DB::raw("SUBSTRING(sum_ven.datos_creacion,42,8)"), 'us_1.dni')
            ->where([
                ['sum_ven.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_ven.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_ven.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])->get();

        return [
            'lista_ventas' => $lista_ventas,
            'lista_ventas_detalles' => $lista_ventas_detalles
        ];
    }
}
