<?php

namespace Modules\Creditos\Presentation\Controllers\Cuenta;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Creditos\Presentation\Controllers\Cuenta\CuentaMovimientoController;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;

use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CuentaController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function usuarios_cuenta()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'GESTION', 'CREDITOS_CUENTA');

            if ($band == 1) {

                return Inertia::render('Creditos/Cuenta/usuarios_cuenta');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar_usuarios(Request $request)
    {

        $agencia_id = $request->input('agencia_id');

        if ($agencia_id == 0 || $agencia_id == null) {

            return [];
        }

        $conexion = 'master_' .  $agencia_id;

        $agencia  = Agencia::find($agencia_id);
        $nombre_agencia = $agencia->nombre;

        $cuentas = CuentaUsuario::on($conexion)->from('cuenta_usuarios as us_cu')
            ->select(
                'us_cu.id',
                'us_cu.dni',
                'us_cu.con_cuenta',
                'us_cu.monto',
                'us.usuario',
                'ca.cargo',
                DB::raw("'" . $nombre_agencia . "' as agencia")
            )
            ->join('solucion_master.usuarios as us', 'us.dni', 'us_cu.dni')
            ->join('solucion_master.cargos as ca', 'us.cargo_id', 'ca.id')
            ->where([
                ['us.habilitado', 1]
            ])
            ->orderBy('us.usuario', 'ASC')
            ->get();

        return response()->json([
            'cuentas' => $cuentas
        ]);
    }
    public function listar_cuentas(Request $request)
    {
        $agencia_id = $request->agencia_id;
        if ($agencia_id == 0 || $agencia_id == null) {

            return [];
        }

        $conexion = 'master_' .  $agencia_id;

        return  CuentaUsuario::on($conexion)->from('cuenta_usuarios as usu_cue')
            ->select(
                'usu_cue.id',
                'usu_cue.dni',
                'usu_cue.con_cuenta',
                'usu_cue.monto',
                'usu.usuario',

                'car.cargo',

                'age.nombre as agencia',
                'age.id_agencia as agencia_id'
            )
            ->join('solucion_master.usuarios as usu', 'usu.dni', 'usu_cue.dni')
            ->join('solucion_master.cargos as car', 'usu.cargo_id', 'car.id')
            ->join('solucion_master.agencias as age', 'usu.agencia_id', 'age.id_agencia')
            ->where([
                ['usu_cue.con_cuenta', 1],
                ['usu.habilitado', 1]
            ])
            ->orderBy('usu.usuario', 'ASC')
            ->get();
    }
    public function mi_cuenta()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MI_CUENTA', 'CREDITOS_CUENTA');

            if ($band == 1) {

                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;

                $tu_cuenta = CuentaUsuario::on($conexion)->from('cuenta_usuarios as us_cu')
                    ->select(
                        'us_cu.id',
                        'us_cu.dni',
                        'us_cu.monto',
                        'us_cu.con_cuenta',

                        'us.agencia_id',
                        'us.usuario',

                        'ag.nombre as agencia',
                    )
                    ->join('solucion_master.usuarios as us', 'us.dni', 'us_cu.dni')
                    ->join('solucion_master.agencias as ag', 'us.agencia_id', 'ag.id_agencia')
                    ->where('us_cu.dni', $x['usuario_dni'])
                    ->get()->last();

                return Inertia::render('Creditos/Cuenta/mi_cuenta', [
                    'agencia_id' => intval($agencia_id),
                    'tu_cuenta' => $tu_cuenta
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function mostrar_cuentas()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MOSTRAR_CUENTAS', 'CREDITOS_CUENTA');

            if ($band == 1) {

                return Inertia::render('Creditos/Cuenta/mostrar_cuentas');
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
    public function habilitar_deshabilitar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $dni = $request->dni;
        $con_cuenta = $request->con_cuenta;
        CuentaUsuario::on($conexion)->where('dni', $dni)
            ->update([
                'con_cuenta' => $con_cuenta,
                'datos_actualizacion' => $datos_registro
            ]);
        return redirect()->route('cue.usuarios_cuenta');
    }
    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------

}
