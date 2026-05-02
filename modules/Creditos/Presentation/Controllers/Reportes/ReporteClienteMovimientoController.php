<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\General\Presentation\Controllers\PermisosController;

use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Movimiento;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class ReporteClienteMovimientoController extends Controller
{
    public function movimientos()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_MOVIMIENTOS', 'CREDITOS_REPORTES');


            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ]);

                $usuarios = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )
                    ->whereIn('cargo_id', $cargos)
                    ->orderBy('usuario', 'asc')
                    ->get();

                return Inertia::render('Creditos/Reportes/Clientes/movimientos', [
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
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta  = $request->fecha_hasta;

        $por_asesor = filter_var($request->por_asesor, FILTER_VALIDATE_BOOLEAN);

        $lista_movimientos = Movimiento::on($conexion)->from('cliente_movimientos as cli_mov')
            ->select(
                'cli_mov.id',
                'cli_mov.dni',
                'cli_mov.cliente',
                'cli_mov.asesor_origen',
                'cli_mov.asesor_destino',
                DB::raw("SUBSTRING(cli_mov.datos_creacion,11,19) as fecha_movimiento"),


                'usu_1.usuario as usuario_asesor_origen',
                'usu_2.usuario as usuario_asesor_destino',
                'usu_3.usuario as usuario_registro'

            )
            ->join('solucion_master.usuarios as usu_1', 'cli_mov.asesor_origen', 'usu_1.dni')
            ->join('solucion_master.usuarios as usu_2', 'cli_mov.asesor_destino', 'usu_2.dni')
            ->join('solucion_master.usuarios as usu_3',  DB::raw("SUBSTR(cli_mov.datos_creacion,42,8)"), 'usu_3.dni')
            ->whereBetween(DB::raw("SUBSTRING(cli_mov.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
            ->orderBy('id', 'desc')
            ->get();

        if ($por_asesor) {
            $asesor_id = $request->asesor_id;
            $lista_movimientos = $lista_movimientos->filter(function ($item) use ($asesor_id) {
                return $item->asesor_origen == $asesor_id || $item->asesor_destino == $asesor_id;
            });
        }

        return ['lista_movimientos' => $lista_movimientos];
    }

    public function exportar(Request $request)
    {
        $tipo = $request->tipo;

        $lista_movimientos =
            json_decode($request->lista_movimientos);
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        // Ordenando array de datos-------------------------------
        $data = [];
        $orden = 1;
        foreach ($lista_movimientos as $item) {
            $object = (object)[
                'orden' => $orden,
                'dni' =>  $item->dni,
                'cliente' => $item->cliente,
                'asesor_origen' => $item->usuario_asesor_origen,
                'asesor_destino' => $item->usuario_asesor_destino,
                'fecha_registro' => $item->fecha_movimiento,
                'usuario_registro' => $item->usuario_registro,

            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load('./report_templates/creditos/reportes/rptClientesMovimientos.xlsx');
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];

        while ($celda <= 7) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 6)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;

            $celda++;
        }

        $sheet->removeRow(7);

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('F2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-----------------------------
        $indice = 5;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                if ($indice % 2 == 0) {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_2[$columna_2 - 1]);
                } else {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_2 - 1]);
                }

                $columna_2 += 1;
            }

            $indice += 1;
        }

        $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(9);

        $rango = "B$indice:H$indice";
        $sheet->getStyle($rango)
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THICK)
            ->setColor(new Color('#BCBCBC'));


        // Exportar para descarga-------------------------

        if ($tipo == 'XLSX') {

            $writer = new Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $ret['data'] = base64_encode(ob_get_contents());
            ob_end_clean();
        } else if ($tipo == 'PDF') {

            // $spreadsheet->getDefaultStyle()->applyFromArray(
            //     [
            //         'borders' => [
            //             'allBorders' => [
            //                 'borderStyle' => Border::BORDER_THIN,
            //                 'color' => ['rgb' => 'ffffff'],
            //             ],

            //         ]
            //     ]
            // );

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
