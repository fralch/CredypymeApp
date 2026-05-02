<?php

namespace Modules\Creditos\Presentation\Controllers\Mantenimiento\Credito;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Producto;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Subproducto;
use Modules\Creditos\Presentation\Controllers\CreditosController;


use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CreditoSubproductoController extends Controller
{
    public function subproductos()
    {

        $x = session()->all();
        $conexion = 'master_' .  $x['id_agencia'];


        if (empty($x['usuario_dni'])) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITO_SUBPRODUCTOS', 'CREDITOS_MANTENIMIENTO');
            if ($band == 1) {
                $subproductos = Subproducto::on($conexion)->from('credito_subproductos as cre_sub_pro')
                    ->select(
                        'cre_sub_pro.id',
                        'cre_sub_pro.subproducto',
                        'cre_sub_pro.producto_id',
                        'cre_pro.producto',
                        'cre_sub_pro.descripcion',
                        'cre_sub_pro.habilitado'

                    )
                    ->join('credito_productos as cre_pro', 'cre_sub_pro.producto_id', 'cre_pro.id')
                    ->get();
                $productos = Producto::on($conexion)->select()->get();
                return Inertia::render(
                    'Creditos/Mantenimiento/credito_subproductos',
                    [
                        'subproductos' => $subproductos,
                        'productos' => $productos
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
    public function listarSubproductos(Request $request)
    {
        // return $request;
        $id_agencia = $request->id_agencia;
        $conexion = 'master_' .  $id_agencia;
        $subproductos = Subproducto::on($conexion)->from('credito_subproductos as cre_sub_pro')
            ->select(
                'cre_sub_pro.id',
                'cre_sub_pro.subproducto',
                'cre_sub_pro.producto_id',
                'cre_pro.producto',
                'cre_sub_pro.descripcion',
                'cre_sub_pro.habilitado'

            )
            ->join('credito_productos as cre_pro', 'cre_sub_pro.producto_id', 'cre_pro.id')
            ->get();
        return $subproductos;
    }
    public function verificar(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;


        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $subproducto = $request->subproducto;
        $producto_id = $request->producto_id;

        $subproducto_existe = Subproducto::on($conexion)->select('id')
            ->where([['subproducto',  $subproducto], ['producto_id', $producto_id]])
            ->get();

        if ($modo == "EDITAR") {
            if (count($subproducto_existe) > 0) {
                if ($subproducto_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($subproducto_existe) > 0) {
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
        $subproducto = mb_strtoupper($request->subproducto);
        $producto_id = $request->producto_id;
        $descripcion = (new CreditosController)->verificar_nulo($request->descripcion);

        if ($descripcion != null) {
            $descripcion = mb_strtoupper($descripcion);
        }

        $habilitado = $request->habilitado;


        if ($modo == "NUEVO") {
            Subproducto::on($conexion)->create(array(
                'subproducto' =>  $subproducto,
                'producto_id' => $producto_id,
                'habilitado' => 1,
                'descripcion' => $descripcion,
                'datos_creacion' => $datos_registro,

            ));
        } else if ($modo == "EDITAR") {
            $id = $request->id;
            Subproducto::on($conexion)->where('id', $id)
                ->update([
                    'subproducto' =>  $subproducto,
                    'producto_id' => $producto_id,
                    'habilitado' => $habilitado,
                    'descripcion' => $descripcion,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('man.cre_subproductos');
    }
}
