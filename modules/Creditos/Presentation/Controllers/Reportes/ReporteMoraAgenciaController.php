<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;

use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Records\CreditoResumenRecord;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Records\CreditoRegistroRecord;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CentralRiesgo;

use Modules\Creditos\Presentation\Controllers\CreditosController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReporteMoraAgenciaController extends Controller
{
    public function listar_recursos()
    {

        $cargos = Cargo::select('id')->whereIn('cargo', [
            'ASESOR DE NEGOCIOS',
            'JEFE DE CRÉDITOS',
            'COORDINADOR DE CRÉDITOS'
        ]);

        $usuarios = Usuario::select('dni', 'usuario', 'agencia_id', 'habilitado')
            ->whereIn('cargo_id', $cargos)
            ->get();

        return ['usuarios' => $usuarios];
    }

    public function buscar(Request $request)
    {
        $resultado = (new ReporteControlMoraController)->buscar_mora($request);

        asort($resultado['fechas']);
        $arrayXfecha = [];
        $arrayCompleto = [];

        foreach ($resultado['cartera_mora'] as $value) {
            foreach ($value->datos as $value2) {
                $arrayXfecha[] = $value2;
            }
        }

        foreach ($resultado['fechas'] as $value) {

            $objeto_datos = (object) [
                'fecha' => $value,
                "saldo_total" => 0,
                "cantidad_creditos" => 0,
                "clientes_activos" => 0,
                "tasa_promedio" => 0,
                "tasa_rotacion" => 0,

                "cantidad_creditos_1" => 0,
                "cantidad_clientes_1" => 0,
                "saldo_capital_1" => 0,
                "porcentaje_1" => 0,

                "cantidad_creditos_2" => 0,
                "cantidad_clientes_2" => 0,
                "saldo_capital_2" => 0,
                "porcentaje_2" => 0,

                "cantidad_creditos_3" => 0,
                "cantidad_clientes_3" => 0,
                "saldo_capital_3" => 0,
                "porcentaje_3" => 0,

                "cantidad_creditos_4" => 0,
                "cantidad_clientes_4" => 0,
                "saldo_capital_4" => 0,
                "porcentaje_4" => 0,

                "cantidad_creditos_5" => 0,
                "cantidad_clientes_5" => 0,
                "saldo_capital_5" => 0,
                "porcentaje_5" => 0,

                "cantidad_creditos_6" => 0,
                "cantidad_clientes_6" => 0,
                "saldo_capital_6" => 0,
                "porcentaje_6" => 0,

                "clientes_seleccion_total" => 0,
                "creditos_seleccion_total" => 0,
                "porcentaje_seleccion_total" => 0,
                "capital_seleccion_total" => 0
            ];

            $cantidad = 0;

            foreach ($arrayXfecha as $item) {

                if ($item->fecha == $value) {


                    $objeto_datos->saldo_total += floatval($item->saldo_total);
                    $objeto_datos->cantidad_creditos += floatval($item->cantidad_creditos);
                    $objeto_datos->clientes_activos += floatval($item->clientes_activos);

                    if ($item->tasa_promedio != null) {
                        $objeto_datos->tasa_promedio += floatval($item->tasa_promedio);
                        $cantidad += 1;
                    }

                    $objeto_datos->tasa_rotacion += floatval($item->tasa_rotacion);

                    $objeto_datos->cantidad_creditos_1 += floatval($item->cantidad_creditos_1);
                    $objeto_datos->cantidad_clientes_1 += floatval($item->cantidad_clientes_1);
                    $objeto_datos->saldo_capital_1 += floatval($item->saldo_capital_1);
                    $objeto_datos->porcentaje_1 = null;
                    $objeto_datos->cantidad_creditos_2 += floatval($item->cantidad_creditos_2);
                    $objeto_datos->cantidad_clientes_2 += floatval($item->cantidad_clientes_2);
                    $objeto_datos->saldo_capital_2 += floatval($item->saldo_capital_2);
                    $objeto_datos->porcentaje_2 = null;
                    $objeto_datos->cantidad_creditos_3 += floatval($item->cantidad_creditos_3);
                    $objeto_datos->cantidad_clientes_3 += floatval($item->cantidad_clientes_3);
                    $objeto_datos->saldo_capital_3 += floatval($item->saldo_capital_3);
                    $objeto_datos->porcentaje_3 = null;
                    $objeto_datos->cantidad_creditos_4 += floatval($item->cantidad_creditos_4);
                    $objeto_datos->cantidad_clientes_4 += floatval($item->cantidad_clientes_4);
                    $objeto_datos->saldo_capital_4 += floatval($item->saldo_capital_4);
                    $objeto_datos->porcentaje_4 = null;
                    $objeto_datos->cantidad_creditos_5 += floatval($item->cantidad_creditos_5);
                    $objeto_datos->cantidad_clientes_5 += floatval($item->cantidad_clientes_5);
                    $objeto_datos->saldo_capital_5 += floatval($item->saldo_capital_5);
                    $objeto_datos->porcentaje_5 = null;
                    $objeto_datos->cantidad_creditos_6 += floatval($item->cantidad_creditos_6);
                    $objeto_datos->cantidad_clientes_6 += floatval($item->cantidad_clientes_6);
                    $objeto_datos->saldo_capital_6 += floatval($item->saldo_capital_6);
                    $objeto_datos->porcentaje_6 = null;
                    $objeto_datos->clientes_seleccion_total += floatval($item->clientes_seleccion_total);
                    $objeto_datos->creditos_seleccion_total += floatval($item->creditos_seleccion_total);
                    $objeto_datos->porcentaje_seleccion_total = null;
                    $objeto_datos->capital_seleccion_total += floatval($item->capital_seleccion_total);
                }
            }

            // dd($objeto_datos);
            if ($objeto_datos->saldo_total == 0) {
                $objeto_datos->tasa_rotacion = 0;
            } else {
                $objeto_datos->tasa_rotacion = ($objeto_datos->tasa_rotacion / $objeto_datos->saldo_total) * 100;
            }

            $objeto_datos->tasa_promedio = $objeto_datos->tasa_promedio / $cantidad;

            $arrayCompleto[] = $objeto_datos;
        }
        foreach ($arrayCompleto as $val) {
            $val->porcentaje_1 = floatval($val->saldo_capital_1) * 100 / $val->saldo_total;
            $val->porcentaje_2 = floatval($val->saldo_capital_2) * 100 / $val->saldo_total;
            $val->porcentaje_3 = floatval($val->saldo_capital_3) * 100 / $val->saldo_total;
            $val->porcentaje_4 = floatval($val->saldo_capital_4) * 100 / $val->saldo_total;
            $val->porcentaje_5 = floatval($val->saldo_capital_5) * 100 / $val->saldo_total;
            $val->porcentaje_6 = floatval($val->saldo_capital_6) * 100 / $val->saldo_total;
            $val->porcentaje_seleccion_total = $val->capital_seleccion_total * 100 / $val->saldo_total;
        }

        return $arrayCompleto;
    }

    public function exportar(Request $request)
    {

        $cartera_mora = json_decode($request->cartera_mora);
        $totales = json_decode($request->totales);

        $data_totales = [];


        $data_totales = (object)[
            't_saldo_total' =>  $totales->t_saldo_total,
            't_cantidad_creditos' => $totales->t_cantidad_creditos,
            't_clientes_activos' => $totales->t_clientes_activos,
            'p_tasa_promedio' => $totales->p_tasa_promedio / $totales->cantidad,
            'espaciador' =>  $totales->espaciador,

            't_cantidad_creditos_1' =>  $totales->t_cantidad_creditos_1,
            't_cantidad_clientes_1' =>  $totales->t_cantidad_clientes_1,
            't_saldo_capital_1' =>  $totales->t_saldo_capital_1,
            'espaciador_1' =>  $totales->espaciador_1,

            't_cantidad_creditos_2' =>  $totales->t_cantidad_creditos_2,
            't_cantidad_clientes_2' =>  $totales->t_cantidad_clientes_2,
            't_saldo_capital_2' =>  $totales->t_saldo_capital_2,
            'espaciador_2' =>  $totales->espaciador_2,

            't_cantidad_creditos_3' =>  $totales->t_cantidad_creditos_3,
            't_cantidad_clientes_3' =>  $totales->t_cantidad_clientes_3,
            't_saldo_capital_3' =>  $totales->t_saldo_capital_3,
            'espaciador_3' =>  $totales->espaciador_3,

            't_cantidad_creditos_4' =>  $totales->t_cantidad_creditos_4,
            't_cantidad_clientes_4' =>  $totales->t_cantidad_clientes_4,
            't_saldo_capital_4' =>  $totales->t_saldo_capital_4,
            'espaciador_4' =>  $totales->espaciador_4,

            't_cantidad_creditos_5' =>  $totales->t_cantidad_creditos_5,
            't_cantidad_clientes_5' =>  $totales->t_cantidad_clientes_5,
            't_saldo_capital_5' =>  $totales->t_saldo_capital_5,
            'espaciador_5' =>  $totales->espaciador_5,

            't_cantidad_creditos_6' =>  $totales->t_cantidad_creditos_6,
            't_cantidad_clientes_6' =>  $totales->t_cantidad_clientes_6,
            't_saldo_capital_6' =>  $totales->t_saldo_capital_6,
            'espaciador_6' =>  $totales->espaciador_6,

            'espaciador_7' =>  $totales->espaciador_7,
            'espaciador_8' =>  $totales->espaciador_8,
            'espaciador_9' =>  $totales->espaciador_9,
            't_capital_seleccion_total' =>  $totales->t_capital_seleccion_total,

        ];

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");

        $inputFileName = './report_templates/creditos/reportes/rptControlMoraAgencia.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        // $spreadsheet = new Spreadsheet();
        // dd($spreadsheet);
        $sheet = $spreadsheet->getActiveSheet();

        $formato_encabezado = $sheet->getStyle('B7')->exportArray();

        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 34) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda = $sheet->getStyle($columna_1 . 7)->exportArray();

            $formato_totales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }



        $indice = 7;
        foreach ($cartera_mora as $item_1) {

            $columna_2 = 1;
            foreach ($item_1 as $valor_1) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor_1);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                $columna_2 += 1;
            }
            $indice += 1;
        }

        $columna_3 = 1;
        $sheet->setCellValue((new CreditosController)->num2char($columna_3) . $indice, 'TOTAL');
        $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_totales[$columna_3 - 1]);
        $columna_3 = 2;

        foreach ($data_totales as $valor_2) {
            $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_totales[$columna_3 - 1]);
            $sheet->setCellValue((new CreditosController)->num2char($columna_3) . $indice, $valor_2);
            $columna_3 += 1;
        }
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptControlMoraAgencia', 5);


        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function fecha_ultimo_mes(request $request)
    {
        $agencia_id = $request->input('agencia_id');

        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $mes = date("m", strtotime($fecha_actual));
        $mes = ltrim($mes, "0");
        $año = date("Y", strtotime($fecha_actual));

        if ($mes == 1) {
            $mes = 12;
            $año -= 1;
        } else {
            $mes -= 1;
        }

        return ['año_actual' =>  $año, 'mes_actual' => $mes];
    }

    public function buscar_mensual(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion_records = 'records_' .  $agencia_id;
        $conexion_master = 'master_' .  $agencia_id;
        $db_master = 'solucion_master_' .  $agencia_id;

        $año_desde = $request->año_desde;
        $año_hasta = $request->año_hasta;

        $desde_mes = $request->desde_mes;
        $hasta_mes = $request->hasta_mes;

        $riesgos_seleccionados = json_decode($request->riesgos_seleccionados);

        $filtro_usuario = filter_var($request->filtro_usuario, FILTER_VALIDATE_BOOLEAN);

        if ($filtro_usuario) {

            $usuarios = json_decode($request->usuarios);
            $rango = CreditoResumenRecord::on($conexion_records)->from('credito_resumen_records as cre_res_rec')
                ->select(
                    'cre_res_rec.id',

                )
                ->whereIn('cre_res_rec.asesor_id', $usuarios)
                ->whereBetween(DB::raw("year(cre_res_rec.fecha_cartera)"), [$año_desde, $año_hasta])
                ->get();
        } else {
            $rango = CreditoResumenRecord::on($conexion_records)->from('credito_resumen_records as cre_res_rec')
                ->select(
                    'cre_res_rec.id',

                )
                ->whereBetween(DB::raw("year(cre_res_rec.fecha_cartera)"), [$año_desde, $año_hasta])
                ->get();
        }

        $rango_fechas = [];
        $rango_fecha_mes = [];

        $fecha1 = $año_desde . '-' . $desde_mes . '-01';
        $fecha2 = $año_hasta . '-' . $hasta_mes . '-01';

        $fecha1 = date("Y-m-d", strtotime($fecha1));
        $fecha2 = date("Y-m-d", strtotime($fecha2));

        for ($i = $fecha1; $i <= $fecha2; $i = date("Y-m-d", strtotime($i . "+ 1 month"))) {
            $rango_fechas[] = $i;
        }

        foreach ($rango_fechas as $item) {
            $item =  date("Y-m-01", strtotime($item));
            $rango_fecha_mes[] = $item;
        }

        $control_mora = CreditoResumenRecord::on($conexion_records)->from('credito_resumen_records as cre_res_rec')
            ->select(
                'cre_res_rec.asesor_id',
                'cre_res_rec.tipo_riesgo_id',
                'cre_res_rec.cantidad',
                'cre_res_rec.capital_total',
                'cre_res_rec.saldo_capital',
                'cre_res_rec.saldo_total',
                'cre_res_rec.porcentaje_tipo',
                'cre_res_rec.porcentaje_cartera',
                'cre_res_rec.porcentaje_cartera',
                'cre_res_rec.cantidad_clientes',
                'cre_res_rec.porcentaje_saldo_total',
                'cre_res_rec.porcentaje_saldo_cartera',
                'cre_res_rec.cantidad_clientes_activos',
                'cre_res_rec.tasa_promedio',

                'cre_res_rec.fecha_cartera as fecha',

                'usu.usuario as usuario_asesor',
                'cre_rie.tipo_riesgo'
            )
            ->join('solucion_master.usuarios as usu', 'cre_res_rec.asesor_id', 'usu.dni')
            ->join("$db_master.credito_central_riesgo as cre_rie", 'cre_res_rec.tipo_riesgo_id', 'cre_rie.id')
            ->whereIn('cre_res_rec.id', $rango)
            ->whereIn('cre_res_rec.fecha_cartera', $rango_fecha_mes)
            ->orderby('usu.usuario', 'asc')
            ->orderby('cre_res_rec.fecha_cartera', 'asc')
            ->get();

        $asesores = [];

        $tipo_riesgos = CentralRiesgo::on($conexion_master)->select('id')->orderby('id', 'asc')->get();

        $control_mora = $control_mora->toArray();
        $fechas = [];

        foreach ($control_mora as $item_1) {
            if (!in_array($item_1['fecha'], $fechas)) {
                $fechas[] = $item_1['fecha'];
            }
        }

        asort($fechas);

        foreach ($fechas as $item_2) {

            $asesores = [];

            $filtrado_fecha = [];

            $filtrado_fecha = array_filter($control_mora, function ($request) use ($item_2) {

                return  $request['fecha'] == $item_2;
            });

            foreach ($filtrado_fecha as $item_3) {
                if (!in_array($item_3['asesor_id'], $asesores)) {
                    $asesores[] = $item_3['asesor_id'];
                }
            }

            foreach ($asesores as $item_4) {

                $objeto_datos = (object) [
                    'fecha' => $item_2,
                    'asesor' =>  $item_4,
                    'saldo_total' => null,
                    'cantidad_creditos' => null,
                    'clientes_activos' => null,
                    'tasa_promedio' => null,

                ];

                $saldo_total = 0;
                $cantidad_creditos = 0;
                $clientes_activos = 0;
                $tasa_promedio = 0;
                $cantidad = 0;


                $clientes_seleccion_total = 0;
                $creditos_seleccion_total = 0;
                $porcentaje_seleccion_total = 0;
                $capital_seleccion_total = 0;



                foreach ($tipo_riesgos as $item_6) {

                    $variable_1 = 'cantidad_creditos_' . $item_6->id;
                    $variable_2 = 'cantidad_clientes_' . $item_6->id;
                    $variable_3 = 'saldo_capital_' . $item_6->id;
                    $variable_4 = 'porcentaje_' . $item_6->id;


                    $objeto_datos->$variable_1 = 0;
                    $objeto_datos->$variable_2 = 0;
                    $objeto_datos->$variable_3 = 0;
                    $objeto_datos->$variable_4 = 0;

                    foreach ($filtrado_fecha as $item_5) {

                        if ($item_5['asesor_id'] == $item_4 && $item_5['tipo_riesgo_id'] == $item_6->id) {

                            $cantidad += 1;

                            $saldo_total += $item_5['saldo_capital'];
                            $cantidad_creditos += $item_5['cantidad'];
                            $tasa_promedio += $item_5['tasa_promedio'];


                            // dd($tasa_promedio, $cantidad);
                            $objeto_datos->$variable_1 = $item_5['cantidad'];
                            $objeto_datos->$variable_2 = $item_5['cantidad_clientes'];
                            $objeto_datos->$variable_3 = $item_5['saldo_capital'];
                            $objeto_datos->$variable_4 = $item_5['porcentaje_saldo_cartera'];

                            $clientes_seleccion_total +=  $item_5['cantidad_clientes'];

                            if (in_array($item_5['tipo_riesgo_id'], $riesgos_seleccionados)) {

                                $creditos_seleccion_total +=  $item_5['cantidad'];
                                $porcentaje_seleccion_total +=  $item_5['porcentaje_saldo_cartera'];
                                $capital_seleccion_total += $item_5['saldo_capital'];
                            }
                            $objeto_datos->clientes_activos = $item_5['cantidad_clientes_activos'];
                        }
                    }
                }

                $objeto_datos->saldo_total = $saldo_total;
                $objeto_datos->cantidad_creditos = $cantidad_creditos;
                $objeto_datos->tasa_promedio = $tasa_promedio / $cantidad;

                $objeto_datos->clientes_seleccion_total = $clientes_seleccion_total;
                $objeto_datos->creditos_seleccion_total = $creditos_seleccion_total;
                $objeto_datos->porcentaje_seleccion_total = $porcentaje_seleccion_total;
                $objeto_datos->capital_seleccion_total = $capital_seleccion_total;

                $resultado[] = $objeto_datos;
            }
        }

        $arrayCompleto = [];

        foreach ($fechas as $value) {

            $objeto_datos = (object) [
                'fecha' => $value,
                "saldo_total" => 0,
                "cantidad_creditos" => 0,
                "clientes_activos" => 0,
                "tasa_promedio" => 0,

                "cantidad_creditos_1" => 0,
                "cantidad_clientes_1" => 0,
                "saldo_capital_1" => 0,
                "porcentaje_1" => 0,

                "cantidad_creditos_2" => 0,
                "cantidad_clientes_2" => 0,
                "saldo_capital_2" => 0,
                "porcentaje_2" => 0,

                "cantidad_creditos_3" => 0,
                "cantidad_clientes_3" => 0,
                "saldo_capital_3" => 0,
                "porcentaje_3" => 0,

                "cantidad_creditos_4" => 0,
                "cantidad_clientes_4" => 0,
                "saldo_capital_4" => 0,
                "porcentaje_4" => 0,

                "cantidad_creditos_5" => 0,
                "cantidad_clientes_5" => 0,
                "saldo_capital_5" => 0,
                "porcentaje_5" => 0,

                "cantidad_creditos_6" => 0,
                "cantidad_clientes_6" => 0,
                "saldo_capital_6" => 0,
                "porcentaje_6" => 0,

                "clientes_seleccion_total" => 0,
                "creditos_seleccion_total" => 0,
                "porcentaje_seleccion_total" => 0,
                "capital_seleccion_total" => 0
            ];

            $cantidad = 0;
            foreach ($resultado as $item) {

                if ($item->fecha == $value) {

                    $objeto_datos->saldo_total += floatval($item->saldo_total);
                    $objeto_datos->cantidad_creditos += floatval($item->cantidad_creditos);
                    $objeto_datos->clientes_activos += floatval($item->clientes_activos);

                    if ($item->tasa_promedio != null) {
                        $objeto_datos->tasa_promedio += floatval($item->tasa_promedio);
                        $cantidad += 1;
                    }

                    $objeto_datos->cantidad_creditos_1 += floatval($item->cantidad_creditos_1);
                    $objeto_datos->cantidad_clientes_1 += floatval($item->cantidad_clientes_1);
                    $objeto_datos->saldo_capital_1 += floatval($item->saldo_capital_1);
                    $objeto_datos->porcentaje_1 = null;
                    $objeto_datos->cantidad_creditos_2 += floatval($item->cantidad_creditos_2);
                    $objeto_datos->cantidad_clientes_2 += floatval($item->cantidad_clientes_2);
                    $objeto_datos->saldo_capital_2 += floatval($item->saldo_capital_2);
                    $objeto_datos->porcentaje_2 = null;
                    $objeto_datos->cantidad_creditos_3 += floatval($item->cantidad_creditos_3);
                    $objeto_datos->cantidad_clientes_3 += floatval($item->cantidad_clientes_3);
                    $objeto_datos->saldo_capital_3 += floatval($item->saldo_capital_3);
                    $objeto_datos->porcentaje_3 = null;
                    $objeto_datos->cantidad_creditos_4 += floatval($item->cantidad_creditos_4);
                    $objeto_datos->cantidad_clientes_4 += floatval($item->cantidad_clientes_4);
                    $objeto_datos->saldo_capital_4 += floatval($item->saldo_capital_4);
                    $objeto_datos->porcentaje_4 = null;
                    $objeto_datos->cantidad_creditos_5 += floatval($item->cantidad_creditos_5);
                    $objeto_datos->cantidad_clientes_5 += floatval($item->cantidad_clientes_5);
                    $objeto_datos->saldo_capital_5 += floatval($item->saldo_capital_5);
                    $objeto_datos->porcentaje_5 = null;
                    $objeto_datos->cantidad_creditos_6 += floatval($item->cantidad_creditos_6);
                    $objeto_datos->cantidad_clientes_6 += floatval($item->cantidad_clientes_6);
                    $objeto_datos->saldo_capital_6 += floatval($item->saldo_capital_6);
                    $objeto_datos->porcentaje_6 = null;
                    $objeto_datos->clientes_seleccion_total += floatval($item->clientes_seleccion_total);
                    $objeto_datos->creditos_seleccion_total += floatval($item->creditos_seleccion_total);
                    $objeto_datos->porcentaje_seleccion_total = null;
                    $objeto_datos->capital_seleccion_total += floatval($item->capital_seleccion_total);
                }
            }
            $objeto_datos->tasa_promedio = $objeto_datos->tasa_promedio / $cantidad;

            $arrayCompleto[] = $objeto_datos;
        }
        foreach ($arrayCompleto as $val) {
            $val->porcentaje_1 = floatval($val->saldo_capital_1) * 100 / $val->saldo_total;
            $val->porcentaje_2 = floatval($val->saldo_capital_2) * 100 / $val->saldo_total;
            $val->porcentaje_3 = floatval($val->saldo_capital_3) * 100 / $val->saldo_total;
            $val->porcentaje_4 = floatval($val->saldo_capital_4) * 100 / $val->saldo_total;
            $val->porcentaje_5 = floatval($val->saldo_capital_5) * 100 / $val->saldo_total;
            $val->porcentaje_6 = floatval($val->saldo_capital_6) * 100 / $val->saldo_total;
            $val->porcentaje_seleccion_total = $val->capital_seleccion_total * 100 / $val->saldo_total;
        }

        return $arrayCompleto;
    }
    public function exportar_mensual(Request $request)
    {
        // dd($request);

        $cartera_mora = json_decode($request->cartera_mora);
        $totales = json_decode($request->totales);

        $data_totales = [];


        $data_totales = (object)[
            't_saldo_total' =>  $totales->t_saldo_total,
            't_cantidad_creditos' => $totales->t_cantidad_creditos,
            't_clientes_activos' => $totales->t_clientes_activos,
            'p_tasa_promedio' => $totales->p_tasa_promedio / $totales->cantidad,

            't_cantidad_creditos_1' =>  $totales->t_cantidad_creditos_1,
            't_cantidad_clientes_1' =>  $totales->t_cantidad_clientes_1,
            't_saldo_capital_1' =>  $totales->t_saldo_capital_1,
            'espaciador_1' =>  $totales->espaciador_1,

            't_cantidad_creditos_2' =>  $totales->t_cantidad_creditos_2,
            't_cantidad_clientes_2' =>  $totales->t_cantidad_clientes_2,
            't_saldo_capital_2' =>  $totales->t_saldo_capital_2,
            'espaciador_2' =>  $totales->espaciador_2,

            't_cantidad_creditos_3' =>  $totales->t_cantidad_creditos_3,
            't_cantidad_clientes_3' =>  $totales->t_cantidad_clientes_3,
            't_saldo_capital_3' =>  $totales->t_saldo_capital_3,
            'espaciador_3' =>  $totales->espaciador_3,

            't_cantidad_creditos_4' =>  $totales->t_cantidad_creditos_4,
            't_cantidad_clientes_4' =>  $totales->t_cantidad_clientes_4,
            't_saldo_capital_4' =>  $totales->t_saldo_capital_4,
            'espaciador_4' =>  $totales->espaciador_4,

            't_cantidad_creditos_5' =>  $totales->t_cantidad_creditos_5,
            't_cantidad_clientes_5' =>  $totales->t_cantidad_clientes_5,
            't_saldo_capital_5' =>  $totales->t_saldo_capital_5,
            'espaciador_5' =>  $totales->espaciador_5,

            't_cantidad_creditos_6' =>  $totales->t_cantidad_creditos_6,
            't_cantidad_clientes_6' =>  $totales->t_cantidad_clientes_6,
            't_saldo_capital_6' =>  $totales->t_saldo_capital_6,
            'espaciador_6' =>  $totales->espaciador_6,

            'espaciador_7' =>  $totales->espaciador_7,
            'espaciador_8' =>  $totales->espaciador_8,
            'espaciador_9' =>  $totales->espaciador_9,
            't_capital_seleccion_total' =>  $totales->t_capital_seleccion_total,

        ];

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");

        $inputFileName = './report_templates/creditos/reportes/rptControlMoraAgenciaMensual.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        // $spreadsheet = new Spreadsheet();
        // dd($spreadsheet);
        $sheet = $spreadsheet->getActiveSheet();

        $formato_encabezado = $sheet->getStyle('B7')->exportArray();

        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 33) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda = $sheet->getStyle($columna_1 . 7)->exportArray();

            $formato_totales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }



        $indice = 7;
        foreach ($cartera_mora as $item_1) {

            $columna_2 = 1;
            foreach ($item_1 as $valor_1) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor_1);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                $columna_2 += 1;
            }
            $indice += 1;
        }

        $columna_3 = 1;
        $sheet->setCellValue((new CreditosController)->num2char($columna_3) . $indice, 'TOTAL');
        $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_totales[$columna_3 - 1]);
        $columna_3 = 2;

        foreach ($data_totales as $valor_2) {
            $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_totales[$columna_3 - 1]);
            $sheet->setCellValue((new CreditosController)->num2char($columna_3) . $indice, $valor_2);
            $columna_3 += 1;
        }
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptControlMoraAgenciaMensual', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
