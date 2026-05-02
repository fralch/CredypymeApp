<?php

namespace App\Http\Middleware;

use Modules\General\Infrastructure\Persistence\Eloquent\Dispositivo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {

        if (session('usuario_dni') == null) {

            $permisos_array = null;
            $permisos_object = null;
            $datos_aplicacion = null;
            $datos_aplicacion_local = null;
            $agencias = null;
            $version = null;
            $datos_cuenta = null;
            $datos_caja = null;
            $datos_carrito = null;
        } else {

            $habilitado  = Usuario::where([
                ['dni', session('usuario_dni')],
                ['habilitado', 1]
            ])->get()->last();

            if ($habilitado == null) {

                session()->forget('usuario_dni');
                session()->forget('usuario');
                session()->forget('nombres');
                session()->forget('id_agencia');
                session()->forget('nombre_agencia');
                session()->forget('dispositivo');

                $permisos_array = null;
                $permisos_object = null;
                $datos_aplicacion = null;
                $datos_aplicacion_local = null;
                $agencias = null;
                $version = null;
                $datos_cuenta = null;
                $datos_caja = null;
                $datos_carrito = null;
            } else {
                $permisos = DB::table('usuarios_permisos')
                    ->join('permisos', 'usuarios_permisos.permiso_id', '=', 'permisos.id')
                    ->select('permisos.area', 'permisos.modulo', 'permiso_id', 'acceso_agencias')
                    ->where('usuarios_permisos.usuario_id', session('usuario_dni'))
                    ->get();

                $datos_aplicacion = DB::table('datos_aplicacion')->get();
                $version = DB::select("SELECT * FROM versiones ORDER by id_version DESC LIMIT 1");
                $agencias = DB::table('agencias')->select('id_agencia as id', 'nombre as agencia', 'direccion', 'dis.distrito', 'nueva_empresa')
                    ->join('distritos as dis', 'agencias.distrito_id', 'dis.id')
                    ->orderBy('agencia', 'asc')
                    ->get();

                foreach ($permisos as $permiso) {
                    $permisos_array[] = $permiso->area . '/' . $permiso->modulo;
                    $permisos_object[] = [
                        'permiso_id' => $permiso->permiso_id,
                        'permiso' => $permiso->area . '/' . $permiso->modulo,
                        'acceso_agencias' => $permiso->acceso_agencias
                    ];
                }

                $conexion = 'master_' .  session('id_agencia');

                // Datos de la aplicación segun agencia del usuario

                $datos_aplicacion_local = DB::connection($conexion)->table('datos_aplicacion')->get();
                // Verificar si el usuario tiene una cuenta activa
                $datos_cuenta = DB::connection($conexion)->table('cuenta_usuarios as cue_usu')
                    ->where([['dni', session('usuario_dni')], ['con_cuenta', 1]])
                    ->get()->last();

                $datos_carrito = null;

                $agencia_id = intval(session('id_agencia'));

                $datos_carrito = DB::connection($conexion)->table('credito_carritos as cre_car')
                    ->select(
                        'id',
                        'fecha_apertura',
                        DB::raw("$agencia_id as agencia_id")
                    )
                    ->where([['usuario_id', session('usuario_dni')], ['fecha_cierre', null]])
                    ->get()->last();

                if (empty((array)$datos_cuenta)) {
                    $datos_cuenta = null;
                }

                // ----------------------------------------------------
                // Verificar si el usuario tiene una caja aperturada
                $datos_caja = DB::connection($conexion)->table('caja_registros')->where([
                    ['dni', session('usuario_dni')],
                    ['datos_cierre', null]
                ])->get()->last();

                if (empty((array)$datos_caja)) {
                    $datos_caja = null;
                }
                // ----------------------------------------------------
            }
        }


        return array_merge(parent::share($request), [
            //

            'appName' => config('app.name'),

            // Lazily
            'user_session' => fn() => $request->session()
                ? $request->session()->all()
                : null,

            'user_permissions' => fn() => $request->session()
                ?   [
                    'permisos' => $permisos_array,
                    'permisos_detalle' => $permisos_object,
                ]
                : [
                    'permisos' => null,
                    'permisos_detalle' => null,
                ],

            'application' => fn() => $request->session()
                ?   [
                    'data' => $datos_aplicacion,
                    'data_local' => $datos_aplicacion_local,
                    'version' => $version,
                    'agencias' => $agencias

                ]
                : [
                    'data' => null,
                    'data_local' => null,
                    'version' => null,
                    'agencias' => null
                ],



            'creditos_datos' => fn() => $request->session() ?
                [
                    'datos_caja' => $datos_caja,
                    'datos_cuenta' => $datos_cuenta,
                    'datos_carrito' => $datos_carrito
                ]
                : [
                    'datos_caja' => null,
                    'datos_cuenta' => null,
                    'datos_carrito' => null,
                ]
        ]);
    }
}
