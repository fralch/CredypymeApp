<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\Usuarios\Usuario;

use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Caja\ComisionPago;
use App\Models\Creditos\Mantenimiento\Credito\Comision;
use App\Models\Creditos\Caja\PagoCuota;
use App\Models\Creditos\Caja\PagoMora;
use App\Models\Creditos\Caja\PagoNotificacion;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Caja\Caja;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Credito\Records\CreditoResumenRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReporteProductividadController extends Controller
{
    public function productividad_asesor($modo)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_PRODUCTIVIDAD_ASESOR', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MI_PRODUCTIVIDAD_ASESOR', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {


                if ($modo == 'completo') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->where('cargo_id', 2)
                        ->where('habilitado', 1)
                        ->orderBy('usuario', 'asc')
                        ->get();
                } elseif ($modo == 'personal') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->where('dni', session('usuario_dni'))
                        ->orderBy('usuario', 'asc')
                        ->get();
                }

                return Inertia::render('Creditos/Reportes/Creditos/productividad_asesor', [
                    'modo' => $modo,
                    'usuarios' => $usuarios,
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
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $por_asesor = filter_var($request->por_asesor, FILTER_VALIDATE_BOOLEAN);

        $esquema = Cliente::on($conexion)->select(
            DB::raw("0 as pago_id"),
            DB::raw("0 as credito_id"),
            DB::raw("null as fecha_pago_credito"),
            DB::raw("null as fecha_pago"),
            DB::raw("null as asesor_id"),

            DB::raw("0 as capital"),
            DB::raw("0 as interes"),
            DB::raw("0 as redondeo"),
            DB::raw("0 as moras"),
            DB::raw("0 as notificaciones"),
            DB::raw("0 as dscto_mora"),
            DB::raw("0 as dscto_notificaciones"),
            DB::raw("0 as dscto_interes"),
        )->take(1);


        $rango = PagoCuota::on($conexion)->select('id')
            ->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_cuotas = PagoCuota::on($conexion)->from('caja_pago_cuotas as caj_pag_cuo')
                ->select(
                    'caj_pag_cuo.id as pago_id',
                    'cre_reg.id as credito_id',
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_cuo.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_cuo.fecha_pago',
                    'cre_reg.asesor_id',

                    'caj_pag_cuo.capital_pagado as capital',
                    'caj_pag_cuo.interes_pagado as interes',
                    'caj_pag_cuo.redondeo_pagado as redondeo',
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes")
                )
                ->join('credito_registros as cre_reg', 'caj_pag_cuo.credito_id', 'cre_reg.id')
                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id',  'cli_reg.id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                ->wherein('caj_pag_cuo.id', $rango);
        } else {
            $pago_cuotas = [];
        }

        $rango = PagoMora::on($conexion)->select('id')
            ->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_moras = PagoMora::on($conexion)->from('caja_pago_moras as caj_pag_mor')
                ->select(
                    'caj_pag_mor.id as pago_id',
                    'cre_reg.id as credito_id',
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_mor.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_mor.fecha_pago',
                    'cre_reg.asesor_id',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    'caj_pag_mor.monto as moras',
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes")
                )
                ->join('credito_registros as cre_reg', 'caj_pag_mor.credito_id', 'cre_reg.id')
                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id',  'cli_reg.id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                ->whereIn('caj_pag_mor.id', $rango);
        } else {
            $pago_moras = [];
        }

        $rango = PagoMora::on($conexion)->select('id')
            ->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_notificaciones = PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
                ->select(
                    'caj_pag_not.id as pago_id',
                    'cre_reg.id as credito_id',
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_not.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_not.fecha_pago',
                    'cre_reg.asesor_id',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    'caj_pag_not.monto as notificaciones',
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes")

                )
                ->join('credito_notificaciones as cre_not', 'caj_pag_not.notificacion_id', 'cre_not.id')
                ->join('credito_registros as cre_reg', 'cre_not.credito_id', 'cre_reg.id')
                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id',  'cli_reg.id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                ->whereIn('caj_pag_not.id', $rango);
        } else {
            $pago_notificaciones = [];
        }

        $rango = Credito::on($conexion)
            ->select('id')
            ->where('fecha_hora_cancelado', '<>', null)
            ->whereBetween('fecha_hora_cancelado', [$fecha_desde, $fecha_hasta])
            ->get();

        if (count($rango) > 0) {
            $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
            $estado_id = $estado->id;

            $descuentos = Credito::on($conexion)->from('credito_registros as cre_reg')
                ->select(
                    'cre_reg.id as pago_id',
                    'cre_reg.id as credito_id',
                    DB::raw("CONCAT(cre_reg.id,'-',cre_reg.fecha_hora_cancelado)as fecha_pago_credito"),
                    'cre_reg.fecha_hora_cancelado as fecha_pago',
                    'cre_reg.asesor_id',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    'cre_reg.dscto_mora_cancelado as dscto_mora',
                    'cre_reg.dscto_notificaciones_cancelado as dscto_notificaciones',
                    'cre_reg.dscto_interes_cancelado as dscto_interes'
                )
                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id',  'cli_reg.id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                ->where('cre_reg.estado_id', $estado_id)
                ->whereIn('cre_reg.id', $rango);
        } else {
            $descuentos = [];
        }

        $listas = [
            $pago_cuotas,
            $pago_moras,
            $pago_notificaciones,
            $descuentos
        ];

        foreach ($listas as $value) {
            if ($value != []) {
                $esquema->union($value);
            }
        }

        $lista = $esquema->orderBy('fecha_pago', 'desc')
            ->get();

        if ($por_asesor) {
            $asesor_id = $request->asesor_id;
            $lista = $lista->where('asesor_id', $asesor_id);
        }

        $lista = $lista->groupBy('fecha_pago_credito')->map(function ($row) {

            return (object)[
                'credito_id' => $row[0]->credito_id,
                'fecha_pago' =>  $row[0]->fecha_pago,
                'asesor_id' =>  $row[0]->asesor_id,
                'capital' => $row->sum('capital'),
                'interes' => $row->sum('interes'),
                'redondeo' => $row->sum('redondeo'),
                'moras' => $row->sum('moras'),
                'notificaciones' => $row->sum('notificaciones'),
                'dscto_mora' => $row->sum('dscto_mora'),
                'dscto_notificaciones' => $row->sum('dscto_notificaciones'),
                'dscto_interes' => $row->sum('dscto_interes')
            ];
        });

        $lista_cobranzas = [];
        foreach ($lista as  $value) {
            if ($value->asesor_id != null) {
                $value->total_pago = floatval($value->capital) +
                    floatval($value->interes) +
                    floatval($value->redondeo) +
                    floatval($value->moras) +
                    floatval($value->notificaciones) -
                    floatval($value->dscto_mora) -
                    floatval($value->dscto_notificaciones) -
                    floatval($value->dscto_interes);
                $value->total_productividad = floatval($value->total_pago) -
                    floatval($value->capital);

                $lista_cobranzas[] = $value;
            }
        }

        $lista_cobranzas = collect($lista_cobranzas);

        $total_capital = $lista_cobranzas->sum('capital');
        $total_interes = $lista_cobranzas->sum('interes');
        $total_redondeo = $lista_cobranzas->sum('redondeo');
        $total_mora = $lista_cobranzas->sum('moras');
        $total_notificaciones = $lista_cobranzas->sum('notificaciones');
        $total_dscto_mora = $lista_cobranzas->sum('dscto_mora') * -1;
        $total_dscto_notificaciones = $lista_cobranzas->sum('dscto_notificaciones') * -1;
        $total_dscto_interes = $lista_cobranzas->sum('dscto_interes') * -1;
        $total_pago = $lista_cobranzas->sum('total_pago');
        $total_productividad = $lista_cobranzas->sum('total_productividad');

        $lista_cobranzas_agrupado = $lista_cobranzas->groupBy('asesor_id')->map(function ($row) {

            return [
                'asesor_id' => $row[0]->asesor_id,
                'cantidad' => $row->count('credito_id'),
                'capital' => $row->sum('capital'),
                'interes' => $row->sum('interes'),
                'redondeo' => $row->sum('redondeo'),
                'moras' => $row->sum('moras'),
                'notificaciones' => $row->sum('notificaciones'),
                'dscto_mora' => $row->sum('dscto_mora'),
                'dscto_notificaciones' => $row->sum('dscto_notificaciones'),
                'dscto_interes' => $row->sum('dscto_interes'),
                'total_pago' => $row->sum('total_pago'),
                'total_productividad' => $row->sum('total_productividad')
            ];
        });

        // Para que los indices empiezen en 0
        $lista = $lista_cobranzas_agrupado->values();
        $lista = $lista->toArray();

        $conexion_records = 'records_' . $agencia_id;
        $total_saldo_capital = 0;

        $lista_cobranzas_agrupado = [];
        foreach ($lista as $value) {

            $value = (object)$value;

            $datos_asesor = Usuario::select('usuario')
                ->where('dni', $value->asesor_id)
                ->get()->last();

            $value->usuario_asesor = $datos_asesor->usuario;

            $saldos_asesor = CreditoResumenRecord::on($conexion_records)
                ->select(DB::raw("SUM(saldo_capital) as saldo_capital"))
                ->where([
                    ['fecha_cartera', $request->fecha_hasta],
                    ['asesor_id', $value->asesor_id]
                ])
                ->groupBy('asesor_id')->get()->last();


            if ($saldos_asesor == null) {
                $saldo_capital = 0;
            } else {
                $saldo_capital = $saldos_asesor->saldo_capital;
            }
            $value->saldo_capital =  $saldo_capital;
            $total_saldo_capital += $saldo_capital;

            $lista_cobranzas_agrupado[] = $value;
        }

        $totales = (object)[
            'total_saldo_capital' => $total_saldo_capital,
            'total_capital' => $total_capital,
            'total_interes' => $total_interes,
            'total_redondeo' => $total_redondeo,
            'total_mora' => $total_mora,
            'total_notificaciones' => $total_notificaciones,
            'total_dscto_mora' => $total_dscto_mora,
            'total_dscto_notificaciones' => $total_dscto_notificaciones,
            'total_dscto_interes' => $total_dscto_interes,
            'total_pago' => $total_pago,
            'total_productividad' => $total_productividad
        ];

        return [
            'lista_cobranzas_agrupado' => $lista_cobranzas_agrupado,
            'totales' => $totales
        ];
    }

    public function exportar(Request $request)
    {
        $modo = $request->modo;
        if ($modo == 'DETALLADO') {
            return $this->exportar_detallado($request);
        } else if ($modo == 'AGRUPADO') {
            return $this->exportar_agrupado($request);
        }
    }

    public function exportar_detallado($request)
    {

        $datos =   json_decode($request->datos);
        $totales =   json_decode($request->totales);
        $tipo = $request->tipo;

        $data = [];

        $orden = 1;
        foreach ($datos as $item) {
            $object = (object)[
                'numero' => $orden,
                'expediente' => $item->numero_expediente . '-' . $item->numero_credito,
                'numero_cuota' => $item->numero_cuota,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'asesor' =>  $item->usuario_asesor,
                'fecha_pago' => $item->fecha_pago,
                'capital' => $item->capital,
                'interes' => $item->interes,
                'redondeo' => $item->redondeo,
                'moras' => $item->moras,
                'notificaciones' => $item->notificaciones,
                'dscto_mora' => $item->dscto_mora,
                'dscto_notificaciones' => $item->dscto_notificaciones,
                'dscto_interes' => $item->dscto_interes,
                'total' => $item->total_pago,
                'productividad' => $item->total_productividad,
                'caja' => $item->usuario_caja

            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptProductividadDetallado');
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptProductividad.xlsx");
        $sheet = $spreadsheet->getActiveSheet();


        //    Obteniendo formatos-----------------------------
        $celda = 1;

        $lista_formato_celdas_1 = [];
        $lista_formato_celdas_2 = [];
        $lista_formato_totales = [];

        while ($celda <= 17) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celdas_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celdas_2 = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_totales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formato_celdas_1[] = $formato_celdas_1;
            $lista_formato_celdas_2[] = $formato_celdas_2;
            $lista_formato_totales[] = $formato_totales;

            $celda++;
        }

        $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);

        // Insertando datos-----------------------------
        $indice = 5;
        foreach ($data as $item) {
            $columna_2 = 1;

            foreach ($item as $value) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $value);
                if ($indice % 2 == 0) {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formato_celdas_2[$columna_2 - 1]);
                } else {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formato_celdas_1[$columna_2 - 1]);
                }
                $columna_2 += 1;
            }
            $indice += 1;
        }

        $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(9);

        $indice += 1;


        $sheet->setCellValue('G' . $indice, 'TOTAL');
        $sheet->setCellValue('H' . $indice, $totales->total_capital);
        $sheet->setCellValue('I' . $indice, $totales->total_interes);
        $sheet->setCellValue('J' . $indice, $totales->total_redondeo);
        $sheet->setCellValue('K' . $indice, $totales->total_mora);
        $sheet->setCellValue('L' . $indice, $totales->total_notificaciones);
        $sheet->setCellValue('M' . $indice, $totales->total_dscto_mora);
        $sheet->setCellValue('N' . $indice, $totales->total_dscto_notificaciones);
        $sheet->setCellValue('O' . $indice, $totales->total_dscto_interes);
        $sheet->setCellValue('P' . $indice, $totales->total_pago);
        $sheet->setCellValue('Q' . $indice, $totales->total_productividad);


        foreach ($lista_formato_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        // Exportar para descarga-------------------------

        if ($tipo == 'XLSX') {

            $writer = new Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $ret['data'] = base64_encode(ob_get_contents());
            ob_end_clean();
        } else if ($tipo == 'PDF') {

            $spreadsheet->getDefaultStyle()->applyFromArray(
                [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'ffffff'],
                        ],

                    ]
                ]
            );

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
            $writer->SetFont('verdana');

            ob_start();
            $writer->save('php://output');

            $ret['data'] = base64_encode(ob_get_contents());
            ob_end_clean();
        }

        return $ret['data'];
    }
    public function exportar_agrupado($request)
    {
        $datos =   json_decode($request->datos);
        $totales =   json_decode($request->totales);
        $tipo = $request->tipo;

        $data = [];

        foreach ($datos as $item) {
            $object = (object)[
                'asesor' =>  $item->usuario_asesor,
                'cantidad' =>  $item->cantidad,
                'saldo_capital' => $item->saldo_capital,
                'capital' => $item->capital,
                'interes' => $item->interes,
                'redondeo' => $item->redondeo,
                'moras' => $item->moras,
                'notificaciones' => $item->notificaciones,
                'dscto_mora' => $item->dscto_mora,
                'dscto_notificaciones' => $item->dscto_notificaciones,
                'dscto_interes' => $item->dscto_interes,
                'total' => $item->total_pago,
                'productividad' => $item->total_productividad,
            ];
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptProductividadAgrupado');
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptProductividad.xlsx");
        $sheet = $spreadsheet->getActiveSheet();


        //    Obteniendo formatos-----------------------------
        $celda = 1;

        $lista_formato_celdas_1 = [];
        $lista_formato_celdas_2 = [];
        $lista_formato_totales = [];

        while ($celda <= 13) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celdas_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celdas_2 = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_totales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formato_celdas_1[] = $formato_celdas_1;
            $lista_formato_celdas_2[] = $formato_celdas_2;
            $lista_formato_totales[] = $formato_totales;

            $celda++;
        }

        $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);

        // Insertando datos-----------------------------
        $indice = 5;
        foreach ($data as $item) {
            $columna_2 = 1;

            foreach ($item as $value) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $value);
                if ($indice % 2 == 0) {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formato_celdas_2[$columna_2 - 1]);
                } else {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formato_celdas_1[$columna_2 - 1]);
                }
                $columna_2 += 1;
            }
            $indice += 1;
        }

        $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(9);

        $indice += 1;


        $sheet->setCellValue('C' . $indice, 'TOTAL');
        $sheet->setCellValue('D' . $indice, $totales->total_saldo_capital);
        $sheet->setCellValue('E' . $indice, $totales->total_capital);
        $sheet->setCellValue('F' . $indice, $totales->total_interes);
        $sheet->setCellValue('G' . $indice, $totales->total_redondeo);
        $sheet->setCellValue('H' . $indice, $totales->total_mora);
        $sheet->setCellValue('I' . $indice, $totales->total_notificaciones);
        $sheet->setCellValue('J' . $indice, $totales->total_dscto_mora);
        $sheet->setCellValue('K' . $indice, $totales->total_dscto_notificaciones);
        $sheet->setCellValue('L' . $indice, $totales->total_dscto_interes);
        $sheet->setCellValue('M' . $indice, $totales->total_pago);
        $sheet->setCellValue('N' . $indice, $totales->total_productividad);


        foreach ($lista_formato_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        // Exportar para descarga-------------------------

        if ($tipo == 'XLSX') {

            $writer = new Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $ret['data'] = base64_encode(ob_get_contents());
            ob_end_clean();
        } else if ($tipo == 'PDF') {

            $spreadsheet->getDefaultStyle()->applyFromArray(
                [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'ffffff'],
                        ],

                    ]
                ]
            );

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
            $writer->SetFont('verdana');

            ob_start();
            $writer->save('php://output');

            $ret['data'] = base64_encode(ob_get_contents());
            ob_end_clean();
        }

        return $ret['data'];
    }
}
