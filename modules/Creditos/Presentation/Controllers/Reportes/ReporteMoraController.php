<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CentralRiesgo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function GuzzleHttp\Promise\each;

class ReporteMoraController extends Controller
{

    public function mora_asesor($modo)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MORA_ASESOR', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MI_MORA_ASESOR', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ])->get();

                if ($modo == 'personal') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->where('dni', session('usuario_dni'))
                        ->get();
                } else if ($modo == 'completo') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->whereIn('cargo_id', $cargos)
                        ->orderBy('usuario', 'asc')
                        ->get();
                }
                return Inertia::render('Creditos/Reportes/Creditos/mora_asesor', [
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
        $agencia_id = $request->agencia_id;


        $filtro_usuario = $request->filtro_usuario;
        $lista_asesores = [];
        if ($filtro_usuario == 'true') {
            $lista_asesores = json_decode($request->usuarios);
        }

        $lista_detalle = $this->mora_detalle($agencia_id, $lista_asesores);
        $lista_resumen = $this->mora_resumen($agencia_id, $lista_asesores);

        return [
            'lista_detalle' => $lista_detalle,
            'lista_resumen' => $lista_resumen
        ];
    }



    public function mora_detalle($agencia_id, $lista_asesores)
    {
        $conexion = 'master_' .  $agencia_id;

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        if ($lista_asesores == []) {
            $rango = Credito::on($conexion)->select('id')
                ->where('estado_id', $estado_id)
                ->get();
        } else {
            $rango = Credito::on($conexion)->select('id')
                ->where('estado_id', $estado_id)
                ->whereIn('asesor_id', $lista_asesores)
                ->get();
        }

        $rango = $rango->toArray();

        $lista_creditos = [];
        foreach (array_chunk($rango, 1000) as $row) {
            $creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
                ->select(
                    'cre_reg.id',
                    'cre_reg.dias_atraso',
                    'cre_reg.capital_total',
                    'cre_reg.capital_pagado',
                    'cre_reg.saldo_total',
                    'cre_reg.fecha_ultimo_pago',
                    'cre_reg.asesor_id',

                    'cli_reg.apellido_paterno',
                    'cli_reg.apellido_materno',
                    'cli_reg.nombres',

                    'cre_apr.plazo',
                    'cre_apr.periodo_pago',
                    'cre_apr.tasa_interes',

                    'usu.usuario as usuario_asesor',

                    DB::raw("(SELECT cre_cen_rie.nombre_breve
                FROM credito_central_riesgo cre_cen_rie
                WHERE cre_reg.dias_atraso between cre_cen_rie.dias_desde
                AND cre_cen_rie.dias_hasta) as tipo_riesgo")
                )
                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                ->join('solucion_master.usuarios as usu', 'cre_reg.asesor_id', 'usu.dni')
                ->whereIn('cre_reg.id', $row)
                ->orderBy('usu.usuario', 'asc')
                ->orderBy('dias_atraso', 'asc')
                ->get();

            foreach ($creditos as $item) {
                $lista_creditos[] = $item;
            }
        }

        return $lista_creditos;
    }
    public function mora_resumen($agencia_id, $lista_asesores)
    {
        $conexion = 'master_' .  $agencia_id;

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        if ($lista_asesores == []) {
            $rango = Credito::on($conexion)->select('id')
                ->where('estado_id', $estado_id)
                ->get();
        } else {
            $rango = Credito::on($conexion)->select('id')
                ->where('estado_id', $estado_id)
                ->whereIn('asesor_id', $lista_asesores)
                ->get();
        }

        $rango = $rango->toArray();

        $lista_creditos = [];
        foreach (array_chunk($rango, 1000) as $row) {
            $creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
                ->select(
                    'cre_reg.id',
                    'cre_reg.asesor_id',
                    DB::raw("COUNT(cre_reg.id) as cantidad_creditos"),
                    DB::raw("COUNT(DISTINCT(cre_reg.cliente_id)) as cantidad_clientes"),
                    DB::raw("SUM(cre_reg.capital_total) as capital_total"),
                    DB::raw("SUM(cre_reg.capital_pagado) as capital_pagado"),
                    DB::raw("SUM(cre_reg.saldo_total) as saldo_total"),

                    DB::raw("AVG(cre_apr.tasa_interes) as tasa_promedio"),


                    'usu.usuario as usuario_asesor',

                    DB::raw("(SELECT cre_cen_rie.id
                FROM credito_central_riesgo cre_cen_rie
                WHERE cre_reg.dias_atraso between cre_cen_rie.dias_desde
                AND cre_cen_rie.dias_hasta) as tipo_riesgo_id"),

                )
                ->join('solucion_master.usuarios as usu', 'cre_reg.asesor_id', 'usu.dni')
                ->join('credito_aprobaciones as cre_apr', 'cre_apr.id', 'cre_reg.aprobacion_id')
                ->whereIn('cre_reg.id', $row)
                ->groupBy('asesor_id', 'tipo_riesgo_id')
                ->orderBy('usu.usuario', 'asc')
                ->orderBy('tipo_riesgo_id', 'asc')
                ->get();

            foreach ($creditos as $item) {
                $clientes_activos = Credito::on($conexion)
                    ->whereIn('id', $row)
                    ->where('asesor_id', $item->asesor_id)
                    ->distinct('cliente_id')->count();

                $item->clientes_activos = $clientes_activos;

                $lista_creditos[] = $item;
            }
        }

        $lista_creditos = collect($lista_creditos);

        // dd($lista_creditos);

        $riesgos = CentralRiesgo::on($conexion)->select('id', 'nombre_breve')->get();

        foreach ($riesgos as $item) {
            $total_clientes = $lista_creditos->where('tipo_riesgo_id', $item->id)->sum('cantidad_creditos');
            $total_capital = $lista_creditos->where('tipo_riesgo_id', $item->id)->sum('capital_total');
            $total_capital_pagado = $lista_creditos->where('tipo_riesgo_id', $item->id)->sum('capital_pagado');

            $total_saldo_capital = $total_capital - $total_capital_pagado;

            foreach ($lista_creditos->where('tipo_riesgo_id', $item->id) as $item_1) {
                $item_1->porcentaje_cartera = ($item_1->cantidad_creditos / $total_clientes) * 100;
                $item_1->tipo_riesgo = $item->nombre_breve;

                $saldo_capital = $item_1->capital_total - $item_1->capital_pagado;

                if($total_saldo_capital == 0){

                    $item_1->porcentaje_riesgo = 0;
                }else{
                    $item_1->porcentaje_riesgo = $saldo_capital / $total_saldo_capital;
                }

                

            }
        }

        // dd($lista_creditos);

        $lista_creditos = $lista_creditos->values();

        return $lista_creditos;
    }

    public function exportar(Request $request)
    {
        $modo = $request->modo;

        if ($modo == 'detalle') {
            return $this->exportar_detallado($request);
        } else if ($modo == 'resumen') {
            return $this->exportar_resumido($request);
        }
    }

    public function exportar_detallado($request)
    {
        $tipo = $request->tipo;

        $agencia_id = $request->agencia_id;


        $lista_datos = json_decode($request->lista_datos);
        // Ordenando array de datos-------------------------------

        $data = [];
        $total_asesores = [];
        $total_general = (object)[
            'total_cantidad' => 0,
            'total_capital' => 0,
            'total_saldo_capital' => 0,
            'total_saldo_total' => 0
        ];
        $asesores = [];

        $lista_datos = collect($lista_datos);
        // $orden = 1;

        foreach ($lista_datos as $item) {

            if (!in_array($item->asesor_id, $asesores)) {
                $asesores[] = $item->asesor_id;
                $total_asesores[] = (object)[
                    'asesor_id' => $item->asesor_id,
                    'usuario_asesor' => $item->usuario_asesor,
                    'total_cantidad' => 0,
                    'total_capital' => 0,
                    'total_saldo_capital' => 0,
                    'total_saldo_total' => 0
                ];
            }


            $object = (object)[
                // 'numero' => $orden,

                'asesor' => $item->usuario_asesor,
                'tipo_riesgo' => $item->tipo_riesgo,
                'dias_atraso' => $item->dias_atraso,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'tasa_interes' => floatval($item->tasa_interes / 100),
                'capital' => $item->capital_total,
                'plazo' => round($item->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($item->periodo_pago),
                'periodo_pago' => $item->periodo_pago,
                'fecha_ultimo_pago' => $item->fecha_ultimo_pago,
                'saldo_capital' => $item->capital_total - $item->capital_pagado,
                'saldo_total' => $item->saldo_total
            ];
            // $orden += 1;

            $data[] = $object;
        }

        foreach ($total_asesores as $item) {
            $item->total_cantidad = $lista_datos->where('asesor_id', $item->asesor_id)->count();
            $item->total_capital = $lista_datos->where('asesor_id', $item->asesor_id)->sum('capital_total');
            $item->total_saldo_capital = $lista_datos->where('asesor_id', $item->asesor_id)->sum('capital_total') - $lista_datos->where('asesor_id', $item->asesor_id)->sum('capital_pagado');
            $item->total_saldo_total = $lista_datos->where('asesor_id', $item->asesor_id)->sum('saldo_total');


            $total_general->total_cantidad += $item->total_cantidad;
            $total_general->total_capital += $item->total_capital;
            $total_general->total_saldo_capital += $item->total_saldo_capital;
            $total_general->total_saldo_total += $item->total_saldo_total;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptMoraAsesorDetalle.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];

        $lista_formatos_subtotales = [];
        $lista_formatos_totales = [];

        $formato_encabezado = $sheet->getStyle("B5")->exportArray();

        while ($celda <= 12) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 7)->exportArray();


            $formato_subtotales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $formato_totales = $sheet->getStyle($columna_1 . 10)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;

            $lista_formatos_subtotales[] = $formato_subtotales;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        // dd($lista_formatos_celdas[10]);
        $sheet->removeRow(5);
        $sheet->removeRow(6);
        $sheet->removeRow(7);
        $sheet->removeRow(8);
        $sheet->removeRow(9);
        $sheet->removeRow(10);

        if ($tipo == "completo") {
            $sheet->setCellValue('B2', 'MORA POR ASESOR (DETALLE)');
        }
        if ($tipo == "personal") {
            $sheet->setCellValue('B2', 'MI MORA POR ASESOR (DETALLE)');
        }

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));


        // Insertando datos-----------------------------
        $indice = 5;
        foreach ($total_asesores as $item) {
            $columna_2 = 1;

            $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $item->usuario_asesor);
            $rango =  'B' . $indice . ':' . 'M' . $indice;
            $sheet->mergeCells($rango);
            $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($formato_encabezado);

            $indice += 1;

            $orden = 1;

            foreach (collect($data)->where('asesor', $item->usuario_asesor) as $item_1) {

                $columna_3 = 2;
                $sheet->setCellValue('B' . $indice, $orden);

                if ($indice % 2 == 0) {
                    $sheet->getStyle('B' . $indice)->applyFromArray($lista_formatos_celdas_1[0]);
                } else {
                    $sheet->getStyle('B' . $indice)->applyFromArray($lista_formatos_celdas_2[0]);
                }

                foreach ($item_1 as $valor) {

                    $sheet->setCellValue((new CreditosController)->num2char($columna_3) . $indice, $valor);


                    if ($indice % 2 == 0) {
                        $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_3 - 1]);
                    } else {
                        $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_celdas_2[$columna_3 - 1]);
                    }

                    $columna_3 += 1;
                }

                $orden += 1;
                $indice += 1;
            }

            $sheet->setCellValue('F' . $indice, $item->total_cantidad . ' crédito(s)');
            $sheet->setCellValue('H' . $indice, $item->total_capital);
            $sheet->setCellValue('L' . $indice, $item->total_saldo_capital);
            $sheet->setCellValue('M' . $indice, $item->total_saldo_total);

            foreach ($lista_formatos_subtotales as $key => $value) {
                $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
            }
            $indice += 2;
            $sheet->insertNewRowBefore($indice + 1);
        }

        $sheet->setCellValue('F' . $indice, $total_general->total_cantidad . ' crédito(s)');
        $sheet->setCellValue('H' . $indice, $total_general->total_capital);
        $sheet->setCellValue('L' . $indice, $total_general->total_saldo_capital);
        $sheet->setCellValue('M' . $indice, $total_general->total_saldo_total);

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptMoraAsesorDetalle', 5);

        // Exportar para descarga-------------------------
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
    public function exportar_resumido($request)
    {
        $tipo = $request->tipo;

        $agencia_id = $request->agencia_id;

        $lista_datos = json_decode($request->lista_datos);
        // Ordenando array de datos-------------------------------

        $data = [];
        $total_asesores = [];
        $total_general = (object)[
            'total_creditos' => 0,
            'total_capital' => 0,
            'total_saldo_capital' => 0,
            'total_saldo_total' => 0
        ];
        $asesores = [];

        $lista_datos = collect($lista_datos);

        foreach ($lista_datos as $item) {

            if (!in_array($item->asesor_id, $asesores)) {
                $asesores[] = $item->asesor_id;
                $total_asesores[] = (object)[
                    'asesor_id' => $item->asesor_id,
                    'usuario_asesor' => $item->usuario_asesor,
                    'total_creditos' => 0,
                    'total_clientes' => 0,
                    'total_capital' => 0,
                    'total_saldo_capital' => 0,
                    'total_saldo_total' => 0
                ];
            }

            $object = (object)[

                'asesor' => $item->usuario_asesor,
                'tipo_riesgo' => $item->tipo_riesgo,
                'cantidad_creditos' => $item->cantidad_creditos,
                'clientes_tipo' => $item->cantidad_clientes,
                'clientes_activos' => $item->clientes_activos,

                'tasa_promedio' => ($item->tasa_promedio / 100),

                'porcentaje_creditos' => 0,
                'porcentaje_tipo' => ($item->porcentaje_cartera / 100),
                'capital' => $item->capital_total,
                'saldo_capital' => $item->capital_total - $item->capital_pagado,
                'porcentaje_riesgo' => $item->porcentaje_riesgo,
                'porcentaje_saldo' => 0,
                'saldo_total' => $item->saldo_total

            ];

            $data[] = $object;
        }

        foreach ($total_asesores as $item) {
            $item->total_creditos = $lista_datos->where('asesor_id', $item->asesor_id)->sum('cantidad_creditos');
            $item->total_clientes = $lista_datos->where('asesor_id', $item->asesor_id)->sum('cantidad_clientes');
            $item->total_capital = $lista_datos->where('asesor_id', $item->asesor_id)->sum('capital_total');
            $item->total_saldo_capital = $lista_datos->where('asesor_id', $item->asesor_id)->sum('capital_total') - $lista_datos->where('asesor_id', $item->asesor_id)->sum('capital_pagado');
            $item->total_saldo_total = $lista_datos->where('asesor_id', $item->asesor_id)->sum('saldo_total');


            $total_general->total_creditos += $item->total_creditos;
            $total_general->total_capital += $item->total_capital;
            $total_general->total_saldo_capital += $item->total_saldo_capital;
            $total_general->total_saldo_total += $item->total_saldo_total;

            foreach ($data as $item_1) {
                if ($item->usuario_asesor == $item_1->asesor) {
                    $item_1->porcentaje_creditos = $item_1->cantidad_creditos / $item->total_creditos;
                    $item_1->porcentaje_saldo = $item_1->saldo_capital / $item->total_saldo_capital;
                }
            }
        }



        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptMoraAsesorResumen.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];
        $lista_formatos_subtotales = [];
        $lista_formatos_totales = [];

        $formato_encabezado = $sheet->getStyle("B5")->exportArray();

        while ($celda <= 14) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 7)->exportArray();

            $formato_subtotales = $sheet->getStyle($columna_1 . 8)->exportArray();
            $formato_totales = $sheet->getStyle($columna_1 . 10)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;

            $lista_formatos_subtotales[] = $formato_subtotales;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }



        $sheet->removeRow(5);
        $sheet->removeRow(6);
        $sheet->removeRow(7);
        $sheet->removeRow(8);
        $sheet->removeRow(9);
        $sheet->removeRow(10);

        if ($tipo == "completo") {
            $sheet->setCellValue('B2', 'MORA POR ASESOR (RESUMEN)');
        }
        if ($tipo == "personal") {
            $sheet->setCellValue('B2', 'MI MORA POR ASESOR (RESUMEN)');
        }


        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));

        // Insertando datos-----------------------------
        $indice = 5;

        foreach ($total_asesores as $item) {
            $columna_2 = 1;

            $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $item->usuario_asesor);
            $rango =  'B' . $indice . ':' . 'O' . $indice;
            $sheet->mergeCells($rango);
            $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($formato_encabezado);

            $indice += 1;
            $orden = 1;

            foreach (collect($data)->where('asesor', $item->usuario_asesor) as $item_1) {

                $columna_3 = 2;
                $sheet->setCellValue('B' . $indice, $orden);

                if ($indice % 2 == 0) {
                    $sheet->getStyle('B' . $indice)->applyFromArray($lista_formatos_celdas_1[0]);
                } else {
                    $sheet->getStyle('B' . $indice)->applyFromArray($lista_formatos_celdas_2[0]);
                }

                foreach ($item_1 as $valor) {
                    $sheet->setCellValue((new CreditosController)->num2char($columna_3) . $indice, $valor);
                    if ($indice % 2 == 0) {
                        $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_3 - 1]);
                    } else {
                        $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_celdas_2[$columna_3 - 1]);
                    }
                    $columna_3 += 1;
                }
                $orden += 1;

                $indice += 1;
            }

            $sheet->setCellValue('D' . $indice, 'SUBTOTAL');
            $sheet->setCellValue('E' . $indice, $item->total_creditos);
            $sheet->setCellValue('F' . $indice, $item->total_clientes);
            $sheet->setCellValue('K' . $indice, $item->total_capital);
            $sheet->setCellValue('L' . $indice, $item->total_saldo_capital);
            $sheet->setCellValue('O' . $indice, $item->total_saldo_total);

            foreach ($lista_formatos_subtotales as $key => $value) {
                $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
            }
            $indice += 2;
            $sheet->insertNewRowBefore($indice + 1);
        }

        $sheet->setCellValue('D' . $indice, 'TOTAL');
        $sheet->setCellValue('E' . $indice, $total_general->total_creditos);
        $sheet->setCellValue('K' . $indice, $total_general->total_capital);
        $sheet->setCellValue('L' . $indice, $total_general->total_saldo_capital);
        $sheet->setCellValue('O' . $indice, $total_general->total_saldo_total);

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }
        // Exportar para descarga-------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptMoraAsesorResumen', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        // dd($nombre_archivo);

        return ['path_xlsx' => $path_xlsx];
    }
}
