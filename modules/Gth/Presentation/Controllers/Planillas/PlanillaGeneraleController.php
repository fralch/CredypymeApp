<?php

namespace Modules\Gth\Presentation\Controllers\Planillas;



use App\Http\Controllers\Controller;
use Modules\Gth\Presentation\Controllers\GthController;
use Modules\General\Presentation\Controllers\PermisosController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\PlanillaUsuario;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\PlanillaGeneral;

use Inertia\Inertia;

class PlanillaGeneraleController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function index()
    {
    }

    public function planilla_general()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PLANILLA_GENERAL', 'GTH_PLANILLAS');

            if ($band == 1) {
                $agencias = Agencia::select('id_agencia', 'nombre')
                    ->orderby('id_agencia', 'asc')
                    ->get();
                return Inertia::render('Gth/Planillas/planilla_general', [
                    'agencias' => $agencias
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function historial_planillas()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_PLANILLAS', 'GTH_PLANILLAS');

            if ($band == 1) {
                $agencias = Agencia::select('id_agencia', 'nombre')
                    ->orderby('id_agencia', 'asc')
                    ->get();

                return Inertia::render(
                    'Gth/Planillas/historial_planillas',
                    ['agencias' => $agencias]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------
    public function generar_plantilla(Request $request)
    {
        $mes = $request->mes;
        $anio = $request->anio;
        $id_agencia = $request->id_agencia;

        $verificar = PlanillaGeneral::from('planillas_generales as pla_gen')
            ->join('usuarios as us', 'us.dni', 'pla_gen.dni')
            ->join('agencias as ag', 'ag.id_agencia', 'us.agencia_id')
            ->where([
                ['pla_gen.mes', $mes],
                ['pla_gen.año', $anio],
                ['ag.id_agencia', $id_agencia]
            ])->get();

        if (count($verificar) > 0) {
            $resultado = 0;
            return $resultado;
            exit;
        }

        $hasta = $anio . '-' . $mes . '-' . '26';

        if ($mes == 1) {
            $anio -= 1;
            $mes = 12;
        };
        $desde = $anio . '-' . (intval($mes) - 1) . '-' . '25';

        $datos = DB::select("WITH
        VACAS AS(
        SELECT
            usuarios.dni,
            SUM(
                planillas_vacaciones_detalles.dias_tomados
            ) AS vacaciones
        FROM
            usuarios
        INNER JOIN planillas_vacaciones_detalles ON planillas_vacaciones_detalles.dni = usuarios.dni
        WHERE DATE (SUBSTRING(planillas_vacaciones_detalles.datos_creacion,42,8)) BETWEEN $desde AND $hasta
        GROUP BY
            usuarios.dni
    ),
    FALTA AS(
        SELECT
            usuarios.dni,
            COUNT(asistencia_faltas.fecha) AS faltas
        FROM
            usuarios
        INNER JOIN asistencia_faltas ON asistencia_faltas.usuario_id = usuarios.dni
        WHERE
            DATE(fecha) BETWEEN $desde AND $hasta
        GROUP BY
            usuarios.dni
    ),
    TARDE AS(
        SELECT
            usuarios.dni,
            asistencia_faltas.fecha,
            SUM(asistencia_tardanzas.minutos) AS tardanzas
        FROM
            usuarios
        INNER JOIN asistencia_marcajes ON asistencia_marcajes.usuario_id = usuarios.dni
        INNER JOIN asistencia_tardanzas ON asistencia_tardanzas.marcaje_id = asistencia_marcajes.id
        INNER JOIN asistencia_faltas ON asistencia_faltas.usuario_id = usuarios.dni

        WHERE
            DATE(fecha) BETWEEN $desde AND $hasta
        GROUP BY
            usuarios.dni
    )


    SELECT
        usuarios.dni,
        usuarios.nombres,
        VACAS.vacaciones,
        FALTA.faltas,
        TARDE.tardanzas,
        usuarios.apellido_paterno,
        usuarios.apellido_materno,
        agencias.nombre AS agencia,

        planillas_usuarios.fecha_ingreso,
        planillas_usuarios.remuneracion_basica,
        planillas_usuarios.remuneracion_real,
        planillas_usuarios.essalud,

        planillas_sist_pensiones.porcentaje

    FROM
        usuarios

    LEFT JOIN VACAS ON VACAS.dni = usuarios.dni
    LEFT JOIN FALTA ON FALTA.dni = usuarios.dni
    LEFT JOIN TARDE ON TARDE.dni = usuarios.dni

    INNER JOIN agencias ON agencias.id_agencia = usuarios.agencia_id

    LEFT JOIN planillas_usuarios ON planillas_usuarios.dni = usuarios.dni

    LEFT JOIN planillas_sist_pensiones ON planillas_sist_pensiones.id_sis_pensiones = planillas_usuarios.id_sis_pensiones

    where agencias.id_agencia= $id_agencia");





        //     $datos = DB::select("WITH
        //     VACAS AS(



        //     SELECT
        //         usuarios.dni,
        //         SUM(
        //             planillas_vacaciones_detalles.dias_tomados
        //         ) AS vacaciones
        //     FROM
        //         usuarios
        //     INNER JOIN planillas_vacaciones_detalles ON planillas_vacaciones_detalles.dni = usuarios.dni
        //     WHERE DATE(planillas_vacaciones_detalles.fecha_creacion) BETWEEN '" . $desde . "' AND '" . $hasta . "'
        //     GROUP BY
        //         usuarios.dni
        // ),
        // FALTA AS(
        //     SELECT
        //         usuarios.dni,
        //         COUNT(asistencia_faltas.fecha) AS faltas
        //     FROM
        //         usuarios
        //     INNER JOIN asistencia_faltas ON asistencia_faltas.dni = usuarios.dni
        //     WHERE
        //         DATE(fecha) BETWEEN '" . $desde . "' AND '" . $hasta . "'
        //     GROUP BY
        //         usuarios.dni
        // ),
        // TARDE AS(
        //     SELECT
        //         usuarios.dni,
        //         SUM(asistencia_tardanzas.minutos) AS tardanzas
        //     FROM
        //         usuarios
        //     INNER JOIN asistencia_tardanzas ON asistencia_tardanzas.dni = usuarios.dni
        //     WHERE
        //         DATE(fecha) BETWEEN '" . $desde . "' AND '" . $hasta . "'
        //     GROUP BY
        //         usuarios.dni
        // )


        // SELECT
        //     usuarios.dni,
        //     usuarios.nombres,
        //     VACAS.vacaciones,
        //     FALTA.faltas,
        //     TARDE.tardanzas,
        //     usuarios.apellido_paterno,
        //     usuarios.apellido_materno,
        //     agencias.nombre AS agencia,

        //     planillas_usuarios.fecha_ingreso,
        //     planillas_usuarios.remuneracion_basica,
        //     planillas_usuarios.remuneracion_real,
        //     planillas_usuarios.essalud,

        //     planillas_sist_pensiones.porcentaje

        // FROM
        //     usuarios

        // LEFT JOIN VACAS ON VACAS.dni = usuarios.dni
        // LEFT JOIN FALTA ON FALTA.dni = usuarios.dni
        // LEFT JOIN TARDE ON TARDE.dni = usuarios.dni

        // INNER JOIN agencias ON agencias.id_agencia = usuarios.agencia_id

        // LEFT JOIN planillas_usuarios ON planillas_usuarios.dni = usuarios.dni

        // LEFT JOIN planillas_sist_pensiones ON planillas_sist_pensiones.id_sis_pensiones = planillas_usuarios.id_sis_pensiones

        // where agencias.id_agencia= " . $id_agencia . "");

        return $datos;
    }

    public function guardar_planilla(Request $request)
    {
        $datos_registro = (new GthController)->datos_registro();



        if ($request->historial === 1) {


            PlanillaGeneral::where([
                ['mes', $request->mes],
                ['año', $request->anio],
                ['dni', $request->dni]
            ])
                ->update([

                    'dias_computables' => $request->dias_computables,
                    'dias_vacaciones' => $request->dias_vacaciones,
                    'dias_faltas' => $request->dias_faltas,
                    'dias_no_laborados' => $request->dias_no_laborados,
                    'prorrateo_remuneracion_basica' => $request->prorrateo_remuneracion_basica,
                    'remuneracion_vacaciones' => $request->remuneracion_vacaciones,
                    'prorrateo_condiciones_trabajo' => $request->prorrateo_condiciones_trabajo,
                    'asig_familiar' => 0,
                    'riesgo_caja' => $request->riesgo_caja,
                    'bonificaciones' => $request->bonificaciones,
                    'comisiones' => $request->comisiones,
                    'otras_asignaciones' => 0,
                    'total_remuneracion_real' => $request->TotalmasComisiones,
                    'total_remuneracion_computable' => $request->remuneracion_basica,
                    'desc_sistema_pensiones' => $request->des_siste_pensiones,
                    'desc_renta_quinta' => 0,
                    'adelantos' => $request->adelantos,
                    'desc_faltas' => $request->descFaltas,
                    'desc_tardanzas' => $request->tardanzas,
                    'desc_otros_riesgo_caja' => $request->desc_otros_riesgo_caja,
                    'desc_otros_credito' => $request->desc_otros_credito,
                    'desc_otros' => $request->desc_otros,
                    'total_descuentos' => $request->Totaldesc,
                    'total_neto' => $request->total_neto,
                    'essalud' => $request->essalud,
                    'datos_actualizacion' => $datos_registro


                ]);
        } else {
            PlanillaGeneral::create([
                'dni' => $request->dni,
                'dias_computables' => $request->dias_computables,
                'dias_vacaciones' => $request->vacaciones,
                'dias_faltas' => $request->faltas,
                'dias_no_laborados' => $request->dias_no_laborados,
                'prorrateo_remuneracion_basica' => $request->prorrateo_remuneracion_basica,
                'remuneracion_vacaciones' => $request->remuneracion_vacaciones,
                'prorrateo_condiciones_trabajo' => $request->prorrateo_condiciones_trabajo,
                'asig_familiar' => 0,
                'riesgo_caja' => $request->riesgoCaja,
                'bonificaciones' => $request->bonificaci,
                'comisiones' => $request->comisiones,
                'otras_asignaciones' => 0,
                'total_remuneracion_real' => $request->TotalmasComisiones,
                'total_remuneracion_computable' => $request->remuneracion_basica,
                'desc_sistema_pensiones' => $request->des_siste_pensiones,
                'desc_renta_quinta' => 0,
                'adelantos' => $request->adelantoss,
                'desc_faltas' => $request->descFaltas,
                'desc_tardanzas' => $request->tardanzas,
                'desc_otros_riesgo_caja' => $request->descRiesCj,
                'desc_otros_credito' => $request->descCredit,
                'desc_otros' => $request->descuOtros,
                'total_descuentos' => $request->Totaldesc,
                'total_neto' => $request->total_neto,
                'essalud' => $request->essalud,
                'mes' => $request->mes,
                'año' => $request->anio,
                'datos_creacion' => $datos_registro
            ]);
        }
    }




    public function listar_historial_planilla(Request $request)
    {
        $mes = $request->mes;
        $año = $request->anio;
        $id_agencia = $request->id_agencia;


        return PlanillaGeneral::from('planillas_generales as pl_ge')->select(
            'us.nombres',
            'us.dni',
            'us.apellido_paterno',
            'us.apellido_materno',
            'pl_us.remuneracion_basica',
            'pl_us.remuneracion_real',
            'pl_us.essalud',
            'pl_sis.porcentaje',
            'ag.nombre AS agencia',
            'pl_ge.dias_computables',
            'pl_ge.dias_vacaciones',
            'pl_ge.dias_faltas',
            'pl_ge.dias_no_laborados',
            'pl_ge.prorrateo_remuneracion_basica',
            'pl_ge.remuneracion_vacaciones',
            'pl_ge.prorrateo_condiciones_trabajo',
            'pl_ge.asig_familiar',
            'pl_ge.riesgo_caja',
            'pl_ge.bonificaciones',
            'pl_ge.comisiones',
            'pl_ge.otras_asignaciones',
            'pl_ge.total_remuneracion_real',
            'pl_ge.total_remuneracion_computable',
            'pl_ge.total_neto',
            'pl_ge.desc_sistema_pensiones',
            'pl_ge.desc_renta_quinta',
            'pl_ge.adelantos',
            'pl_ge.desc_faltas',
            'pl_ge.desc_tardanzas',
            'pl_ge.desc_otros_riesgo_caja',
            'pl_ge.desc_otros_credito',
            'pl_ge.desc_otros',
            'pl_ge.total_descuentos',
            'pl_ge.total_neto',
            'pl_ge.essalud',
            'pl_ge.mes',
            'pl_ge.año',
            'pl_ge.datos_creacion',
            'pl_ge.datos_actualizacion'
        )
            ->join('usuarios as us', 'us.dni', 'pl_ge.dni')
            ->leftjoin('agencias as ag', 'ag.id_agencia', 'us.agencia_id')
            ->leftjoin('planillas_usuarios as pl_us', 'pl_us.dni', 'us.dni')
            ->leftjoin('planillas_vacaciones as pl_va', 'pl_va.dni', 'us.dni')
            ->leftjoin('planillas_sist_pensiones as pl_sis', 'pl_sis.id_sis_pensiones', 'pl_us.id_sis_pensiones')

            ->leftjoin('asistencia_marcajes as am', 'am.usuario_id', 'us.dni')
            ->leftjoin('asistencia_tardanzas as ta', 'ta.marcaje_id', 'am.id')

            ->leftjoin('asistencia_faltas as fa', 'fa.usuario_id', 'us.dni')


            ->where([['pl_ge.mes', $mes], ['pl_ge.año', $año], ['ag.id_agencia', $id_agencia]])
            ->groupBy('pl_ge.dni')
            ->get();
    }


    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------




}
