<?php

namespace Modules\Logistica\Presentation\Controllers\Suministros;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Logistica\Presentation\Controllers\LogisticaController;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Suministros\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuministroProveedorController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function proveedores()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'SUMINISTROS_PROVEEDORES', 'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {

                $proveedores = Proveedor::all();

                return Inertia::render(
                    'Logistica/Mantenimiento/suministros_proveedores',
                    ['proveedores' => $proveedores]
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
    public function verificar(Request $request)
    {
        $resultado = 'NO EXISTE';
        $modo = $request->modo;
        $id = $request->id;
        $proveedor = $request->proveedor;

        $proveedor_existe = Proveedor::select('id')
            ->where('proveedor',  $proveedor)
            ->get();

        if ($modo == "EDITAR") {
            if (count($proveedor_existe) > 0) {
                if ($proveedor_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($proveedor_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {

        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $proveedor = mb_strtoupper($request->proveedor);
        $direccion = mb_strtoupper($request->direccion);

        $representante = (new LogisticaController)->verificar_nulo($request->representante);
        if ($representante != null) {
            $representante = mb_strtoupper($representante);
        }
        $ruc = (new LogisticaController)->verificar_nulo($request->ruc);
        $dni = (new LogisticaController)->verificar_nulo($request->dni);
        $telefono = (new LogisticaController)->verificar_nulo($request->telefono);
        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {

            Proveedor::create(
                [
                    'proveedor' => $proveedor,
                    'direccion' => $direccion,
                    'ruc' => $ruc,
                    'telefono' => $telefono,
                    'representante' => $representante,
                    'dni' => $dni,
                    'habilitado' => $habilitado,
                    'datos_creacion' => $datos_registro
                ]

            );
        } else if ($modo == "EDITAR") {
            $id = $request->id;
            Proveedor::where('id', $id)
                ->update([
                    'proveedor' => $proveedor,
                    'direccion' => $direccion,
                    'ruc' => $ruc,
                    'telefono' => $telefono,
                    'representante' => $representante,
                    'dni' => $dni,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return redirect()->route('log.man.sum_proveedores');
    }

    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------

}
