<?php

namespace App\Http\Controllers\Gth\ColaboradorMes;

use App\Http\Controllers\Gth\GthController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Gth\ColaboradorMes\EquipoIntegrante;
use App\Models\Gth\ColaboradorMes\Categoria;
use App\Models\Gth\ColaboradorMes\Evaluacion;
use App\Models\Gth\ColaboradorMes\EvaluacionPregunta;
use App\Http\Controllers\General\PermisosController;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ColaboradorMesEvaluacionPreguntaController extends Controller
{

    public function guardar_mi_evaluacion(Request $request)
    {
        $resultado = 'ERROR';
        $preguntas_calificadas = $request->preguntas_calificadas;

        foreach ($preguntas_calificadas as $pregunta_calificada) {

            $id_evaluaciones[] = $pregunta_calificada['id'];
            EvaluacionPregunta::where('colaboradormes_evaluacion_preguntas.id', $pregunta_calificada['id_evaluacion_pregunta'])
                ->update(['puntuacion' => $pregunta_calificada['puntajePregunta']]);
        }

        // Obtener puntajes total
        $id_evaluaciones = array_unique($id_evaluaciones);

        foreach ($id_evaluaciones as $id_evaluacion) {
            $puntaje_evaluaciones[] = (object) ['id' => $id_evaluacion, 'puntajeTotal' => 0];
        }

        foreach ($preguntas_calificadas as $pregunta_calificada) {
            foreach ($puntaje_evaluaciones as $puntaje_evaluacion) {
                if ($puntaje_evaluacion->id == $pregunta_calificada['id']) {
                    $puntaje_evaluacion->puntajeTotal += round($pregunta_calificada['puntajePregunta'] * $pregunta_calificada['peso_categoria'], 2);
                }
            }
        }


        foreach ($puntaje_evaluaciones as $puntaje_evaluacion) {


            $datos_registro = (new GthController)->datos_registro();

            Evaluacion::where('colaboradormes_evaluaciones.id', $puntaje_evaluacion->id)
                ->update([
                    'culminado' => 1,
                    'puntuacion_total' => $puntaje_evaluacion->puntajeTotal,
                    'datos_actualizacion' => $datos_registro,


                ]);
        }

        $resultado = 'EXITO';
        return $resultado;
    }

    public function mis_evaluaciones_pendientes()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_EVALUACIONES', 'GTH_COLABORADOR_MES');
            if ($band == 1) {
                return Inertia::render('Gth/ColaboradorMes/mis_evaluaciones');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function preguntas_evaluaciones_1(Request $request)
    {
        $examen_id = $request->examen_id;
        $x = session()->all();

        $preguntas_evaluaciones = Evaluacion::from('colaboradormes_evaluaciones as cm_eva')
            ->select(
                'cm_eva.id as evaluacion_id',
                'cm_eva.examen_id',
                DB::raw("CONCAT(us.nombres,' ',us.apellido_paterno,' ',us.apellido_materno) as nombre_evaluado"),
                'us.usuario',
                'cm_eva.evaluado_id',
                'cm_eva.año',
                'cm_eva.mes',
                'cm_pre.pregunta',
                'cm_pre.id as pregunta_id',
                'cm_pre.criterio',
                'cm_cat.peso as peso_categoria',
                'cm_pre.categoria_id',
                'cm_cat.categoria as nombre_categoria',
                'cm_niv.descripcion as nombres_nivel',
                'cm_eva.puntuacion_total',

            )
            ->join('colaboradormes_examenes as cm_exa', 'cm_eva.examen_id', '=', 'cm_exa.id')
            ->join('colaboradormes_examen_preguntas as cm_exa_pre', 'cm_exa.id', '=', 'cm_exa_pre.examen_id')
            ->join('colaboradormes_preguntas as cm_pre', 'cm_exa_pre.pregunta_id', '=', 'cm_pre.id')
            ->join('colaboradormes_categorias as cm_cat', 'cm_pre.categoria_id', '=', 'cm_cat.id')
            ->join('colaboradormes_niveles as cm_niv', 'cm_cat.nivel_id', '=', 'cm_niv.id')
            ->join('usuarios as us', 'cm_eva.evaluado_id', '=', 'us.dni')
            ->where([['cm_eva.evaluador_id', $x['usuario_dni']], ['cm_eva.culminado', 0]])
            ->where('cm_eva.examen_id', $examen_id)
            ->get();

        $evaluados_ = [];

        foreach ($preguntas_evaluaciones as $item) {
            if (!in_array($item->evaluado_id, array_column($evaluados_, 'evaluado_id'))) {
                $evaluados_[] = [
                    'evaluado_id' => $item->evaluado_id,
                    'usuario' => $item->usuario
                ];
            }
        }

        $examen_detalle = [];
        $categorias = [];
        $evaluaciones = [];

        foreach ($preguntas_evaluaciones as $item) {
            if (!in_array($item->categoria_id, $categorias)) {
                $categorias[] = $item->categoria_id;
            }
        }
        foreach ($preguntas_evaluaciones as $i) {
            if (!in_array($i->evaluacion_id, $evaluaciones)) {
                $evaluaciones[] = $i->evaluacion_id;
            }
        }

        foreach ($categorias as $item) {

            $filtrado_categorias = $preguntas_evaluaciones->where('categoria_id', $item)->toArray();

            foreach ($filtrado_categorias as $element) {
                $objeto_categoria = (object) [
                    'categoria_id' => $item,
                    'nombre_categoria' => $element['nombre_categoria'],
                    'peso_categoria' => $element['peso_categoria'],
                    'niveles' => $element['nombres_nivel'],
                    'evaluacion_id' => $element['evaluacion_id'],
                    'datos' => [],
                ];
            }
            foreach ($filtrado_categorias as $item_2) {

                $objeto_datos = (object) [
                    'criterio' => null,
                    'pregunta' => null,
                    'examen_id' => null,
                    'evaluacion_id' => null,
                    'pregunta_id' => null,
                    'seleccion'    => [],

                ];

                $objeto_datos->criterio = $item_2['criterio'];
                $objeto_datos->pregunta = $item_2['pregunta'];
                $objeto_datos->examen_id = $item_2['examen_id'];
                $objeto_datos->evaluacion_id = $item_2['evaluacion_id'];
                $objeto_datos->pregunta_id = $item_2['pregunta_id'];

                foreach ($evaluados_ as $item_3) {
                    $objeto_datos->seleccion[] = (object) [
                        'evaluado_id' => $item_3["evaluado_id"],
                        'nombres_nivel' =>  $item_2['nombres_nivel'],
                        'puntuacion_total' =>  $item_2['puntuacion_total'],
                        'evaluacion_id' => $item_2['evaluacion_id'],
                    ];
                    // 'evaluado_id' => $item_3,
                    //     'nombres_nivel' =>  $item_2['nombres_nivel'],
                    //     'puntuacion_total' =>  $item_2['puntuacion_total'],

                }




                // comprobar que no se repita el criterio y la pregunta en el array de datos
                $band = 0;
                foreach ($objeto_categoria->datos as $item_3) {
                    if ($item_3->criterio == $objeto_datos->criterio && $item_3->pregunta == $objeto_datos->pregunta) {
                        $band = 1;
                    }
                }
                if ($band == 0) {
                    $objeto_categoria->datos[] = $objeto_datos;
                }
            }


            $examen_detalle[] = $objeto_categoria;
        }

        return [
            'examen_detalle' => $examen_detalle,
            'preguntas_evaluaciones' => $preguntas_evaluaciones,
            'evaluados_' => $evaluados_,
            'evaluaciones' => $evaluaciones,

        ];
    }

    public function preguntas_evaluaciones(Request $request)
    {
        $examen_id = $request->examen_id;

        $criterios_preguntas = Evaluacion::from('colaboradormes_evaluaciones as cm_eva')
            ->select(
                'cm_eva.id as evaluacion_id',
                'cm_eva.examen_id',
                'cm_eva.evaluado_id',
                'cm_eva.año',
                'cm_eva.mes',
                'cm_pre.pregunta',
                'cm_pre.id as pregunta_id',
                'cm_pre.criterio',
                'cm_pre.categoria_id',
                'cm_eva.puntuacion_total',

            )
            ->join('colaboradormes_examenes as cm_exa', 'cm_eva.examen_id',  'cm_exa.id')
            ->join('colaboradormes_examen_preguntas as cm_exa_pre', 'cm_exa.id',  'cm_exa_pre.examen_id')
            ->join('colaboradormes_preguntas as cm_pre', 'cm_exa_pre.pregunta_id',  'cm_pre.id')

            ->where('cm_eva.examen_id', $examen_id)
            ->get();

        return ['preguntasEvaluacionesFiltrado' => $criterios_preguntas];
    }

    public function guarda_puntos_evaluacion(Request $request)
    {
        // return $request->all();
        $examen_id = $request->examen_id;
        $y = session()->all();

        $guardado_exitoso = 0; // bandera para saber si se guardo correctamente

        foreach ($request->datos as $rq) {
            $evaluacion_id = Evaluacion::where('evaluador_id',  $y['usuario_dni'])
                ->where('evaluado_id', $rq['evaluado_id'])
                ->first()->id;


            foreach ($rq["pregunta"] as $prgt) {
                $puntos = $prgt['respuesta'];
                $pregunta_id = $prgt['id'];


                //actualizar puntos
                $puntos_evaluacion = EvaluacionPregunta::where('evaluacion_id', $evaluacion_id)
                    ->where('pregunta_id', $pregunta_id)
                    ->first();
                $puntos_evaluacion->puntuacion = $puntos;
                $puntos_evaluacion->save();
            }

            // actualizar  puntuacion total de la evaluacion y culminarla
            // $puntos_total = Evaluacion::from('colaboradormes_evaluaciones as cm_eva')
            // ->where('evaluador_id',  $y['usuario_dni'])
            // ->where('evaluado_id', $rq['evaluado_id'])
            // ->first()->id;

            $evaluacion = Evaluacion::where('evaluador_id',  $y['usuario_dni'])
                ->where('evaluado_id', $rq['evaluado_id'])
                ->first();
            $evaluacion->puntuacion_total = $rq['calculo_puntuacion'];
            $evaluacion->culminado = 1;
            $evaluacion->save();


            $guardado_exitoso = $puntos_evaluacion->save() && $evaluacion->save();
        }

        if ($guardado_exitoso) {
            $evaluaciones = Evaluacion::from('colaboradormes_evaluaciones as cm_eva')->select(
                'cm_eva.examen_id',
                'cm_eva.año',
                'cm_eva.mes',
                'cm_exa.examen as nombre'
            )
                ->join('colaboradormes_examenes as cm_exa', 'cm_eva.examen_id', '=', 'cm_exa.id')
                ->where([['cm_eva.evaluador_id', $y['usuario_dni']], ['cm_eva.culminado', 0]])
                ->orderBy('cm_eva.año', 'asc')
                ->orderBy('cm_eva.mes', 'asc')
                ->distinct()
                ->get();

            return $evaluaciones; // retorna las evaluaciones que aun no han sido culminadas
        } else {
            return 0;
        }
    }

    public function listar_mis_evaluaciones()
    {
        $evaluaciones = Evaluacion::from('colaboradormes_evaluaciones as col_eva')
            ->select(

                'col_eva.examen_id',
                'col_eva.equipo_id',
                'col_eva.año',
                'col_eva.mes',

                'cm_exa.examen as nombre',
                'cm_equi.equipo as equipo'
            )
            ->join('colaboradormes_examenes as cm_exa', 'col_eva.examen_id',  'cm_exa.id')
            ->join('colaboradormes_equipos as cm_equi', 'col_eva.equipo_id',  'cm_equi.id')
            ->where([['col_eva.evaluador_id', session('usuario_dni')], ['col_eva.culminado', 0]])
            ->orderBy('col_eva.año', 'asc')
            ->orderBy('col_eva.mes', 'asc')
            ->distinct()
            ->get();

        return ['evaluaciones' => $evaluaciones];
    }

    public function generar_evaluacion(Request $request)
    {

        $examen_id = $request->examen_id;
        $equipo_id = $request->equipo_id;
        $anio = $request->anio;
        $mes = $request->mes;

        $evaluador_id = session('usuario_dni');

        $evaluacion = Evaluacion::select(
            'id',
            'evaluado_id'
        )
            ->where([
                ['evaluador_id', $evaluador_id],
                ['examen_id', $examen_id],
                ['equipo_id', $equipo_id],
                ['año', $anio],
                ['mes', $mes],
                ['culminado', 0]
            ])
            ->get();

        $evaluaciones_id = array_column($evaluacion->toArray(), 'id');

        $preguntas  = EvaluacionPregunta::from('colaboradormes_evaluacion_preguntas as col_eva_pre')
            ->select(
                'col_pre.id',
                'col_pre.criterio',
                'col_pre.pregunta',
                'col_pre.categoria_id',

                'col_cat.categoria',
                'col_cat.peso',

                'col_niv.nivel',
                'col_niv.descripcion as escalas'
            )
            ->join('colaboradormes_preguntas as col_pre', 'col_eva_pre.pregunta_id', 'col_pre.id')
            ->join('colaboradormes_categorias as col_cat', 'col_pre.categoria_id', 'col_cat.id')
            ->join('colaboradormes_niveles as col_niv', 'col_cat.nivel_id', 'col_niv.id')
            ->whereIn('col_eva_pre.evaluacion_id', $evaluaciones_id)
            ->orderBy('col_cat.categoria', 'asc')
            ->distinct()
            ->get();

        $preguntas_avance  = EvaluacionPregunta::from('colaboradormes_evaluacion_preguntas as col_eva_pre')
            ->select(
                'col_eva_pre.evaluacion_id',
                'col_eva_pre.pregunta_id',
                'col_eva_pre.puntuacion',
                'col_eva_pre.escala_resultado',

                'col_eva.evaluado_id',
            )
            ->join('colaboradormes_evaluaciones as col_eva', 'col_eva_pre.evaluacion_id', 'col_eva.id')
            ->whereIn('col_eva_pre.evaluacion_id', $evaluaciones_id)
            ->get();

        $evaluados = array_column($evaluacion->toArray(), 'evaluado_id');

        $evaluados = Usuario::select(
            'dni',
            'usuario'
        )
            ->whereIn('dni', $evaluados)
            ->get();

        return [
            'preguntas' => $preguntas,
            'evaluados' => $evaluados,
            'preguntas_avance' => $preguntas_avance
        ];
    }

    public function guardar_avance(Request $request)
    {

        $response = new \stdClass;

        $evaluacion = json_decode($request->evaluacion);
        $lista_resultados = json_decode($request->lista_resultados);

        $datos_registro = (new GthController)->datos_registro();

        $examen_id = $evaluacion->examen_id;
        $equipo_id = $evaluacion->equipo_id;

        $evaluaciones = Evaluacion::select(
            'id',
            'evaluado_id'
        )
            ->where([
                ['evaluador_id', session('usuario_dni')],
                ['examen_id', $examen_id],
                ['equipo_id', $equipo_id]
            ])
            ->get();

        foreach ($evaluaciones as $key => $item) {
            foreach ($lista_resultados as $key => $item_1) {

                if ($item->evaluado_id == $item_1->usuario_id) {
                    EvaluacionPregunta::where([
                        ['evaluacion_id', $item->id],
                        ['pregunta_id', $item_1->pregunta_id]
                    ])->update([
                        'puntuacion' => $item_1->valor,
                        'escala_resultado' => $item_1->escala,
                        'datos_actualizacion' => $datos_registro
                    ]);
                }
            }
        }

        $response->success = true;

        return  $response;
    }

    public function terminar(Request $request)
    {
        $response = new \stdClass;

        $evaluacion = json_decode($request->evaluacion);
        $lista_resultados = json_decode($request->lista_resultados);

        $datos_registro = (new GthController)->datos_registro();

        $examen_id = $evaluacion->examen_id;
        $equipo_id = $evaluacion->equipo_id;

        $anio = $request->anio;
        $mes = $request->mes;

        $evaluaciones = Evaluacion::select(
            'id',
            'evaluado_id'
        )
            ->where([
                ['evaluador_id', session('usuario_dni')],
                ['examen_id', $examen_id],
                ['equipo_id', $equipo_id],
                ['año', $anio],
                ['mes', $mes],
                ['culminado', 0]
            ])
            ->get();

        foreach ($evaluaciones as $key => $item) {
            $total = 0;
            foreach ($lista_resultados as $key => $item_1) {
                if ($item->evaluado_id == $item_1->usuario_id) {

                    $total += round(($item_1->valor) * ($item_1->peso), 2);

                    EvaluacionPregunta::where([
                        ['evaluacion_id', $item->id],
                        ['pregunta_id', $item_1->pregunta_id]
                    ])->update([
                        'puntuacion' => $item_1->valor,
                        'escala_resultado' => $item_1->escala,
                        'datos_actualizacion' => $datos_registro
                    ]);
                }
            }
            Evaluacion::where('id', $item->id)
                ->update(['culminado' => 1, 'puntuacion_total' => $total]);
        }

        $response->success = true;

        return  $response;
    }
}
