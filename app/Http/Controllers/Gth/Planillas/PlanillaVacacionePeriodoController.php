<?php

namespace App\Http\Controllers\Gth\Planillas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\GthController;
use App\Models\Gth\Planillas\VacacionDetalle;
use App\Models\Gth\Planillas\VacacionPeriodo;
use App\Models\Gth\Planillas\Vacacion;
use App\Models\Gth\Planillas\PlanillaUsuario;

use Illuminate\Http\Request;


class PlanillaVacacionePeriodoController extends Controller
{
    public function calcular_vacaciones(
        $fecha_actual_corta,
        $fecha_actual_larga,
        $fecha_nueva_corta,
        $datos_sesion
    ) {

        $datos_registro = (new GthController)->datos_registro();
        $usuarios_planilla = PlanillaUsuario::from('planillas_usuarios as pl_us')
            ->select(
                'pl_us.id_planillas_usuarios',
                'pl_us.dni',
                'pl_us.fecha_ingreso_planilla',
                'pl_us.planilla',
                'pl_va.id as pl_va_id',
                'pl_va.vencidas',
                'pl_va.truncadas',
                'pl_va.indemnizadas',
                'pl_va.adelantadas',
            )
            ->join('planillas_vacaciones as pl_va', 'pl_va.dni', 'pl_us.dni')
            ->where([['fecha_ingreso_planilla', '!=', null], ['planilla', 1]])
            ->get();

        $vacacion_dia = (15 / 12) / 30;

        foreach ($usuarios_planilla    as $usuario_planilla) {
            $vencidas = $usuario_planilla->vencidas;
            $truncadas = $usuario_planilla->truncadas;
            $indemnizadas = $usuario_planilla->indemnizadas;
            $adelantadas = $usuario_planilla->adelantadas;
            $vencidas_periodo = $usuario_planilla->vencidas_periodo;

            $nuevas_truncadas = $truncadas +  $vacacion_dia;

            if ($nuevas_truncadas < (15 / 2)) {
                Vacacion::where('dni', $usuario_planilla->dni)
                    ->update([
                        'truncadas' => $nuevas_truncadas,

                        'datos_actualizacion' => $datos_registro,
                    ]);
            } elseif ($nuevas_truncadas >= (15 / 2)) {

                $nuevas_vencidas = $vencidas + $nuevas_truncadas;
                Vacacion::where('dni', $usuario_planilla->dni)
                    ->update([
                        'vencidas' => $nuevas_vencidas,
                        'truncadas' => 0,

                        'datos_actualizacion' => $datos_registro
                    ]);

                $periodo_desde = $usuario_planilla->fecha_ingreso_planilla;
                $periodo_hasta = date("Y-m-d", strtotime($periodo_desde . "+ 1 year"));
                $periodo_hasta = date("Y-m-d", strtotime($periodo_hasta . "- 1 days"));


                $ultimo_periodo = VacacionPeriodo::where(
                    'dni',
                    $usuario_planilla->dni
                )->get()->last();

                if ($ultimo_periodo == null) {
                    $periodo_desde = $usuario_planilla->fecha_ingreso_planilla;
                    $periodo_hasta = date("Y-m-d", strtotime($periodo_desde . "+ 1 year"));
                    $periodo_hasta = date("Y-m-d", strtotime($periodo_hasta . "- 1 days"));

                    VacacionPeriodo::create(array(
                        'dni' => $usuario_planilla->dni,
                        'periodo_desde' => $periodo_desde,
                        'periodo_hasta' => $periodo_hasta,
                        'acumulado' => $nuevas_truncadas,
                        'completado' => 0,

                        'datos_creacion' => $datos_registro
                    ));
                } else {
                    if ($ultimo_periodo->completado == 0) {
                        $acumulado = $ultimo_periodo->acumulado;
                        $nuevo_acumulado = $acumulado + $nuevas_truncadas;
                        VacacionPeriodo::where('id', $ultimo_periodo->id)
                            ->update([
                                'acumulado' => $nuevo_acumulado,
                                'completado' => 1,

                                'datos_actualizacion' => $datos_registro

                            ]);
                    } elseif ($ultimo_periodo->completado == 1) {


                        $periodo_desde = $ultimo_periodo->periodo_hasta;
                        $periodo_hasta = date("Y-m-d", strtotime($periodo_desde . "+ 1 year"));
                        $periodo_hasta = date("Y-m-d", strtotime($periodo_hasta . "- 1 days"));

                        VacacionPeriodo::create(array(
                            'dni' => $usuario_planilla->dni,
                            'periodo_desde' => $periodo_desde,
                            'periodo_hasta' => $periodo_hasta,
                            'acumulado' => $nuevas_truncadas,
                            'completado' => 0,
                            'datos_creacion' => $datos_registro

                        ));
                    }
                }
            }
        }
        return 'SUCCESS';
    }

    public function buscar_periodo(Request $request)
    {
        $id_periodo = $request->id_periodo;
        $periodo_detalle = VacacionPeriodo::select('periodo_desde', 'periodo_hasta')
            ->where('id', $id_periodo)->get();
        return $periodo_detalle;
    }
    public function total_dia_acumulados(Request $request)
    {
        // return $request;
        // $dni= $request;

        $dni = $request[0];

        // return Vacacion::select('planillas_vacaciones.dni', 'planillas_vacaciones.vencidas', DB::raw('SUM(planillas_vacaciones.dias_tomados) as total_dias_tomados'), 'planillas_usuarios.fecha_ingreso_planilla')
        //     ->join('planillas_usuarios', 'planillas_usuarios.dni', 'planillas_vacaciones.dni')
        //     ->where('planillas_vacaciones.dni', $dni)
        //     ->get();

        return VacacionPeriodo::join('planillas_usuarios', 'planillas_usuarios.dni', 'planillas_vacaciones_periodos.dni')
            ->join('usuarios', 'usuarios.dni', 'planillas_vacaciones_periodos.dni')
            ->where('planillas_vacaciones_periodos.dni', $dni)
            ->get();
    }
}
