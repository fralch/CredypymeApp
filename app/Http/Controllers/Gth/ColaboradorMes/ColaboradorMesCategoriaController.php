<?php

namespace App\Http\Controllers\Gth\ColaboradorMes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gth\GthController;
use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\ColaboradorMes\Categoria;
use App\Models\Gth\ColaboradorMes\Nivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ColaboradorMesCategoriaController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function categorias()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'COLABORADOR_MES_CATEGORIAS', 'GTH_MANTENIMIENTO');
            if ($band == 1) {

                return Inertia('Gth/Mantenimiento/ColaboradorMes/categorias');
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

    public function listar_recursos()
    {
        $categorias = Categoria::from('colaboradormes_categorias as cm_ca')->select(
            'cm_ca.id',
            'cm_ca.categoria',
            'cm_ca.descripcion',
            'cm_ca.peso',
            'cm_ca.nivel_id',
            'cm_ni.nivel',
            'cm_ca.habilitado',
        )
            ->leftjoin('colaboradormes_niveles as cm_ni', 'cm_ni.id', '=', 'cm_ca.nivel_id')
            ->orderBy('cm_ca.categoria', 'asc')->get();

        $niveles = Nivel::where('habilitado', 1)->orderBy('nivel', 'asc')->get();

        return ['categorias' => $categorias, 'niveles' => $niveles];
    }

    public function verificar(Request $request)
    {
        $modo = $request->modo;
        $id = $request->id;
        $categoria = $request->categoria;

        $resultado = 'NO EXISTE';
        if ($modo == 'NUEVO') {
            $categoria_existe = Categoria::select('id')->where('categoria', $categoria)->get();
            if (count($categoria_existe) > 0) {
                $resultado = 'EXISTE';
            } else {
                $resultado = 'NO EXISTE';
            }
        } else if ($modo == 'EDITAR') {
            $categoria_existe = Categoria::select('id')->where('categoria', $categoria)->get();
            if (count($categoria_existe) > 0) {
                if ($categoria_existe[0]['id'] == $id) {
                    $resultado = 'NO EXISTE';
                } else {
                    $resultado = 'EXISTE';
                }
            } else {
                $resultado = 'NO EXISTE';
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $response = new \stdClass();

        $datos_registro = (new GthController)->datos_registro();
        $modo = $request->modo;
        $categoria = strtoupper($request->categoria);
        $descripcion = strtoupper($request->descripcion);

        $peso = $request->peso;
        $nivel_id = $request->nivel_seleccionado;
        $habilitado = $request->habilitado;

        if ($habilitado == "true") {
            $habilitado = 1;
        }

        if ($habilitado == "false") {
            $habilitado = 0;
        }

        if ($modo == 'NUEVO') {

            Categoria::create(array(
                'categoria' => $categoria,
                'descripcion' => $descripcion,
                'peso' => $peso,
                'nivel_id' => $nivel_id,
                'habilitado' => $habilitado,
                'datos_creacion' => $datos_registro
            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Categoria::where('id', $id)->update([
                'categoria' => $categoria,
                'descripcion' => $descripcion,
                'peso' => $peso,
                'nivel_id' => $nivel_id,
                'habilitado' => $habilitado,
                'datos_actualizacion' => $datos_registro
            ]);
        }

        $response->success = true;
        return $response;
    }

    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------
}
