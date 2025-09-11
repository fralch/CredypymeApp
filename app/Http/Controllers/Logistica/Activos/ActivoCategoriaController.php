<?php

namespace App\Http\Controllers\Logistica\Activos;

use App\Models\Logistica\Activos\Categoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Logistica\LogisticaController;
use Inertia\Inertia;

class ActivoCategoriaController extends Controller
{
    public function categorias()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ACTIVOS_CATEGORIAS',  'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $categorias = Categoria::all();
                return Inertia::render('Logistica/Mantenimiento/activos_categorias')
                    ->with('categorias', $categorias);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function verificar(Request $request)
    {
        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $categoria = $request->categoria;

        $categoria_existe = Categoria::select('id')
            ->where('categoria', $categoria)
            ->get();

        if ($modo == "EDITAR") {
            if (count($categoria_existe) > 0) {
                if ($categoria_existe[0]['id'] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($categoria_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $categoria = mb_strtoupper($request->categoria);

        $descripcion = (new LogisticaController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($descripcion);
        };

        $vida_util = $request->vida_util;
        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Categoria::create(array(
                'categoria' =>  $categoria,
                'descripcion' => $descripcion,
                'vida_util' => $vida_util,
                'habilitado' => 1,
                'datos_creacion' => $datos_registro,
            ));
        } else {
            $id = $request->id;
            Categoria::where('id', $id)->update([
                'categoria' =>  $categoria,
                'vida_util' => $vida_util,
                'habilitado' => $habilitado,
                'descripcion' => $descripcion,
                'datos_actualizacion' => $datos_registro,
            ]);
        }
        return redirect()->route('log.man.act_categorias');
    }
}
