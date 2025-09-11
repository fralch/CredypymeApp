<?php

namespace App\Http\Controllers\Gth\ColaboradorMes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gth\GthController;
use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\ColaboradorMes\Examen;
use App\Models\Gth\ColaboradorMes\Evaluacion;
use App\Models\Gth\ColaboradorMes\EvaluacionPregunta;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use App\Models\General\Agencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ColaboradorMesResultadosController extends Controller
{

    public function resultados()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'RESULTADOS_EVALUACION', 'GTH_COLABORADOR_MES');
            if ($band == 1) {

                return Inertia::render('Gth/ColaboradorMes/resultados_evaluacion');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar(request $request)
    {
        $agencia_id = $request->agencia_seleccionada;
        $fecha_seleccionada = $request->fecha_seleccionada;

        $año_seleccionado = date("Y", strtotime($fecha_seleccionada));
        $mes_seleccionado = date("m", strtotime($fecha_seleccionada));
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        $examen = Examen::where('examen', 'GTH')->get()->last();
        $examen_id = $examen->id;

        $lista_resultados =  Evaluacion::from('colaboradormes_evaluaciones as col_eva')
            ->select(
                'age.nombre as agencia',
                'usu.dni',
                'usu.usuario',
                'usu.apellido_paterno',
                'usu.apellido_materno',
                'usu.nombres',
                'car.cargo',
                'col_eva.examen_id',
                DB::raw("AVG(col_eva.puntuacion_total) as promedio"),
                DB::raw("IF(col_eva.examen_id!='$examen_id',0,1) as examen_gth")
            )
            ->join('usuarios as usu', 'usu.dni', 'col_eva.evaluado_id')
            ->join('agencias as age', 'age.id_agencia', 'usu.agencia_id')
            ->join('cargos as car', 'car.id', 'usu.cargo_id')
            ->where([
                ['col_eva.año', $año_seleccionado],
                ['col_eva.mes', $mes_seleccionado],
                ['col_eva.culminado', 1],
                ['usu.agencia_id', $agencia_id]
            ])
            ->groupBy('col_eva.evaluado_id', 'col_eva.examen_id')
            ->orderBy('col_eva.evaluado_id', 'asc')
            ->get();

        $lista_nueva = (array)[];

        foreach ($lista_resultados as $item) {

            $evaluado_id = $item->dni;

            $filtrado = array_filter($lista_nueva, function ($var) use ($evaluado_id) {
                return  $var->dni == $evaluado_id;
            });

            if (count($filtrado) == 0) {
                $object = (object)[
                    'agencia' => $item->agencia,
                    'dni' => $item->dni,
                    'usuario' => $item->usuario,
                    'apellido_paterno' => $item->apellido_paterno,
                    'apellido_materno' => $item->apellido_materno,
                    'nombres' => $item->nombres,
                    'cargo' => $item->cargo,
                    'promedio' => 0,
                ];

                $evaluaciones = array_filter($lista_resultados->toArray(), function ($var_2) use ($evaluado_id) {
                    return  $var_2['dni'] == $evaluado_id;
                });

                $puntuacion_no_gth = 0;
                $contador = 0;
                $puntuacion_gth = 0;
                $total = 0;

                foreach ($evaluaciones as $item_2) {

                    if ($item_2['examen_id'] != $examen_id) {
                        $puntuacion_no_gth += $item_2['promedio'];
                        $contador++;
                    } else {
                        $puntuacion_gth += $item_2['promedio'];
                    }

                    if ($contador == 0) {
                        $total = $puntuacion_gth;
                    } else {
                        $total = ($puntuacion_no_gth / $contador) + $puntuacion_gth;
                    }
                }

                $object->promedio = $total;

                $lista_nueva[] = $object;
            }
        };


        $lista_nueva = collect($lista_nueva)->sortBy(function ($item) {
            return -$item->promedio; // Sort by age in descending order
        })->values()->all();

        $nombre_mes = $meses[$mes_seleccionado - 1];

        return [
            'lista_resultados' => $lista_nueva,
            'nombre_mes' => $nombre_mes,
            'año_seleccionado' => $año_seleccionado
        ];
    }


    public function exportar(request $request)
    {

        $datos_recibidos =   json_decode($request->datos);
        $tipo = $request->tipo;

        $data = [];

        $orden = 1;

        foreach ($datos_recibidos as $item) {
            $object = (object)[
                'numero' => $orden,
                'agencia' => $item->agencia,
                'dni' => $item->dni,
                'apellido_paterno' =>  $item->apellido_paterno,
                'apellido_materno' => $item->apellido_materno,
                'nombres' => $item->nombres,
                'cargo' => $item->cargo,
                'promedio' => $item->promedio,

            ];
            $orden += 1;
            $data[] = $object;
        }


        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName =  './report_templates/gth/rptResultadosEvaluacion.xlsx';

        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];

        while ($celda <= 9) {
            $columna_1 = (new GthController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 6)->exportArray();

            $formato_total = $sheet->getStyle($columna_1 . 8)->exportArray();


            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;
            $lista_formatos_totales[] = $formato_total;


            $celda++;
        }

        $sheet->removeRow(7);

        // Insertando datos-----------------------------
        $indice = 5;
        $total = 0;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new GthController)->num2char($columna_2) . $indice, $valor);
                if ($indice < 8) {
                    $sheet->getStyle((new GthController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_2[$columna_2 - 1]);
                } else {
                    $sheet->getStyle((new GthController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_2 - 1]);
                }

                $columna_2 += 1;
            }

            $indice += 1;
            $total += 1;
        }
        $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(7);

        $indice += 1;
        $sheet->setCellValue('G' . $indice, 'TOTAL ' . $total . ' registro(s)');

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new GthController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        $nombre_archivo = (new GthController)->concatenar_aleatorio('rptResultadosEvaluacion', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function detalle(Request $request)
    {
        $fecha_seleccionada = $request->fecha_seleccionada;
        $evaluado_id = $request->evaluado_id;

        $año_seleccionado = date("Y", strtotime($fecha_seleccionada));
        $mes_seleccionado = date("m", strtotime($fecha_seleccionada));

        $evaluaciones = Evaluacion::from('colaboradormes_evaluaciones as col_eva')
            ->select(
                'col_eva.id',
                'col_eva.puntuacion_total',

                'usu_1.usuario as evaluador',
                'usu_2.usuario as evaluado',

                'col_equ.equipo',
                'col_exa.examen',

            )
            ->join('usuarios as usu_1', 'col_eva.evaluador_id', 'usu_1.dni')
            ->join('usuarios as usu_2', 'col_eva.evaluado_id', 'usu_2.dni')
            ->join('colaboradormes_equipos as col_equ', 'col_eva.equipo_id', 'col_equ.id')
            ->join('colaboradormes_examenes as col_exa', 'col_eva.examen_id', 'col_exa.id')
            ->where([
                ['evaluado_id', $evaluado_id],
                ['año', $año_seleccionado],
                ['mes', $mes_seleccionado],
                ['culminado', 1]
            ])
            ->get();

        $evaluaciones_id = array_column($evaluaciones->toArray(), 'id');

        $evaluaciones_detalle =  EvaluacionPregunta::from('colaboradormes_evaluacion_preguntas as col_eva_pre')
            ->select(
                'col_eva_pre.puntuacion',
                'col_eva_pre.escala_resultado',
                'col_eva_pre.evaluacion_id',

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
            ->get();

        return [
            'evaluaciones' => $evaluaciones,
            'evaluaciones_detalle' => $evaluaciones_detalle
        ];
    }
}
