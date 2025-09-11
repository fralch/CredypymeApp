<?php

namespace App\Http\Controllers\Gth\Usuarios;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\GthController;
use App\Models\Gth\Usuarios\UsuarioHorario;
use App\Models\Gth\Mantenimiento\Asistencia\Horario;
use App\Models\Gth\Usuarios\Usuario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HorarioAsignadoController extends Controller

{

    public function usuarios_horarios()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HORARIOS', 'GTH_USUARIOS');
            if ($band == 1) {
                $horarios = Horario::select(
                    'id',
                    'horario',
                    'hora_entrada_mañana',
                    'hora_salida_mañana',
                    'hora_entrada_tarde',
                    'hora_salida_tarde',
                    'hora_entrada_mañana_s',
                    'hora_salida_mañana_s',
                    'tolerancia'
                )->orderBy('horario', 'asc')->get();

                $usuarios_horarios = UsuarioHorario::from('asistencia_usuarios_horarios as uh')
                    ->select(
                        'us.dni',
                        'us.nombres',
                        'us.apellido_paterno',
                        'us.apellido_materno',
                        'uh.horario_id',
                        'uh.tolerancia_personal',
                        'uh.marca_asistencia',
                        'h.hora_entrada_mañana',
                        'h.hora_salida_mañana',
                        'h.hora_entrada_tarde',
                        'h.hora_salida_tarde',
                        'h.hora_entrada_mañana_s',
                        'h.hora_salida_mañana_s',
                        'h.horario',
                        'h.tolerancia as toleranciaHorario',
                        'ag.nombre as agencia'
                    )
                    ->join('usuarios as us', 'uh.usuario_id', '=', 'us.dni')
                    ->join('asistencia_horarios as h', 'h.id', '=', 'uh.horario_id')
                    ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                    ->where('us.habilitado', 1)
                    ->orderBy('horario', 'asc')
                    ->get();

                return Inertia::render('Gth/Usuarios/horarios', ['horarios' => $horarios, 'usuarios_horarios' => $usuarios_horarios]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }



    // -----------FUNCIONES QUE NO DEVUELVEN VISTAS --------------

    public function asignar_horario(Request $request)
    {

        $resultado = 'ERROR';
        $dni = $request->dni;
        $id_horario = $request->horario_id;
        $habilitado = $request->habilitado;
        $datos_registro = (new GthController)->datos_registro();
        // $fecha_registro = (new GthController)->fecha_larga_aplicacion();
        // $x = session()->all();
        // $usuario_registro = $x['usuario_dni'];

        UsuarioHorario::where('usuario_id', $dni)
            ->update([
                'horario_id' => $id_horario,
                'datos_actualizacion' => $datos_registro,
                'marca_asistencia' => $habilitado,
            ]);
        $resultado = 'EXITO';
        return $resultado;
    }

    public function asignar_tolerancia(Request $request)
    {
        $resultado = 'ERROR';
        $dni = $request->dni;
        $toleranciaPersonal = $request->toleranciaPersonal;
        $datos_registro = (new GthController)->datos_registro();
        // $fecha_registro = (new GthController)->fecha_larga_aplicacion();
        // $x = session()->all();
        // $usuario_registro = $x['usuario_dni'];

        UsuarioHorario::where('usuario_id', $dni)
            ->update([
                'tolerancia_personal' => $toleranciaPersonal,
                'datos_actualizacion' => $datos_registro,

            ]);
        $resultado = 'EXITO';
        return $resultado;
    }
}
