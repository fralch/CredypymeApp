<?php

namespace App\Http\Controllers\Creditos\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;


use App\Models\General\Agencia;
use App\Models\General\Datos_aplicacion;

use App\Models\Creditos\Cuenta\CuentaUsuario;
use App\Models\Creditos\Mantenimiento\Transacciones\Categoria;
use App\Models\Creditos\Mantenimiento\Transacciones\Comprobante;
use App\Models\Creditos\Mantenimiento\TransaccionCategoria;
use App\Models\Creditos\Mantenimiento\Subcategoria;

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
