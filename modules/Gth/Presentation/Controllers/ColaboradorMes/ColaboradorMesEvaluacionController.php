<?php

namespace Modules\Gth\Presentation\Controllers\ColaboradorMes;

use Modules\Gth\Presentation\Controllers\GthController;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Equipo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Examen;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\EquipoIntegrante;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Evaluacion;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\EvaluacionPregunta;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\ExamenPregunta;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Pregunta;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ColaboradorMesEvaluacionController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function asignar_evaluacion()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASIGNAR_EVALUACION', 'GTH_COLABORADOR_MES');
            if ($band == 1) {
                $equipos = Equipo::from('colaboradormes_equipos as cm_eq')
                    ->select('cm_eq.id', 'cm_eq.equipo', 'cm_eq.responsable_id')
                    ->where('cm_eq.habilitado', 1)
                    ->orderBy('equipo', 'asc')->get();
                $examenes = Examen::select('id', 'examen')->where('habilitado', 1)->orderBy('examen', 'asc')->get();
                $integrantes_equipo = EquipoIntegrante::from('colaboradormes_integrantes as cm_eqi')
                    ->select(
                        'cm_eqi.id',
                        'cm_eqi.equipo_id',
                        'cm_eqi.usuario_id',
                        'cm_eq.equipo',
                        'cm_eq.responsable_id',
                        DB::raw("CONCAT(us.nombres,' ',us.apellido_paterno,' ',us.apellido_materno) AS nombre_integrante"),
                        'ag.nombre as agencia_integrante',
                        'ca.cargo as cargo_integrante',
                    )
                    ->join('colaboradormes_equipos as cm_eq', 'cm_eq.id', '=', 'cm_eqi.equipo_id')
                    ->join('usuarios as us', 'cm_eqi.usuario_id', '=', 'us.dni')
                    ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                    ->join('cargos as ca', 'us.cargo_id', '=', 'ca.id')
                    ->get();

                return Inertia::render('Gth/ColaboradorMes/asignar_evaluacion', [
                    'equipos' => $equipos,
                    'integrantes_equipo' => $integrantes_equipo,
                    'examenes' => $examenes
                ]);
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

    public function comprobar_evaluacion_asignada(Request $request)
    {
        $evaluacion_asignada_r = Evaluacion::from('colaboradormes_evaluaciones as col_eva')
            ->select('col_eva.examen_id')
            ->where([
                ['col_eva.equipo_id', $request->equipo_id],
                ['col_eva.año', $request->anio],
                ['col_eva.mes', $request->mes],
                ['col_eva.evaluador_id', $request->responsable]
            ])
            ->distinct()
            ->get();

        $evaluacion_asignada_i = Evaluacion::from('colaboradormes_evaluaciones as col_eva')
            ->select('col_eva.examen_id')
            ->where([
                ['col_eva.equipo_id', $request->equipo_id],
                ['col_eva.año', $request->anio],
                ['col_eva.mes', $request->mes],
                ['col_eva.evaluado_id', $request->responsable]
            ])
            ->distinct()
            ->get();

        return [
            'evaluacion_asignada_r' => $evaluacion_asignada_r,
            'evaluacion_asignada_i' => $evaluacion_asignada_i
        ];
    }

    public function verificar_evaluacion(Request $request)
    {
        $evaluacion = json_decode($request->evaluacion);
        $resultado = 'NO EXISTE';
        $tipo = $evaluacion->tipo;
        $examen_id = $evaluacion->examen_id;
        $equipo_id = $evaluacion->equipo_id;
        $anio = $evaluacion->anio;
        $mes = $evaluacion->mes;

        $responsables = Equipo::select('responsable_id')->where('id', $equipo_id)->get()->last();
        $responsables = json_decode($responsables->responsable_id);

        if ($tipo == 'R_I') {

            foreach ($responsables as $item) {
                $evaluacion_existente = Evaluacion::select('id')
                    ->where([
                        ['examen_id', $examen_id],
                        ['año', $anio],
                        ['mes', $mes],
                        ['equipo_id', $equipo_id],
                        ['evaluador_id', $item->dni]
                    ])
                    ->get();
                if (count($evaluacion_existente) > 0) {
                    $resultado = 'EXISTE';
                    break;
                } else {
                    $resultado = 'NO EXISTE';
                }
            }
        } else if ($tipo == 'I_R') {

            foreach ($responsables as $item) {
                $evaluacion_existente = Evaluacion::select('id')
                    ->where([
                        ['examen_id', $examen_id],
                        ['año', $anio],
                        ['mes', $mes],
                        ['equipo_id', $equipo_id],
                        ['evaluado_id', $item->dni]
                    ])
                    ->get();
                if (count($evaluacion_existente) > 0) {
                    $resultado = 'EXISTE';
                    break;
                } else {
                    $resultado = 'NO EXISTE';
                }
            }
        }

        return $resultado;
    }
    public function guardar_evaluacion(Request $request)
    {

        $response = new \stdClass();

        $evaluacion = json_decode($request->evaluacion);

        $datos_registro = (new GthController)->datos_registro();

        $tipo = $evaluacion->tipo;
        $examen_id = $evaluacion->examen_id;
        $equipo_id = $evaluacion->equipo_id;
        $anio = $evaluacion->anio;
        $mes = $evaluacion->mes;

        $responsables = Equipo::select('responsable_id')
            ->where('id', $equipo_id)->get()->last();

        $responsables = json_decode($responsables->responsable_id);

        $preguntas = ExamenPregunta::from('colaboradormes_examen_preguntas as  col_exa_pre')
            ->select('col_exa_pre.pregunta_id')
            ->join('colaboradormes_preguntas as col_pre', 'col_exa_pre.pregunta_id', 'col_pre.id')
            ->where([
                ['col_exa_pre.examen_id', $examen_id],
                ['col_pre.habilitado', 1]
            ])
            ->get();

        foreach ($responsables as $value) {
            $responsable_id = $value->dni;

            $integrantes = EquipoIntegrante::select('usuario_id')
                ->where('equipo_id', $equipo_id)
                ->get();

            if ($tipo == 'R_I') {
                foreach ($integrantes as $item) {
                    $evaluacion =  Evaluacion::create(
                        [
                            'examen_id' => $examen_id,
                            'evaluador_id' => $responsable_id,
                            'evaluado_id' => $item->usuario_id,
                            'año' => $anio,
                            'mes' => $mes,
                            'equipo_id' => $equipo_id,
                            'datos_creacion' => $datos_registro
                        ]
                    );

                    $evaluacion_id = $evaluacion->id;

                    foreach ($preguntas as $item_1) {
                        EvaluacionPregunta::create(array(
                            'evaluacion_id' => $evaluacion_id,
                            'pregunta_id' => $item_1->pregunta_id,
                            'puntuacion' => '0',
                            'datos_creacion' => $datos_registro
                        ));
                    }
                }
            } else if ($tipo == 'I_R') {
                foreach ($integrantes as $item) {

                    $evaluacion = Evaluacion::create(
                        [
                            'examen_id' => $examen_id,
                            'evaluador_id' => $item->usuario_id,
                            'evaluado_id' => $responsable_id,
                            'año' => $anio,
                            'mes' => $mes,
                            'equipo_id' => $equipo_id,
                            'datos_creacion' => $datos_registro
                        ]
                    );

                    $evaluacion_id = $evaluacion->id;

                    foreach ($preguntas as $item_1) {
                        EvaluacionPregunta::create(array(
                            'evaluacion_id' => $evaluacion_id,
                            'pregunta_id' => $item_1->pregunta_id,
                            'puntuacion' => '0',
                            'datos_creacion' => $datos_registro,
                        ));
                    }
                }
            }
        }

        $response->success = true;

        return $response;
    }

    public function seguimiento_evaluacion()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'SEGUIMIENTO_EVALUACION', 'GTH_COLABORADOR_MES');
            if ($band == 1) {
                $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();


                return Inertia::render('Gth/ColaboradorMes/seguimiento_evaluaciones', ['agencias' => $agencias]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar_seguimiento_evaluacion(request $request)
    {

        $año_mes_seleccionado = $request->año_mes_seleccionado;


        $año_seleccionado = date("Y", strtotime($año_mes_seleccionado));

        $mes_seleccionado = date("m", strtotime($año_mes_seleccionado));


        // dd($año_seleccionado,$mes_seleccionado );

        $lista_seguimiento_evaluaciones =  EvaluacionPregunta::from('colaboradormes_evaluaciones as col_eva')
            ->select(
                'age.nombre as agencia_evaluador',
                'usu_1.usuario as usuario1',
                'usu_2.usuario as usuario2',
                'cm_exa.examen',
                'col_eva.culminado',
                DB::raw("SUBSTR(col_eva.datos_actualizacion ,11,19) as fecha")

            )
            ->join('usuarios as usu_1', 'usu_1.dni', 'col_eva.evaluador_id')
            ->join('agencias as age', 'age.id_agencia', 'usu_1.agencia_id')
            ->join('usuarios as usu_2', 'usu_2.dni', 'col_eva.evaluado_id')
            ->join('colaboradormes_examenes as cm_exa', 'cm_exa.id', 'col_eva.examen_id')
            ->where('col_eva.año', $año_seleccionado)
            ->where('col_eva.mes', $mes_seleccionado)
            ->orderby('usuario1', 'desc')
            ->get();

        return ['lista_seguimiento_evaluaciones' => $lista_seguimiento_evaluaciones];

        // dd($lista_seguimiento_evaluaciones);
    }



    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------
}
