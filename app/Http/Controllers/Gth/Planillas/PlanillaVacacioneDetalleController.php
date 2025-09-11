<?php

namespace App\Http\Controllers\Gth\Planillas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\GthController;
use App\Models\Gth\Planillas\VacacionDetalle;
use App\Models\Gth\Planillas\VacacionPeriodo;
use App\Models\Gth\Planillas\Vacacion;

use App\Models\General\Agencia;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PlanillaVacacioneDetalleController extends Controller
{
    public function historial_vacaciones()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_VACACIONES', 'GTH_PLANILLAS');

            if ($band == 1) {
                $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();

                return Inertia::render(
                    'Gth/Planillas/historial_vacaciones',
                    [
                        'agencias' => $agencias
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
    public function historial_vacaciones_listar(Request $request)
    {

        $v_desde = $request->dtpDesde;
        $v_hasta = $request->dtpHasta;
        // return $v_desde;


        if (!$v_desde == null && !$v_hasta == null) {
            return  VacacionDetalle::from('planillas_vacaciones_detalles as pl_va_de')
                ->select(
                    'pl_va_de.id',
                    DB::raw("CONCAT(us_1.apellido_paterno,' ',us_1.apellido_materno,' ',us_1.nombres) as colaborador"),
                    'pl_va_de.desde',
                    'pl_va_de.hasta',
                    'pl_va_de.periodo_id',
                    'pl_va_de.dias_tomados',
                    'pl_va_de.datos_creacion',
                    DB::raw("STR_TO_DATE(pl_va_de.created_at, '%Y-%m-%d') as fecha_creacion"),
                    'us_2.usuario as usuario_creacion',
                    'ag.nombre as nombre_agencia'
                )
                ->join('usuarios as us_1', 'us_1.dni', 'pl_va_de.dni')
                ->join('usuarios as us_2', 'us_2.dni', DB::raw("SUBSTRING(pl_va_de.datos_creacion,42,8)"))
                ->join('agencias as ag', 'us_1.agencia_id', 'ag.id_agencia')
                ->whereBetween(DB::raw("STR_TO_DATE(pl_va_de.created_at, '%Y-%m-%d')"), [$v_desde, $v_hasta])
                ->orderBy('datos_creacion', 'desc')
                ->get();
        }
    }

    public function asignar_vacaciones(Request $request)
    {

        $datos_registro = (new GthController)->datos_registro();
        $dni = $request->dni;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $dias_a_tomar = $request->dias_a_tomar;
        $total_acumulado = $request->total_acumulado;
        $periodos_filtrado = $request->periodos_filtrado;


        foreach ($periodos_filtrado as $periodo) {

            if ($periodo['dias_a_tomar'] > 0) {
                // Obtener los periodos involucrados en la asignación
                $periodos_id[] = (object)[
                    'id' => $periodo['id'],
                    'dias_tomados' => $periodo['dias_a_tomar']
                ];

                // Quitar la cantidad de días tomados de cada periodo
                $nuevo_acumulado = round($periodo['acumulado'], 2) - round($periodo['dias_a_tomar'], 2);

                VacacionPeriodo::where('id', $periodo['id'])
                    ->update([
                        'acumulado' => $nuevo_acumulado,
                        'datos_actualizacion' => $datos_registro

                    ]);
            }
        }

        // Insertar en planillas_vacaciones_detalles

        VacacionDetalle::create(array(
            'dni' => $dni,
            'desde' => $fecha_desde,
            'hasta' => $fecha_hasta,
            'periodo_id' => json_encode($periodos_id),
            'dias_tomados' => $dias_a_tomar,
            'datos_creacion' => $datos_registro

        ));

        //Quitar la cantidad total de días tomados del total de vacaciones vencidas

        $nuevo_vencidas = round($total_acumulado, 2) - round($dias_a_tomar, 2);
        Vacacion::where('dni', $dni)->update([
            'vencidas' => $nuevo_vencidas,
            'datos_actualizacion' => $datos_registro
        ]);
        $resultado = 'EXITO';
        return $resultado;

        // return redirect()->route('planillas.vacaciones');
    }
}
