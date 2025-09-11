<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Caja\AdelantoHaber;
use App\Models\Creditos\Caja\Caja;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\General\Agencia;


use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteAdelantoController extends Controller
{

    public function adelanto_haberes()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_ADELANTO_HABERES', 'CREDITOS_REPORTES');
            if ($band == 1) {

                $usuarios = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )->orderBy('usuario', 'asc')->get();

                return Inertia::render('Creditos/Reportes/Caja/adelanto_haberes', [
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


        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $filtro_usuario = $request->filtro_usuario;



        $agencias = Agencia::all();
        $adelantos = [];
        foreach ($agencias as $item_1) {
            $conexion_2 = 'master_' .  $item_1->id_agencia;

            $adelanto = AdelantoHaber::on($conexion_2)->from('transaccion_adelanto_haberes as tra_ade_hab')
                ->select(
                    'tra_ade_hab.id',
                    'tra_ade_hab.usuario_id',
                    'tra_ade_hab.descripcion',
                    'tra_ade_hab.monto',
                    'tra_ade_hab.agencia_caja',
                    'tra_ade_hab.caja_id',
                    'tra_ade_hab.fecha_adelanto',
                    'tra_ade_hab.datos_creacion',

                    'age.nombre as agencia',

                    'usu.apellido_paterno',
                    'usu.apellido_materno',
                    'usu.nombres',
                    'usu.usuario as usuario_adelanto'
                )
                ->join('solucion_master.usuarios as usu', 'tra_ade_hab.usuario_id', 'usu.dni')
                ->join('solucion_master.agencias as age', 'usu.agencia_id', 'age.id_agencia')
                ->whereBetween('tra_ade_hab.fecha_adelanto', [$fecha_desde, $fecha_hasta])
                ->where('tra_ade_hab.agencia_caja', $agencia_id)
                ->get();

            foreach ($adelanto as $item) {
                $adelantos[] = $item;
            }
        };


        $adelantos = collect($adelantos);

        if ($filtro_usuario == 'true') {

            $usuarios = json_decode($request->usuarios);
            $adelantos =  $adelantos->whereIn('usuario_id', $usuarios)->values();

            // dd($adelantos);
        }
        $adelantos =  $adelantos->sortByDesc('fecha_adelanto')->values();

        $index = 0;
        foreach ($adelantos as  $item) {
            $agencia_caja = $item->agencia_caja;
            $conexion_caja = 'master_' . $agencia_caja;
            $index += 1;
            $item->index = $index;

            $datos_caja = Caja::on($conexion_caja)->from('caja_registros as caj_reg')
                ->select('us.usuario', 'caj_reg.dni')
                ->join('solucion_master.usuarios as us', 'caj_reg.dni', 'us.dni')
                ->where('caj_reg.id', $item->caja_id)
                ->get()->last();


            $item->usuario_registro = $datos_caja->usuario;
            $item->usuario_dni_registro = $datos_caja->dni;
        }

        $total = $adelantos->sum('monto');
        // dd($adelantos);

        return [
            'adelantos' => $adelantos,
            'total' => $total
        ];
    }

    public function exportar(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $adelantos = json_decode($request->lista_adelantos);
        $total_adelantos = $request->total_adelantos;

        $data = [];

        $orden = 1;
        foreach ($adelantos as $item) {

            $object = (object)[
                'orden' => $orden,
                'fecha_adelanto' => $item->fecha_adelanto,
                'dni' => $item->usuario_id,
                'colaborador' => $item->usuario_adelanto,
                'agencia' => $item->agencia,
                'motivo' => $item->descripcion,
                'monto' => $item->monto,
                'otorgado_por' => $item->usuario_registro,
            ];

            $data[] = $object;
            $orden += 1;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/caja/reportes/rptAdelantoHaberes.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];
        $lista_formatos_totales = [];

        while ($celda <= 8) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda_1 = $sheet->getStyle($columna_1 . 4)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 5)->exportArray();

            $formato_totales = $sheet->getStyle($columna_1 . 6)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        $sheet->removeRow(5);
        $sheet->removeRow(6);

        // Insertando datos-----------------------------
        $indice = 4;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                if ($item->orden % 2 == 0) {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_2[$columna_2 - 1]);
                } else {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_2 - 1]);
                }

                $columna_2 += 1;
            }

            $indice += 1;
        }

        $sheet->setCellValue('H' . $indice, $total_adelantos);

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptAdelantoHaberes', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');


        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
    public function exportarAgrupado(Request $request)
    {
        // return $request;
        // Ordenando array de datos-------------------------------
        $adelantos = json_decode($request->lista_adelantos);

        $asesores = [];

        foreach ($adelantos as $key => $item) {
            if (!in_array($item->usuario_adelanto, $asesores)) {
                $asesores[] = $item->usuario_adelanto;
            }
        }
        $total_adelantos = 0;
        $data = [];
        $orden = 1;
        foreach ($asesores as $key => $value) {
            $data_asesor = (object)[
                'asesores' => $value,
                'adelantos' => [],
                'totales' => 0,
            ];
            foreach ($adelantos as $key => $item) {
                if ($item->usuario_adelanto == $value) {
                    $object = (object)[
                        'orden' => $orden,
                        'fecha_adelanto' => $item->fecha_adelanto,
                        'motivo' => $item->descripcion,
                        'agencia' => $item->agencia,
                        'monto' => $item->monto,
                        'otorgado_por' => $item->usuario_registro,

                    ];
                    $data_asesor->adelantos[] = $object;
                    $data_asesor->totales += $item->monto;
                    $total_adelantos += $item->monto;
                    $orden += 1;
                }
            }
            $data[] = $data_asesor;
        }
        // return $data;



        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/caja/reportes/rptAdelantoHaberesAgrupado.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_titulo = [];
        $lista_formatos_subtotal = [];
        $lista_formatos_pares = [];
        $lista_formatos_impares = [];

        while ($celda <= 6) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda_totales = $sheet->getStyle($columna_1 . 10)->exportArray();
            $formato_celda_subtotales = $sheet->getStyle($columna_1 . 8)->exportArray();
            $formato_celda_pares = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_celda_impares = $sheet->getStyle($columna_1 . 7)->exportArray();
            $formato_celda_subtitulo = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_titulo = $sheet->getStyle($columna_1 . 3)->exportArray();

            $lista_formatos_titulo[] = $formato_celda_titulo;
            $lista_formatos_pares[] = $formato_celda_pares;
            $lista_formatos_impares[] = $formato_celda_impares;
            $lista_formatos_subtotal[] = $formato_celda_subtotales;
            $celda++;
        }

        $sheet->removeRow(10);
        $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);

        $sheet->setCellValue('G' . 3, $request->fechas);
        $sheet->getStyle('G' . 3)->applyFromArray($lista_formatos_titulo);


        $indice = 4;
        foreach ($data as $item) {
            $indice += 1;
            $rango =  'B' . $indice . ':' . 'G' . $indice;
            $sheet->mergeCells($rango);

            $sheet->setCellValue('B' . $indice, strtoupper($item->asesores));
            $sheet->getStyle('B' . $indice)->applyFromArray($formato_celda_subtitulo);

            foreach ($item->adelantos as $item_1) {
                $indice += 1;

                $columna_2 = 1;
                $lista_completa = [
                    'orden' => $item_1->orden,
                    'fecha' => $item_1->fecha_adelanto,
                    'motivo' => $item_1->motivo,
                    'agencia' => $item_1->agencia,
                    'monto' => $item_1->monto,
                    'otorgado' => $item_1->otorgado_por,
                ];
                foreach ($lista_completa as $k => $valor_1) { // ORDENAR EL ARREGLO VALOR_1 PARA QUE CUADRE EN LA TABLA
                    $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor_1);
                    if ($indice % 2 == 0) {
                        $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_pares[$columna_2 - 1]);
                    }
                    if ($indice % 2 != 0) {
                        $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_impares[$columna_2 - 1]);
                    }
                    $columna_2 += 1;
                }
            }
            $indice += 1;
            $columna_sub = 5;
            $sheet->setCellValue((new CreditosController)->num2char($columna_sub) . $indice, $item->totales);
            $sheet->getStyle((new CreditosController)->num2char($columna_sub) . $indice)->applyFromArray($lista_formatos_subtotal[$columna_sub - 1]);
        }
        $indice += 2;

        $sheet->setCellValue('F' . $indice, $total_adelantos);
        $sheet->getStyle('F' . $indice)->applyFromArray($formato_celda_totales);


        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptAdelantoHaberesAgrupado', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');


        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
