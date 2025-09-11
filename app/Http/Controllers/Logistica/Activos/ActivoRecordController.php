<?php

namespace App\Http\Controllers\Logistica\Activos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;

use App\Models\General\Agencia;
use App\Models\Logistica\Estado;
use App\Models\Logistica\Activos\Activo;
use App\Models\Logistica\Activos\Tipo;
use App\Models\Logistica\Activos\Records\ActivoRecord;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;

class ActivoRecordController extends Controller
{
    public function historial_depreciacion()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_DEPRECIACION_LOCAL',  'LOGISTICA_ACTIVOS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_DEPRECIACION_GENERAL',  'LOGISTICA_ACTIVOS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_DEPRECIACION', 'LOGISTICA_ACTIVOS');

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

                return Inertia::render(
                    'Logistica/Activos/Historial/historial_depreciacion',
                    []
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar_depreciacion(Request $request)
    {

        $operador = '=';
        $agencia_id = $request->agencia_id;
        if ($agencia_id == 'TODAS') {
            $operador = '>';
            $agencia_id = '0';
        }

        $fecha_desde = $request->fecha_desde;

        $fecha_hasta = $request->fecha_hasta;
        $fecha_hasta = date("Y-m-d", strtotime($fecha_hasta . "- 1 days"));

        $estados = Estado::select('id')->whereIn('estado', ['VENDIDO', 'MALOGRADO'])->get();

        $tipo = Tipo::select('id')->where('tipo', 'BIEN DEPRECIABLE')->get()->last();





        // $lista_depreciacion = DB::connection('records')->select(DB::raw("
        // select
        // '" . $fecha_desde . "' as fecha_desde,
        // '" . $fecha_hasta . "' as fecha_hasta,
        // t_1.agencia_id,
        // ag.nombre as agencia,
        // sum(t_1.total_inicial) as total_inicial,
        // sum(t_1.total_final) as total_final
        //  from(
        //     select
        //     agencia_id,
        //     sum(valor_actual) as total_inicial,
        //     0 as total_final
        //     from activo_inventario_records air
        //     join solucion_master.aplicacion_cierres as apl_cie on air.cierre_id = apl_cie.id
        //     where STR_TO_DATE(SUBSTRING(apl_cie.datos_creacion,11,19), '%Y-%m-%d') = '" . $fecha_desde . "'
        //     and agencia_id " . $operador . " " . $agencia_id . "
        //     and tipo_id = $tipo->id
        //     and estado_id not in (" . implode(',', $lista_estados) . ")
        //     group by agencia_id

        //     union all

        //     select
        //     agencia_id,
        //     0 as total_inicial,
        //     sum(valor_actual) as total_final
        //     from activo_inventario_records air
        //     join solucion_master.aplicacion_cierres as apl_cie on air.cierre_id = apl_cie.id
        //     where STR_TO_DATE(SUBSTRING(apl_cie.datos_creacion,11,19), '%Y-%m-%d') = '" . $fecha_hasta . "'
        //     and agencia_id " . $operador . " " . $agencia_id . "
        //     and tipo_id = $tipo->id
        //     and estado_id not in  (" . implode(',', $lista_estados) . ")
        //     group by agencia_id

        //     )  t_1
        //     join solucion_master.agencias ag on t_1.agencia_id=ag.id_agencia
        //     group by (agencia_id)"));

        // $lista_depreciacion_detalle = DB::connection('records')->select(DB::raw("
        // select
        // '" . $fecha_desde . "' as fecha_desde,
        // '" . $fecha_hasta . "' as fecha_hasta,
        // t_1.agencia_id,
        // t_1.activo_id,
        // t_1.codigo,
        // t_1.descripcion,
        // log_con.condicion,
        // ag.nombre as agencia,
        // sum(t_1.valor_inicial) as valor_inicial,
        // sum(t_1.valor_final) as valor_final
        //  from(
        //     select
        //     activo_id,
        //     codigo,
        //     descripcion,
        //     condicion_id,
        //     agencia_id,
        //     valor_actual as valor_inicial,
        //     0 as valor_final
        //     from activo_inventario_records air
        //     join solucion_master.aplicacion_cierres as apl_cie on air.cierre_id = apl_cie.id
        //     where STR_TO_DATE(SUBSTRING(apl_cie.datos_creacion,11,19), '%Y-%m-%d') = '" . $fecha_desde . "'
        //     and agencia_id " . $operador . " " . $agencia_id . "
        //     and tipo_id = $tipo->id
        //     and estado_id not in (" . implode(',', $lista_estados) . ")

        //     union all

        //     select
        //     activo_id,
        //                agencia_id,
        //     0 as valor_inicial,
        //     valor_actual as valor_final
        //     from activo_inventario_records air
        //     join solucion_master.aplicacion_cierres as apl_cie on air.cierre_id = apl_cie.id
        //     where STR_TO_DATE(SUBSTRING(apl_cie.datos_creacion,11,19), '%Y-%m-%d') = '" . $fecha_hasta . "'
        //     and agencia_id " . $operador . " " . $agencia_id . "
        //     and tipo_id = $tipo->id
        //     and estado_id not in  (" . implode(',', $lista_estados) . ")

        //     )  t_1
        //     join solucion_master.agencias ag on t_1.agencia_id=ag.id_agencia
        //     join solucion_master.activo_inventario act_inv on t_1.activo_id=act_inv.id
        //     join solucion_master.logistica_condiciones log_con on t_1.condicion_id = log_con.id
        //     group by (activo_id)"));

        // return [
        //     'lista_depreciacion' => $lista_depreciacion,
        //     'lista_depreciacion_detalle' => $lista_depreciacion_detalle
        // ];
    }

    public function cierre_dia($cierre_id)
    {

        $activo = Activo::all()->map(function (Activo $activo) use ($cierre_id) {
            $datos_registro = (new LogisticaController)->datos_registro();
            date_default_timezone_set("America/Lima");

            $tipo = Tipo::select('id')->where('tipo', 'BIEN DEPRECIABLE')->get()->last();
            $estados = Estado::select('id')->whereIn('estado', ['VENDIDO', 'MALOGRADO'])->get();

            $lista_estados = [];
            foreach ($estados as $item) {
                $lista_estados[] = $item->id;
            }

            if (($activo->tipo_id == $tipo->id) && !in_array($activo->estado_id, $lista_estados)) {

                $valor_depre_anual = $activo->valor_actual / $activo->vida_util;
                $valor_depre_diario = $valor_depre_anual / 365;
                $valor_actual = $activo->valor_actual - $valor_depre_diario;

                $valor_actual = $valor_actual < 0 ? 0 : $valor_actual;  // si el activo tiene valor actual 0 ya no se deprecia más

                Activo::where('id', $activo->id)
                    ->update([
                        'valor_actual' => $valor_actual,
                        'datos_actualizacion' => $datos_registro
                    ]);
            } else {
                $valor_depre_anual = 0;
                $valor_depre_diario = 0;
                $valor_actual = $activo->valor_actual;
            }

            return [
                'activo_id' => $activo->id,
                'agencia_id' => $activo->agencia_id,
                // 'nombre_id' => $activo->nombre_id,
                // 'codigo' => $activo->codigo,
                // 'tipo_id' => $activo->tipo_id,
                // 'responsable_id' => $activo->responsable_id,
                // 'ubicacion_id' => $activo->ubicacion_id,
                // 'descripcion' => $activo->descripcion,
                // 'cantidad' => $activo->cantidad,
                // 'marca' => $activo->marca,
                // 'modelo' => $activo->modelo,
                // 'placa' => $activo->placa,
                // 'caracteristicas' => $activo->caracteristicas,
                // 'color' => $activo->color,
                // 'condicion_id' => $activo->condicion_id,
                // 'estado_id' => $activo->estado_id,
                // 'fecha_compra' => $activo->fecha_compra,
                // 'valor_compra' => $activo->valor_compra,
                // 'vida_util' => $activo->vida_util,
                // 'porcentaje_depreciacion' => $activo->porcentaje_depreciacion,
                'valor_actual' => $activo->valor_actual,
                // 'fecha_ultimo_inventario' => $activo->fecha_ultimo_inventario,
                // 'datos_actualizacion_origen' => $activo->datos_actualizacion,
                // 'updated_at_origen' => $activo->updated_at,
                'cierre_id' => $cierre_id,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        });

        $activo = $activo->toArray();

        ActivoRecord::insert($activo);
        return 'SUCCESS';
    }
}
