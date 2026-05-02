<?php

namespace Modules\Creditos\Presentation\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;


use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Categoria;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Comprobante;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\TransaccionCategoria;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Subcategoria;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TransaccionComprobanteController extends Controller
{
    //
    public function comprobantes()
    {
        $x = session()->all();
        $conexion = 'master_' .  $x['id_agencia'];


        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSACCION_COMPROBANTES', 'CREDITOS_MANTENIMIENTO');
            // $band = 1; // No  de esto
            if ($band == 1) {
                $comprobantes = Comprobante::on($conexion)->select()->get();

                return Inertia::render('Creditos/Mantenimiento/Transacciones-Comprobante', [
                    'comprobantes' => $comprobantes,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listarComprobantes(Request $request)
    {
        // return $request;
        $id_agencia = $request->id_agencia;
        $conexion = 'master_' .  $id_agencia;
        return $comprobantes = Comprobante::on($conexion)->select()->get();
    }
    public function validarExiste(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $validar_duplicados =  Comprobante::on($conexion)->where('comprobante', $request->comprobante)->get();

        if (count($validar_duplicados)) {
            return 'EXISTE';
        } else {
            return 'NO_EXISTE';
        }
    }

    public function guardarComprobante(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;
        // return $request;
        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));

        if ($request->modo == 1) {
            Comprobante::on($conexion)->create([
                'comprobante' => strtoupper($request->comprobante),
                'descripcion' => strtoupper($request->descripcion),
                'habilitado' => 1,
                'datos_creacion' => $datos_registro
            ]);
        } elseif ($request->modo == 0) {
            Comprobante::on($conexion)->where('id', $request->id)
                ->update([
                    'comprobante' => strtoupper($request->comprobante),
                    'descripcion' => strtoupper($request->descripcion),
                    'habilitado' => $request->habilitado,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return redirect()->route('mant.transaccion_comprobantes');
    }
}
