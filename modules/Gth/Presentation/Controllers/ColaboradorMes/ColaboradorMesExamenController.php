<?php

namespace Modules\Gth\Presentation\Controllers\ColaboradorMes;


use Modules\Gth\Presentation\Controllers\GthController;
use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Categoria;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Pregunta;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Examen;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\ExamenPregunta;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ColaboradorMesExamenController extends Controller
{
    public function examenes()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'EXAMENES', 'GTH_COLABORADOR_MES');
            if ($band == 1) {

                return Inertia('Gth/ColaboradorMes/examenes');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar_recursos()
    {
        $examenes = Examen::orderBy('examen', 'asc')->get();

        $examenes_preguntas = ExamenPregunta::from('colaboradormes_examen_preguntas as cm_exp')
            ->select(
                'cm_exp.examen_id',
                'cm_exp.pregunta_id',
                'cm_pre.pregunta',
                'cm_pre.criterio',
                'cm_pre.categoria_id',
                'cm_cat.categoria as nombre_categoria',
                'cm_cat.peso'
            )
            ->join('colaboradormes_preguntas as cm_pre', 'cm_exp.pregunta_id', '=', 'cm_pre.id')
            ->join('colaboradormes_categorias as cm_cat', 'cm_pre.categoria_id', '=', 'cm_cat.id')
            ->get();

        $categorias = Categoria::orderBy('categoria', 'asc')->get();

        $preguntas = Pregunta::from('colaboradormes_preguntas as cm_pre')
            ->select(
                'cm_pre.id',
                'cm_pre.pregunta',
                'cm_pre.criterio',
                'cm_cat.categoria as nombre_categoria',
            )
            ->join('colaboradormes_categorias as cm_cat', 'cm_pre.categoria_id', '=', 'cm_cat.id')
            ->where('cm_pre.habilitado', '1')
            ->get();

        return [
            'examenes' => $examenes,
            'examenes_preguntas' => $examenes_preguntas,
            'categorias' => $categorias,
            'preguntas' => $preguntas
        ];
    }
    public function guardar(Request $request)
    {

        // dd($request);
        $modo = $request->modo;
        $resultado = 'ERROR';
        $datos_registro = (new GthController)->datos_registro();
        $nombre = mb_strtoupper($request->examen);
        $preguntas = $request->preguntas_seleccionadas;
        $habilitado = $request->habilitado;

        // dd($preguntas);

        if ($habilitado == true) {

            $habilitado = 1;
        }

        if ($habilitado == false) {

            $habilitado = 0;
        }

        if ($modo == "NUEVO") {

            // dd($habilitado);

            $id_examen = Examen::create(array(
                'examen' => $nombre,
                'datos_creacion' => $datos_registro,
                'habilitado' => $habilitado,
            ));
            $id_examen = $id_examen->id;


            foreach ($preguntas as $pregunta) {
                ExamenPregunta::create(array(
                    'examen_id' => $id_examen,
                    'pregunta_id' => $pregunta,
                    'datos_creacion' => $datos_registro,
                    'datos_actualizacion' => $datos_registro


                ));
            }
            $resultado = 'EXITO';
        } else if ($modo == "EDITAR") {

            // dd($habilitado);


            $id_examen = $request->id;


            Examen::where('id', $id_examen)->update([
                'examen' => $nombre,
                'datos_actualizacion' => $datos_registro,
                'habilitado' => $habilitado

            ]);
            ExamenPregunta::where('examen_id', $id_examen)->delete();

            foreach ($preguntas as $pregunta) {
                ExamenPregunta::create(array(
                    'examen_id' => $id_examen,
                    'pregunta_id' => $pregunta,
                    'datos_creacion' => $datos_registro,
                    'datos_actualizacion' => $datos_registro

                ));
            }
            $resultado = 'EXITO';
        }

        return $resultado;
    }

    public function ver(Request $request)
    {
        // dd($request);

        $examen_id = $request->examen_id;

        $examenes_detallado = ExamenPregunta::from('colaboradormes_examen_preguntas as cm_exp')
            ->select(
                'cm_exp.examen_id',
                'cm_exp.pregunta_id',
                'cm_pre.pregunta',
                'cm_pre.criterio',
                'cm_pre.categoria_id',
                'cm_cat.categoria as nombre_categoria',
                'cm_cat.peso'
            )
            // ->join('colaboradormes_examenes as cm_exa', 'cm_exp.examen_id', '=', 'cm_exa.id')
            ->join('colaboradormes_preguntas as cm_pre', 'cm_exp.pregunta_id', '=', 'cm_pre.id')
            ->join('colaboradormes_categorias as cm_cat', 'cm_pre.categoria_id', '=', 'cm_cat.id')
            ->join('colaboradormes_niveles as cm_niv', 'cm_cat.nivel_id', '=', 'cm_niv.id')

            // ->where('cm_exa.habilitado', '1')
            ->where('cm_pre.habilitado', '1')
            ->where('cm_cat.habilitado', '1')
            ->where('cm_niv.habilitado', '1')
            ->where('cm_exp.examen_id', $examen_id)
            ->orderby('cm_cat.peso', 'desc')
            ->get();

        // dd($examenes_detallado);

        $categorias = [];

        foreach ($examenes_detallado as $item) {
            if (!in_array($item->categoria_id, $categorias)) {
                $categorias[] = $item->categoria_id;
            }
        }

        $examenes_detallado = $examenes_detallado->toArray();
        $examen_detalle = [];

        foreach ($categorias as $item) {


            $filtrado_categorias = array_filter($examenes_detallado, function ($var) use ($item) {
                return  $var['categoria_id'] == $item;
            });


            foreach ($filtrado_categorias as $element) {
                $objeto_categoria = (object) [
                    'categoria_id' => $item,
                    'nombre_categoria' => $element['nombre_categoria'],
                    'peso_categoria' => $element['peso'],
                    'datos' => [],
                ];
                break;
            }



            foreach ($filtrado_categorias as $item_2) {

                $objeto_datos = (object) [
                    'criterio' => null,
                    'pregunta' => null,

                ];

                $objeto_datos->criterio = $item_2['criterio'];
                $objeto_datos->pregunta = $item_2['pregunta'];

                $objeto_categoria->datos[] = $objeto_datos;
            }
            // dd($objeto_categoria);


            $examen_detalle[] = $objeto_categoria;
        }

        // dd($examen_detalle);

        return [
            'examen_detalle' => $examen_detalle,

        ];
    }

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------
    public function verificar(Request $request)
    {
        $modo = $request->modo;

        $nombre = $request->nombre;
        $resultado = 'NO EXISTE';
        $examen_existe = Examen::select('id')->where('examen', $nombre)->get();

        // dd($request);

        if ($modo == "NUEVO") {


            if (count($examen_existe) != 0) {
                $resultado = 'EXISTE';
            }
        } else if ($modo == "EDITAR") {


            $id_examen = $request->id;

            if (count($examen_existe) != 0) {
                if ($examen_existe[0]['id'] == $id_examen) {
                    $resultado = 'NO EXISTE';
                } else {
                    $resultado = 'EXISTE';
                }
            }

            // dd($resultado);
        }

        return $resultado;
    }
}
