<?php

namespace App\Http\Controllers\Gth\Usuarios;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\GthController;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Gth\Usuarios\Cesado;
use Illuminate\Support\Facades\DB;
use App\Models\General\Agencia;
use Illuminate\Http\Request;

use Inertia\Inertia;

class CesadoController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function usuarios_cesados()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CESADOS', 'GTH_USUARIOS');
            if ($band == 1) {
                $agencias = Agencia::select('id_agencia', 'nombre')->get();
                $usuarios_cesados = Cesado::from('usuarios_cesados as ces')->select(

                    'us.dni',
                    'us.usuario',
                    'us.fecha_nacimiento',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'us.sexo',
                    'us.direccion',
                    'dis.distrito as distrito',
                    'pro.provincia as provincia',
                    'dep.departamento as departamento',
                    'us.telefono',
                    'us.correo_corporativo',
                    'ca.cargo',
                    'h.horario',
                    'age.nombre as nombre_agencia',
                    // 'ces_1.fecha_creacion',
                    // 'us_1.usuario as datos_creacion'
                )->distinct()
                    // ->where('us.habilitado', '0')
                    // ->leftjoin('cesados as ces', 'us.dni', 'ces_1.dni')

                    ->join('usuarios as us', 'ces.usuario_id',  'us.dni')
                    ->join('agencias as age', 'us.agencia_id',  'age.id_agencia')
                    ->leftjoin('asistencia_usuarios_horarios as uh', 'uh.usuario_id', 'us.dni')
                    ->leftjoin('asistencia_horarios as h', 'h.id', 'uh.horario_id')

                    ->leftjoin('departamentos as dep', 'us.departamento_id',  'dep.id')
                    ->leftjoin('provincias as pro', 'us.provincia_id',  'pro.id')
                    ->leftjoin('distritos as dis', 'us.distrito_id',  'dis.id')

                    ->join('cargos as ca', 'us.cargo_id',  'ca.id')

                    ->where('us.habilitado', 0)
                    ->orderby('nombre_agencia', 'asc')
                    ->orderby('us.apellido_paterno', 'asc')
                    ->get();

                $historial_cesados = Cesado::from('usuarios_cesados as ces')->select(

                    'ces.usuario_id',
                    'ces.motivo',
                    'us_1.usuario as datos_creacion',

                    DB::raw("SUBSTRING(ces.created_at,1,10) as fecha_creacion"),
                )
                    ->leftjoin('usuarios as us_1', DB::raw("SUBSTRING(ces.datos_creacion,42,8)"), '=', 'us_1.dni')
                    ->orderby('ces.usuario_id', 'asc')
                    ->get();



                return Inertia::render(
                    'Gth/Usuarios/cesados',
                    [
                        'agencias' => $agencias,
                        'usuarios_cesados' => $usuarios_cesados,
                        'historial_cesados' => $historial_cesados

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


    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------

    public function habilitar_cesado(Request $request)
    {
        // dd($request);
        $datos_registro = (new GthController)->datos_registro();
        $dni = $request->dni;
        // $usuario = Cesado::where('dni', $dni)->get()->last();
        // $fecha_registro = (new GthController)->fecha_larga_aplicacion();
        // $x = session()->all();
        // $usuario_registro = $x['usuario_dni'];

        // Usuario::create(array(
        //     'dni' => $usuario->dni,
        //     'usuario' => $usuario->usuario,
        //     'clave' => $usuario->clave,
        //     'nombres' => $usuario->nombres,
        //     'apellido_paterno' => $usuario->apellido_paterno,
        //     'apellido_materno' => $usuario->apellido_materno,
        //     'sexo' => $usuario->sexo,
        //     'direccion' => $usuario->direccion,
        //     'id_distrito' => $usuario->id_distrito,
        //     'id_provincia' => $usuario->id_provincia,
        //     'id_departamento' => $usuario->id_departamento,
        //     'fechaNacimiento' => $usuario->fechaNacimiento,
        //     'telefono' => $usuario->telefono,
        //     'correoPrincipal' => $usuario->correoPrincipal,
        //     'id' => $usuario->id,
        //     'id_agencia' => $usuario->id_agencia,
        //     'toleranciaPersonal' => 0,
        //     'fecha_creacion' => $fecha_registro,
        //     'datos_creacion' => $usuario_registro
        // ));

        Usuario::where('dni', $dni)->update([
            'habilitado' => 1,
            'datos_actualizacion' => $datos_registro
        ]);
        // Cesado::where('dni', $dni)->delete();

        return 'EXITO';
    }

    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------

}
