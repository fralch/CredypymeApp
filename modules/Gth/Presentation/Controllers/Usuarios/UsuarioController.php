<?php

namespace Modules\Gth\Presentation\Controllers\Usuarios;


use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\General\Presentation\Controllers\DispositivoController;
use Modules\General\Presentation\Controllers\SesionController;

use Modules\Gth\Presentation\Controllers\GthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\UsuarioHorario;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Mantenimiento\Asistencia\Horario;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Cesado;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\General;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\Vacacion;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;

use Modules\General\Infrastructure\Persistence\Eloquent\Departamento;
use Modules\General\Infrastructure\Persistence\Eloquent\Provincia;
use Modules\General\Infrastructure\Persistence\Eloquent\Distrito;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Inertia\Inertia;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Sesion;
use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas\PlanillaUsuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CentralRiesgo;
use Modules\Creditos\Presentation\Controllers\Clientes\ClientesController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;


class UsuarioController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA-----------------

    public function welcome()
    {
        $x = session()->all();

        if (!empty($x['usuario_dni'])) {
            (new SesionController)->cerrar_sesion();
        }

        return view('welcome');
    }


    public function login()
    {
        return view('login');
    }

    public function home($mensaje = "ACEPTADO")
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            $version = DB::select("SELECT * FROM versiones ORDER by id_version DESC LIMIT 1");

            return view('home')->with('version', $version)->with('mensaje', $mensaje);
        }
    }

    public function cerrar_sesion()
    {
        session()->forget('usuario_dni');
        session()->forget('usuario');
        session()->forget('nombres');
        session()->forget('id_agencia');
        session()->forget('nombre_agencia');
        session()->forget('nombre_cargo');
        session()->forget('dispositivo');
    }

    // public function index()
    // {
    //     $x = session()->all();

    //     if (empty($x['usuario_dni'])) {
    //         return redirect('/');
    //     } else {

    //         $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PRINCIPAL', 'GTH');
    //         if ($band == 1) {
    //             return Inertia::render('Gth/UsuariosAsistencia/principal_usuarios_asistencia');
    //         } else {
    //             $mensaje = 'RECHAZADO';
    //             return (new UsuarioController)->home($mensaje);
    //             die();
    //         }
    //     }
    // }

    public function usuarios_gestion()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'GESTION', 'GTH_USUARIOS');

            if ($band == 1) {

                $clave_permiso = 0;

                $clave_permiso = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAMBIAR_CLAVE', 'GTH_USUARIOS');



                $usuarios = Usuario::from('usuarios as us')->select(
                    'us.dni',
                    'us.usuario',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'us.sexo',
                    'us.distrito_id',
                    'us.provincia_id',
                    'us.departamento_id',
                    'us.habilitado',
                    'us.fecha_nacimiento',
                    'us.direccion',
                    'us.telefono',
                    'us.correo_corporativo',
                    'us.cargo_id',
                    'ca.cargo',
                    'us.agencia_id',
                    'ag.nombre AS nombreAgencia',
                    'us.usuario_real',
                )
                    ->where('us.habilitado', '1')
                    ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                    ->join('cargos as ca', 'us.cargo_id', '=', 'ca.id')
                    ->orderBy('nombreAgencia', 'asc')->orderBy('usuario', 'asc')->get();

                $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();

                $distritos = Distrito::orderBy('distrito', 'asc')->get();
                $provincias = Provincia::orderBy('provincia', 'asc')->get();
                $departamentos = Departamento::orderBy('departamento', 'asc')->get();
                $cargos = Cargo::select('id', 'cargo')->where('habilitado', '1')->orderBy('cargo', 'asc')->get();
                $horarios = Horario::select('id', 'horario')->orderBy('horario', 'asc')->get();


                return Inertia::render(
                    'Gth/Usuarios/gestion',
                    [
                        'usuarios' => $usuarios,
                        'agencias' => $agencias,
                        'cargos' => $cargos,
                        'distritos' => $distritos,
                        'provincias' => $provincias,
                        'departamentos' => $departamentos,
                        'horarios' => $horarios,
                        'clave_permiso' => $clave_permiso,
                    ]
                );
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }

    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------



    public function validarUsuario(Request $request)
    {
        $usuario = $request->usuario;
        $clave = $request->clave;
        $validando_usuario = Usuario::select(
            'usuarios.dni',
            'usuarios.usuario',
            'usuarios.clave',
            'usuarios.nombres',
            'usuarios.apellido_paterno',
            'usuarios.apellido_materno',
            'usuarios.agencia_id',
            'usuarios.actualizo_clave',
            'ag.nombre as nombre_agencia',
            'ca.cargo'
        )
            ->join('agencias as ag', 'usuarios.agencia_id', 'ag.id_agencia')
            ->join('cargos as ca', 'usuarios.cargo_id', 'ca.id')
            ->where('usuario', $usuario)->where('usuarios.habilitado', 1)->get();

        if (count($validando_usuario) == 1) {

            if (Hash::check($clave, $validando_usuario[0]['clave'])) {

                $usuario_encontrado = $validando_usuario[0];
                $id_agencia = $usuario_encontrado['agencia_id'];

                // Verificar acceso del dispositivo
                $dispositivo = (new DispositivoController)->informacion($id_agencia);
                $autorizado = $dispositivo->autorizado;

                if ($autorizado) {
                    $nombres = $usuario_encontrado['nombres'] . " " . $usuario_encontrado['apellido_paterno'] . " " . $usuario_encontrado['apellido_materno'];
                    $usuario = $usuario_encontrado['usuario'];
                    $dni = $usuario_encontrado['dni'];

                    $nombre_agencia = $usuario_encontrado['nombre_agencia'];
                    $nombre_cargo = $usuario_encontrado['cargo'];

                    // verificar si tiene la sesion abierta en otro dispositivo
                    $sesion_abierta = Sesion::where('usuario_id', $dni)->where('conectado', 1)->get()->last();

                    // si la sesion esta abierta en otro dispositivo, cierra la sesion
                    if ($sesion_abierta) {
                        // obtener el id de la sesion
                        $id_sesion = $sesion_abierta->get_id;
                        // cerrar la sesion
                        Session::getHandler()->destroy($id_sesion);
                    }

                    if ($usuario_encontrado['actualizo_clave'] == 0) {
                        return redirect('usuario/actualizar_clave/' . $dni);
                    } else {
                        session(['usuario_dni' => $dni]);
                        session(['nombres' => $nombres]);
                        session(['usuario' => $usuario]);
                        session(['id_agencia' => $id_agencia]);
                        session(['nombre_agencia' => $nombre_agencia]);
                        session(['nombre_cargo' => $nombre_cargo]);
                        session(['dispositivo' => $dispositivo]);

                        session()->forget('usuario_no_valido');

                        return redirect('/home');
                    }
                } else {
                    return view('cuatrocientoscuatro')->with('mensajeTitulo', '¡Ups!')->with('mensajeContenido', 'Este equipo no está autorizado para usar la aplicación.');
                    exit();
                }
            } else {
                session(['usuario_no_valido' => 'Contraseña incorrecta, intente nuevamente']);
                return redirect('/login');
                exit();
            }
        } else {
            session(['usuario_no_valido' => 'Usuario incorrecto, intente nuevamente']);
            return redirect('/login');
            exit();
        }
    }

    function ComprobarSesion()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return response()->json(['sesion' => false]);
        } else {
            return response()->json(['sesion' => true]);
        }
    }

    public function actualizar_clave(Request $request)
    {
        $dni = $request->dni;
        return Inertia::render('Gth/Usuarios/actualizar_clave', ['usuarioDni' => $dni]);
    }

    function guardando_nueva_clave(Request $request)
    {
        $clave_anteior = $request->clave_anterior;
        $clave_nueva =  $request->clave_nueva;
        $clave_repetida =  $request->repetir_clave;
        $dni = $request->dni;

        $usuario = Usuario::select('clave')->where('dni', $dni)->get()->last();

        if (Hash::check($clave_anteior, $usuario->clave)) {
            if ($clave_nueva == $clave_repetida) {
                Usuario::where('dni', $dni)->update(['clave' => Hash::make($clave_nueva), 'actualizo_clave' => 1]);
                return response()->json(['success' => true, 'message' => 'Operación exitosa']);
            } else {
                return response()->json(['success' => false, 'message' => 'Las claves no coinciden']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'La clave anterior es incorrecta']);
        }
    }
    //api credicheck
    public function validarUsuario_api(Request $request)
    {
        // return $request;
        $usuario = $request->usuario;
        $clave = $request->clave;
        $usuario_inhabilidatado = Usuario::select(
            'usuarios.dni',
            'usuarios.usuario',
            'usuarios.clave',
            'usuarios.nombres',
            'usuarios.apellido_paterno',
            'usuarios.apellido_materno',
            'usuarios.agencia_id',
            'usuarios.habilitado',
            'ag.nombre as nombre_agencia',
            'ca.cargo',
            'ca.jefatura'
        )
            ->join('agencias as ag', 'usuarios.agencia_id', 'ag.id_agencia')
            ->join('cargos as ca', 'usuarios.cargo_id', 'ca.id')
            ->where('usuario', $usuario)
            ->where('usuarios.habilitado', 0)
            ->get();

        if (count($usuario_inhabilidatado) == 1) {
            $usuario_object = [
                'login' => false,
                'mensaje' => 'Usted no tiene permisos para acceder a esta aplicación.',
            ];
            return response()->json($usuario_object);
            exit();
        }

        //-------------------------------------
        $validando_usuario = Usuario::select(
            'usuarios.dni',
            'usuarios.usuario',
            'usuarios.clave',
            'usuarios.nombres',
            'usuarios.apellido_paterno',
            'usuarios.apellido_materno',
            'usuarios.agencia_id',
            'usuarios.habilitado',
            'ag.nombre as nombre_agencia',
            'ca.cargo',
            'ca.jefatura'

        )
            ->join('agencias as ag', 'usuarios.agencia_id', 'ag.id_agencia')
            ->join('cargos as ca', 'usuarios.cargo_id', 'ca.id')
            ->where('usuario', $usuario)
            ->where('usuarios.habilitado', 1)
            ->get();

        if (count($validando_usuario) == 1) {

            if (Hash::check($clave, $validando_usuario[0]['clave'])) {
                $usuario_encontrado = $validando_usuario[0];
                $id_agencia = $usuario_encontrado['agencia_id'];

                // dd($validando_usuario);

                $dispositivo = (new DispositivoController)->informacion($id_agencia);

                $nombres = $usuario_encontrado['nombres'] . " " . $usuario_encontrado['apellido_paterno'] . " " . $usuario_encontrado['apellido_materno'];
                $usuario = $usuario_encontrado['usuario'];
                $dni = $usuario_encontrado['dni'];

                $nombre_agencia = $usuario_encontrado['nombre_agencia'];
                $nombre_cargo = $usuario_encontrado['cargo'];
                $habilitado = $usuario_encontrado['habilitado'];
                $jefatura = $usuario_encontrado['jefatura'];


                session(['usuario_dni' => $dni]);
                session(['nombres' => $nombres]);
                session(['usuario' => $usuario]);
                session(['id_agencia' => $id_agencia]);
                session(['nombre_agencia' => $nombre_agencia]);
                session(['nombre_cargo' => $nombre_cargo]);
                session(['jefatura' => $jefatura]);
                session(['dispositivo' => $dispositivo]);

                $usuario_object = [
                    'login' => true,
                    'usuario_dni' => $dni,
                    'nombres' => $nombres,
                    'usuario' => $usuario,
                    'id_agencia' => $id_agencia,
                    'nombre_agencia' => $nombre_agencia,
                    'nombre_cargo' => $nombre_cargo,
                    'dispositivo' => $dispositivo,
                    'jefatura' => $jefatura,
                    'habilitado' => $habilitado
                ];

                return response()->json($usuario_object);
            } else {
                $usuario_object = [
                    'login' => false,
                    'mensaje' => 'Contraseña incorrecta, intente nuevamente'
                ];

                return response()->json($usuario_object);
                exit();
            }
        } else {
            $usuario_object = [
                'login' => false,
                'mensaje' => 'Usuario incorrecto, intente nuevamente'
            ];
            return response()->json($usuario_object);
            exit();
        }
    }



    public function guardar_clave_usuario(Request $request)
    {

        // dd($request);
        $clave_nueva =  $request->clave_nueva;
        $clave_repetida =  $request->clave_repetida;
        $habilitado =  $request->habilitado;


        if ($clave_nueva == $clave_repetida) {

            if ($habilitado == "false") {
                $habilitado = 1;
            }
            if ($habilitado == "true") {
                $habilitado = 0;
            }

            // dd($habilitado);


            Usuario::where('dni', $request->dni)
                ->update(['clave' => Hash::make($request->clave_repetida), 'actualizo_clave' => $habilitado]);



            return "CORRECTO";
        } else {
            return "INCORRECTO_REPETICION";
        }
    }
    public function generar_clave()

    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $rango = 36;
        $cantidad = 10;

        $clave_aleatoria = '';
        for ($i = 0; $i < $cantidad; $i++) {
            $caracter_aleatorio =  $caracteres[mt_rand(0, $rango - 1)];
            $clave_aleatoria .= $caracter_aleatorio;
        }
        return $clave_aleatoria;
    }


    public function guardar_clave(Request $request)
    {
        // dd($request);


        $clave_actual =  $request->clave_actual;
        $usuario =  $request->usuario;
        $clave_nueva =  $request->clave_nueva;
        $clave_repetida =  $request->clave_repetida;


        $validando_usuario = Usuario::select(
            'usuarios.dni',
            'usuarios.usuario',
            'usuarios.clave',

        )->where('usuario', $usuario)->get();

        if (Hash::check($clave_actual, $validando_usuario[0]['clave'])) {

            if ($clave_nueva == $clave_repetida) {

                Usuario::where('dni', $request->dni)
                    ->update(['clave' => Hash::make($request->clave_repetida)]);
                return "CORRECTO";
            } else {
                return "INCORRECTO_REPETICION";
            }
        } else {
            return "INCORRECTO_CLAVE";
        }
    }


    public function verificar_usuario(Request $request)

    {
        $modo = $request->modo;
        $dni = $request->dni;
        $usuario = $request->usuario;


        if ($modo == 'EDITAR') {

            $existe_usuario = Usuario::select('usuario', 'dni')->where('usuario', $usuario)->get();


            if (count($existe_usuario) == 0) {
                return "CORRECTO";
            } else {
                if ($existe_usuario[0]['dni'] == $dni) {
                    return "CORRECTO";
                } else {
                    return "INCORRECTO_U";
                }
            }
        } else if ($modo == 'NUEVO') {

            $existe_dni = Usuario::select('dni')->where('dni', $dni)->get();
            $existe_usuario = Usuario::select('usuario')->where('usuario', $usuario)->get();

            if (count($existe_dni) == 0) {

                if (count($existe_usuario) == 0) {
                    return 'CORRECTO';
                } else {
                    return 'INCORRECTO_U';
                }
            } else {
                return 'INCORRECTO_D';
            }
        }
    }

    public function guardar_usuario(Request $request)
    {
        // dd($request);

        $form_datos_usuario = json_decode($request->form_datos_usuario);
        $modo_2 = $request->modo_2;

        // dd($form_datos_usuario,$modo_2);


        $datoscreacion = (new GthController)->datos_registro();

        $dni = $form_datos_usuario->dni;
        $usuario = mb_strtolower($form_datos_usuario->usuario);

        $nombres = trim(mb_strtoupper($form_datos_usuario->nombres));
        $apellidoPaterno = trim(mb_strtoupper($form_datos_usuario->apellido_paterno));
        $apellidoMaterno = trim(mb_strtoupper($form_datos_usuario->apellido_materno));

        $modo = $form_datos_usuario->modo;


        $sexo = $form_datos_usuario->sexo;
        $direccion = mb_strtoupper($form_datos_usuario->direccion);
        $distrito_id = $form_datos_usuario->distrito_id;
        $provincia_id = $form_datos_usuario->provincia_id;
        $departamento_id = $form_datos_usuario->departamento_id;
        $fechaNacimiento = $form_datos_usuario->fecha_nacimiento;
        $telefono = $form_datos_usuario->telefono;
        $correoPrincipal = $form_datos_usuario->correo_corporativo;
        $cargo_id = $form_datos_usuario->cargo_id;
        $agencia_id = $form_datos_usuario->agencia_id;
        $usuario_real = $form_datos_usuario->usuario_real;

        $agencia_nombre = Agencia::select('nombre')->where('id_agencia', $agencia_id)->get()->last();
        $agencia_nombre = mb_strtoupper($agencia_nombre->nombre);
        $conexion = 'master_' .  $agencia_id;

        $id_horario = $form_datos_usuario->horario_id;


        $resultado = 'ERROR';

        // dd($agencia_nombre);

        if ($modo == 'NUEVO') {


            $clave = Hash::make($dni);
            Usuario::create(array(
                'dni' => $dni,
                'usuario' => $usuario,
                'clave' => $clave,
                'nombres' => $nombres,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'sexo' => $sexo,
                'direccion' => $direccion,
                'distrito_id' => $distrito_id,
                'provincia_id' => $provincia_id,
                'departamento_id' => $departamento_id,
                'fecha_nacimiento' => $fechaNacimiento,
                'telefono' => $telefono,
                'correo_corporativo' => $correoPrincipal,
                'agencia_id' => $agencia_id,
                'cargo_id' => $cargo_id,
                'habilitado' => 1,
                'usuario_real' => 1,
                'datos_creacion' => $datoscreacion



            ));

            UsuarioHorario::create(array(
                'usuario_id' => $dni,
                'horario_id' => $id_horario,
                'datos_creacion' => $datoscreacion
            ));

            Vacacion::create([
                'dni' => $dni,
                'datos_creacion' => (new GthController)->datos_registro()

            ]);


            PlanillaUsuario::create([
                'dni' => $dni,
                'datos_creacion' => (new GthController)->datos_registro()

            ]);
            CuentaUsuario::on($conexion)->create([
                'dni' => $dni,
                'con_cuenta' => 0,
                'monto' => 0,
                'datos_creacion' => (new GthController)->datos_registro()
            ]);
            Sesion::create(['usuario_id' => $dni]);
        } else if ($modo == 'EDITAR') {
            Usuario::where('dni', $dni)->update([
                'usuario' => $usuario,
                'nombres' => $nombres,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'sexo' => $sexo,
                'direccion' => $direccion,
                'distrito_id' => $distrito_id,
                'provincia_id' => $provincia_id,
                'departamento_id' => $departamento_id,
                'fecha_nacimiento' => $fechaNacimiento,
                'telefono' => $telefono,
                'correo_corporativo' => $correoPrincipal,
                'agencia_id' => $agencia_id,
                'cargo_id' => $cargo_id,
                'usuario_real' => $usuario_real,
                'datos_actualizacion' => $datoscreacion
            ]);
            $resultado = 'EXITO';
        }

        return redirect()->route('gth.usu.usuarios_gestion');
    }

    public function cesar_usuario(Request $request)
    {
        $datoscreacion = (new GthController)->datos_registro();

        // dd($request);
        $dni = $request->dni;
        $motivo_cese =  $request->motivo_cese;
        $usuario = Usuario::where('dni', $dni)->get()->last();


        Cesado::create(array(
            'usuario_id' => $usuario->dni,
            // 'usuario' => $usuario->usuario,
            'motivo' => $motivo_cese,
            'datos_creacion' => $datoscreacion,
        ));

        Usuario::where('dni', $dni)->update([
            'habilitado' => 0,
            'datos_actualizacion' => $datoscreacion,

        ]);
        // Usuario::where('dni', $dni)->delete();

        return 'EXITO';
    }



    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    public function changepass()
    {
        // $datos=  $request->except('_token');
        // $dni= $datos['dni'];
        // $newPass=$datos['dni'];
        // $passwordencryp = Hash::make($newPass);

        // DB::table('usuarios')
        //             ->where('dni', $dni)
        //             ->update(['clave' => $passwordencryp]);
        // return redirect('/usuarios_gestion');

        $usuarios = DB::select('SELECT usuarios.dni FROM usuarios where usuarios.clave = usuarios.dni ');

        foreach ($usuarios as $value) {
            $newPass = $value->dni;
            $passwordencryp = Hash::make($newPass);
            DB::table('usuarios')
                ->where('dni', $value->dni)
                ->update(['clave' => $passwordencryp]);
        }

        return redirect()->route('gth.usu.usuarios_gestion');
    }

    public function listar_por_cargos_agencia(Request $request)
    {

        $cargos = json_decode($request->cargos);
        $agencia = $request->agencia;


        return Usuario::from('usuarios as us')
            ->select(
                'us.dni',
                'us.nombres',
                'us.apellido_paterno',
                'us.usuario'
            )->join('cargos as car', 'us.cargo_id', 'car.id')
            ->where([
                ['us.agencia_id', $agencia],
                ['us.habilitado', 1]

            ])
            ->whereIn('car.cargo', $cargos)
            ->orderBy('us.usuario', 'asc')
            ->get();
    }
    // --------------------------------------------------------------------------
    public function mis_datos_personales(Request $request)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MI_INFORMACION', 'GTH_USUARIOS');

            if ($band == 1) {
                $dni = $x['usuario_dni'];
                $mi_usuario = Usuario::from('usuarios as us')->select(
                    'us.dni',
                    'us.usuario',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'us.sexo',
                    'us.distrito_id',
                    'us.provincia_id',
                    'us.departamento_id',
                    'us.fecha_nacimiento',
                    'us.direccion',
                    'us.telefono',
                    'us.correo_corporativo',
                    'ca.cargo',
                    'ag.nombre AS nombre_agencia',
                    'ha.tolerancia_personal',
                    'ho.hora_entrada_mañana',
                    'ho.hora_salida_mañana',
                    'ho.hora_entrada_tarde',
                    'ho.hora_salida_tarde',
                    'ho.hora_entrada_mañana_s',
                    'ho.hora_salida_mañana_s',
                    'ho.horario',
                    'ho.tolerancia as toleranciaHorario',
                )
                    ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                    ->join('cargos as ca', 'us.cargo_id', '=', 'ca.id')
                    ->join('asistencia_usuarios_horarios as ha', 'us.dni', '=', 'ha.usuario_id')
                    ->join('asistencia_horarios as ho', 'ha.horario_id', '=', 'ho.id')
                    ->where('us.dni', $dni)
                    ->orderBy('us.agencia_id', 'asc')->get();


                $distritos = Distrito::orderBy('distrito', 'asc')->get();
                $provincias = Provincia::orderBy('provincia', 'asc')->get();
                $departamentos = Departamento::orderBy('departamento', 'asc')->get();

                return Inertia::render('Gth/Usuarios/mi_informacion', [
                    'mi_usuario' => $mi_usuario,
                    'distritos' => $distritos,
                    'provincias' => $provincias,
                    'departamentos' => $departamentos,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function verificar_mi_usuario(Request $request)
    {
        $x = session()->all();
        $dni = $x['usuario_dni'];
        $usuario = $request->usuario;

        $existe_usuario = Usuario::select('dni', 'usuario')->where('usuario', $usuario)->get();

        if (count($existe_usuario) > 0) {

            if ($existe_usuario[0]['dni'] == $dni) {
                return "CORRECTO";
            } else {
                return "INCORRECTO";
            }
        } else {
            return "CORRECTO";
        }
    }
    public function guardar_mi_usuario(Request $request)
    {
        $x = session()->all();
        $dni = $x['usuario_dni'];

        $usuario = $request->usuario;
        $nombres = mb_strtoupper($request->nombres);
        $apellidoPaterno = mb_strtoupper($request->apellido_paterno);
        $apellidoMaterno = mb_strtoupper($request->apellido_materno);
        $sexo = $request->sexo;
        $direccion = $request->direccion;
        $id_distrito = $request->distrito_id;
        $id_departamento = $request->departamento_id;
        $id_provincia = $request->provincia_id;
        $fechaNacimiento = $request->fecha_nacimiento;
        $telefono = $request->telefono;
        $correoPrincipal = $request->correo_corporativo;

        $usurtartio = Usuario::where('dni', $dni)->update([
            // 'usuario' => $usuario,
            'nombres' => $nombres,
            'apellido_paterno' => $apellidoPaterno,
            'apellido_materno' => $apellidoMaterno,
            'sexo' => $sexo,
            'direccion' => $direccion,
            'distrito_id' => $id_distrito,
            'departamento_id' => $id_departamento,
            'provincia_id' => $id_provincia,
            'fecha_nacimiento' => $fechaNacimiento,
            'telefono' => $telefono,
            'correo_corporativo' => $correoPrincipal
        ]);

        return 'EXITO';
    }
    public function mora_resumen()

    {
        $x = session()->all();

        // dd($x);


        $dni = $x['usuario_dni'];
        $conexion = 'master_' . $x['id_agencia'];

        // dd($dni, $conexion);


        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;


        $rango = Credito::on($conexion)->select('id')
            ->where('estado_id', $estado_id)
            ->where('asesor_id', $dni)
            ->get();

        // dd($rango);

        $lista_creditos = [];

        $creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.asesor_id',
                DB::raw("COUNT(cre_reg.id) as cantidad_creditos"),
                DB::raw("COUNT(DISTINCT(cre_reg.cliente_id)) as cantidad_clientes"),
                DB::raw("SUM(cre_reg.capital_total) as capital_total"),
                DB::raw("SUM(cre_reg.capital_pagado) as capital_pagado"),
                DB::raw("SUM(cre_reg.saldo_total) as saldo_total"),

                'usu.usuario as usuario_asesor',

                DB::raw("(SELECT cre_cen_rie.id
                FROM credito_central_riesgo cre_cen_rie
                WHERE cre_reg.dias_atraso between cre_cen_rie.dias_desde
                AND cre_cen_rie.dias_hasta) as tipo_riesgo_id"),

            )
            ->join('solucion_master.usuarios as usu', 'cre_reg.asesor_id', 'usu.dni')

            ->whereIn('cre_reg.id', $rango)

            ->groupBy('asesor_id', 'tipo_riesgo_id')
            ->orderBy('usu.usuario', 'asc')
            ->orderBy('tipo_riesgo_id', 'asc')
            ->get();

        // dd($creditos);

        foreach ($creditos as $item) {
            $clientes_activos = Credito::on($conexion)
                ->whereIn('id', $rango)
                ->where('asesor_id', $item->asesor_id)
                ->distinct('cliente_id')->count();

            // dd($clientes_activos);

            $item->clientes_activos = $clientes_activos;

            $lista_creditos[] = $item;
        }


        $lista_creditos = collect($lista_creditos);

        $riesgos = CentralRiesgo::on($conexion)->select('id', 'nombre_breve')->get();

        foreach ($riesgos as $item) {

            foreach ($lista_creditos->where('tipo_riesgo_id', $item->id) as $item_1) {
                $item_1->tipo_riesgo = $item->nombre_breve;
            }
        }

        $lista_creditos = $lista_creditos->values();

        $cantidad_creditos = 0;
        $saldo_capital_total = 0;


        $resultado = (object)[
            'cliente_activos' => 0,
            'cantidad_creditos' => 0,
            'saldo_capital_total' => 0,

            'normal' => 0,
            'saldo_capital_normal' => 0,
            'porcentaje_normal' => 0,

            'cpp' => 0,
            'saldo_capital_cpp' => 0,
            'porcentaje_cpp' => 0,

            'deficiente' => 0,
            'saldo_capital_deficiente' => 0,
            'porcentaje_deficiente' => 0,

            'dudoso' => 0,
            'saldo_capital_dudoso' => 0,
            'porcentaje_dudoso' => 0,

            'perdida' => 0,
            'saldo_capital_perdida' => 0,
            'porcentaje_perdida' => 0,

            'perdida_total' => 0,
            'saldo_capital_perdida_total' => 0,
            'porcentaje_perdida_total' => 0,



        ];


        if (count($lista_creditos) != 0) {

            foreach ($lista_creditos as  $value) {

                $cantidad_creditos += $value->cantidad_creditos;

                $saldo_capital_total += $value->capital_total - $value->capital_pagado;


                if ($value->tipo_riesgo == 'NORMAL') {

                    $resultado->normal = $value->cantidad_creditos;
                    $resultado->saldo_capital_normal = $value->capital_total - $value->capital_pagado;
                }
                if ($value->tipo_riesgo == 'CPP') {

                    $resultado->cpp = $value->cantidad_creditos;
                    $resultado->saldo_capital_cpp = $value->capital_total - $value->capital_pagado;
                }
                if ($value->tipo_riesgo == 'DEFICIENTE') {

                    $resultado->deficiente = $value->cantidad_creditos;
                    $resultado->saldo_capital_deficiente = $value->capital_total - $value->capital_pagado;
                }
                if ($value->tipo_riesgo == 'DUDOSO') {

                    $resultado->dudoso = $value->cantidad_creditos;
                    $resultado->saldo_capital_dudoso = $value->capital_total - $value->capital_pagado;
                }
                if ($value->tipo_riesgo == 'PÉRDIDA') {

                    $resultado->perdida = $value->cantidad_creditos;
                    $resultado->saldo_capital_perdida = $value->capital_total - $value->capital_pagado;
                }
                if ($value->tipo_riesgo == 'TOTAL') {

                    $resultado->perdida_total = $value->cantidad_creditos;
                    $resultado->saldo_capital_perdida_total = $value->capital_total - $value->capital_pagado;
                }
            }
            $resultado->cliente_activos = $lista_creditos[0]->clientes_activos;
            $resultado->cantidad_creditos = $cantidad_creditos;
            $resultado->saldo_capital_total = $saldo_capital_total;

            $resultado->porcentaje_normal = round($resultado->saldo_capital_normal * 100 / $saldo_capital_total, 2);
            $resultado->porcentaje_cpp = round($resultado->saldo_capital_cpp * 100 / $saldo_capital_total, 2);
            $resultado->porcentaje_deficiente = round($resultado->saldo_capital_deficiente * 100 / $saldo_capital_total, 2);
            $resultado->porcentaje_dudoso = round($resultado->saldo_capital_dudoso * 100 / $saldo_capital_total, 2);
            $resultado->porcentaje_perdida = round($resultado->saldo_capital_perdida * 100 / $saldo_capital_total, 2);
            $resultado->porcentaje_perdida_total = round($resultado->saldo_capital_perdida_total * 100 / $saldo_capital_total, 2);
        };



        // dd($resultado);

        return ['lista_resumen' => $resultado];

        // dd($resultado);
    }
}
