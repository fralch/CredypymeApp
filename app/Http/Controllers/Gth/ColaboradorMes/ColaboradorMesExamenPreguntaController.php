<?php

namespace App\Http\Controllers\Gth\ColaboradorMes;

use App\Http\Controllers\Gth\GthController;
use App\Http\Controllers\Controller;
use App\Models\Gth\ColaboradorMes\Categoria;
use App\Models\Gth\ColaboradorMes\Pregunta;
use App\Models\Gth\ColaboradorMes\Examen;
use App\Models\Gth\ColaboradorMes\ExamenPregunta;
use App\Http\Controllers\General\PermisosController;
use Inertia\Inertia;

use Illuminate\Http\Request;

class ColaboradorMesExamenPreguntaController extends Controller
{
    public function guardar_examen(Request $request)
    {
        $datos_registro = (new GthController)->datos_registro();
        $resultado = 'ERROR';
        $nombre = mb_strtoupper($request->nombre);
        $preguntas = $request->preguntas_seleccionadas;
        // dd($datos_registro);
        // date_default_timezone_set("America/Lima");
        // $fecha_registro = (new GthController)->fecha_larga_aplicacion();
        // $x = session()->all();
        // $usuario_registro = $x['usuario_dni'];

        $id_examen = Examen::create(array(
            'examen' => $nombre,
            'datos_creacion' => $datos_registro,
            'habilitado' => 1
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

        return $resultado;
    }
    public function examenes_disponibles()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'EXAMENES', 'GTH_COLABORADOR_MES');
            if ($band == 1) {
                $examenes = Examen::orderBy('examen', 'asc')->get();
                $examenes_preguntas = ExamenPregunta::from('colaboradormes_examen_preguntas as cm_exp')
                    ->select(
                        'cm_exp.examen_id',
                        'cm_exp.pregunta_id',
                        'cm_pre.pregunta',
                        'cm_pre.valor',
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
                        'cm_pre.valor',
                        'cm_cat.categoria as nombre_categoria',
                        'cm_cat.peso as peso_categoria',
                        'cm_cat.id as id_categoria'
                    )
                    ->join('colaboradormes_categorias as cm_cat', 'cm_pre.categoria_id', '=', 'cm_cat.id')
                    ->get();
                return Inertia::render('Gth/ColaboradorMes/examenes', [
                    'examenes' => $examenes, 'examenes_preguntas' => $examenes_preguntas,
                    'categorias' => $categorias, 'preguntas' => $preguntas
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function editar_examen(Request $request)
    {
        $datos_registro = (new GthController)->datos_registro();
        $resultado = 'ERROR';
        $id_examen = $request->id;
        $nombre = mb_strtoupper($request->examen);
        $preguntas = $request->preguntas_seleccionadas;

        // date_default_timezone_set("America/Lima");
        // $fecha_registro = (new GthController)->fecha_larga_aplicacion();
        // $x = session()->all();
        // $usuario_registro = $x['usuario_dni'];

        Examen::where('id', $id_examen)->update([
            'examen' => $nombre,
            'datos_actualizacion' => $datos_registro
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

        return $resultado;
    }
}
