<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gth\Usuarios\UsuarioController;

use App\Models\Gth\Usuarios\Usuario;
use App\Models\General\Usuarios_permiso;
use App\Models\General\Cargos_permiso;
use App\Models\General\Permiso;
use App\Models\General\Agencia;
use App\Models\General\Cargo;
use Illuminate\Http\Request;
use App\Http\Controllers\General\GeneralController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class PermisosController extends Controller
{

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------
    public function verificar_permiso(Request $request)
    {
        //dd($request);
        $modo = $request->modo;
        $modulo = $request->modulo;
        $id_permiso = $request->id_permiso;
        if ($modo == 'NUEVO') {
            $area = $request->areaNueva;
        } else {
            $area = $request->areaExistente;
        }
        // dd($area);
        $existe_permiso = Permiso::select('id')->where([['modulo', $modulo], ['area', $area]])
            ->get();

        if ($id_permiso == 0) {
            if (count($existe_permiso) == 0) {
                $resultado = "NO EXISTE";
            } else {
                $acceso_agencias = Usuarios_permiso::select('acceso_agencias')->where('permiso_id', $id_permiso)->where('usuario_id',)->get();
                $resultado = "EXISTE";
            }
        } else
            if ($id_permiso = !0) {
            if (count($existe_permiso) == 0) {
                $resultado = 'NO EXISTE';
            } else if ($existe_permiso[0]['id'] == $id_permiso) {
                $resultado = 'NO EXISTE';
            } else {
                $resultado = 'EXISTE';
            }
        }
        //dd($resultado);
        return $resultado;
    }




    public function guardar_permiso(Request $request)
    {
        // dd($request);
        $modal = $request->modal;
        $modo = $request->modo;
        $modulo = mb_strtoupper($request->modulo);
        $datos_registro = (new GeneralController)->datos_registro();
        if ($modo == 'NUEVO') {
            $area = mb_strtoupper($request->areaNueva);
        } else {
            $area = mb_strtoupper($request->areaExistente);
        }

        // dd($modal);
        if ($modal == 'AGREGAR') {
            Permiso::create(array(
                'modulo' => $modulo,
                'area' => $area,
                'datos_creacion' => $datos_registro,
            ));
            // $resultado = 'EXITO';
        } else if ($modal == 'EDITAR') {
            $permiso_id = $request->permiso_id;
            // dd($id_permiso, $area);
            Permiso::where('id', $permiso_id)
                ->update([
                    'modulo' => $modulo,
                    'area' => $area,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }

        return redirect()->route('gen.per.listar_permisos');
    }

    public function eliminar_permiso(Request $request)
    {
        $usuario_permiso_id = $request->id;
        Usuarios_permiso::where('id', $usuario_permiso_id)->delete();

        return redirect()->back();
    }
    public function eliminar_todos_permisos(Request $request)
    {
        $dni_a = $request->dni_a;
        $modo = $request->modo;

        if ($modo == 'completo') {

            Usuarios_permiso::where('usuario_id', $dni_a)->delete();
        } elseif ($modo == 'credito') {

            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'CREDITOS%')
                ->where('usuario_id', $dni_a)
                ->delete();
        } elseif ($modo == 'logistica') {

            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'LOGISTICA%')
                ->where('usuario_id', $dni_a)
                ->delete();
        } elseif ($modo == 'gth') {

            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'GTH%')
                ->where('usuario_id', $dni_a)
                ->delete();
        }
        $resultado = "EXITO";

        return $resultado;
    }


    //Funcion que recorre todo permisosagencias y busca el id_permiso
    public function getArrayFiltered($aFilterKey, $aFilterValue, $array, $keyarray)
    {
        $filtered_array = array();
        foreach ($array as $value) {
            if (isset($value[$aFilterKey])) {
                if ($value[$aFilterKey] == $aFilterValue) {
                    $filtered_array[][$keyarray] = $value[$keyarray];
                }
            }
        }
        return $filtered_array;
    }

    public function asignar_permiso(Request $request)
    {
        $dni = $request->dni;
        $permiso = new PermisosController();
        $permisosNuevos = $request->permisoSeleccionados;
        $permisosagencias = $request->permisosAgencia;
        $datos_registro = (new GeneralController)->datos_registro();
        foreach ($permisosNuevos as $id_permiso) {
            //dd($id_permiso);
            $acceso_agencias = $permiso->getArrayFiltered('permiso_id', $id_permiso, $permisosagencias, 'agencia_id');

            Usuarios_permiso::create(
                array(
                    'usuario_id' => $dni,
                    'permiso_id' => $id_permiso,
                    'datos_creacion' => $datos_registro,
                    'acceso_agencias' =>  json_encode($acceso_agencias),
                )
            );
        }
        return redirect()->back();
    }

    //--------------------FUNCION EDITAR ACCESOS DE AGENCIAS ----------------
    public function editar_permiso_agencia(Request $request)
    {
        $acceso_agencias = array();
        $lista_agencias_editar = $request->lista_agencias_editar;
        $id_usuario_permiso = $request->id;
        $datos_registro = (new GeneralController)->datos_registro();

        foreach ($lista_agencias_editar as $agencia) {
            $acceso_agencias[]['agencia_id'] = $agencia;
            json_encode($acceso_agencias);
        }


        Usuarios_permiso::where('id', $id_usuario_permiso)
            ->update([
                'acceso_agencias' =>  $acceso_agencias,
                'datos_actualizacion' => $datos_registro,
            ]);
        return redirect()->back();
    }

    //--------------------FUNCION PARA COPIAR PERMISO----------------------
    public function copiar_permiso(Request $request)
    {
        // dd($request);
        $dni_de = $request->dni_de;
        $dni_a = $request->dni_a;
        $modo = $request->modo;

        $datos_registro = (new GeneralController)->datos_registro();

        if ($modo == 'completo') {

            Usuarios_permiso::where('usuario_id', $dni_a)->delete();

            $permisosActualesDE = Usuarios_permiso::select('permiso_id', 'acceso_agencias')
                ->where('usuario_id', $dni_de)
                ->get();
        } elseif ($modo == 'credito') {

            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'CREDITOS%')
                ->where('usuario_id', $dni_a)
                ->delete();

            $permisosActualesDE = Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->select('permiso_id', 'acceso_agencias')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'CREDITOS%')
                ->where('usuario_id', $dni_de)
                ->get();
        } elseif ($modo == 'logistica') {

            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'LOGISTICA%')
                ->where('usuario_id', $dni_a)
                ->delete();
            $permisosActualesDE = Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->select('permiso_id', 'acceso_agencias')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'LOGISTICA%')
                ->where('usuario_id', $dni_de)
                ->get();
        } elseif ($modo == 'gth') {
            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'GTH%')
                ->where('usuario_id', $dni_a)
                ->delete();
            $permisosActualesDE = Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->select('permiso_id', 'acceso_agencias')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'GTH%')
                ->where('usuario_id', $dni_de)
                ->get();
        }


        foreach ($permisosActualesDE as $permisoActualDE) {
            Usuarios_permiso::create(array(
                'usuario_id' => $dni_a,
                'permiso_id' => $permisoActualDE->permiso_id,
                'acceso_agencias' => $permisoActualDE->acceso_agencias,
                'datos_creacion' => $datos_registro,
            ));
            // dd($permisosActualesDE);
        }
        return redirect()->back();
    }

    public function copiar_permiso_cargo(Request $request)
    {
        // dd($request);
        $cargo_id_de = $request->cargo_id_de;
        $dni_a = $request->dni_a;
        $modo = $request->modo;

        $datos_registro = (new GeneralController)->datos_registro();


        if ($modo == 'completo') {

            Usuarios_permiso::where('usuario_id', $dni_a)->delete();

            $permisosActualesDE = Cargos_permiso::select('permiso_id', 'acceso_agencias')
                ->where('cargo_id', $cargo_id_de)
                ->get();
        } elseif ($modo == 'credito') {

            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'CREDITOS%')
                ->where('usuario_id', $dni_a)
                ->delete();
            $permisosActualesDE = Cargos_permiso::from('cargos_permisos as car_per')

                ->select('permiso_id', 'acceso_agencias')
                ->join('permisos as p', 'p.id', 'car_per.permiso_id')
                ->where('cargo_id', $cargo_id_de)
                ->where('p.area', 'like', 'CREDITOS%')
                ->get();
        } elseif ($modo == 'logistica') {
            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'LOGISTICA%')
                ->where('usuario_id', $dni_a)
                ->delete();
            $permisosActualesDE = Cargos_permiso::from('cargos_permisos as car_per')

                ->select('permiso_id', 'acceso_agencias')
                ->join('permisos as p', 'p.id', 'car_per.permiso_id')
                ->where('cargo_id', $cargo_id_de)
                ->where('p.area', 'like', 'LOGISTICA%')
                ->get();
        } elseif ($modo == 'gth') {
            Usuarios_permiso::from('usuarios_permisos as usu_per')
                ->join('permisos as p', 'p.id', 'usu_per.permiso_id')
                ->where('p.area', 'like', 'GTH%')
                ->where('usuario_id', $dni_a)
                ->delete();
            $permisosActualesDE = Cargos_permiso::from('cargos_permisos as car_per')

                ->select('permiso_id', 'acceso_agencias')
                ->join('permisos as p', 'p.id', 'car_per.permiso_id')
                ->where('cargo_id', $cargo_id_de)
                ->where('p.area', 'like', 'GTH%')
                ->get();
        }


        foreach ($permisosActualesDE as $permisoActualDE) {
            Usuarios_permiso::create(array(
                'usuario_id' => $dni_a,
                'permiso_id' => $permisoActualDE->permiso_id,
                'acceso_agencias' => $permisoActualDE->acceso_agencias,
                'datos_creacion' => $datos_registro,
            ));
            // dd($permisosActualesDE);
        }
        return redirect()->back();
    }
    // ------------------------------


    public function index()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PRINCIPAL', 'GENERAL');
            if ($band == 1) {
                return Inertia::render('General/principal');
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }

    public function listar_permisos()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            $band = $this->verificarPermiso($x['usuario_dni'], 'PERMISOS', 'GENERAL_PERMISOS');

            if ($band == 1) {
                $modulos = Permiso::select('area')->distinct('id', 'area')->orderBy('area', 'asc')->get();
                $permisos = Permiso::select('id', 'area', 'modulo')->orderBy('area', 'asc')->get();
                return Inertia::render('General/Permisos/permisos', ['modulos' => $modulos, 'permisos' => $permisos]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function permisos_usuarios($modo)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            if ($modo == 'completo') {
                $band = $this->verificarPermiso($x['usuario_dni'], 'PERMISOS_USUARIOS', 'GENERAL_PERMISOS');
            } else if ($modo == 'credito') {
                $band = $this->verificarPermiso($x['usuario_dni'], 'PERMISOS_USUARIOS_CREDITO', 'GENERAL_PERMISOS');
            } else if ($modo == 'logistica') {
                $band = $this->verificarPermiso($x['usuario_dni'], 'PERMISOS_USUARIOS_LOGISTICA', 'GENERAL_PERMISOS');
            } else if ($modo == 'gth') {
                $band = $this->verificarPermiso($x['usuario_dni'], 'PERMISOS_USUARIOS_GTH', 'GENERAL_PERMISOS');
            }

            if ($band == 1) {


                $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();
                $usuarios = Usuario::from('usuarios AS us')

                    ->select(
                        'us.dni',
                        'nombres',
                        'apellido_paterno',
                        'apellido_materno',
                        'us.agencia_id',
                        'age.nombre AS nombreAgencia',
                        'us.usuario',
                        'us.cargo_id',
                        'c.cargo',
                    )
                    ->join('agencias AS age', 'us.agencia_id', '=', 'age.id_agencia')
                    ->join('cargos as c', 'us.cargo_id', '=', 'c.id')
                    ->where('us.habilitado', 1)
                    ->orderBy('nombres', 'asc')
                    ->get();
                $cargos = Cargo::select(
                    'id',
                    'cargo'
                )
                    ->orderBy('cargo', 'asc')
                    ->get();

                return Inertia::render(
                    'General/Permisos/permisos_usuarios',
                    [
                        'modo' => $modo,
                        'agencias' => $agencias,
                        'usuarios' => $usuarios,
                        'cargos' => $cargos
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

    public function editar_permiso($dni, $modo)
    {



        $datosUsuario = Usuario::from('usuarios AS us')
            ->select(
                'dni',
                'nombres',
                'apellido_paterno',
                'apellido_materno',
                'us.agencia_id',
                'age.nombre',
                'us.cargo_id',
                'car.cargo',
                'us.usuario'
            )
            ->join('agencias AS age', 'us.agencia_id', '=', 'age.id_agencia')
            ->join('cargos AS car', 'us.cargo_id', '=', 'car.id')
            ->where('dni', $dni)
            ->orderBy('nombres', 'asc')->get();

        $usuarios = Usuario::from('usuarios AS us')
            ->select(
                'car.cargo',
                'us.dni',
                'nombres',
                'apellido_paterno',
                'apellido_materno',
                'us.agencia_id',
                'age.nombre AS nombreAgencia',
                'us.usuario'
            )
            ->join('agencias AS age', 'us.agencia_id', '=', 'age.id_agencia')
            ->join('cargos AS car', 'us.cargo_id', '=', 'car.id')
            ->orderBy('nombres', 'asc')
            ->get();

        if ($modo == 'completo') {

            $permisosActuales = Permiso::from('permisos AS p')
                ->select('modulo', 'area', 'nombres', 'apellido_paterno', 'apellido_materno', 'up.id', 'up.acceso_agencias', 'p.id as permiso_id')
                ->join('usuarios_permisos AS up', 'up.permiso_id', '=', 'p.id')
                ->join('usuarios AS us', 'us.dni', '=', 'up.usuario_id')
                ->where('up.usuario_id', $dni)
                ->orderBy('area', 'asc')
                ->orderBy('modulo', 'asc')->get();

            $permisosDisponibles = Permiso::from('permisos AS p')
                ->select('id', 'modulo', 'area')
                ->whereNotIn('id', Usuarios_permiso::from('usuarios_permisos')
                    ->select('usuarios_permisos.permiso_id')
                    ->where('usuario_id', $dni))
                ->get();

            $areasDispos = Permiso::from('permisos AS p')
                ->select('area')->distinct('id', 'area')
                ->whereNotIn('id', Usuarios_permiso::from('usuarios_permisos')
                    ->select('usuarios_permisos.permiso_id')
                    ->where('usuario_id', $dni))
                ->get();

            $areasActuales = Permiso::from('permisos AS p')
                ->select('area')->distinct('id', 'area')
                ->join('usuarios_permisos AS up', 'up.permiso_id', '=', 'p.id')
                ->join('usuarios AS us', 'us.dni', '=', 'up.usuario_id')
                ->where('up.usuario_id', $dni)
                ->orderBy('area', 'asc')->get();
        } elseif ($modo == 'credito') {

            $permisosActuales = Permiso::from('permisos AS p')
                ->select('modulo', 'area', 'nombres', 'apellido_paterno', 'apellido_materno', 'up.id', 'up.acceso_agencias', 'p.id as permiso_id')
                ->join('usuarios_permisos AS up', 'up.permiso_id', '=', 'p.id')
                ->join('usuarios AS us', 'us.dni', '=', 'up.usuario_id')
                ->where('up.usuario_id', $dni)
                ->where('p.area', 'like', 'CREDITOS%')
                ->orderBy('area', 'asc')
                ->orderBy('modulo', 'asc')->get();

            $permisosDisponibles = Permiso::from('permisos AS p')
                ->select('id', 'modulo', 'area')
                ->whereNotIn('id', Usuarios_permiso::from('usuarios_permisos')
                    ->select('usuarios_permisos.permiso_id')
                    ->where('usuario_id', $dni))
                ->where('p.area', 'like', 'CREDITOS%')
                ->get();

            $areasDispos = Permiso::from('permisos AS p')
                ->select('area')->distinct('id', 'area')
                ->whereNotIn('id', Usuarios_permiso::from('usuarios_permisos')
                    ->select('usuarios_permisos.permiso_id')
                    ->where('usuario_id', $dni))
                ->where('p.area', 'like', 'CREDITOS%')
                ->get();

            $areasActuales = Permiso::from('permisos AS p')
                ->select('area')->distinct('id', 'area')
                ->join('usuarios_permisos AS up', 'up.permiso_id', '=', 'p.id')
                ->join('usuarios AS us', 'us.dni', '=', 'up.usuario_id')
                ->where('up.usuario_id', $dni)
                ->where('p.area', 'like', 'CREDITOS%')
                ->orderBy('area', 'asc')->get();
        } elseif ($modo == 'logistica') {

            $permisosActuales = Permiso::from('permisos AS p')
                ->select('modulo', 'area', 'nombres', 'apellido_paterno', 'apellido_materno', 'up.id', 'up.acceso_agencias', 'p.id as permiso_id')
                ->join('usuarios_permisos AS up', 'up.permiso_id', '=', 'p.id')
                ->join('usuarios AS us', 'us.dni', '=', 'up.usuario_id')
                ->where('up.usuario_id', $dni)
                ->where('p.area', 'like', 'LOGISTICA%')
                ->orderBy('area', 'asc')
                ->orderBy('modulo', 'asc')->get();

            $permisosDisponibles = Permiso::from('permisos AS p')
                ->select('id', 'modulo', 'area')
                ->whereNotIn('id', Usuarios_permiso::from('usuarios_permisos')
                    ->select('usuarios_permisos.permiso_id')
                    ->where('usuario_id', $dni))
                ->where('p.area', 'like', 'LOGISTICA%')
                ->get();

            $areasDispos = Permiso::from('permisos AS p')
                ->select('area')->distinct('id', 'area')
                ->whereNotIn('id', Usuarios_permiso::from('usuarios_permisos')
                    ->select('usuarios_permisos.permiso_id')
                    ->where('usuario_id', $dni))
                ->where('p.area', 'like', 'LOGISTICA%')
                ->get();

            $areasActuales = Permiso::from('permisos AS p')
                ->select('area')->distinct('id', 'area')
                ->join('usuarios_permisos AS up', 'up.permiso_id', '=', 'p.id')
                ->join('usuarios AS us', 'us.dni', '=', 'up.usuario_id')
                ->where('up.usuario_id', $dni)
                ->where('p.area', 'like', 'LOGISTICA%')
                ->orderBy('area', 'asc')->get();
        } elseif ($modo == 'gth') {

            $permisosActuales = Permiso::from('permisos AS p')
                ->select('modulo', 'area', 'nombres', 'apellido_paterno', 'apellido_materno', 'up.id', 'up.acceso_agencias', 'p.id as permiso_id')
                ->join('usuarios_permisos AS up', 'up.permiso_id', '=', 'p.id')
                ->join('usuarios AS us', 'us.dni', '=', 'up.usuario_id')
                ->where('up.usuario_id', $dni)
                ->where('p.area', 'like', 'GTH%')
                ->orderBy('area', 'asc')
                ->orderBy('modulo', 'asc')->get();

            $permisosDisponibles = Permiso::from('permisos AS p')
                ->select('id', 'modulo', 'area')
                ->whereNotIn('id', Usuarios_permiso::from('usuarios_permisos')
                    ->select('usuarios_permisos.permiso_id')
                    ->where('usuario_id', $dni))
                ->where('p.area', 'like', 'GTH%')
                ->get();

            $areasDispos = Permiso::from('permisos AS p')
                ->select('area')->distinct('id', 'area')
                ->whereNotIn('id', Usuarios_permiso::from('usuarios_permisos')
                    ->select('usuarios_permisos.permiso_id')
                    ->where('usuario_id', $dni))
                ->where('p.area', 'like', 'GTH%')
                ->get();

            $areasActuales = Permiso::from('permisos AS p')
                ->select('area')->distinct('id', 'area')
                ->join('usuarios_permisos AS up', 'up.permiso_id', '=', 'p.id')
                ->join('usuarios AS us', 'us.dni', '=', 'up.usuario_id')
                ->where('up.usuario_id', $dni)
                ->where('p.area', 'like', 'GTH%')
                ->orderBy('area', 'asc')->get();
        }




        $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();
        $cargos = Cargo::select(
            'id',
            'cargo',
            'descripcion'
        )
            ->orderBy('cargo', 'asc')
            ->get();


        $acceso_agencias = [];

        foreach ($permisosActuales as $permisoactual) {
            $jsonaccesoagencias = json_decode($permisoactual->acceso_agencias, true);
            if (!is_null($jsonaccesoagencias)) {
                foreach ($jsonaccesoagencias as $agencia) {
                    $acceso_agencias[] = ['permiso_id' => $permisoactual->permiso_id, 'agencia_id' => $agencia['agencia_id']];
                }
            }
        }

        foreach ($agencias as $agencia) {
            if ($agencia->nombre == 'EL TAMBO') {
                $agencia->nombre = 'TAMBO';
            }
        }

        return Inertia::render('General/Permisos/permisos_usuarios_editar', [
            'modo' => $modo,
            'datosUsuarios' => $datosUsuario,
            'dni' => $dni,
            'permisosActuales' => $permisosActuales,
            'permisosDisponibles' => $permisosDisponibles,
            'areasDispos' => $areasDispos,
            'areasActuales' => $areasActuales,
            'agencias' => $agencias,
            'usuarios' => $usuarios,
            'cargos' => $cargos,
            'acceso_agencias' => $acceso_agencias,
        ]);
    }



    // --------------------FUNCIÓN PARA VERIFICAR PERMISO-----------------------
    public function verificarPermiso($dni, $modulo, $area)
    {
        $band = 0;

        $id_permiso = Permiso::select('id')->where('modulo', $modulo)->where('area', $area)->get();

        $result = Usuarios_permiso::select('id')
            ->where('usuarios_permisos.permiso_id', $id_permiso[0]['id'])->where('usuarios_permisos.usuario_id', $dni)->get();

        if (empty($result[0]['id'])) {
            $band = 0;
        } else {
            $band = 1;
        }

        return $band;
    }
    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------


    // public function permisos_crear()
    // {

    //     $x = session()->all();

    //     if (empty($x['usuario_dni'])) {
    //         return view('welcome');
    //     } else {

    //         $band = $this->verificarPermiso($x['usuario_dni'], 'LISTAR PERMISOS', 'PERMISOS');

    //         if ($band == 1) {
    //             $lista_areas = Permiso::select('area')->distinct('area')->orderBy('area', 'asc')->get();
    //             return view('Permisos.permisos_crear')->with('areas', $lista_areas);
    //             die();
    //         } else {
    //             $mensajeTitulo = '¡Ups!';
    //             $mensajeContenido = 'No tienes permitido ver este contenido.';
    //             return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
    //             die();
    //         }
    //     }
    // }


    public function permisos_usuarios_editar($dni)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            $band = $this->verificarPermiso($x['usuario_dni'], 'PERMISOS_USUARIOS', 'GENERAL_PERMISOS');

            if ($band == 1) {
                $datosUsuario = Usuario::select('dni', 'nombres', 'apellido_paterno', 'apellido_materno')->where('dni', $dni)->orderBy('nombres', 'asc')->get();
                $permisosActuales = Usuarios_permiso::select('usuarios_permisos.id', 'permisos.id', 'permisos.modulo', 'permisos.area')
                    ->join('permisos', 'usuarios_permisos.permiso_id', '=', 'permisos.id')
                    ->where('usuarios_permisos.usuario_id', $dni)
                    ->orderBy('permisos.area', 'asc')
                    ->get();

                $permisosDisponibles = DB::select("SELECT id_permiso,modulo,area FROM permisos WHERE id_permiso NOT IN (
                    SELECT usuarios_permisos.permiso_id FROM usuarios_permisos
                    WHERE usuario_id = '$dni')");

                return view('Permisos.permisos_usuarios_editar')->with('usuario', $datosUsuario)->with('permisosActuales', $permisosActuales)->with('permisosDisponibles', $permisosDisponibles);
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


    public function permisos($area = NULL)
    {

        $area_val = $area;

        if (empty($area_val) or $area_val == "TODOS") {
            return Permiso::select('id', 'modulo', 'area')->orderBy('area', 'asc')->get();
        } else {
            return Permiso::select('id', 'modulo', 'area')->where('area', $area_val)->orderBy('area', 'asc')->get();
        }
    }

    public function permiso_guardar(Request $request)
    {

        $modulo = mb_strtoupper($request->permiso);

        if (empty($request->areaNueva)) {
            $area = mb_strtoupper($request->areaExistente);
        } else {
            $area = mb_strtoupper($request->areaNueva);
        }

        $existe = DB::select('SELECT id FROM permisos where modulo = :modulo AND area =:area', ['modulo' => $modulo, 'area' => $area]);

        if (empty($existe)) {
            DB::insert('INSERT INTO permisos (modulo,area) values (?, ?)', [$modulo, $area]);
            return redirect('/permisos_listar');
        } else {
            Session::flash('validar_permiso', 'Este permiso ya está creado, intente con otro NOMBRE y/o ÁREA.');
            return redirect('/permisos_crear');
            exit();
        }
    }
    // --------------------------------------------------------------------------
}
