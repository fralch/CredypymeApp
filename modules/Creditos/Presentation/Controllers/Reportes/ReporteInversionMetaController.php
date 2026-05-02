<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMeta;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMetaMovimiento;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Inversion\ProductosMeta;



use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class ReporteInversionMetaController extends Controller
{
    public function movimientos_meta()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'INVERSION_MOVIMIENTOS_META', 'CREDITOS_REPORTES');

            if ($band == 1) {
                $agencias = Agencia::all();
                $lista_productos_meta = [];
                foreach ($agencias as $item) {
                    $conexion = 'master_' .  $item->id_agencia;

                    $id_agencia = $item->id_agencia;
                    $productos_meta = ProductosMeta::on($conexion)
                        ->select(
                            'id',
                            'producto',
                            DB::raw("$id_agencia as agencia_id")



                        )
                        ->where('habilitado', 1)
                        ->orderBy('id', 'asc')
                        ->get();
                    foreach ($productos_meta as $item) {
                        $lista_productos_meta[] = $item;
                    }
                    // dd($lista_productos_meta);
                }
                return Inertia::render('Creditos/Reportes/Inversion/movimientos_meta', [
                    'lista_productos_meta' => $lista_productos_meta,
                ]);
            } else {
                return redirect('/');
            }
        }
    }

    public function avance_meta($modo)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'INVERSION_AVANCE_META', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'INVERSION_MI_AVANCE_META', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {



                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ])->get();

                if ($modo == 'completo') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->whereIn('cargo_id', $cargos)
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

                $agencias = Agencia::all();
                $lista_productos_meta = [];

                foreach ($agencias as $item) {
                    $conexion = 'master_' .  $item->id_agencia;

                    $id_agencia = $item->id_agencia;
                    $productos_meta = ProductosMeta::on($conexion)
                        ->select(
                            'id',
                            'producto',
                            DB::raw("$id_agencia as agencia_id")



                        )
                        ->where('habilitado', 1)
                        ->orderBy('id', 'asc')
                        ->get();
                    foreach ($productos_meta as $item) {
                        $lista_productos_meta[] = $item;
                    }
                    // dd($lista_productos_meta);
                }

                // dd($lista_productos_meta);



                return Inertia::render('Creditos/Reportes/Inversion/avance_meta', [
                    'modo' => $modo,
                    'usuarios' => $usuarios,
                    'lista_productos_meta' => $lista_productos_meta,
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
        $fecha_hasta =  $request->fecha_hasta;

        $por_producto_meta = $request->por_producto_meta;


        $por_tipo_movimiento = filter_var($request->por_tipo_movimiento, FILTER_VALIDATE_BOOLEAN);

        $condiciones = [];


        if ($por_tipo_movimiento) {
            $tipo_movimiento = $request->tipo_movimiento_seleccionado;
            $condiciones[] = ['tipo', $tipo_movimiento];
        }

        $rango = InversionMetaMovimiento::on($conexion)->select('id')
            ->whereBetween(DB::raw('SUBSTR(datos_creacion,11,10)'), [$fecha_desde, $fecha_hasta])
            ->where($condiciones)
            ->get();

        $lista_movimientos = InversionMetaMovimiento::on($conexion)->from('inversion_meta_movimientos as inv_met_mov')
            ->select(
                'inv_met_mov.id',
                'inv_met_mov.tipo',
                'inv_met_mov.monto',
                'inv_met_mov.comentario',
                'inv_met_mov.agencia_caja',
                'inv_met_mov.caja_id',
                DB::raw("SUBSTR(inv_met_mov.datos_creacion,11,19) as fecha_movimiento"),

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',

                'inv_pro_met.producto',
                'inv_pro_met.id as producto_id'

            )->join('inversion_meta_registros as inv_met', 'inv_met_mov.inversion_id', 'inv_met.id')
            ->leftjoin('inversion_productos_meta as inv_pro_met', 'inv_pro_met.id', 'inv_met.producto_meta_id')

            ->join('cliente_registros as cli_reg', 'inv_met.cliente_id', 'cli_reg.id')
            ->whereIn('inv_met_mov.id', $rango)
            ->orderBy('inv_met_mov.id', 'desc')
            ->get();

        foreach ($lista_movimientos as $item) {
            $conexion_caja = 'master_' . $item->agencia_caja;
            $datos_caja = Caja::on($conexion_caja)
                ->select('usu.usuario')
                ->from('caja_registros as caj_reg')
                ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                ->where('caj_reg.id', $item->caja_id)
                ->get()->last();

            $item->usuario_caja = $datos_caja->usuario;
        }

        if ($por_producto_meta == 'true') {
            $producto_meta_id = $request->producto_meta_id;
            $lista_movimientos = $lista_movimientos->where('producto_id', $producto_meta_id);
        }

        return ['lista_movimientos' => $lista_movimientos];
    }
    public function exportar(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $movimientos = json_decode($request->lista_movimientos);
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;


        $data = [];
        $orden = 0;
        foreach ($movimientos as $item) {
            $orden += 1;
            $object = (object)[
                'orden' => $orden,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'producto_meta' => $item->producto,
                'tipo_movimiento' => $item->tipo == 'I' ? 'ABONO' : 'RETIRO',
                'monto' => $item->monto,
                'fecha_registro' => $item->fecha_movimiento,
                'caja' => $item->usuario_caja,
                'comentario' => $item->comentario,


            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $inputFileName = './report_templates/creditos/reportes/rptMovimientosMeta.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();


        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];

        while ($celda <= 8) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 6)->exportArray();

            $formato_total = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;
            $lista_formatos_totales[] = $formato_total;


            $celda++;
        }

        $sheet->removeRow(7);

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('I2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-----------------------------
        $indice = 5;
        $suma_acumulado = 0;

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
            $suma_acumulado = $suma_acumulado + $item->monto;
        }
        $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(7);

        $indice += 1;

        $sheet->setCellValue('F' . $indice, $suma_acumulado);


        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptMovimientosMeta', 5);


        // dd($spreadsheet);


        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function buscar_avance(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $por_asesor = $request->por_asesor;
        $por_producto_meta = $request->por_producto_meta;


        $lista_inversiones_meta = InversionMeta::on($conexion)->from('inversion_meta_registros as inv_met_reg')
            ->select(
                'inv_met_reg.id',
                'inv_met_reg.fecha_apertura',
                'inv_met_reg.valor_meta',
                'inv_met_reg.acumulado',
                DB::raw("ROUND(inv_met_reg.acumulado * 100 / inv_met_reg.valor_meta ,2 ) as porcentaje"),


                'cli_reg.dni',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.asesor_id as dni_asesor',

                'usu.usuario as usuario_asesor',

                'inv_pro_met.producto',
                'inv_pro_met.id as producto_id'

            )->join('cliente_registros as cli_reg', 'cli_reg.id', 'inv_met_reg.cliente_id')
            ->leftjoin('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
            ->leftjoin('inversion_productos_meta as inv_pro_met', 'inv_pro_met.id', 'inv_met_reg.producto_meta_id')
            ->where('inv_met_reg.fecha_cierre', null)
            ->orderBy('inv_met_reg.fecha_apertura', 'desc')
            ->get();



        if ($por_asesor == 'true') {
            $asesor_id = $request->asesor_id;
            $lista_inversiones_meta = $lista_inversiones_meta->where('dni_asesor', $asesor_id);
        }

        if ($por_producto_meta == 'true') {
            $producto_meta_id = $request->producto_meta_id;
            $lista_inversiones_meta = $lista_inversiones_meta->where('producto_id', $producto_meta_id);
        }

        $lista_inversiones_meta = $lista_inversiones_meta->values();

        return ['lista_inversiones_meta' => $lista_inversiones_meta];
    }

    public function exportar_avance(Request $request)
    {
        $datos_recibidos =   json_decode($request->datos_tabla);
        $tipo = $request->tipo;

        $data = [];

        $orden = 1;

        foreach ($datos_recibidos as $item) {
            $object = (object)[
                'numero' => $orden,
                'dni' => $item->dni,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'asesor' => $item->usuario_asesor,
                'producto' => $item->producto,
                'valor_meta' => $item->valor_meta,
                'acumulado' => $item->acumulado,
                'porcentaje' => $item->porcentaje / 100,
                'fecha_apertura' => $item->fecha_apertura,
            ];
            $orden += 1;
            $data[] = $object;
        }


        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName =  './report_templates/creditos/reportes/rptAvanceMeta.xlsx';

        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];

        while ($celda <= 9) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 6)->exportArray();

            $formato_total = $sheet->getStyle($columna_1 . 8)->exportArray();


            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;
            $lista_formatos_totales[] = $formato_total;


            $celda++;
        }

        $sheet->removeRow(7);

        // Insertando datos-----------------------------
        $indice = 5;
        $total = 0;
        $suma_acumulado = 0;
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
            $total += 1;


            $suma_acumulado = $suma_acumulado + $item->acumulado;
        }
        $indice += 1;
        $sheet->setCellValue('G' . $indice, $suma_acumulado);
        $sheet->setCellValue('I' . $indice, 'TOTAL ' . $total . ' registro(s)');

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptAvanceMeta', 5);

        if ($tipo == 'XLSX') {


            $writer = new Xlsx($spreadsheet);
            $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';


            return ['path_xlsx' => $path_xlsx];
        } elseif ($tipo == 'PDF') {

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
            $writer->SetFont('verdana');

            $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
            $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';


            return ['path_pdf' => $path_pdf];
        }
    }
}
