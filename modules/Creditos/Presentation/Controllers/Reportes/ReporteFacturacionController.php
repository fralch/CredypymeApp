<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Desembolso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;

use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;

use ZipArchive;

class ReporteFacturacionController extends Controller
{

    public function desembolsos_facturados()
    {
        if (empty(session('usuario_dni'))) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso(session('usuario_dni'), 'DESEMBOLSOS_FACTURADOS', 'CREDITOS_CAJA');
            if ($band == 1) {

                return Inertia('Creditos/Caja/desembolsos_facturados');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar_facturados(Request $request)
    {
        $agencia_id = $request->agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $agencias = [];
        if ($agencia_id == 'TODAS') {
            $agencias = Agencia::select('id_agencia as id')->get();
        } else {
            $agencias[] = (object)['id' => intval($agencia_id)];
        }

        $lista_desembolsos = [];

        foreach ($agencias as $item) {
            $conexion = 'master_' .  $item->id;

            $desembolsos = Desembolso::on($conexion)->from('caja_desembolsos as caj_des')
                ->select(
                    'caj_des.id',
                    'caj_des.credito_id',
                    'caj_des.interes_total',
                    'caj_des.porcentaje_igv',
                    'caj_des.importe_igv',
                    'caj_des.importe_gravado',
                    'caj_des.facturado',
                    'caj_des.agencia_caja',
                    'caj_des.caja_id',

                    'cre_reg.fecha_desembolso',
                    'cre_reg.agencia_id',

                    'cli_reg.dni',
                    DB::raw("CONCAT(cli_reg.apellido_paterno,' ',cli_reg.apellido_materno,' ',cli_reg.nombres) as cliente"),

                    'cre_apr.monto',
                    'cre_apr.cuota',
                    'cre_apr.plazo',
                    'cre_apr.periodo_pago',
                    'cre_apr.tasa_interes',

                    'usu.usuario as usuario_asesor',
                    'age.nombre as agencia',
                    'cre_fac.datos_comprobante',
                    'cre_fac.nueva_empresa',
                )
                ->join('credito_registros as cre_reg', 'caj_des.credito_id', 'cre_reg.id')
                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                ->join('solucion_master.agencias as age', 'cre_reg.agencia_id', 'age.id_agencia')
                ->join('solucion_master.usuarios as usu', 'cre_reg.asesor_id', 'usu.dni')
                ->join('credito_facturados as cre_fac', 'cre_fac.desembolso_id', 'caj_des.id')
                ->whereBetween("cre_reg.fecha_desembolso", [$fecha_desde, $fecha_hasta])
                ->get();

            $desembolsos = $desembolsos->map(function ($row, $index) {

                if ($row['datos_comprobante'] != null) {
                    $datos_comprobante = json_decode($row['datos_comprobante']);

                    $serie = $datos_comprobante->serie;
                    $numero = $datos_comprobante->numero;

                    $numero_documento = $serie . '-' . $numero;
                } else {
                    $numero_documento = '-';
                }

                $conexion_caja = 'master_' . $row['agencia_caja'];
                $caja_id = $row['caja_id'];

                $datos_caja = Caja::on($conexion_caja)->from('caja_registros as caj_reg')
                    ->select('usu.usuario')
                    ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                    ->where('caj_reg.id', $caja_id)
                    ->get()
                    ->last();

                $usuario_caja = $datos_caja->usuario;

                return (object)[
                    'index' => $index,
                    'id' => $row['id'],
                    'credito_id' => $row['credito_id'],
                    'interes_total' => $row['interes_total'],
                    'porcentaje_igv' => $row['porcentaje_igv'],
                    'importe_igv' => $row['importe_igv'],
                    'importe_gravado' => $row['importe_gravado'],
                    'facturado' => $row['facturado'],
                    'numero_documento' => $numero_documento,
                    'fecha_desembolso' => $row['fecha_desembolso'],
                    'usuario_caja' => $row['usuario_caja'],
                    'agencia_id' => $row['agencia_id'],
                    'dni' => $row['dni'],
                    'cliente' => $row['cliente'],
                    'monto' => $row['monto'],
                    'cuota' => $row['cuota'],
                    'plazo' => $row['plazo'],
                    'periodo_pago' => $row['periodo_pago'],
                    'tasa_interes' => $row['tasa_interes'],
                    'usuario_asesor' => $row['usuario_asesor'],
                    'agencia' => $row['agencia'],
                    'usuario_caja' => $usuario_caja,
                    'datos_comprobante' => $row['datos_comprobante'],
                    'comprobante' => $row['comprobante'],
                    'nueva_empresa' => $row['nueva_empresa'],
                ];
            });


            $lista_desembolsos = array_merge($lista_desembolsos, $desembolsos->toArray());
        }

        $lista_desembolsos = collect($lista_desembolsos)->sortBy('fecha_desembolso')->values();

        return ['lista_desembolsos' => $lista_desembolsos];
    }

    public function descargar_xml(Request $request)
    {
        $urls = json_decode($request->urls);

        // Define the path where the ZIP file will be saved
        $zip_name  = (new CreditosController)->concatenar_aleatorio('facturados', 5);
        $zip_name .= '.zip';
        $zipFilePath = public_path('temp_files/' . $zip_name);

        // Create a new ZipArchive instance
        $zip = new ZipArchive;

        // Open the ZIP archive for writing
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {

            // Add each XML file to the ZIP archive
            $xmlFiles = $urls;

            foreach ($xmlFiles as $item) {
                $xmlContent = file_get_contents($item->source);
                $xmlFileName = basename($item->name);
                $zip->addFromString($xmlFileName, $xmlContent);
            }

            // Close the ZIP archive
            $zip->close();
            // Return a success message or redirect the user to a page showing the download link
            // dd('ZIP file created and saved at: ' . $zipFilePath);
        }

        $path_zip = '/temp_files/' . $zip_name;
        return ['path_zip' => $path_zip];
    }

    public function exportar_facturados(Request $request)
    {

        $lista_desembolsos = json_decode($request->lista_desembolsos);


        $data = [];
        $orden = 1;

        foreach ($lista_desembolsos as $item) {

            $fecha_desembolso = date('d/m/Y', strtotime(substr($item->fecha_desembolso, 0, 10)));

            $object = (object)[
                'vou_origen' => '02',
                'vou_numero' => $orden,
                'vou_fecha' => $fecha_desembolso,
                'documento' => '03',
                'numero' => $item->numero_documento,
                'fecha_documento' => $fecha_desembolso,
                'fecha_vencimiento' => $fecha_desembolso,
                'codigo' => $item->dni,
                'valor_exp' => null,
                'base_imponible' => $item->importe_gravado,
                'inafecto' => null,
                'exonerado' => null,
                'isc' => null,
                'igv' => $item->importe_igv,
                'otros_trib' => null,
                'imp_bolsa' => null,
                'moneda' => 'S',
                'tc' => 1.00,
                'glosa' => 'POR INTERES COMPENSATORIO',
                'cta_ingreso' => 70321,
                'cta_igv' => 40111,
                'cta_o_trib' => null,
                'cta_cobrar' => 12121,
                'c_costo' => null,
                'presupuesto' => null,
                'r_doc' => null,
                'r_num' => null,
                'r_fecha' => null,
                'ruc' => $item->dni,
                'r_social' => mb_strtoupper($item->cliente),
            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptDesembolsosFacturados.xlsx';

        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $columna = 0;
        $lista_formatos_columnas_1 = [];
        $numero_celdas =  30;

        while ($columna <= $numero_celdas) {
            $letra_columna = (new CreditosController)->num2char($columna);
            $formato_columna_1 = $sheet->getStyle($letra_columna . 2)->exportArray();

            $lista_formatos_columnas_1[] = $formato_columna_1;
            $columna++;
        }

        // Insertando datos-----------------------------
        $indice = 2;

        foreach ($data as $item) {
            $columna_1 = 0;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_1) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_1) . $indice)->applyFromArray($lista_formatos_columnas_1[$columna_1]);

                $columna_1 += 1;
            }

            $indice += 1;
        }

        // Exportar para descarga-------------------------

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptDesembolsosFacturados', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function seguimiento_facturados()
    {
        if (empty(session('usuario_dni'))) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso(session('usuario_dni'), 'CAJA_SEGUIMIENTO_FACTURADOS', 'CREDITOS_REPORTES');

            if ($band == 1) {

                return Inertia(
                    'Creditos/Reportes/Caja/seguimiento_facturados'
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function buscar_pagos(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' . $agencia_id;

        $fecha_facturacion = $request->fecha_facturacion;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $agencias = [];
        if ($agencia_id == 'TODAS') {
            $agencias = Agencia::select('id_agencia as id')->get();
        } else {
            $agencias[] = (object)['id' => intval($agencia_id)];
        }

        $lista_facturados = [];

        foreach ($agencias as $item) {

            $conexion = 'master_' . $item->id;

            $esquema = Credito::on($conexion)->from('credito_registros as cre_reg')
                ->select(
                    DB::raw("0 as pago_id"),
                    'cre_reg.id as credito_id',

                    'cre_reg.cliente_id',
                    DB::raw("SUBSTR(cre_reg.fecha_desembolso,1,10) as fecha_desembolso"),
                    'cre_reg.fecha_desembolso as fecha_hora_desembolso',
                    'cli_reg.dni as codigo_cliente',
                    DB::raw("CONCAT(cli_reg.apellido_paterno,' ',cli_reg.apellido_materno,' ',cli_reg.nombres) as cliente"),
                    'cli_reg.numero_expediente',
                    'cli_reg.agencia_id',
                    'cre_apro.numero_credito',
                    'cre_fac.datos_comprobante',
                    'cre_fac.nueva_empresa',
                    'caj_des.monto',
                    'caj_des.interes_total',
                    DB::raw("null as ultimo_pago_cuota"),
                    DB::raw("null as ultimo_pago_general"),
                    'cre_reg.fecha_hora_cancelado',
                    'cre_est.estado',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes")
                )
                ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
                ->join('credito_facturados as cre_fac', 'caj_des.id', 'cre_fac.desembolso_id')
                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                ->join('credito_aprobaciones as cre_apro', 'cre_reg.aprobacion_id', 'cre_apro.id')
                ->join('credito_estados as cre_est', 'cre_reg.estado_id', 'cre_est.id')
                ->where(DB::raw("SUBSTR(cre_reg.fecha_desembolso,1,7)"), $fecha_facturacion);
            // ->where(DB::raw("SUBSTR(cre_reg.fecha_desembolso,1,10)"), '2022-05-21');


            $rango_id = array_column($esquema->get()->toArray(), 'credito_id');

            $rango = PagoCuota::on($conexion)
                ->select('id')
                ->whereIn('credito_id', $rango_id)
                ->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])
                ->get();

            if (count($rango) > 0) {
                $pago_cuotas = PagoCuota::on($conexion)->from('caja_pago_cuotas as caj_pag_cuo')
                    ->select(
                        'caj_pag_cuo.id as pago_id',
                        'cre_reg.id as credito_id',

                        DB::raw("null as cliente_id"),
                        DB::raw("null as fecha_desembolso"),
                        DB::raw("null as fecha_hora_desembolso"),
                        DB::raw("null as codigo_cliente"),
                        DB::raw("null as cliente"),
                        DB::raw("null as numero_expediente"),
                        DB::raw("null as agencia_id"),
                        DB::raw("null as numero_credito"),
                        DB::raw("null as datos_comprobante"),
                        DB::raw("null as nueva_empresa"),
                        DB::raw("0 as monto"),
                        DB::raw("0 as interes_total"),
                        'caj_pag_cuo.fecha_pago as ultimo_pago_cuota',
                        'caj_pag_cuo.fecha_pago as ultimo_pago_general',
                        DB::raw("null as fecha_hora_cancelado"),
                        DB::raw("null as estado"),

                        'caj_pag_cuo.capital_pagado as capital',
                        DB::raw("(caj_pag_cuo.interes_pagado + 
                    caj_pag_cuo.redondeo_pagado) as interes"),
                        DB::raw("0 as moras"),
                        DB::raw("0 as notificaciones"),
                        DB::raw("0 as dscto_mora"),
                        DB::raw("0 as dscto_notificaciones"),
                        DB::raw("0 as dscto_interes")
                    )
                    ->join('credito_registros as cre_reg', 'caj_pag_cuo.credito_id', 'cre_reg.id')
                    ->whereIn('caj_pag_cuo.id', $rango);
            } else {
                $pago_cuotas = [];
            }

            $rango = PagoMora::on($conexion)->select('id')
                ->whereIn('credito_id', $rango_id)
                ->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])
                ->get();

            if (count($rango) > 0) {
                $pago_moras = PagoMora::on($conexion)->from('caja_pago_moras as caj_pag_mor')
                    ->select(
                        'caj_pag_mor.id as pago_id',
                        'cre_reg.id as credito_id',

                        DB::raw("null as cliente_id"),
                        DB::raw("null as fecha_desembolso"),
                        DB::raw("null as fecha_hora_desembolso"),
                        DB::raw("null as codigo_cliente"),
                        DB::raw("null as cliente"),
                        DB::raw("null as numero_expediente"),
                        DB::raw("null as agencia_id"),
                        DB::raw("null as numero_credito"),
                        DB::raw("null as datos_comprobante"),
                        DB::raw("null as nueva_empresa"),
                        DB::raw("0 as monto"),
                        DB::raw("0 as interes_total"),
                        DB::raw("null as ultimo_pago_cuota"),
                        'caj_pag_mor.fecha_pago as ultimo_pago_general',
                        DB::raw("null as fecha_hora_cancelado"),
                        DB::raw("null as estado"),

                        DB::raw("0 as capital"),
                        DB::raw("0 as interes"),
                        'caj_pag_mor.monto as moras',
                        DB::raw("0 as notificaciones"),
                        DB::raw("0 as dscto_mora"),
                        DB::raw("0 as dscto_notificaciones"),
                        DB::raw("0 as dscto_interes"),

                    )
                    ->join('credito_registros as cre_reg', 'caj_pag_mor.credito_id', 'cre_reg.id')
                    ->whereIn('caj_pag_mor.id', $rango);
            } else {
                $pago_moras = [];
            }

            $rango = PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
                ->select('caj_pag_not.id')
                ->join('credito_notificaciones as cre_not', 'caj_pag_not.notificacion_id', 'cre_not.id')
                ->whereIn('cre_not.credito_id', $rango_id)
                ->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

            if (count($rango) > 0) {
                $pago_notificaciones = PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
                    ->select(
                        'caj_pag_not.id as pago_id',
                        'cre_reg.id as credito_id',

                        DB::raw("null as cliente_id"),
                        DB::raw("null as fecha_desembolso"),
                        DB::raw("null as fecha_hora_desembolso"),
                        DB::raw("null as codigo_cliente"),
                        DB::raw("null as cliente"),
                        DB::raw("null as numero_expediente"),
                        DB::raw("null as agencia_id"),
                        DB::raw("null as numero_credito"),
                        DB::raw("null as datos_comprobante"),
                        DB::raw("null as nueva_empresa"),
                        DB::raw("0 as monto"),
                        DB::raw("0 as interes_total"),
                        DB::raw("null as ultimo_pago_cuota"),
                        'caj_pag_not.fecha_pago as ultimo_pago_general',
                        DB::raw("null as fecha_hora_cancelado"),
                        DB::raw("null as estado"),

                        DB::raw("0 as capital"),
                        DB::raw("0 as interes"),
                        DB::raw("0 as moras"),
                        'caj_pag_not.monto as notificaciones',
                        DB::raw("0 as dscto_mora"),
                        DB::raw("0 as dscto_notificaciones"),
                        DB::raw("0 as dscto_interes"),

                    )
                    ->join('credito_notificaciones as cre_not', 'caj_pag_not.notificacion_id', 'cre_not.id')
                    ->join('credito_registros as cre_reg', 'cre_not.credito_id', 'cre_reg.id')
                    ->whereIn('caj_pag_not.id', $rango);
            } else {
                $pago_notificaciones = [];
            }

            $rango = Credito::on($conexion)
                ->select('id')
                ->where('fecha_hora_cancelado', '<>', null)
                ->whereIn('id', $rango_id)
                ->whereBetween('fecha_hora_cancelado', [$fecha_desde, $fecha_hasta])
                ->get();

            if (count($rango) > 0) {
                $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
                $estado_id = $estado->id;

                $descuentos = Credito::on($conexion)->from('credito_registros as cre_reg')
                    ->select(
                        'cre_reg.id as pago_id',
                        'cre_reg.id as credito_id',

                        DB::raw("null as cliente_id"),
                        DB::raw("null as fecha_desembolso"),
                        DB::raw("null as fecha_hora_desembolso"),
                        DB::raw("null as codigo_cliente"),
                        DB::raw("null as cliente"),
                        DB::raw("null as numero_expediente"),
                        DB::raw("null as agencia_id"),
                        DB::raw("null as numero_credito"),
                        DB::raw("null as datos_comprobante"),
                        DB::raw("null as nueva_empresa"),
                        DB::raw("0 as monto"),
                        DB::raw("0 as interes_total"),
                        DB::raw("null as ultimo_pago_cuota"),
                        DB::raw("null as ultimo_pago_general"),
                        DB::raw("null as fecha_hora_cancelado"),
                        DB::raw("null as estado"),

                        DB::raw("0 as capital"),
                        DB::raw("0 as interes"),
                        DB::raw("0 as moras"),
                        DB::raw("0 as notificaciones"),
                        'cre_reg.dscto_mora_cancelado as dscto_mora',
                        'cre_reg.dscto_notificaciones_cancelado as dscto_notificaciones',
                        'cre_reg.dscto_interes_cancelado as dscto_interes'
                    )
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

            $lista = $esquema->orderBy('pago_id', 'asc')->get();

            $lista = $lista->groupBy('credito_id')->map(function ($row) {

                if ($row[0]->datos_comprobante != null) {
                    $datos_comprobante = json_decode($row[0]->datos_comprobante);
                    $serie = $datos_comprobante->serie;
                    $numero_documento = $serie . '-' . $row[0]->numero_expediente . '-' . $row[0]->numero_credito;
                } else {
                    $numero_documento = '-';
                }

                return (object)[
                    'credito_id' => $row[0]->credito_id,
                    'cliente_id' => $row[0]->cliente_id,
                    'fecha_desembolso' => $row[0]->fecha_desembolso,
                    'fecha_hora_desembolso' => $row[0]->fecha_hora_desembolso,
                    'codigo_cliente' => $row[0]->codigo_cliente,
                    'cliente' => $row[0]->cliente,
                    'numero_documento' => $numero_documento,
                    'nueva_empresa' => $row[0]->nueva_empresa,
                    'agencia_id' => $row[0]->agencia_id,
                    'ultimo_pago_cuota' => $row->max('ultimo_pago_cuota'),
                    'ultimo_pago_general' => $row->max('ultimo_pago_general'),
                    'fecha_hora_cancelado' => $row[0]->fecha_hora_cancelado,
                    'estado' => $row[0]->estado,

                    'monto' => $row[0]->monto,
                    'interes_total' => $row[0]->interes_total,
                    'capital' => $row->sum('capital'),
                    'interes' => $row->sum('interes'),
                    'moras' => $row->sum('moras'),
                    'notificaciones' => $row->sum('notificaciones'),
                    'dscto_mora' => $row->sum('dscto_mora'),
                    'dscto_notificaciones' => $row->sum('dscto_notificaciones'),
                    'dscto_interes' => $row->sum('dscto_interes'),

                ];
            });


            $lista_facturados = array_merge($lista_facturados, $lista->toArray());
        }
        $lista_facturados = collect($lista_facturados)->sortBy('fecha_hora_desembolso')->values();

        $lista_facturados = $lista_facturados->map(function ($row, $index) {
            return [
                'index' => $index,
                'credito_id' => $row->credito_id,

                'cliente_id' => $row->cliente_id,
                'fecha_desembolso' => $row->fecha_desembolso,
                'fecha_hora_desembolso' => $row->fecha_hora_desembolso,
                'codigo_cliente' => $row->codigo_cliente,
                'cliente' => $row->cliente,
                'numero_documento' => $row->numero_documento,
                'nueva_empresa' => $row->nueva_empresa,
                'agencia_id' => $row->agencia_id,
                'ultimo_pago_cuota' => $row->ultimo_pago_cuota,
                'ultimo_pago_general' => $row->ultimo_pago_general,
                'fecha_hora_cancelado' => $row->fecha_hora_cancelado,
                'estado' => $row->estado,

                'monto' => $row->monto,
                'interes_total' => $row->interes_total,
                'capital' => $row->capital,
                'interes' => $row->interes,
                'moras' => $row->moras,
                'notificaciones' => $row->notificaciones,
                'dscto_mora' => $row->dscto_mora,
                'dscto_notificaciones' => $row->dscto_notificaciones,
                'dscto_interes' => $row->dscto_interes,
            ];
        });


        $total_monto = $lista_facturados->sum('monto');
        $total_interes = $lista_facturados->sum('interes_total');

        $total_capital_p = $lista_facturados->sum('capital');
        $total_interes_p = $lista_facturados->sum('interes');
        $total_mora_p = $lista_facturados->sum('moras');
        $total_notificaciones_p = $lista_facturados->sum('notificaciones');

        $total_dscto_mora = $lista_facturados->sum('dscto_mora') * -1;
        $total_dscto_notificaciones = $lista_facturados->sum('dscto_notificaciones') * -1;
        $total_dscto_interes = $lista_facturados->sum('dscto_interes') * -1;

        $totales = (object)[
            'total_monto' => $total_monto,
            'total_interes' => $total_interes,

            'total_capital_p' => $total_capital_p,
            'total_interes_p' => $total_interes_p,
            'total_mora_p' => $total_mora_p,
            'total_notificaciones_p' => $total_notificaciones_p,

            'total_dscto_mora' => $total_dscto_mora,
            'total_dscto_notificaciones' => $total_dscto_notificaciones,
            'total_dscto_interes' => $total_dscto_interes,
        ];


        return [
            'lista_facturados' => $lista_facturados,
            'totales' => $totales
        ];
    }

    public function exportar_pagos(Request $request)
    {
        $fecha_facturacion = $request->fecha_facturacion;
        $lista_facturados = json_decode($request->lista_facturados);

        $data = [];
        $orden = 1;

        foreach ($lista_facturados as $item) {
            $object = (object)[
                'numero' => $orden,
                'fecha_desembolso' => $item->fecha_desembolso,
                'codigo_cliente' => $item->codigo_cliente,
                'cliente' => $item->cliente,
                'numero_documento' => $item->numero_documento,
                'monto' => $item->monto,
                'interes_total' => $item->interes_total,
                'capital' => $item->capital,
                'interes' => $item->interes,
                'moras' => $item->moras,
                'notificaciones' => $item->notificaciones,
                'dscto_interes' => $item->dscto_interes,
                'dscto_mora' => $item->dscto_mora,
                'dscto_notificaciones' => $item->dscto_notificaciones,

                'estado' => $item->estado,
                'ultimo_pago_cuota' => $item->ultimo_pago_cuota,
                'ultimo_pago_general' => $item->ultimo_pago_general,
                'fecha_hora_cancelado' => $item->fecha_hora_cancelado
            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptSeguimientoFacturados.xlsx';

        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $columna = 1;
        $lista_formatos_columnas_1 = [];
        $numero_celdas =  18;

        while ($columna <= $numero_celdas) {
            $letra_columna = (new CreditosController)->num2char($columna);
            $formato_columna_1 = $sheet->getStyle($letra_columna . 6)->exportArray();

            $lista_formatos_columnas_1[] = $formato_columna_1;
            $columna++;
        }

        // Insertando datos-----------------------------

        $fecha_separada = explode('-', $fecha_facturacion);

        $anio = intval($fecha_separada[0]);
        $mes = intval($fecha_separada[1]);
        $mes = mb_strtoupper((new CreditosController)->nombre_mes($mes));
        $sheet->setCellValue('G2', $mes . ' - ' . $anio);


        $indice = 6;

        foreach ($data as $item) {
            $columna_1 = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_1) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_1) . $indice)->applyFromArray($lista_formatos_columnas_1[$columna_1 - 1]);

                $columna_1 += 1;
            }

            $indice += 1;
        }

        // Exportar para descarga-------------------------

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptSeguimientoFacturados', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
