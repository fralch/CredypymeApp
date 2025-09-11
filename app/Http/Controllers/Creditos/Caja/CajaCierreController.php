<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Creditos\CreditosController;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class CajaCierreController extends Controller
{

    public function imprimir(Request $request)
    {
        $modo = $request->modo;

        if ($modo == 'billeteo') {
            return  $this->imprimir_billeteo($request);
        } elseif ($modo == 'cierre') {
            return  $this->imprimir_cierre($request);
        }
    }

    public function imprimir_billeteo($request)
    {
        $agencia_id = session('id_agencia');
        $billeteo = json_decode($request->billeteo);
        $usuario_caja = session('nombres') . ' - ' . session('usuario');

        // Leer Plantilla-------------------------
        $inputFileName = './report_templates/caja/reportes/rptBilleteo.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos -----------------------------

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));

        $sheet->setCellValue('C4', $usuario_caja);

        $sheet->setCellValue('C7', $billeteo->un_centimo);
        $sheet->setCellValue('C8', $billeteo->diez_centimos);
        $sheet->setCellValue('C9', $billeteo->veinte_centimos);
        $sheet->setCellValue('C10', $billeteo->cincuenta_centimos);
        $sheet->setCellValue('C11', $billeteo->un_sol);
        $sheet->setCellValue('C12', $billeteo->dos_soles);
        $sheet->setCellValue('C13', $billeteo->cinco_soles);
        $sheet->setCellValue('C14', $billeteo->diez_soles);
        $sheet->setCellValue('C15', $billeteo->veinte_soles);
        $sheet->setCellValue('C16', $billeteo->cincuenta_soles);
        $sheet->setCellValue('C17', $billeteo->cien_soles);
        $sheet->setCellValue('C18', $billeteo->doscientos_soles);

        $sheet->setCellValue('C23', session('nombres'));

        // Convertir PDF-------------------------

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptBilleteo', 5);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }

    public function imprimir_cierre($request)
    {
        $agencia_id = session('id_agencia');
        $datos_cierre = json_decode($request->datos_cierre);

        $usuario = $datos_cierre->caja_usuario;
        $fecha_apertura_sistema = $datos_cierre->fecha_apertura_sistema;
        $fecha_cierre_sistema = $datos_cierre->fecha_cierre_sistema;
        $fecha_apertura_real = $datos_cierre->fecha_apertura_real;
        $fecha_cierre_real = $datos_cierre->fecha_cierre_real;
        $operaciones = $datos_cierre->operaciones;
        $efectivo_caja = $datos_cierre->efectivo_caja;
        $total_ingresos = $datos_cierre->total_ingresos;
        $total_egresos = $datos_cierre->total_egresos;

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptCierreCaja.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];
        $lista_formatos_subtotales = [];
        $lista_formatos_totales = [];


        while ($celda <= 3) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda = $sheet->getStyle($columna_1 . 9)->exportArray();
            $formato_subtotales = $sheet->getStyle($columna_1 . 10)->exportArray();
            $formato_total = $sheet->getStyle($columna_1 . 11)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_subtotales[] = $formato_subtotales;
            $lista_formatos_totales[] = $formato_total;

            $celda++;
        }

        $sheet->removeRow(12);
        $sheet->removeRow(11);
        $sheet->removeRow(10);

        // Insertando datos-----------------------------

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));

        $sheet->setCellValue('B3', 'USUARIO: ' . $usuario);
        $sheet->setCellValue('B5', 'Fecha apertura de sistema: ' . $fecha_apertura_sistema);
        $sheet->setCellValue('B6', 'Fecha cierre de sistema: ' . $fecha_cierre_sistema);
        $sheet->setCellValue('C5', 'Fecha apertura real: ' . $fecha_apertura_real);
        $sheet->setCellValue('C6', 'Fecha cierre real: ' . $fecha_cierre_real);

        $indice = 9;

        foreach ($operaciones as $item) {
            $columna = 1;
            foreach ($item as $value) {
                $sheet->setCellValue((new CreditosController)->num2char($columna) . $indice, $value);
                $sheet->getStyle((new CreditosController)->num2char($columna) . $indice)->applyFromArray($lista_formatos_celdas[$columna - 1]);
                $columna += 1;
            }

            $indice += 1;
            $sheet->insertNewRowBefore($indice);
        }

        $columna = 1;

        $sheet->setCellValue((new CreditosController)->num2char($columna) . $indice, 'SUBTOTAL');
        $sheet->setCellValue((new CreditosController)->num2char($columna + 1) . $indice, $total_ingresos);
        $sheet->setCellValue((new CreditosController)->num2char($columna + 2) . $indice, $total_egresos);

        foreach ($lista_formatos_subtotales as $item) {
            $sheet->getStyle((new CreditosController)->num2char($columna) . $indice)->applyFromArray($item);
            $columna += 1;
        }
        $sheet->getRowDimension($indice)->setRowHeight(20);

        $indice += 1;
        $sheet->insertNewRowBefore($indice);
        $sheet->setCellValue('B' . $indice, 'TOTAL EFECTIVO');

        $rango =  'C' . $indice . ':' . 'D' . $indice;
        $sheet->mergeCells($rango);
        $sheet->setCellValue('C' . $indice, $efectivo_caja);

        $sheet->getStyle('B' . $indice)->applyFromArray($lista_formatos_totales[0]);
        $sheet->getStyle('C' . $indice)->applyFromArray($lista_formatos_totales[1]);

        $sheet->setCellValue('C' . $indice + 2, $usuario);
        $sheet->getRowDimension($indice)->setRowHeight(25);

        // Convertir PDF-------------------------

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCierreCaja', 5);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }
}
