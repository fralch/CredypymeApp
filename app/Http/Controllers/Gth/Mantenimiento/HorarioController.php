<?php

namespace App\Http\Controllers\Gth\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\Mantenimiento\Asistencia\Horario;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Gth\GthController;

class HorarioController extends Controller
{


    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------


    public function  horarios()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASISTENCIA_HORARIOS', 'GTH_MANTENIMIENTO');
            if ($band == 1) {
                $horarios = Horario::all();
                return Inertia::render(
                    'Gth/Mantenimiento/Asistencia/horarios',
                    [
                        'horarios' => $horarios
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



    // --------------------FUNCIONES QUE NO DEVUELVEN UNA VISTA---------------------


    public function guardar_horario(Request $request)
    {
        // dd($request);
        $datos_registro = (new GthController)->datos_registro();
        $id = $request->id;
        $horario = $request->horario;
        $tolerancia = $request->tolerancia;
        $hora_entrada_mañana =  $request->hora_entrada_mañana;
        $hora_salida_mañana = $request->hora_salida_mañana;
        $hora_entrada_tarde = $request->hora_entrada_tarde;
        $hora_salida_tarde = $request->hora_salida_tarde;
        $hora_entrada_mañana_s = $request->hora_entrada_mañana_s;
        $hora_salida_mañana_s = $request->hora_salida_mañana_s;

        $modo = $request->modo;


        if ($modo == "EDITAR") {

            Horario::where('id', $id)
                ->update([
                    'horario' => $horario,
                    'hora_entrada_mañana' => $hora_entrada_mañana,
                    'hora_salida_mañana' => $hora_salida_mañana,
                    'hora_entrada_tarde' => $hora_entrada_tarde,
                    'hora_salida_tarde' => $hora_salida_tarde,
                    'hora_entrada_mañana_s' => $hora_entrada_mañana_s,
                    'hora_salida_mañana_s' => $hora_salida_mañana_s,
                    'tolerancia' => $tolerancia,
                    'datos_actualizacion' => $datos_registro
                ]);
        } else if ($modo == "NUEVO") {
            Horario::create([
                'horario' => $horario,
                'hora_entrada_mañana' => $hora_entrada_mañana,
                'hora_salida_mañana' => $hora_salida_mañana,
                'hora_entrada_tarde' => $hora_entrada_tarde,
                'hora_salida_tarde' => $hora_salida_tarde,
                'hora_entrada_mañana_s' => $hora_entrada_mañana_s,
                'hora_salida_mañana_s' => $hora_salida_mañana_s,
                'tolerancia' => $tolerancia,
                'datos_creacion' => $datos_registro
            ]);
        }
        return redirect()->route('gth.us_as.horarios');
    }

    // ------------------------------------------------------------------------------


}
