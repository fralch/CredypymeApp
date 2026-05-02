<?php

namespace Modules\Creditos\Presentation\Controllers\Cuenta;

use App\Http\Controllers\Controller;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Movimiento;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Carbon\Carbon;

class CuentaMovimientoController extends Controller
{
    public function ultimos_movimientos($cuenta_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $fecha_sistema = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $fecha_actual = date("Y-m-d", strtotime(date($fecha_sistema)));
        $fecha_primer_dia = Carbon::parse($fecha_sistema)->startOfMonth()->toDateString();

        $ultimos_movimientos = Movimiento::on($conexion)->where('cuenta_id', $cuenta_id)
            ->where(DB::raw("SUBSTR(fecha_movimiento,1,10)"), '>=', $fecha_primer_dia)
            ->where(DB::raw("SUBSTR(fecha_movimiento,1,10)"), '<=', $fecha_actual)
            ->orderby('id', 'desc')->get();
        return [
            'ultimos_movimientos' => $ultimos_movimientos,
            'fecha_desde' => $fecha_primer_dia,
            'fecha_hasta' => $fecha_actual
        ];
    }

    public function listar_movimientos(Request $request)
    {

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $detalle_cuenta = json_decode($request->detalle_cuenta);
        $agencia_id = $detalle_cuenta->agencia_id;

        // dd($agencia_id);
        $cuenta_id = $detalle_cuenta->id;

        $conexion = 'master_' .  $agencia_id;

        $fecha_actual_servidor = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $fecha_desde = date("Y-m-d", strtotime($fecha_desde));
        $fecha_hasta = date("Y-m-d", strtotime($fecha_hasta));

        $lista_movimientos = Movimiento::on($conexion)->where('cuenta_id', $cuenta_id)
            ->where(DB::raw("SUBSTR(fecha_movimiento,1,10)"), '>=', $fecha_desde)
            ->where(DB::raw("SUBSTR(fecha_movimiento,1,10)"), '<=', $fecha_hasta)
            ->orderby('id', 'desc')->get();

        return ['lista_movimientos' => $lista_movimientos];
    }

    public function exportar(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $detalle_cuenta = json_decode($request->detalle_cuenta);
        $lista_movimientos = $this->listar_movimientos($request)['lista_movimientos'];

        $data = [];
        $orden = 1;

        foreach ($lista_movimientos as $item) {

            $object = (object)[
                'fecha_movimiento' => Date::dateTimeToExcel(Carbon::parse($item->fecha_movimiento)),
                'tipo' => $item->tipo,
                'descripcion' => $item->descripcion,
                'monto' => $item->monto,
            ];
            $orden += 1;

            $data[] = $object;
        }


        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptCuentaMovimientos.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();


        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 4) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $lista_formatos_celdas[] = $formato_celda_1;

            $celda++;
        }

        // Rellenando TÍTULO y ENCABEZADOS ---------------

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($detalle_cuenta->agencia_id));
        $titulo = 'MOVIMIENTOS EN CUENTA - ' . $detalle_cuenta->usuario;
        $sheet->setCellValue('B2', $titulo);
        $sheet->setCellValue('E3', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-------------------------------

        $indice = 5;

        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);

                $columna_2 += 1;
            }

            $indice += 1;
        }

        // Creando archivo y ruta-------------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCuentaMovimientos', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
