<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\General\PermisosController;

use App\Models\General\Cargo;
use App\Models\Gth\Usuarios\Usuario;

use App\Models\Creditos\Clientes\Transferencia;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class ReporteClienteTransferenciaController extends Controller
{
    public function transferencias()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_TRANSFERENCIAS', 'CREDITOS_REPORTES');


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

                return Inertia::render('Creditos/Reportes/Clientes/transferencias', [
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

        $lista_transferencias = Transferencia::on($conexion)->from('cliente_transferencias as cli_tra')
            ->select(
                'cli_tra.id',
                'cli_tra.dni',
                'cli_tra.cliente',
                'cli_tra.asesor_origen',
                'cli_tra.asesor_destino',
                'cli_tra.evaluacion_financiera',
                'cli_tra.album_fotos',
                DB::raw("SUBSTRING(cli_tra.datos_creacion,11,19) as fecha_transferencia"),

                'age_1.nombre as agencia_origen',
                'usu_1.usuario as usuario_asesor_origen',
                'age_2.nombre as agencia_destino',
                'usu_2.usuario as usuario_asesor_destino',
                'usu_3.usuario as usuario_registro'
            )
            ->join('solucion_master.agencias as age_1', 'cli_tra.agencia_origen', 'age_1.id_agencia')
            ->join('solucion_master.usuarios as usu_1', 'cli_tra.asesor_origen', 'usu_1.dni')
            ->join('solucion_master.agencias as age_2', 'cli_tra.agencia_destino', 'age_2.id_agencia')
            ->join('solucion_master.usuarios as usu_2', 'cli_tra.asesor_destino', 'usu_2.dni')
            ->join('solucion_master.usuarios as usu_3',  DB::raw("SUBSTR(cli_tra.datos_creacion,42,8)"), 'usu_3.dni')
            // ->whereBetween(DB::raw("SUBSTRING(cli_tra.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
            ->orderBy('id', 'desc')
            ->get();

        if ($por_asesor) {
            $asesor_id = $request->asesor_id;
            $lista_transferencias = $lista_transferencias->filter(function ($item) use ($asesor_id) {
                return $item->asesor_origen == $asesor_id;
            });
        }

        return ['lista_transferencias' => $lista_transferencias];
    }

    public function exportar(Request $request)
    {
        $tipo = $request->tipo;

        $lista_transferencias =
            json_decode($request->lista_transferencias);
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        // Ordenando array de datos-------------------------------
        $data = [];
        $orden = 1;
        foreach ($lista_transferencias as $item) {
            $object = (object)[
                'orden' => $orden,
                'dni' =>  $item->dni,
                'cliente' => $item->cliente,
                'agencia_origen' => $item->agencia_origen,
                'asesor_origen' => $item->usuario_asesor_origen,
                'agencia_destino' => $item->agencia_destino,
                'asesor_destino' => $item->usuario_asesor_destino,
                'evaluacion_financiera' => $item->evaluacion_financiera == 1 ? 'SI' : 'NO',
                'album_fotos' => $item->album_fotos == 1 ? 'SI' : 'NO',
                'fecha_registro' => $item->fecha_transferencia,
                'usuario_registro' => $item->usuario_registro,

            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load('./report_templates/creditos/reportes/rptClientesTransferencias.xlsx');
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];

        while ($celda <= 11) {
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
        $sheet->setCellValue('K2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

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

        $rango = "B$indice:L$indice";
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
