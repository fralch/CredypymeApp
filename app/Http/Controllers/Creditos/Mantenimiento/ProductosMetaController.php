<?php

namespace App\Http\Controllers\Creditos\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\General\PermisosController;
use App\Models\Creditos\Mantenimiento\Inversion\ProductosMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductosMetaController extends Controller
{
    public function productos_meta()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'INVERSION_PRODUCTOS_META', 'CREDITOS_MANTENIMIENTO');
            if ($band == 1) {
                return Inertia::render(
                    'Creditos/Mantenimiento/inversion_productos_meta'

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
    public function listar(Request $request)
    {
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;
        $productos_meta = ProductosMeta::on($conexion)->get();

        return ['productos_meta' => $productos_meta];
    }

    public function verificar(Request $request)

    {
        $resultado = "NO EXISTE";
        $agencia_id = $request->agencia_seleccionada;
        $modo = $request->modo;
        $id = $request->id;
        $producto = $request->producto;

        $conexion = 'master_' .  $agencia_id;
        $producto_meta_existe = ProductosMeta::on($conexion)->select('id')->where('producto', $producto)->get();

        if ($modo == "EDITAR") {
            if (count($producto_meta_existe) > 0) {
                if ($producto_meta_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($producto_meta_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)

    {
        $agencia_id = $request->agencia_seleccionada;
        $conexion = 'master_' .  $agencia_id;
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $modo = $request->modo;
        $producto = mb_strtoupper($request->producto);

        $descripcion = (new CreditosController)->verificar_nulo($request->descripcion);
        $orientacion = (new CreditosController)->verificar_nulo($request->orientacion);

        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        if ($orientacion != null) {
            $orientacion = mb_strtoupper($request->orientacion);
        };

        $valor_meta = $request->valor_meta;

        if ($modo == "NUEVO") {
            ProductosMeta::on($conexion)->create(
                [
                    'producto' => $producto,
                    'valor_meta' => $valor_meta,
                    'descripcion' => $descripcion,
                    'orientacion' => $orientacion,
                    'habilitado' => 1,
                    'datos_creacion' => $datos_registro,
                ]

            );
        } else if ($modo == 'EDITAR') {
            $habilitado = $request->habilitado;
            $id = $request->id;
            ProductosMeta::on($conexion)->where('id', $id)
                ->update([
                    'producto' => $producto,
                    'valor_meta' => $valor_meta,
                    'descripcion' => $descripcion,
                    'orientacion' => $orientacion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('man.inversion.productos_meta');
    }
}
