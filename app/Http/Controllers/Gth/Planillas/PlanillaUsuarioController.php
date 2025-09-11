<?php

namespace App\Http\Controllers\Gth\Planillas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\GthController;

use App\Models\Gth\Planillas\PlanillaUsuario;
use App\Models\Gth\Mantenimiento\Planilla\SistPension;
use App\Models\General\Agencia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PlanillaUsuarioController extends Controller
{
    public function datos_usuarios()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'GESTION', 'GTH_PLANILLAS');

            if ($band == 1) {
                $siste_pensiones = SistPension::select(
                    'id_sis_pensiones',
                    'nombre',
                    'tipo_comision',
                    'tipo'
                )->get();

                $agencias = Agencia::select('id_agencia', 'nombre')
                    ->orderby('id_agencia', 'asc')
                    ->get();

                $planillas_datos = PlanillaUsuario::from('planillas_usuarios as pl_us')
                    ->select(
                        'pl_sis_pen.tipo',
                        'pl_sis_pen.nombre as nombre_sis_pen',
                        'pl_sis_pen.tipo_comision',
                        'pl_sis_pen.id_sis_pensiones',
                        'pl_us.id_planillas_usuarios',
                        'us.dni',
                        'us.nombres',
                        'us.apellido_paterno',
                        'us.apellido_materno',
                        'ag.nombre AS agencia',
                        'pl_us.remuneracion_basica',
                        'pl_us.remuneracion_real',
                        'pl_us.planilla',
                        'ag.id_agencia',
                        'pl_us.cuspp',
                        'pl_us.fecha_ingreso',
                        'pl_us.fecha_ingreso_planilla'
                    )
                    ->join('usuarios as us', 'us.dni', 'pl_us.dni')
                    ->join('agencias as ag', 'ag.id_agencia', 'us.agencia_id')
                    ->leftjoin('planillas_sist_pensiones as pl_sis_pen', 'pl_sis_pen.id_sis_pensiones', 'pl_us.id_sis_pensiones')
                    ->get();
                return Inertia::render('Gth/Planillas/gestion', ['planillas_datos' => $planillas_datos, 'agencias' => $agencias, 'siste_pensiones' => $siste_pensiones]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function guardar_datos_usuario(Request $request)
    {

        $datos_registro = (new GthController)->datos_registro();

        $dni = $request->dni;
        $planilla = $request->planilla;

        $fecha_ingreso = $request->fecha_ingreso;
        $remuneracion_basica = $request->remuneracion_basica;
        $remuneracion_real = $request->remuneracion_real;

        $fecha_ingreso_planilla = $request->fecha_ingreso_planilla;
        $id_sis_pensiones = $request->id_sis_pensiones;
        $cuspp = $request->cuspp;

        if ($planilla != 0) {
            PlanillaUsuario::where('dni', $dni)
                ->update([
                    'planilla' => $planilla,
                    'fecha_ingreso' => $fecha_ingreso,
                    'fecha_ingreso_planilla' => $fecha_ingreso_planilla,
                    'remuneracion_basica' => $remuneracion_basica,
                    'remuneracion_real' => $remuneracion_real,
                    'id_sis_pensiones' => $id_sis_pensiones,
                    'cuspp' => $cuspp,
                    'datos_actualizacion' => $datos_registro,

                ]);
        } else {
            PlanillaUsuario::where('dni', $dni)
                ->update([
                    'planilla' => 0,
                    'fecha_ingreso' => $fecha_ingreso,
                    'fecha_ingreso_planilla' => NULL,
                    'remuneracion_basica' => $remuneracion_basica,
                    'remuneracion_real' => $remuneracion_real,
                    'id_sis_pensiones' => 0,
                    'cuspp' => NULL,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }

        return redirect()->route('gth.pla.datos_usuarios');
    }
}
