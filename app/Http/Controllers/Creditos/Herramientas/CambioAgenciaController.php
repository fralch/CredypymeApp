<?php

namespace App\Http\Controllers\Creditos\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\General\PermisosController;

use App\Models\General\Agencia;
use App\Models\Creditos\Cuenta\CuentaUsuario;
use App\Models\Gth\Usuarios\Usuario;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CambioAgenciaController extends Controller
{
    public function cambio_agencia()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAMBIO_AGENCIA', 'CREDITOS_HERRAMIENTAS');
            if ($band == 1) {
                return Inertia::render('Creditos/Herramientas/cambio_agencia');
            } else {
                return redirect('/');
            }
        }
    }

    public function guardar(Request $request)
    {
        try {
            DB::beginTransaction();

            $usuario_id = $request->usuario_id;

            $agencia_actual = $request->agencia_actual;
            $agencia_nueva = $request->agencia_nueva;

            $conexion_actual = 'master_' . $agencia_actual;
            $conexion_nueva = 'master_' . $agencia_nueva;
            $datos_registro = (new CreditosController)->datos_registro($agencia_nueva);

            $tiene_cuenta = CuentaUsuario::on($conexion_nueva)->where('dni', $usuario_id)->get()->last();

            if ($tiene_cuenta == null) {
                // DATOS DE CUENTA ACTUAL
                $cuenta_actual = CuentaUsuario::on($conexion_actual)->where('dni', $usuario_id)->get()->last();
                $con_cuenta = $cuenta_actual->con_cuenta;

                // CREAR CUENTA
                CuentaUsuario::on($conexion_nueva)->create([
                    'dni' => $usuario_id,
                    'con_cuenta' => $con_cuenta,
                    'monto' => 0,
                    'datos_creacion' => $datos_registro
                ]);
            }

            // CAMBIAR DE AGENCIA

            Usuario::where('dni', $usuario_id)->update([
                'agencia_id' => $agencia_nueva,
                'datos_actualizacion' => $datos_registro
            ]);

            // MODIFICAR SESIÓN

            session()->forget('id_agencia');
            session()->forget('nombre_agencia');

            // APERTURAR SESIÓN

            $agencia = Agencia::find($agencia_nueva);

            session(['id_agencia' => intval($agencia_nueva)]);
            session(['nombre_agencia' => $agencia->nombre]);

            DB::commit();
            return response()->json(['message' => 'success'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
