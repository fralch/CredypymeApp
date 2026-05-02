<?php

namespace Modules\Gth\Presentation\Controllers\Planillas;



use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Presentation\Controllers\GthController;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\VacacionDetalle;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\VacacionPeriodo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\Vacacion;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\PlanillaUsuario;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PlanillaVacacioneController extends Controller
{

    public function vacaciones()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'VACACIONES', 'GTH_PLANILLAS');

            if ($band == 1) {

                $usuarios = Usuario::all();

                $agencias = Agencia::select('id_agencia', 'nombre')
                    ->orderby('id_agencia', 'asc')
                    ->get();

                $vacaciones_usuarios = Vacacion::from('planillas_vacaciones as pl_va')
                    ->select(
                        'pl_va.id',
                        'pl_va.dni',
                        'pl_va.vencidas',
                        'pl_va.truncadas',
                        'pl_va.indemnizadas',
                        'pl_va.adelantadas',
                        'pl_us.fecha_ingreso_planilla',
                        'us.nombres',
                        'us.apellido_paterno',
                        'us.apellido_materno',
                        'us.agencia_id',
                        'ag.nombre as agencia',
                    )
                    ->join('usuarios as us', 'pl_va.dni', 'us.dni')
                    ->join('planillas_usuarios as pl_us', 'pl_us.dni', 'us.dni')
                    ->join('agencias as ag', 'ag.id_agencia', 'us.agencia_id')
                    ->orderBy('pl_us.fecha_ingreso_planilla', 'asc')
                    ->where('pl_us.fecha_ingreso_planilla', '!=', null)
                    ->get();

                $periodos_usuarios = VacacionPeriodo::select(
                    'id',
                    'dni',
                    'periodo_desde',
                    'periodo_hasta',
                    'acumulado',
                    DB::raw('0 as dias_a_tomar')

                )->where('acumulado', '>=', '0.5')->get();
                return Inertia::render('Gth/Planillas/vacaciones', [
                    'vacaciones_usuarios' => $vacaciones_usuarios,
                    'periodos_usuarios' => $periodos_usuarios,
                    'agencias' => $agencias,
                    'usuarios' => $usuarios
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function guardar_dias(Request $request)
    {
        return $request;
        // date_default_timezone_set("America/Lima");
        // $fecha_registro = date('Y-m-d');
        // $usuario_registro = session('usuario_dni');


        // $dni = $request[0][0]['dni'];
        // $dias_a_tomar = $request[0][0]['dias_a_tomar'];
        // $desde = $request[0][0]['fecha_desde'];
        // $hasta = $request[0][0]['fecha_hasta'];

        // $dias_tomados= Vacacion::select('dias_tomados', 'vencidas')->where('dni', $dni)->get()->last();
        // $dias_a_tomados=($dias_tomados['dias_tomados']+$dias_a_tomar);
        // $vencidas = ($dias_tomados['vencidas']-$dias_a_tomar);




        // $verificar = VacacionDetalle::select('dni')->where('fecha_registro', 'like', '%' . $fecha_registro . '%')->where('dni', $dni)->get();

        // if (count($verificar) == 0) {
        //     // $insertar_vacaciones= Vacacion::where('dni', $dni)->update(['dias_tomados' => $dias_a_tomados,  'vencidas' => $vencidas,  'fecha_registro' => $fecha_registro, 'usuario_registro' => $usuario_registro]);
        //     VacacionDetalle::insert([['dni' => $dni, 'dias' => $dias_a_tomar, 'desde' => $desde, 'hasta' => $hasta, 'fecha_registro' => $fecha_registro]]);
        //     exit;
        // } else {
        //     return 0;
        // }
    }


    // public function historial_vacaciones()
    // {

    //     $x = session()->all();

    //     if (empty($x['usuario_dni'])) {
    //         return redirect('/');
    //     } else {

    //         $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_VACACIONES', 'GTH_PLANILLAS');

    //         if ($band == 1) {


    //             $vacaciones_asignadas = VacacionDetalle::from('planillas_vacaciones_detalles as pl_va_de')
    //                 ->select(
    //                     'pl_va_de.id',
    //                     DB::raw("CONCAT(us_1.apellido_paterno,' ',us_1.apellido_materno,' ',us_1.nombres) as colaborador"),
    //                     'pl_va_de.desde',
    //                     'pl_va_de.hasta',
    //                     'pl_va_de.periodo_id',
    //                     'pl_va_de.dias_tomados',
    //                     'pl_va_de.datos_creacion',
    //                     'us_2.usuario as datos_creacion',
    //                     'ag.nombre as nombre_agencia'
    //                 )
    //                 ->join('usuarios as us_1', 'us_1.dni', 'pl_va_de.dni')
    //                 ->join('usuarios as us_2', 'us_2.dni', DB::raw("SUBSTRING(pl_va_de.datos_creacion,42,8)"))
    //                 ->join('agencias as ag', 'us_1.agencia_id', 'ag.id_agencia')
    //                 ->get();

    //             $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();

    //             return Inertia::render(
    //                 'Gth/Planillas/historial_vacaciones',
    //                 [
    //                     'vacaciones_asignadas' => $vacaciones_asignadas,
    //                     'agencias' => $agencias
    //                 ]
    //             );
    //         } else {
    //             $mensajeTitulo = '¡Ups!';
    //             $mensajeContenido = 'No tienes permitido ver este contenido.';
    //             return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
    //             die();
    //         }
    //     }
    // }

    // public function asignar_vacaciones(Request $request)
    // {
    //     // dd(srequest);
    //     $datos_registro = (new GthController)->datos_registro();
    //     $dni = $request->dni;
    //     $fecha_desde = $request->fecha_desde;
    //     $fecha_hasta = $request->fecha_hasta;
    //     $dias_a_tomar = $request->dias_a_tomar;
    //     $total_acumulado = $request->total_acumulado;
    //     $periodos_filtrado = $request->periodos_filtrado;


    //     foreach ($periodos_filtrado as $periodo) {

    //         if ($periodo['dias_a_tomar'] > 0) {
    //             // Obtener los periodos involucrados en la asignación
    //             $periodos_id[] = (object)[
    //                 'id' => $periodo['id'],
    //                 'dias_tomados' => $periodo['dias_a_tomar']
    //             ];

    //             // Quitar la cantidad de días tomados de cada periodo
    //             $nuevo_acumulado = round($periodo['acumulado'], 2) - round($periodo['dias_a_tomar'], 2);

    //             VacacionPeriodo::where('id', $periodo['id'])
    //                 ->update([
    //                     'acumulado' => $nuevo_acumulado,
    //                     'datos_actualizacion' => $datos_registro

    //                 ]);
    //         }
    //     }

    //     // Insertar en planillas_vacaciones_detalles

    //     VacacionDetalle::create(array(
    //         'dni' => $dni,
    //         'desde' => $fecha_desde,
    //         'hasta' => $fecha_hasta,
    //         'periodo_id' => json_encode($periodos_id),
    //         'dias_tomados' => $dias_a_tomar,
    //         'datos_creacion' => $datos_registro

    //     ));

    //     //Quitar la cantidad total de días tomados del total de vacaciones vencidas

    //     $nuevo_vencidas = round($total_acumulado, 2) - round($dias_a_tomar, 2);
    //     Vacacion::where('dni', $dni)->update([
    //         'vencidas' => $nuevo_vencidas,
    //         'datos_actualizacion' => $datos_registro
    //     ]);

    //     return redirect()->route('planillas.vacaciones');
    // }
    // public function total_dia_acumulados(Request $request)
    // {
    //     // return $request;
    //     // $dni= $request;

    //     $dni = $request[0];

    //     // return Vacacion::select('planillas_vacaciones.dni', 'planillas_vacaciones.vencidas', DB::raw('SUM(planillas_vacaciones.dias_tomados) as total_dias_tomados'), 'planillas_usuarios.fecha_ingreso_planilla')
    //     //     ->join('planillas_usuarios', 'planillas_usuarios.dni', 'planillas_vacaciones.dni')
    //     //     ->where('planillas_vacaciones.dni', $dni)
    //     //     ->get();

    //     return VacacionPeriodo::join('planillas_usuarios', 'planillas_usuarios.dni', 'planillas_vacaciones_periodos.dni')
    //         ->join('usuarios', 'usuarios.dni', 'planillas_vacaciones_periodos.dni')
    //         ->where('planillas_vacaciones_periodos.dni', $dni)
    //         ->get();
    // }






    // public function vacaciones_x_agencia(Request $request)
    // {
    //     return Vacacion::select('planillas_vacaciones.dni', 'agencias.nombre AS agencia', 'usuarios.nombres', 'usuarios.apellido_paterno', 'usuarios.apellido_materno', 'planillas_usuarios.fecha_ingreso_planilla')
    //         ->distinct('planillas_vacaciones.dni')
    //         ->join('usuarios', 'usuarios.dni', 'planillas_vacaciones.dni')
    //         ->join('agencias', 'agencias.id_agencia', 'usuarios.agencia_id')
    //         ->join('planillas_usuarios', 'planillas_usuarios.dni', 'planillas_vacaciones.dni')
    //         ->get();
    // }

    // public function buscar_periodo(Request $request)
    // {
    //     $id_periodo = $request->id_periodo;
    //     $periodo_detalle = VacacionPeriodo::select('periodo_desde', 'periodo_hasta')
    //         ->where('id', $id_periodo)->get();
    //     return $periodo_detalle;
    // }
    // public function calcular_vacaciones(
    //     $fecha_actual_corta,
    //     $fecha_actual_larga,
    //     $fecha_nueva_corta,
    //     $datos_sesion
    // ) {

    //     $datos_registro = (new GthController)->datos_registro();
    //     $usuarios_planilla = PlanillaUsuario::from('planillas_usuarios as pl_us')
    //         ->select(
    //             'pl_us.id_planillas_usuarios',
    //             'pl_us.dni',
    //             'pl_us.fecha_ingreso_planilla',
    //             'pl_us.planilla',
    //             'pl_va.id as pl_va_id',
    //             'pl_va.vencidas',
    //             'pl_va.truncadas',
    //             'pl_va.indemnizadas',
    //             'pl_va.adelantadas',
    //         )
    //         ->join('planillas_vacaciones as pl_va', 'pl_va.dni', 'pl_us.dni')
    //         ->where([['fecha_ingreso_planilla', '!=', null], ['planilla', 1]])
    //         ->get();

    //     $vacacion_dia = (15 / 12) / 30;

    //     foreach ($usuarios_planilla    as $usuario_planilla) {
    //         $vencidas = $usuario_planilla->vencidas;
    //         $truncadas = $usuario_planilla->truncadas;
    //         $indemnizadas = $usuario_planilla->indemnizadas;
    //         $adelantadas = $usuario_planilla->adelantadas;
    //         $vencidas_periodo = $usuario_planilla->vencidas_periodo;

    //         $nuevas_truncadas = $truncadas +  $vacacion_dia;

    //         if ($nuevas_truncadas < (15 / 2)) {
    //             Vacacion::where('dni', $usuario_planilla->dni)
    //                 ->update([
    //                     'truncadas' => $nuevas_truncadas,

    //                     'datos_actualizacion' => $datos_registro,
    //                 ]);
    //         } elseif ($nuevas_truncadas >= (15 / 2)) {

    //             $nuevas_vencidas = $vencidas + $nuevas_truncadas;
    //             Vacacion::where('dni', $usuario_planilla->dni)
    //                 ->update([
    //                     'vencidas' => $nuevas_vencidas,
    //                     'truncadas' => 0,

    //                     'datos_actualizacion' => $datos_registro
    //                 ]);

    //             $periodo_desde = $usuario_planilla->fecha_ingreso_planilla;
    //             $periodo_hasta = date("Y-m-d", strtotime($periodo_desde . "+ 1 year"));
    //             $periodo_hasta = date("Y-m-d", strtotime($periodo_hasta . "- 1 days"));


    //             $ultimo_periodo = VacacionPeriodo::where(
    //                 'dni',
    //                 $usuario_planilla->dni
    //             )->get()->last();

    //             if ($ultimo_periodo == null) {
    //                 $periodo_desde = $usuario_planilla->fecha_ingreso_planilla;
    //                 $periodo_hasta = date("Y-m-d", strtotime($periodo_desde . "+ 1 year"));
    //                 $periodo_hasta = date("Y-m-d", strtotime($periodo_hasta . "- 1 days"));

    //                 VacacionPeriodo::create(array(
    //                     'dni' => $usuario_planilla->dni,
    //                     'periodo_desde' => $periodo_desde,
    //                     'periodo_hasta' => $periodo_hasta,
    //                     'acumulado' => $nuevas_truncadas,
    //                     'completado' => 0,

    //                     'datos_creacion' => $datos_registro
    //                 ));
    //             } else {
    //                 if ($ultimo_periodo->completado == 0) {
    //                     $acumulado = $ultimo_periodo->acumulado;
    //                     $nuevo_acumulado = $acumulado + $nuevas_truncadas;
    //                     VacacionPeriodo::where('id', $ultimo_periodo->id)
    //                         ->update([
    //                             'acumulado' => $nuevo_acumulado,
    //                             'completado' => 1,

    //                             'datos_actualizacion' => $datos_registro

    //                         ]);
    //                 } elseif ($ultimo_periodo->completado == 1) {


    //                     $periodo_desde = $ultimo_periodo->periodo_hasta;
    //                     $periodo_hasta = date("Y-m-d", strtotime($periodo_desde . "+ 1 year"));
    //                     $periodo_hasta = date("Y-m-d", strtotime($periodo_hasta . "- 1 days"));

    //                     VacacionPeriodo::create(array(
    //                         'dni' => $usuario_planilla->dni,
    //                         'periodo_desde' => $periodo_desde,
    //                         'periodo_hasta' => $periodo_hasta,
    //                         'acumulado' => $nuevas_truncadas,
    //                         'completado' => 0,
    //                         'datos_creacion' => $datos_registro

    //                     ));
    //                 }
    //             }
    //         }
    //     }
    //     return 'SUCCESS';
    // }
}
