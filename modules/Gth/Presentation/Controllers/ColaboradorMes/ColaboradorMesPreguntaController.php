<?php

namespace Modules\Gth\Presentation\Controllers\ColaboradorMes;

use Modules\Gth\Presentation\Controllers\GthController;
use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Pregunta;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ColaboradorMesPreguntaController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function preguntas()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PREGUNTAS', 'GTH_COLABORADOR_MES');
            if ($band == 1) {

                return Inertia('Gth/ColaboradorMes/preguntas');
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
        $categorias = Categoria::orderBy('categoria', 'asc')->get();
        $preguntas = Pregunta::from('colaboradormes_preguntas as cm_pre')
            ->select(
                'cm_pre.id',
                'cm_pre.pregunta',
                'cm_pre.criterio',
                'cm_cat.categoria as nombre_categoria',
                'cm_cat.peso as peso_categoria',
                'cm_cat.id as categoria_id',
                'cm_pre.habilitado',

            )
            ->join('colaboradormes_categorias as cm_cat', 'cm_pre.categoria_id', 'cm_cat.id')
            ->get();

        return [
            'preguntas' => $preguntas,
            'categorias' => $categorias
        ];
    }
    public function verificar(Request $request)
    {
        $modo = $request->modo;
        $id = $request->id;
        $pregunta = $request->pregunta;

        $resultado = 'NO EXISTE';
        if ($modo == 'NUEVO') {
            $pregunta_existe = Pregunta::select('id')->where('pregunta', $pregunta)->get();
            if (count($pregunta_existe) > 0) {
                $resultado = 'EXISTE';
            } else {
                $resultado = 'NO EXISTE';
            }
        } else if ($modo == 'EDITAR') {
            $pregunta_existe = Pregunta::select('id')->where('pregunta', $pregunta)->get();
            if (count($pregunta_existe) > 0) {
                if ($pregunta_existe[0]['id'] == $id) {
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
        $response = new \stdClass;

        $datos_registro = (new GthController)->datos_registro();
        $modo = $request->modo;
        $pregunta = strtoupper($request->pregunta);

        $categoria_id = $request->categoria_id;
        $criterio = strtoupper($request->criterio);
        $habilitado = $request->habilitado;

        if ($habilitado == "true") {
            $habilitado = 1;
        }

        if ($habilitado == "false") {
            $habilitado = 0;
        }

        if ($modo == 'NUEVO') {
            Pregunta::create(array(
                'pregunta' => $pregunta,
                'criterio' => $criterio,
                'categoria_id' => $categoria_id,
                'habilitado' => $habilitado,
                'datos_creacion' => $datos_registro

            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Pregunta::where('id', $id)->update([
                'pregunta' => $pregunta,
                'criterio' => $criterio,
                'categoria_id' => $categoria_id,
                'habilitado' => $habilitado,
                'datos_actualizacion' => $datos_registro
            ]);
        }

        $response->success = true;

        return $response;
    }
}
