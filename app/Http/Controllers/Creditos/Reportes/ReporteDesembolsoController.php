<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Caja\Desembolso;
use App\Models\Creditos\Caja\Caja;
use App\Models\Creditos\Cuenta\CuentaUsuario;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Mantenimiento\Credito\Tipo;
use App\Models\Creditos\Mantenimiento\Credito\Producto;

use App\Models\Creditos\Clientes\Comentario;

use App\Models\General\Agencia;
use App\Models\General\Cargo;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpParser\Node\Scalar\Encapsed;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use Carbon\Carbon;

class ReporteDesembolsoController extends Controller
{

    public function desembolsos_auxiliar($modo)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_DESEMBOLSOS_AUXILIAR', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_MIS_DESEMBOLSOS_AUXILIAR', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {

                $agencias = Agencia::all();

                $usuarios_cuentas = (array)[];

                if ($modo == 'completo') {
                    foreach ($agencias as $item) {
                        $conexion = 'master_' .  $item->id_agencia;
                        $cuentas = CuentaUsuario::on($conexion)
                            ->select('dni')
                            ->where('con_cuenta', 1)
                            ->get();
                        foreach ($cuentas as $item_1) {
                            $usuarios_cuentas[] = $item_1->dni;
                        }
                    }
                } elseif ($modo == 'personal') {
                    $usuarios_cuentas[] = session('usuario_dni');
                }

                $usuarios = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )->whereIn('dni', $usuarios_cuentas)
                    ->orderBy('usuario', 'asc')
                    ->get();

                return Inertia::render('Creditos/Reportes/Caja/desembolsos_auxiliar', [
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

    public function desembolsos_asesor($modo)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_DESEMBOLSOS_ASESOR', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MIS_DESEMBOLSOS_ASESOR', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ])->get();

                $agencias = Agencia::all();

                foreach ($agencias as $item) {
                    $agencia_id = $item->id_agencia;
                    $conexion = 'master_' . $agencia_id;

                    $lista_tipos = Tipo::on($conexion)
                        ->select(
                            'id',
                            'tipo',
                            DB::raw("$agencia_id as agencia_id")
                        )
                        ->where('habilitado', 1)
                        ->orderBy('tipo', 'asc')
                        ->get();
                    foreach ($lista_tipos as $item) {
                        $tipos[] = $item;
                    }
                }

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
                return  [
                    'modo' => $modo,
                    'usuarios' => $usuarios,
                    'tipos' => $tipos,
                ];
            }
        }
    }

    public function buscar(Request $request)
    {
        $modo = $request->modo;


        if ($modo == 'por_auxiliar') {
            return  $this->buscar_por_auxiliar($request);
        } else if ($modo == 'por_asesor') {
            return  $this->buscar_por_asesor($request);
        }
    }

    public function exportar(Request $request)
    {
        $modo = $request->modo;


        if ($modo == 'por_auxiliar') {
            return  $this->exportar_por_auxiliar($request);
        } elseif ($modo == 'por_asesor') {
            return  $this->exportar_por_asesor($request);
        }
    }

    public function buscar_por_auxiliar($request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $filtro_usuario = $request->filtro_usuario;

        $rango_registros = Credito::on($conexion)->select('id')
            ->whereBetween('fecha_desembolso', [$fecha_desde, $fecha_hasta])->get();

        $lista_desembolsos = [];
        $desembolsos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'caj_des.monto',
                'caj_des.interes_total',
                'caj_des.porcentaje_igv',
                'caj_des.importe_igv',
                'caj_des.importe_gravado',
                'caj_des.caja_id',
                'caj_des.agencia_caja',
                'caj_des.modo_desembolso',

                'cre_reg.id',
                'cre_reg.fecha_desembolso',

                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.tasa_interes',
                'cre_apr.numero_credito',

                'cli_reg.id as cliente_id',
                'cli_reg.agencia_id as agencia_cliente',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.codigo_expediente',

                'us_1.usuario as usuario_asesor'
            )
            ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->whereIn('cre_reg.id', $rango_registros)
            ->orderBy('cre_reg.id', 'desc')
            ->get();


        if ($filtro_usuario == 'true') {
            $usuarios = json_decode($request->usuarios);

            foreach ($desembolsos as  $item) {

                $agencia_caja = $item->agencia_caja;
                $conexion_caja = 'master_' . $agencia_caja;

                $datos_caja = Caja::on($conexion_caja)->from('caja_registros as caj_reg')
                    ->select('us.usuario', 'caj_reg.dni')
                    ->join('solucion_master.usuarios as us', 'caj_reg.dni', 'us.dni')
                    ->where('caj_reg.id', $item->caja_id)
                    ->whereIn('caj_reg.dni', $usuarios)
                    ->get()->last();

                $cantidad_comentarios  = Comentario::on($conexion)
                    ->where([
                        ['cliente_id', $item->cliente_id],
                        ['motivo', 'INACTIVO']
                    ])->count();

                $item->cantidad_comentarios = $cantidad_comentarios;


                // Formateando el plazo de pago, identificando PAGO ÚNICO
                $plazo = round($item->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($item->periodo_pago);

                if ($item->periodo_pago == 'PAGO_UNICO') {
                    $plazo .= ' - PU';
                }

                $item->plazo = $plazo;

                if ($datos_caja != null) {
                    $item->usuario_desembolso = $datos_caja->usuario;
                    $item->usuario_dni_desembolso = $datos_caja->dni;
                    $lista_desembolsos[] = $item;
                }
            }
        } else {
            foreach ($desembolsos as $item) {
                $agencia_caja = $item->agencia_caja;
                $conexion_caja = 'master_' . $agencia_caja;

                $datos_caja = Caja::on($conexion_caja)->from('caja_registros as caj_reg')
                    ->select('us.usuario', 'caj_reg.dni')
                    ->join('solucion_master.usuarios as us', 'caj_reg.dni', 'us.dni')
                    ->where('caj_reg.id', $item->caja_id)
                    ->get()->last();

                $item->usuario_desembolso = $datos_caja->usuario;
                $item->usuario_dni_desembolso = $datos_caja->dni;

                // Formateando el plazo de pago, identificando PAGO ÚNICO
                $plazo = round($item->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($item->periodo_pago);

                if ($item->periodo_pago == 'PAGO_UNICO') {
                    $plazo .= ' - PU';
                }

                $item->plazo = $plazo;

                $cantidad_comentarios  = Comentario::on($conexion)
                    ->where([
                        ['cliente_id', $item->cliente_id],
                        ['motivo', 'INACTIVO']
                    ])->count();

                $item->cantidad_comentarios = $cantidad_comentarios;

                $lista_desembolsos[] = $item;
            }
        }

        $lista_desembolsos = collect($lista_desembolsos);

        $total_capital = $lista_desembolsos->sum('monto');
        $total_interes = $lista_desembolsos->sum('interes_total');
        $total_importe_igv = $lista_desembolsos->sum('importe_igv');
        $total_importe_gravado = $lista_desembolsos->sum('importe_gravado');
        $cantidad_registros = $lista_desembolsos->count('id');

        $totales = (object)[
            'total_capital' => $total_capital,
            'total_interes' => $total_interes,
            'total_importe_igv' => $total_importe_igv,
            'total_importe_gravado' => $total_importe_gravado,
            'cantidad_registros' => $cantidad_registros,
        ];

        return [
            'lista_desembolsos' => $lista_desembolsos,
            'totales' => $totales
        ];
    }

    public function buscar_por_asesor($request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $filtro_usuario = $request->filtro_usuario;

        // dd($request);

        $rango_registros = Credito::on($conexion)
            ->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id'
            )
            ->join('credito_aprobaciones as cre_apr', 'cre_apr.id', 'cre_reg.aprobacion_id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->whereBetween('cre_reg.fecha_desembolso', [$fecha_desde, $fecha_hasta]);


        if ($filtro_usuario == 'true') {
            $usuarios = json_decode($request->usuarios);
            $rango_registros = $rango_registros->whereIn('asesor_id', $usuarios);
        }
        $lista_desembolsos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.asesor_id',
                'cre_reg.fecha_desembolso',

                'caj_des.monto',
                'caj_des.interes_total',
                'caj_des.caja_id',
                'caj_des.agencia_caja',
                'caj_des.modo_desembolso',

                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.tasa_interes',
                'cre_apr.numero_credito',
                'cre_apr.considerado_uno',
                'cre_apr.es_especial',

                'cre_tip.id as tipo_id',
                'cre_tip.tipo',

                DB::raw("CONCAT(cli_reg.apellido_paterno,' ',cli_reg.apellido_materno,' ',cli_reg.nombres) as cliente"),
                'cli_reg.codigo_expediente',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_aprobacion',
            )
            ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->leftjoin('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('solucion_master.usuarios as us_1', 'cre_reg.asesor_id', 'us_1.dni')
            ->join('solucion_master.usuarios as us_2', DB::raw("SUBSTR(cre_apr.datos_creacion,42,8)"), 'us_2.dni')
            ->whereIn('cre_reg.id', $rango_registros)
            ->orderBy('cre_reg.id', 'desc')
            ->get();

        $lista_desembolsos = $lista_desembolsos->map(function ($row, $index) {

            $fecha_desembolso_original = $row->fecha_desembolso;
            $fecha_desembolso = date('d/m/Y H:i:s', strtotime($row->fecha_desembolso));

            // Obteniendo el usuario de la caja que desembolsó
            $usuario_desembolso = null;

            $agencia_caja = $row->agencia_caja;
            $conexion_caja = 'master_' . $agencia_caja;

            $datos_caja = Caja::on($conexion_caja)->from('caja_registros as caj_reg')
                ->select('us.usuario', 'caj_reg.dni')
                ->join('solucion_master.usuarios as us', 'caj_reg.dni', 'us.dni')
                ->where('caj_reg.id', $row->caja_id)
                ->get()->last();

            if ($datos_caja != null) {
                $usuario_desembolso = $datos_caja->usuario;
            }

            // Formateando el plazo de pago, identificando PAGO ÚNICO
            $plazo = round($row->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($row->periodo_pago);

            if ($row->periodo_pago == 'PAGO_UNICO') {
                $plazo .= ' - PU';
            }

            return [
                'index' => $index,
                'id' => $row->id,
                'codigo_expediente' => $row->codigo_expediente,
                'numero_credito' => $row->numero_credito,
                'considerado_uno' => $row->considerado_uno == 1 ? 'SI' : 'NO',
                'cliente' => $row->cliente,
                'tipo' => $row->tipo,
                'modo_desembolso' => $row->modo_desembolso,
                'capital' => $row->monto,
                'es_especial' => $row->es_especial,
                'plazo' => $plazo,
                'tasa_interes' => $row->tasa_interes,
                'interes_total' => $row->interes_total,
                'caja_id' => $row->caja_id,
                'fecha_desembolso' => $fecha_desembolso,
                'fecha_desembolso_original' => $fecha_desembolso_original,
                'asesor_id' => $row->asesor_id,
                'usuario_asesor' => $row->usuario_asesor,
                'usuario_aprobacion' => $row->usuario_aprobacion,
                'usuario_desembolso' => $usuario_desembolso,
            ];
        });

        $lista_desembolsos = collect($lista_desembolsos);

        return [
            'lista_desembolsos' => $lista_desembolsos,

        ];
    }

    public function exportar_por_auxiliar($request)
    {

        // Ordenando array de datos-------------------------------
        $desembolsos = json_decode($request->lista_desembolsos);
        $totales = json_decode($request->totales);
        $tipo = $request->tipo;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $agencia_id = session('id_agencia');

        $data = [];

        foreach ($desembolsos as $item) {

            $object = (object)[
                'fecha' => $item->fecha_desembolso,
                'expediente' => $item->codigo_expediente,
                'numero_credito' => $item->numero_credito,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'modo' => $item->modo_desembolso,
                'capital' => $item->monto,
                'plazo' => $item->plazo,
                'caja' => $item->usuario_desembolso,
                'tasa' => $item->tasa_interes / 100,
                'asesor' => $item->usuario_asesor,
                'interes' => $item->interes_total,
                'importe_igv' => $item->importe_igv,
                'importe_gravado' => $item->importe_gravado,

            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $inputFileName = './report_templates/creditos/reportes/rptDesembolsosAuxiliar.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();


        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];
        $lista_formatos_totales = [];


        while ($celda <= 13) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 6)->exportArray();

            $formato_totales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;
            $lista_formatos_totales[] = $formato_totales;

            $celda++;
        }

        $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('M2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

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

        $rango = "B$indice:N$indice";
        $sheet->getStyle($rango)
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color('#BCBCBC'));

        $indice += 1;

        $sheet->setCellValue('E' . $indice, $totales->cantidad_registros . ' desembolso(s)');
        $sheet->setCellValue('G' . $indice, $totales->total_capital);
        $sheet->setCellValue('L' . $indice, $totales->total_interes);
        $sheet->setCellValue('M' . $indice, $totales->total_importe_igv);
        $sheet->setCellValue('N' . $indice, $totales->total_importe_gravado);

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        // Exportar para descarga-------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptDesembolsoAuxiliar', 5);

        if ($tipo == 'XLSX') {

            $writer = new Xlsx($spreadsheet);
            $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

            return ['path_xlsx' => $path_xlsx];
        } else if ($tipo == 'PDF') {

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
            $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
            $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

            return ['path_pdf' => $path_pdf];
        }
    }

    protected function createExternalWriterInstance($orientation, $unit, $paperSize)
    {
        $instance = new \Mpdf\Mpdf($orientation, $unit, $paperSize);

        // more configuration of $instance

        return $instance;
    }

    public function exportar_por_asesor($request)
    {
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $tipo = $request->tipo;

        // Ordenando array de datos-------------------------------
        $desembolsos = json_decode($request->lista_desembolsos);

        $data = [];
        $orden = 1;

        foreach ($desembolsos as $item) {

            $fecha_desembolso = $item->fecha_desembolso;

            $object = (object)[
                'fecha' => $fecha_desembolso,
                'expediente' => $item->codigo_expediente,
                'numero_credito' => $item->numero_credito,
                'numero_uno' => $item->considerado_uno,
                'asesor' => $item->usuario_asesor,
                'cliente' =>  $item->cliente,
                'capital' => $item->capital,
                'plazo' => $item->plazo,
                'tasa' => $item->tasa_interes / 100,
                'interes' => $item->interes_total,
                'tipo' => $item->tipo,
                'modo' => $item->modo_desembolso,
                'es_especial' => $item->es_especial == 1 ? 'SI' : 'NO',
                'aprobacion' => $item->usuario_aprobacion,
                'caja' => $item->usuario_desembolso
            ];
            $orden += 1;

            $data[] = $object;
        }


        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptDesembolsosAsesor.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();


        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 15) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $lista_formatos_celdas[] = $formato_celda_1;

            $celda++;
        }

        $sheet->removeRow(7);

        if ($tipo == "completo") {
            $sheet->setCellValue('B2', 'DESEMBOLSOS POR ASESOR');
        }
        if ($tipo == "personal") {
            $sheet->setCellValue('B2', 'MIS DESEMBOLSOS - ASESOR');
        }

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('N2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-----------------------------
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

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptDesembolsosAsesor', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';


        // dd($nombre_archivo);
        return ['path_xlsx' => $path_xlsx];
    }

    public function seguimiento($cliente_id, Request $request)
    {

        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' . $agencia_id;

        $fecha_desembolso = $request->input('fecha_desembolso');

        $lista_seguimiento = Comentario::on($conexion)
            ->from('cliente_comentarios as cli_com')
            ->select(
                'cli_com.motivo',
                'cli_com.comentario',
                'cli_com.fecha_comentario',

                'usu.usuario as usuario_registro'
            )
            ->join('solucion_master.usuarios as usu', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(cli_com.datos_creacion, '$.usuario'))"), 'usu.dni')
            ->where([
                ['cliente_id', $cliente_id],
                ['motivo', 'INACTIVO']
            ])
            ->orderBy('fecha_comentario', 'desc')
            ->get();

        return response()->json(['lista_seguimiento' => $lista_seguimiento]);
    }

    public function desembosos_tipo(Request $request)
    {
        $x = session()->all();
        $conexion = 'master_' . session('id_agencia');
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_DESEMBOLSOS_TIPO', 'CREDITOS_REPORTES');
            if ($band == 1) {
                $tipos = Tipo::on($conexion)->select('id', 'tipo')->get();
                $productos = Producto::on($conexion)->select('id', 'producto')->get();

                return Inertia::render('Creditos/Reportes/Creditos/desembolsos_tipo', [
                    'tipos' => $tipos,
                    'productos' => $productos
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function desembolsos_tipo_buscar(Request $request)
    {
        // return $request;
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $fechas_desembolsos = Desembolso::on($conexion)
            ->select(DB::raw("SUBSTR(datos_creacion,11,10) as fecha"))
            ->whereBetween(DB::raw("SUBSTR(datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
            ->get();

        $fechas_desembolsos->toArray();
        $fechas = [];
        foreach ($fechas_desembolsos as $value) {
            if (!in_array($value['fecha'], $fechas)) {
                $fechas[] = $value['fecha'];
            }
        }


        $lista_completa = [];
        foreach ($fechas as $value) {
            $fecha = (object) [
                'fecha' => $value,
                'desembolsos' => []
            ];

            $desembolso_tipo =   Credito::on($conexion)->from('credito_registros as cre_reg')
                ->select(
                    'cli_reg.codigo_expediente',
                    'cli_reg.numero_expediente',
                    'cli_reg.apellido_paterno',
                    'cli_reg.apellido_materno',
                    'cli_reg.nombres',
                    'cli_reg.asesor_id',

                    'cre_reg.capital_total',
                    'caj_des.interes_total',

                    'cre_apr.plazo',
                    'cre_apr.periodo_pago',
                    'cre_apr.tasa_interes',
                    'cre_apr.numero_credito',

                    'caj_des.id as desembolso_id',
                    'caj_des.datos_creacion',


                    'cli_neg.nombre  as nombre_negocio',
                    'cli_neg.actividad as actividad_negocio',

                    'cre_tip.tipo',

                    'usu.usuario as usuario',
                    'usu2.usuario as usuario_asesor',

                    'cre_est.estado'

                )
                ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
                ->join('cliente_negocios as cli_neg', 'cre_pro.negocio_id', 'cli_neg.id')
                ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                ->join('credito_estados as cre_est', 'cre_reg.estado_id', 'cre_est.id')
                ->join('solucion_master.usuarios as usu', DB::raw("SUBSTR(caj_des.datos_creacion,42,8)"), 'usu.dni')
                ->join('solucion_master.usuarios as usu2', 'cli_reg.asesor_id', 'usu2.dni')
                ->where(DB::raw("SUBSTR(caj_des.datos_creacion,11,10)"), $value)
                ->orderBy('caj_des.id', 'desc');


            if ($request->filtro_vigentes == 'true') {
                $desembolso_tipo->where('cre_reg.estado_id', '!=', 4);
            }
            if ($request->tipo != '0') {
                $desembolso_tipo->where('cre_apr.tipo_id', $request->tipo);
            }
            if ($request->periodo != '0') {
                $desembolso_tipo->where('cre_apr.periodo_pago', $request->periodo);
            }
            if ($request->producto != '0') {
                $desembolso_tipo->where('cre_apr.producto_id', $request->producto);
            }

            $desembolso_tipo = $desembolso_tipo->get();

            $desembolso_tipo->toArray();
            $fecha->desembolsos = $desembolso_tipo;
            if (count($fecha->desembolsos)) {
                $lista_completa[] = $fecha;
            }
        }
        return $lista_completa;
    }
    private function periodoMedicion($value)
    {
        if ($value == "DIARIO") {
            return "(DÍAS)";
        } elseif ($value == "SEMANAL") {
            return "(SEMANAS)";
        } elseif ($value == "QUINCENAL") {
            return "(QUINCENAS)";
        } elseif ($value == "MENSUAL") {
            return "(MESES)";
        };
    }

    public function exportar_tipo_credito(Request $request)
    {

        $cartera_mora = json_decode($request->creditos_filtrados);
        $totales = json_decode($request->totales);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");

        $inputFileName = './report_templates/creditos/reportes/rptDesembolsosTipo.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        // $spreadsheet = new Spreadsheet();
        // dd($spreadsheet);
        $sheet = $spreadsheet->getActiveSheet();

        $formato_encabezado = $sheet->getStyle('B4')->exportArray();

        $celda = 1;
        $lista_formatos_celdas = [];
        $lista_formatos_celdas_impares = [];
        $lista_formatos_totales = [];

        while ($celda <= 12) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_totales = $sheet->getStyle($columna_1 . 9)->exportArray();
            $formato_celda = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_impar = $sheet->getStyle($columna_1 . 6)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_celdas_impares[] = $formato_celda_impar;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        $sheet->removeRow(10);
        $sheet->removeRow(7);
        // $sheet->removeRow(4);

        $total = (object)[
            'cantidad' => 1,
            'total' => "TOTAL",
            'capital' => 0,
            'interes' => 0,
        ];

        $indice = 4;
        foreach ($cartera_mora as $item) {
            $rango =  'B' . $indice . ':' . 'M' . $indice;

            $sheet->mergeCells($rango);
            $sheet->setCellValue('B' . $indice, $item->fecha);
            $sheet->getStyle('B' . $indice)->applyFromArray($formato_encabezado);
            $subtotal = (object)[
                'cantidad' => 1,
                'subtotal' => "SUBTOTAL",
                'capital' => 0,
                'interes' => 0,
            ];

            foreach ($item->desembolsos as $item_1) {
                $indice += 1;

                $columna_2 = 1;
                $lista_completa = [
                    'codigo_expediente' => $item_1->codigo_expediente,
                    'n_credito' => $item_1->numero_credito,
                    'cliente' => $item_1->apellido_paterno . ' ' . $item_1->apellido_materno . ' ' . $item_1->nombres,
                    'asesor' => $item_1->usuario_asesor,
                    'capital' => $item_1->capital_total,
                    'interes' => $item_1->interes_total,
                    'plazo' => intval($item_1->plazo) . ' ' . $this->periodoMedicion($item_1->periodo_pago),
                    'tasa' => number_format($item_1->tasa_interes, 2) . ' %',
                    'usuario' => $item_1->usuario,
                    'fecha' => substr($item_1->datos_creacion, 10, 11),
                    'hora' => substr($item_1->datos_creacion, 21, 8),
                    'estado' => $item_1->estado,
                ];


                foreach ($lista_completa as $k => $valor_1) { // ORDENAR EL ARREGLO VALOR_1 PARA QUE CUADRE EN LA TABLA

                    if ($k == 'capital') {
                        $subtotal->cantidad += 1;
                        $subtotal->capital += $valor_1;
                    }
                    if ($k == 'interes') {
                        $subtotal->interes += $valor_1;
                    }
                    // if ($subtotal->cantidad % 2 == 0) {
                    //     $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor_1);
                    //     $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                    // } else {
                    //     $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor_1);
                    //     $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_impares[$columna_2 - 1]);
                    // }
                    $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor_1);
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                    $columna_2 += 1;
                }
            }
            $columna_sub = 3;
            $indice += 1;
            foreach ($subtotal as $key => $valor_2) {
                if ($key == 'cantidad') {
                    $total->cantidad += $valor_2;
                    $valor_2 = $valor_2 . " desembolsos";
                }
                if ($key == 'capital') {
                    $total->capital += $valor_2;
                }
                if ($key == 'interes') {
                    $total->interes += $valor_2;
                }
                $sheet->setCellValue((new CreditosController)->num2char($columna_sub) . $indice, $valor_2);
                $sheet->getStyle((new CreditosController)->num2char($columna_sub) . $indice)->applyFromArray($lista_formatos_totales[$columna_sub - 1]);
                $columna_sub += 1;
            }

            $indice += 2;
        }
        $indice += 1;
        $columna_total = 3;
        foreach ($total as $key => $valor_2) {
            if ($key == 'cantidad') {
                $valor_2 = $valor_2 . " desembolsos";
            }
            $sheet->setCellValue((new CreditosController)->num2char($columna_total) . $indice, $valor_2);
            $sheet->getStyle((new CreditosController)->num2char($columna_total) . $indice)->applyFromArray($lista_formatos_totales[$columna_total - 1]);
            $columna_total += 1;
        }

        // foreach ($totales as $valor_2) {
        //     $sheet->getStyle((new CreditosController)->num2char($columna_3) . $indice)->applyFromArray($lista_formatos_totales[$columna_3 - 1]);
        //     $sheet->setCellValue((new CreditosController)->num2char($columna_3) . $indice, $valor_2);
        //     $columna_3 += 1;
        // }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $ret['data'] = base64_encode(ob_get_contents());
        ob_end_clean();

        return $ret['data'];
    }
}
