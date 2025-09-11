<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;
use App\Models\Creditos\Caja\PagoCuota;
use App\Models\General\Cargo;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Credito\Records\CreditoResumenRecord;
use App\Models\Creditos\Credito\CentralRiesgo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReporteControlMoraController extends Controller
{

    public function control_mora($modo)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_CONTROL_MORA', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MI_CONTROL_MORA', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ]);

                if ($modo == 'personal') {
                    $usuarios = Usuario::select('dni', 'usuario', 'agencia_id')
                        ->where('dni', session('usuario_dni'))
                        ->get();
                } else {
                    $usuarios = Usuario::select('dni', 'usuario', 'agencia_id', 'habilitado')
                        ->whereIn('cargo_id', $cargos)
                        ->get();
                }

                return Inertia::render('Creditos/Reportes/Creditos/control_mora', [
                    'modo' => $modo,
                    'usuarios' => $usuarios
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function buscar(Request $request)
    {

        $resultado = $this->buscar_mora($request);
        // dd($resultado);
        return $resultado;
    }

    public function exportar(Request $request)
    {

        // dd($request);
        $tipo = $request->tipo;
        $cartera_mora = json_decode($request->cartera_mora);

        $totales = json_decode($request->totales);

        $data_totales = [];

        $data_totales = (object)[
            't_saldo_total' =>  $totales->t_saldo_total,
            't_cantidad_creditos' => $totales->t_cantidad_creditos,
            't_clientes_activos' => $totales->t_clientes_activos,
            'p_tasa_promedio' => $totales->p_tasa_promedio / $totales->cantidad,
            'p_tasa_rotacion' => $totales->p_tasa_rotacion / $totales->cantidad,

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
            'p_total_riesgos' =>  $totales->p_total_riesgos,
            't_capital_seleccion_total' =>  $totales->t_capital_seleccion_total,

        ];



        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("./report_templates/creditos/reportes/rptControlMora.xlsx");

        $inputFileName = './report_templates/creditos/reportes/rptControlMora.xlsx';

        $spreadsheet = $reader->load($inputFileName);

        $sheet = $spreadsheet->getActiveSheet();

        $formato_encabezado = $sheet->getStyle('B7')->exportArray();

        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 34) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda = $sheet->getStyle($columna_1 . 8)->exportArray();
            $formato_totales = $sheet->getStyle($columna_1 . 9)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        // $sheet->removeRow(9);

        $indice = 7;
        foreach ($cartera_mora as $item) {
            $rango =  'B' . $indice . ':' . 'AI' . $indice;

            $sheet->mergeCells($rango);
            $sheet->setCellValue('B' . $indice, $item->usuario_asesor);
            $sheet->getStyle('B' . $indice)->applyFromArray($formato_encabezado);

            foreach ($item->datos as $item_1) {
                $indice += 1;

                $columna_2 = 1;
                foreach ($item_1 as $valor_1) {
                    $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor_1);
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                    $columna_2 += 1;
                }
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

        // Exportar para descarga-------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptControlMora', 5);

        if ($tipo == 'XLSX') {

            $writer = new Xlsx($spreadsheet);
            $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

            return ['path_xlsx' => $path_xlsx];
        } else if ($tipo == 'PDF') {

            $spreadsheet->getDefaultStyle()->applyFromArray(
                [
                    'borders' => [
                        'borderTop' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                        'borderBottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ]
                ]
            );

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);


            $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
            $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

            // dd($writer);
            return ['path_pdf' => $path_pdf];
        }
    }





    public function buscar_mora($var)
    {

        $agencia_id = $var->agencia_id;
        $conexion_records = 'records_' .  $agencia_id;
        $conexion_master = 'master_' .  $agencia_id;
        $db_master = 'solucion_master_' .  $agencia_id;

        $fecha_desde = $var->fecha_desde;
        $fecha_desde =  date("Y-m-d", strtotime($fecha_desde));
        $fecha_hasta = $var->fecha_hasta;
        $fecha_hasta =  date("Y-m-d", strtotime($fecha_hasta));

        $riesgos_seleccionados = json_decode($var->riesgos_seleccionados);

        $filtro_usuario = $var->filtro_usuario;

        if ($filtro_usuario == 'true') {
            $usuarios = json_decode($var->usuarios);
            $control_mora = CreditoResumenRecord::on($conexion_records)->from('credito_resumen_records as cre_res_rec')
                ->select(
                    'cre_res_rec.asesor_id',
                    'cre_res_rec.tipo_riesgo_id',
                    'cre_res_rec.cantidad',
                    'cre_res_rec.capital_total',
                    'cre_res_rec.saldo_capital',
                    'cre_res_rec.saldo_total',
                    'cre_res_rec.tasa_promedio',

                    'cre_res_rec.porcentaje_tipo',
                    'cre_res_rec.porcentaje_cartera',
                    'cre_res_rec.porcentaje_cartera',
                    'cre_res_rec.cantidad_clientes',
                    'cre_res_rec.porcentaje_saldo_total',
                    'cre_res_rec.porcentaje_saldo_cartera',
                    'cre_res_rec.cantidad_clientes_activos',
                    'cre_res_rec.cantidad_clientes_activos',
                    'cre_res_rec.fecha_cartera as fecha',

                    'usu.usuario as usuario_asesor',
                    'cre_rie.tipo_riesgo'
                )
                ->join('solucion_master.usuarios as usu', 'cre_res_rec.asesor_id', 'usu.dni')
                ->join("$db_master.credito_central_riesgo as cre_rie", 'cre_res_rec.tipo_riesgo_id', 'cre_rie.id')
                ->whereIn('cre_res_rec.asesor_id', $usuarios)
                ->whereBetween('cre_res_rec.fecha_cartera', [$fecha_desde, $fecha_hasta])
                ->orderby('usu.usuario', 'asc')
                ->orderby('cre_res_rec.fecha_cartera', 'asc')
                ->get();
        } else {
            $control_mora = CreditoResumenRecord::on($conexion_records)->from('credito_resumen_records as cre_res_rec')
                ->select(
                    'cre_res_rec.asesor_id',
                    'cre_res_rec.tipo_riesgo_id',
                    'cre_res_rec.cantidad',
                    'cre_res_rec.capital_total',
                    'cre_res_rec.saldo_capital',
                    'cre_res_rec.saldo_total',
                    'cre_res_rec.tasa_promedio',

                    'cre_res_rec.porcentaje_tipo',
                    'cre_res_rec.porcentaje_cartera',
                    'cre_res_rec.porcentaje_cartera',
                    'cre_res_rec.cantidad_clientes',
                    'cre_res_rec.porcentaje_saldo_total',
                    'cre_res_rec.porcentaje_saldo_cartera',
                    'cre_res_rec.cantidad_clientes_activos',
                    'cre_res_rec.cantidad_clientes_activos',

                    'cre_res_rec.fecha_cartera as fecha',

                    'usu.usuario as usuario_asesor',
                    'cre_rie.tipo_riesgo'
                )
                ->join('solucion_master.usuarios as usu', 'cre_res_rec.asesor_id', 'usu.dni')
                ->join("$db_master.credito_central_riesgo as cre_rie", 'cre_res_rec.tipo_riesgo_id', 'cre_rie.id')
                ->whereBetween('cre_res_rec.fecha_cartera', [$fecha_desde, $fecha_hasta])
                ->orderby('usu.usuario', 'asc')
                ->orderby('cre_res_rec.fecha_cartera', 'asc')
                ->get();
        }



        $asesores = [];

        $fecha_desde_pago = date("Y-m-d", strtotime($fecha_desde . "- 1 days"));


        foreach ($control_mora as $item) {
            if (!in_array($item->asesor_id, $asesores)) {
                $asesores[] = $item->asesor_id;
            }
        }

        $tipo_riesgos = CentralRiesgo::on($conexion_master)->select('id')->orderby('id', 'asc')->get();

        $control_mora = $control_mora->toArray();


        $cartera_mora = [];
        $fechas = [];

        foreach ($asesores as $item) {

            $filtrado_asesor = array_filter($control_mora, function ($var) use ($item) {
                return  $var['asesor_id'] == $item;
            });



            foreach ($filtrado_asesor as $element) {
                // $num += 1;
                $objeto_asesor = (object) [
                    'asesor_id' => $item,
                    'usuario_asesor' => $element['usuario_asesor'],
                    'datos' => [],

                ];
                break;
            }

            foreach ($filtrado_asesor as $item_2) {
                if (!in_array($item_2['fecha'], $fechas)) {
                    $fechas[] = $item_2['fecha'];
                }
            }


            foreach ($fechas as $item_3) {


                $interes_pagado = PagoCuota::on($conexion_master)
                    ->where('asesor_id', $item)
                    ->whereBetween('fecha_pago', [$fecha_desde_pago, $item_3])
                    ->sum('interes_pagado');


                $objeto_datos = (object) [
                    'fecha' => $item_3,
                    'saldo_total' => null,
                    'cantidad_creditos' => null,
                    'clientes_activos' => null,
                    'tasa_promedio' => null,
                    'tasa_rotacion' => null,

                ];

                $filtrado_fecha = array_filter($filtrado_asesor, function ($var) use ($item_3) {

                    return  $var['fecha'] == $item_3;
                });


                $saldo_total = 0;
                $cantidad_creditos = 0;
                $clientes_activos = 0;
                $tasa_promedio = 0;
                $tasa_rotacion = 0;
                $cantidad = 0;


                $clientes_seleccion_total = 0;
                $creditos_seleccion_total = 0;
                $porcentaje_seleccion_total = 0;
                $capital_seleccion_total = 0;

                foreach ($tipo_riesgos as $item_4) {

                    $variable_1 = 'cantidad_creditos_' . $item_4->id;
                    $variable_2 = 'cantidad_clientes_' . $item_4->id;
                    $variable_3 = 'saldo_capital_' . $item_4->id;
                    $variable_4 = 'porcentaje_' . $item_4->id;


                    $objeto_datos->$variable_1 = 0;
                    $objeto_datos->$variable_2 = 0;
                    $objeto_datos->$variable_3 = 0;
                    $objeto_datos->$variable_4 = 0;

                    foreach ($filtrado_fecha as $item_5) {

                        if ($item_5['tipo_riesgo_id'] == $item_4->id) {

                            $cantidad += 1;

                            $saldo_total += $item_5['saldo_capital'];
                            $cantidad_creditos += $item_5['cantidad'];
                            $clientes_activos += $item_5['cantidad_clientes'];
                            $tasa_promedio += $item_5['tasa_promedio'];




                            $objeto_datos->$variable_1 = $item_5['cantidad'];
                            $objeto_datos->$variable_2 = $item_5['cantidad_clientes'];
                            $objeto_datos->$variable_3 = $item_5['saldo_capital'];
                            $objeto_datos->$variable_4 = $item_5['porcentaje_saldo_cartera'];

                            $clientes_seleccion_total +=  $item_5['cantidad_clientes'];

                            if (in_array($item_5['tipo_riesgo_id'], $riesgos_seleccionados)) {

                                $creditos_seleccion_total +=  $item_5['cantidad'];
                                // $porcentaje_seleccion_total +=  $item_5['porcentaje_saldo_cartera'];
                                $capital_seleccion_total += $item_5['saldo_capital'];
                            }
                        }



                        $objeto_datos->clientes_activos = $item_5['cantidad_clientes_activos'];
                    }
                }

                if ($var->tipo == 'Xasesor') {
                    if ($saldo_total == 0) {
                        $tasa_rotacion = 0;
                    } else {
                        $tasa_rotacion = ($interes_pagado / $saldo_total) * 100;
                    }
                }
                if ($var->tipo == 'Xagencia') {
                    $tasa_rotacion = $interes_pagado;
                }

                $objeto_datos->saldo_total = $saldo_total;
                $objeto_datos->cantidad_creditos = $cantidad_creditos;
                if ($tasa_promedio == 0) {
                    $objeto_datos->tasa_promedio = 0;
                } else {
                    $objeto_datos->tasa_promedio = $tasa_promedio / $cantidad;
                }

                $objeto_datos->tasa_rotacion = $tasa_rotacion;

                $objeto_datos->clientes_seleccion_total = $clientes_seleccion_total;
                $objeto_datos->creditos_seleccion_total = $creditos_seleccion_total;
                if ($saldo_total != 0) {
                    $objeto_datos->porcentaje_seleccion_total = ($capital_seleccion_total / $saldo_total) * 100;
                } else {

                    $objeto_datos->porcentaje_seleccion_total = 0;
                }
                $objeto_datos->capital_seleccion_total = $capital_seleccion_total;


                $objeto_asesor->datos[] = $objeto_datos;
            }


            $cartera_mora[] = $objeto_asesor;
        }

        return [
            'cartera_mora' => $cartera_mora,
            'lista_asesores' => $asesores,
            'tipo_riesgos' => $tipo_riesgos,
            'fechas' => $fechas,
            'tipo_riesgos_seleccionados' => $riesgos_seleccionados,
        ];
    }
}
