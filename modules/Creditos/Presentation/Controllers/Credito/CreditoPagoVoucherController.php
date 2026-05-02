<?php

namespace Modules\Creditos\Presentation\Controllers\Credito;

use App\Http\Controllers\Controller;

use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoVoucher;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class CreditoPagoVoucherController extends Controller
{

    public function pago_voucher($cliente_id, $agencia_id)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'COPIA_VOUCHER', 'CREDITOS_CREDITO');
            if ($band == 1) {
                $conexion = 'master_' .  $agencia_id;

                $datos_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                    ->select(
                        'cli_reg.id',
                        'ag.nombre as agencia',
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cli_reg.dni',
                        'us.usuario as usuario_asesor'
                    )
                    ->leftjoin('solucion_master.usuarios as us', 'cli_reg.asesor_id', 'us.dni')
                    ->join('solucion_master.agencias as ag', 'ag.id_agencia', 'cli_reg.agencia_id')

                    ->where('cli_reg.id', $cliente_id)
                    ->get()->last();

                $datos_creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
                    ->select(
                        'cre_reg.id',

                        'cre_apr.monto',
                        'cre_apr.plazo',
                        'cre_apr.cuota',
                        'cre_apr.periodo_pago',

                        'caj_des.datos_creacion',

                        'cre_tip.tipo',
                        'cre_est.estado',
                        'cre_prod.producto',

                    )
                    ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                    ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
                    ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                    ->join('credito_productos as cre_prod', 'cre_apr.producto_id', 'cre_prod.id')
                    ->join('credito_estados as cre_est', 'cre_reg.estado_id', 'cre_est.id')
                    ->where('cre_reg.cliente_id', $cliente_id)
                    ->orderBy('cre_reg.fecha_desembolso', 'asc')
                    ->get();


                return Inertia::render('Creditos/Creditos/copia_voucher', [
                    'agencia_id' => intval($agencia_id),
                    'cliente_id' => intval($cliente_id),
                    'datos_creditos' => $datos_creditos,
                    'datos_cliente' => $datos_cliente,


                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function generar_voucher(Request $request)
    {

        // dd($request);
        // Estructura del JSON:

        // $datos_voucher = [
        //     'titulo',
        //     'cliente',
        //     'usuario_asesor',
        //     'monto_cuota',
        //     'cuota_actual',
        //     'conceptos' => ['concepto', 'importe'],
        //     'importe',
        //     'agencia_caja',
        //     'fecha_pago',
        //     'usuario_caja',
        //     'nombre_dispositivo'
        // ];

        $datos_voucher = json_decode($request->datos_voucher);
        $agencia_id = session('id_agencia');

        // Ordenando array de datos-------------------------------

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("./report_templates/caja/reportes/vchCobranza.xlsx");
        $spreadsheet = $reader->load("./report_templates/caja/reportes/vchCobranza.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        $formato_concepto = $sheet->getStyle("B10")->exportArray();
        $formato_importe = $sheet->getStyle("C10")->exportArray();

        // Insertando datos-----------------------------

        $sheet->setCellValue("A3", $datos_voucher->titulo);
        $sheet->setCellValue("A6", $datos_voucher->cliente);
        $sheet->setCellValue("B7", mb_strtoupper($datos_voucher->usuario_asesor));
        $sheet->setCellValue("B8", $datos_voucher->monto_cuota);
        $sheet->setCellValue("C8", $datos_voucher->cuota_actual);

        $fila = 10;

        foreach ($datos_voucher->conceptos as $item) {
            if ($item->concepto != 'Saldo total') {
                $sheet->setCellValue('A' . $fila, $item->concepto);
                $sheet->setCellValue('C' . $fila, $item->importe);

                $rango =  'A' . $fila . ':' . 'B' . $fila;
                $sheet->mergeCells($rango);

                $sheet->getStyle('A' . $fila)->applyFromArray($formato_concepto);
                $sheet->getStyle('C' . $fila)->applyFromArray($formato_importe);
                $fila += 1;

                $sheet->insertNewRowBefore($fila + 1);
            }
        }

        $sheet->setCellValue('C' . $fila + 2, $datos_voucher->importe);

        $datos_agencia = Agencia::select('celular', 'nombre')
            ->where('id_agencia', $agencia_id)
            ->get()->last();

        $texto = null;

        if ($datos_agencia->celular != null) {
            $texto = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $texto->createText('¿Alguna duda?, llámanos o escríbenos al Whatsapp ');
            $numero = $texto->createTextRun($datos_agencia->celular);
            $numero->getFont()->setBold(true);
            $numero->getFont()->setSize(7);


            $sheet->setCellValue('A' . $fila + 5, $texto);
            $sheet->setCellValue('A' . $fila + 6, 'AGENCIA ' . $datos_voucher->agencia_caja);
            $sheet->setCellValue('A' . $fila + 7, $datos_voucher->fecha_pago);
            $sheet->setCellValue('A' . $fila + 8, $datos_voucher->usuario_caja . ' - ' . $datos_voucher->nombre_dispositivo);
        } else {
            $sheet->setCellValue('A' . $fila + 5, 'AGENCIA ' . $datos_agencia->nombre);
            $sheet->setCellValue('A' . $fila + 6, $datos_voucher->fecha_pago);
            $sheet->setCellValue('A' . $fila + 7, $datos_voucher->usuario_caja . ' - ' . $datos_voucher->nombre_dispositivo);
            $sheet->setCellValue('A' . $fila + 8, null);
        }

        // Exportar para descarga-------------------------

        $spreadsheet->getDefaultStyle()->applyFromArray(
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'ffffff'],
                    ],

                ],
            ]
        );

        // Convertir PDF-------------------------

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('vchCobranza', 5);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }
    public function listar_detalle(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $credito_id = $request->credito_id;

        $cuotas_pagos = Cuota::on($conexion)->from('credito_cuotas')
            ->select(
                'credito_id',
                'numero_cuota',
                'fecha_vencimiento',
                'fecha_ultimo_pago',
                'estado',
                'capital',
                'interes',
                'redondeo',
                'cuota',
                'acumulado',
                'dias_atraso',
            )
            ->where('credito_id', $credito_id)->get();

        $vouchers = PagoVoucher::on($conexion)->from('caja_pago_vouchers as caj_pag_vou')
            ->select(
                'caj_pag_vou.id',
                'caj_pag_vou.credito_id',
                'caj_pag_vou.agencia_id',
                'caj_pag_vou.caja_id',
                'caj_pag_vou.datos_voucher',
                'caj_pag_vou.datos_creacion',
                'caj_pag_vou.numero_cuota',
                'caj_pag_vou.datos_creacion',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',


                DB::raw("SUBSTRING(caj_pag_vou.datos_creacion,11,10) as fecha_voucher"),
                DB::raw("SUBSTRING(caj_pag_vou.datos_creacion,21,9) as hora_voucher"),
                DB::raw("SUBSTRING(caj_pag_vou.datos_creacion,11,19) as fecha_hora_voucher"),

            )
            ->join('credito_registros as cre_reg', 'cre_reg.id', 'caj_pag_vou.credito_id')
            ->join('cliente_registros as cli_reg', 'cli_reg.id', 'cre_reg.cliente_id')
            ->where('caj_pag_vou.credito_id', $credito_id)
            ->orderby('caj_pag_vou.id', 'asc')
            ->get();

        foreach ($vouchers as $item) {
            $conexion = 'master_' .  $item->agencia_id;
            $datos_caja = Caja::on($conexion)->from('caja_registros as caj_reg')
                ->select('usu.usuario', 'age.nombre as agencia')
                ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                ->join('solucion_master.agencias as age', 'caj_reg.agencia_id', 'age.id_agencia')
                ->where('caj_reg.id', $item->caja_id)
                ->get()
                ->last();

            if ($datos_caja) {
                $item->agencia_caja = $datos_caja->agencia;
                $item->usuario_caja = $datos_caja->usuario;
            } else {
                $item->agencia_caja = null;
                $item->usuario_caja = null;
            }
        }

        // dd($vouchers);
        return [
            'cuotas_pagos' => $cuotas_pagos,
            'vouchers' => $vouchers,
        ];
    }
}
