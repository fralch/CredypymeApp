<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;

use App\Http\Controllers\Logistica\LogisticaController;

use App\Models\General\Agencia;
use App\Models\General\Cierre;
use App\Models\Logistica\Suministros\Records\SuministroRecord;
use App\Models\Logistica\Suministros\CompraDetalle;
use App\Models\Logistica\Suministros\AsignacionDetalle;
use App\Models\Logistica\Suministros\EnvioDetalle;
use App\Models\Logistica\Suministros\DevolucionDetalle;
use App\Models\Logistica\Suministros\VentaDetalle;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Carbon\Carbon;

class SuministroOperacionesController extends Controller
{
    public function historial_operaciones()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_OPERACIONES_LOCAL',  'LOGISTICA_SUMINISTROS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_OPERACIONES_GENERAL',  'LOGISTICA_SUMINISTROS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_OPERACIONES',  'LOGISTICA_SUMINISTROS');

            if ($band == 1) {


                return Inertia::render('Logistica/Suministros/Historial/historial_operaciones', []);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar_recursos(Request $request)
    {
        $fecha_actual = (new LogisticaController)->fecha_corta_aplicacion();

        $fecha = Carbon::parse($fecha_actual);
        $primer_dia  = $fecha->firstOfMonth()->toDateString();

        return response()->json([
            'fecha_desde' => $primer_dia,
            'fecha_hasta' => $fecha_actual
        ]);
    }

    public function buscar(Request $request)
    {
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = $request->input('fecha_hasta');

        $agencias = Agencia::select('id_agencia', 'nombre as nombre_agencia')
            ->get();

        $lista_operaciones = [];

        foreach ($agencias as $agencia) {
            $lista_operaciones[] = (object)[
                'agencia_id' => $agencia->id_agencia,
                'nombre_agencia' => $agencia->nombre_agencia,
                'total_inicial' => 0,
                'total_compras' => 0,
                'total_asignaciones' => 0,
                'total_devoluciones' => 0,
                'total_envios' => 0,
                'total_recepciones' => 0,
                'total_ventas' => 0,
                'total_final' => 0
            ];
        }

        $fecha_desde = date("Y-m-d", strtotime($fecha_desde . "- 1 day"));

        $cierre = Cierre::where(DB::raw("STR_TO_DATE(SUBSTRING(datos_creacion,11,10), '%Y-%m-%d')"),  $fecha_desde)->get()->last();
        $cierre_id = $cierre->id;

        $suministros_inicial = SuministroRecord::from('suministro_almacen_records as sum_alm_rec')
            ->select(
                'agencia_id',
                DB::raw("SUM(valor_unitario*cantidad) as total_inicial")
            )
            ->where('sum_alm_rec.cierre_id', $cierre_id)
            ->groupBy('agencia_id')
            ->get();

        $compras = CompraDetalle::from('suministro_compras_detalles as sum_com_det')
            ->select(
                'sum_alm.agencia_id',
                DB::raw("SUM(valor_total) as total_compras")
            )
            ->join('suministro_almacen as sum_alm', 'sum_com_det.suministro_id', 'sum_alm.id')
            ->where([
                ['sum_alm.clasificacion', 'SUMINISTRO'],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_com_det.datos_creacion,11,10), '%Y-%m-%d')"), '>', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_com_det.datos_creacion,11,10), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->groupBy('sum_alm.agencia_id')
            ->get();

        $asignaciones = AsignacionDetalle::from('suministro_asignaciones_detalles as sum_asi_det')
            ->select(
                'sum_asi.agencia_id',
                DB::raw("SUM(sum_asi_det.cantidad*sum_asi_det.valor_unitario) as total_asignaciones")
            )
            ->join('suministro_asignaciones as sum_asi', 'sum_asi_det.asignacion_id', 'sum_asi.id')
            ->join('suministro_almacen as sum_alm', 'sum_asi_det.suministro_id', 'sum_alm.id')
            ->where([
                ['sum_alm.clasificacion', 'SUMINISTRO'],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_asi_det.datos_creacion,11,10), '%Y-%m-%d')"), '>', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_asi_det.datos_creacion,11,10), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->groupBy('sum_asi.agencia_id')
            ->get();

        $envios_realizados = EnvioDetalle::from('suministro_envios_detalles as sum_env_det')
            ->select(
                'sum_env.agencia_envio',
                DB::raw("SUM(cantidad*valor_unitario) as total_envios")
            )
            ->join('suministro_envios as sum_env', 'sum_env_det.envio_id', 'sum_env.id')
            ->where([
                ['sum_env_det.situacion', 'CONFIRMADO'],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env_det.datos_creacion,11,10), '%Y-%m-%d')"), '>', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env_det.datos_creacion,11,10), '%Y-%m-%d')"), '<=', $fecha_hasta],

            ])
            ->groupBy('sum_env.agencia_envio')
            ->get();

        $recepciones = EnvioDetalle::from('suministro_envios_detalles as sum_env_det')
            ->select(
                'sum_env.agencia_recepcion',
                DB::raw("SUM(cantidad*valor_unitario) as total_recepciones")
            )
            ->join('suministro_envios as sum_env', 'sum_env_det.envio_id', 'sum_env.id')
            ->where([
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env_det.datos_actualizacion,11,10), '%Y-%m-%d')"), '>', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_env_det.datos_actualizacion,11,10), '%Y-%m-%d')"), '<=', $fecha_hasta],
                ['sum_env_det.situacion', 'CONFIRMADO']
            ])
            ->groupBy('sum_env.agencia_recepcion')
            ->get();

        $devoluciones = DevolucionDetalle::from('suministro_devoluciones_detalles as sum_dev_det')
            ->select(
                'sum_dev.agencia_id',
                DB::raw("SUM(sum_dev_det.cantidad*sum_dev_det.valor_unitario) as total_devoluciones")
            )
            ->join('suministro_devoluciones as sum_dev', 'sum_dev_det.devolucion_id', 'sum_dev.id')
            ->where([
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev_det.datos_creacion,11,10), '%Y-%m-%d')"), '>', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_dev_det.datos_creacion,11,10), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->groupBy('sum_dev.agencia_id')
            ->get();

        $ventas = VentaDetalle::from('suministro_ventas_detalles as sum_ven_det')
            ->select(
                'sum_ven.agencia_id',
                DB::raw("SUM(sum_ven_det.cantidad*sum_ven_det.valor_unitario) as total_ventas")
            )
            ->join('suministro_ventas as sum_ven', 'sum_ven_det.venta_id', 'sum_ven.id')
            ->where([
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_ven_det.datos_creacion,11,10), '%Y-%m-%d')"), '>', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(sum_ven_det.datos_creacion,11,10), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->groupBy('sum_ven.agencia_id')
            ->get();

        $sumatoria = [
            'inicial' => 0,
            'compras' => 0,
            'asignaciones' => 0,
            'devoluciones' => 0,
            'envios' => 0,
            'recepciones' => 0,
            'ventas' => 0,
            'final' => 0,
        ];

        foreach ($lista_operaciones as $total_agencia) {
            foreach ($suministros_inicial as $inicial) {
                if ($total_agencia->agencia_id == $inicial->agencia_id) {
                    $total_agencia->total_inicial += $inicial->total_inicial;
                }
            }

            foreach ($compras as $compra) {
                if ($total_agencia->agencia_id == $compra->agencia_id) {
                    $total_agencia->total_compras += $compra->total_compras;
                }
            }

            foreach ($asignaciones as $asignacion) {
                if ($total_agencia->agencia_id == $asignacion->agencia_id) {
                    $total_agencia->total_asignaciones += $asignacion->total_asignaciones;
                }
            }

            foreach ($envios_realizados as $envio) {
                if ($total_agencia->agencia_id == $envio->agencia_envio) {
                    $total_agencia->total_envios += $envio->total_envios;
                }
            }

            foreach ($recepciones as $recepcion) {
                if ($total_agencia->agencia_id == $recepcion->agencia_recepcion) {
                    $total_agencia->total_recepciones += $recepcion->total_recepciones;
                }
            }

            foreach ($devoluciones as $devolucion) {
                if ($total_agencia->agencia_id == $devolucion->agencia_id) {
                    $total_agencia->total_devoluciones += $devolucion->total_devoluciones;
                }
            }
            foreach ($ventas as $venta) {
                if ($total_agencia->agencia_id == $venta->agencia_id) {
                    $total_agencia->total_ventas += $venta->total_ventas;
                }
            }

            $total_agencia->total_final =
                $total_agencia->total_inicial +
                $total_agencia->total_compras +
                $total_agencia->total_devoluciones +
                $total_agencia->total_recepciones -
                $total_agencia->total_asignaciones -
                $total_agencia->total_envios -
                $total_agencia->total_ventas;

            $sumatoria['inicial'] +=  $total_agencia->total_inicial;
            $sumatoria['compras'] += $total_agencia->total_compras;
            $sumatoria['devoluciones'] += $total_agencia->total_devoluciones;
            $sumatoria['recepciones'] += $total_agencia->total_recepciones;
            $sumatoria['asignaciones'] += $total_agencia->total_asignaciones;
            $sumatoria['envios'] += $total_agencia->total_envios;
            $sumatoria['ventas'] += $total_agencia->total_ventas;
            $sumatoria['final'] += $total_agencia->total_final;
        }


        return response()->json([
            'lista_operaciones' => $lista_operaciones,
            'sumatoria' => $sumatoria
        ]);
    }
}
