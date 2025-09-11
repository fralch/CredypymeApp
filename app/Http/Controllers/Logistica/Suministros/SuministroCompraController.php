<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Models\General\Agencia;
use App\Models\Logistica\Suministros\Compra;
use App\Models\Logistica\Suministros\CompraDetalle;
use App\Models\Logistica\Suministros\Suministro;
use App\Models\Logistica\Suministros\Tipo;
use App\Models\Logistica\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class SuministroCompraController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function historial_compras()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_COMPRAS_LOCAL',  'LOGISTICA_SUMINISTROS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_COMPRAS_GENERAL',  'LOGISTICA_SUMINISTROS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_COMPRAS',  'LOGISTICA_SUMINISTROS');

            if ($band == 1) {

                // if ($tipo_modulo == 'LOCAL') {
                //     $agencias = Agencia::select(
                //         'id_agencia',
                //         'nombre as nombre',
                //     )
                //         ->where('id_agencia', session('id_agencia'))
                //         ->get();
                // } else {
                //     $agencias = Agencia::select(
                //         'id_agencia',
                //         'nombre'
                //     )
                //         ->orderby('nombre', 'asc')
                //         ->get();
                // }

                return Inertia::render(
                    'Logistica/Suministros/Historial/historial_compras',
                    []
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------
    public function listar_recursos(Request $request)
    {
        $fecha_actual = (new LogisticaController)->fecha_corta_aplicacion();

        $fecha = Carbon::parse($fecha_actual);
        $primer_dia  = $fecha->firstOfMonth()->toDateString();

        return response()->json([
            'fecha_desde' => $primer_dia,
            'fecha_hasta' => $fecha_actual
        ]);
    }
    public function comprar(request $request)
    {
        $modo  = $request->modo;
        $datos_registro = (new LogisticaController)->datos_registro();

        if ($modo == "AGREGAR_NUEVO") {

            $usuario_compra = $request->usuario_compra;
            $canasta_compras = json_decode($request->canasta);

            $compra = Compra::create(
                // La agencia por defecto de compra es "ADMINISTRATIVA", por eso es "5"
                [
                    'agencia_id' => 5,
                    'usuario_compra' => $usuario_compra,
                    'datos_creacion' => $datos_registro
                ]
            );

            $compra_id = $compra->id;

            $path_name = pathinfo($_FILES['documento']['name']);
            $extension = "." . $path_name['extension'];
            $documento = "sum_compra_" . $compra_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/logistica/suministros/compras';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $documento;
            move_uploaded_file($archivo, $ruta);

            Compra::where('id', $compra_id)
                ->update([
                    'documento' => $documento,
                    'datos_actualizacion' => $datos_registro,
                ]);

            $estado_id = Estado::select('id')->where('estado', 'DISPONIBLE')->get()->last();

            $estado_id = $estado_id->id;
            foreach ($canasta_compras as $item) {

                $detalle = (new LogisticaController)->verificar_nulo($item->detalle);
                if ($detalle != null) {
                    $detalle = mb_strtoupper($detalle);
                }

                $suministro = Suministro::create(
                    [
                        'agencia_id' => $item->agencia_id,
                        'codigo' => $item->codigo,
                        'suministro' => mb_strtoupper(trim($item->suministro)),
                        'marca' => mb_strtoupper(trim($item->marca)),
                        'detalle' => $detalle,
                        'tipo_id' => $item->tipo_id,
                        'condicion_id' => $item->condicion_id,
                        'clasificacion' => $item->clasificacion,
                        'medicion_id' => $item->medicion_id,
                        'cantidad' => $item->cantidad_compra,
                        'valor_unitario' => $item->valor_unitario,
                        'estado_id' => $estado_id,
                        'datos_creacion' => $datos_registro
                    ]
                );

                $suministro_id = $suministro->id;

                CompraDetalle::create(
                    [
                        'compra_id' => $compra_id,
                        'suministro_id' => $suministro_id,
                        'cantidad' => $item->cantidad_compra,
                        'valor_unitario' => $item->valor_unitario,
                        'valor_total' => $item->valor_total,
                        'igv' => $item->igv,
                        'datos_creacion' => $datos_registro,
                    ]
                );
            }
        } else if ($modo == "AGREGAR_EXISTENTE") {

            $suministro_id = $request->suministro_id;
            $usuario_compra = $request->usuario_compra;
            $cantidad_compra = $request->cantidad_compra;
            $igv = $request->igv;

            $datos_suministro = Suministro::select(
                'agencia_id',
                'valor_unitario',
                'cantidad'
            )->where('id', $suministro_id)->get()->last();

            $agencia_id = $datos_suministro->agencia_id;
            $cantidad_actual = $datos_suministro->cantidad;
            $valor_unitario = $datos_suministro->valor_unitario;

            $nueva_cantidad = $cantidad_actual + $cantidad_compra;

            $compra = Compra::create(
                [
                    'agencia_id' => $agencia_id,
                    'usuario_compra' => $usuario_compra,
                    'datos_creacion' => $datos_registro
                ]
            );

            $compra_id = $compra->id;

            $path_name = pathinfo($_FILES['documento']['name']);
            $extension = "." . $path_name['extension'];
            $documento = "sum_compra_" . $compra_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/logistica/suministros/compras';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $documento;
            move_uploaded_file($archivo, $ruta);

            Compra::where('id', $compra_id)
                ->update([
                    'documento' => $documento,
                    'datos_actualizacion' => $datos_registro,
                ]);

            CompraDetalle::create(
                array(
                    'compra_id' => $compra_id,
                    'suministro_id' => $suministro_id,
                    'cantidad' => $cantidad_compra,
                    'valor_unitario' => $valor_unitario,
                    'igv' => $igv,
                    'datos_creacion' => $datos_registro
                )
            );
            $estado_id = Estado::select('id')->where('estado', 'DISPONIBLE')->get()->last();

            $estado_id = $estado_id->id;

            Suministro::where('id', $suministro_id)
                ->update([
                    'cantidad' => $nueva_cantidad,
                    'estado_id' => $estado_id,
                    'datos_actualizacion' => $datos_registro
                ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Suministros registrados exitosamente.'
        ], 200);
    }

    public function buscar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = date("Y-m-d", strtotime($request->input('fecha_hasta') . "+ 1 days"));

        $lista_compras_id = Compra::select('id')
            ->where([
                ['agencia_id', $agencia_id],
            ])
            ->whereBetween(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(datos_creacion, '$.fecha'))"),
                [$fecha_desde, $fecha_hasta]
            )
            ->get();

        // $lista_agrupado = CompraDetalle::from('suministro_compras as sum_com')
        //     ->select(
        //         'sum_com.id',
        //         'sum_com.documento',
        //         'sum_com.agencia_id',
        //         'sum_com.usuario_compra',
        //         'sum_com.datos_creacion',

        //         'ag.nombre as agencia',
        //         'us_1.usuario as usuario_compra',
        //         'us_2.usuario as usuario_registro'
        //     )
        //     ->join('agencias as ag', 'sum_com.agencia_id',  'ag.id_agencia')
        //     ->join('usuarios as us_1', 'sum_com.usuario_compra',  'us_1.dni')
        //     ->join('usuarios as us_2', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sum_com.datos_creacion, '$.usuario'))"), 'us_2.dni')
        //     ->where([
        //         ['sum_com.agencia_id', $agencia_id],
        //     ])
        //     ->whereBetween(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sum_com.datos_creacion, '$.fecha'))"), [$fecha_desde, $fecha_hasta])
        //     ->orderBy('sum_com.id', 'desc')
        //     ->get();

        // $lista_agrupado_id = $lista_agrupado->pluck('id');

        $lista_detallado = CompraDetalle::from('suministro_compras_detalles as sum_com_det')
            ->select(
                'sum_com_det.id',
                'sum_com_det.compra_id',
                'sum_com_det.suministro_id',
                'sum_com_det.cantidad',
                'sum_com_det.valor_unitario',
                'sum_com_det.valor_total',
                'sum_com_det.igv',

                'sum_com.agencia_id',
                'sum_com.usuario_compra',
                'sum_com.documento',
                'sum_com.datos_creacion',

                'sum_alm.suministro',
                'sum_alm.codigo',
                'sum_alm.clasificacion',

                'sum_tip.tipo',

                'log_con.condicion',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_compra',
                'us_2.usuario as usuario_registro'
            )
            ->join('suministro_compras as sum_com', 'sum_com_det.compra_id',  'sum_com.id')
            ->join('suministro_almacen as sum_alm', 'sum_com_det.suministro_id',  'sum_alm.id')
            ->join('suministro_tipos as sum_tip', 'sum_alm.tipo_id',  'sum_tip.id')
            ->join('logistica_condiciones as log_con', 'sum_alm.condicion_id',  'log_con.id')
            ->join('agencias as ag', 'sum_com.agencia_id',  'ag.id_agencia')
            ->join('usuarios as us_1', 'sum_com.usuario_compra',  'us_1.dni')
            ->join('usuarios as us_2', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sum_com.datos_creacion, '$.usuario'))"), 'us_2.dni')
            ->whereIn('sum_com.id', $lista_compras_id)
            ->orderBy('sum_com_det.id', 'desc')
            ->get();


        $lista_agrupado = $lista_detallado
            ->groupBy('compra_id')
            ->map(function ($items) {
                $first = $items->first();

                return (object)[
                    'id'         => $first->compra_id,
                    'documento' => $first->documento,
                    'agencia_id'        => $first->agencia_id,
                    'usuario_compra'    => $first->usuario_compra,
                    'datos_creacion'    => $first->datos_creacion,
                    'agencia'           => $first->agencia,
                    'usuario_compra' => $first->usuario_compra,
                    'usuario_registro'  => $first->usuario_registro,
                    'monto_total'       => $items->sum('valor_total'),
                ];
            })
            ->values();

        return response()->json([
            'lista_agrupado' => $lista_agrupado,
            'lista_detallado' => $lista_detallado
        ]);
    }

    public function exportar(Request $request)
    {
        $modo = $request->modo;

        if ($modo == 'agrupado') {
            return $this->exportar_agrupado($request);
        } else if ($modo == 'detallado') {
            return $this->exportar_detallado($request);
        }
    }

    public function exportar_agrupado($request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $lista_agrupado = json_decode($request->lista_agrupado);

        $data = [];
        $orden = 1;
        foreach ($lista_agrupado as $item) {

            $object = (object)[
                'orden' => $orden,
                'agencia' => $item->agencia,
                'fecha_compra' =>  Date::dateTimeToExcel(Carbon::parse(json_decode($item->datos_creacion)->fecha)),
                'monto_total' => floatval($item->monto_total),
                'usuario_compra' => $item->usuario_compra,
                'usuario_registro' => $item->usuario_registro,
            ];

            $data[] = $object;

            $orden++;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptSuministrosComprasA');
        $spreadsheet = $reader->load("./report_templates/logistica/reportes/rptSuministrosCompras.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezado

        $sheet->setCellValue('B1', (new LogisticaController)->header_footer($agencia_id));

        // Insertando valores

        $indice = 5;
        $controller = new LogisticaController();

        foreach ($data as $item) {
            $columna = 1;
            foreach ($item as $valor) {
                $columnaLetra = $controller->num2char($columna);
                $cell = $columnaLetra . $indice;

                $sheet->setCellValue($cell, $valor);

                $columna++;
            }
            $indice++;
        }

        // Exportar para descarga-------------------------
        $nombre_archivo = (new LogisticaController)->concatenar_aleatorio('rptSuministrosComprasA', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');

        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
    public function exportar_detallado($request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $lista_detallado = json_decode($request->lista_detallado);

        $data = [];
        $orden = 1;
        foreach ($lista_detallado as $item) {

            $object = (object)[
                'orden' => $orden,
                'agencia' => $item->agencia,
                'fecha_compra' =>  Date::dateTimeToExcel(Carbon::parse(json_decode($item->datos_creacion)->fecha)),
                'usuario_compra' => $item->usuario_compra,
                'codigo' => $item->codigo,
                'suministro' => $item->suministro,
                'tipo' => $item->tipo,
                'condicion' => $item->condicion,
                'cantidad' => floatval($item->cantidad),
                'valor_unitario' => floatval($item->valor_unitario),
                'valor_total' => floatval($item->valor_total),
                'usuario_registro' => $item->usuario_registro,
                'clasificacion' => $item->clasificacion,
            ];

            $data[] = $object;
            $orden++;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptSuministrosComprasD');
        $spreadsheet = $reader->load("./report_templates/logistica/reportes/rptSuministrosCompras.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezado

        $sheet->setCellValue('B1', (new LogisticaController)->header_footer($agencia_id));

        // Insertando valores

        $indice = 5;
        $controller = new LogisticaController();

        foreach ($data as $item) {
            $columna = 1;
            foreach ($item as $valor) {
                $columnaLetra = $controller->num2char($columna);
                $cell = $columnaLetra . $indice;

                $sheet->setCellValue($cell, $valor);

                $columna++;
            }
            $indice++;
        }

        // Exportar para descarga-------------------------
        $nombre_archivo = (new LogisticaController)->concatenar_aleatorio('rptSuministrosComprasD', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');

        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
    // --------------------------------------------------------------------------
