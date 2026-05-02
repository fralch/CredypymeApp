<?php

namespace Modules\Creditos\Presentation\Controllers\Mantenimiento\Credito;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Producto;


use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CreditoProductoController extends Controller
{
    public function productos()
    {
        $x = session()->all();
        $conexion = 'master_' .  $x['id_agencia'];

        if (empty($x['usuario_dni'])) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITO_PRODUCTOS', 'CREDITOS_MANTENIMIENTO');
            if ($band == 1) {
                $productos = Producto::on($conexion)->select()->get();
                return Inertia::render(
                    'Creditos/Mantenimiento/credito_productos',
                    ['productos' => $productos]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------

    public function listarProductos(Request $request)
    {
        // return $request;
        $id_agencia = $request->id_agencia;
        $conexion = 'master_' .  $id_agencia;
        $productos = Producto::on($conexion)->select()->get();
        return $productos;
    }
    public function verificar(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $producto = $request->producto;

        $producto_existe = Producto::on($conexion)->select('id')->where('producto', $producto)->get();

        if ($modo == "EDITAR") {
            if (count($producto_existe) > 0) {
                if ($producto_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($producto_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));

        $modo = $request->modo;
        $producto = mb_strtoupper($request->producto);

        $descripcion = (new CreditosController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Producto::on($conexion)->create(
                [
                    'producto' => $producto,
                    'descripcion' => $descripcion,
                    'habilitado' => 1,
                    'datos_creacion' => $datos_registro,
                ]

            );
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Producto::on($conexion)->where('id', $id)
                ->update([
                    'producto' => $producto,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('man.cre_productos');
    }
    // --------------------------------------------------------------------------

}
