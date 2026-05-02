<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Illuminate\Support\Facades\DB;

use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transaccion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transferencia as CajaTransferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Transferencia as CuentaTransferencia;


use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Categoria;



use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReporteCajaController extends Controller
{

    public function caja_cierres()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_CIERRES', 'CREDITOS_REPORTES');
            if ($band == 1) {



                return Inertia::render('Creditos/Reportes/Caja/cierres_caja');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar_cierres_caja(request $request)
    {

        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $lista_caja_cierres = Caja::on($conexion)->from('caja_registros as caj_reg')
            ->select(
                'caj_reg.id',
                'usu.usuario',
                'usu.nombres',
                'usu.apellido_paterno',
                'usu.apellido_materno',

                "caj_reg.datos_apertura",
                "caj_reg.datos_cierre",
                'caj_reg.comentario_apertura',
                'caj_reg.comentario_cierre',
                'caj_reg.created_at',
                'caj_reg.updated_at',
            )
            ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
            ->whereBetween(
                DB::raw("SUBSTR(caj_reg.datos_cierre,11,10)"),
                [$fecha_desde, $fecha_hasta]
            )
            ->orderBy('caj_reg.id', 'desc')
            ->get();

        $faltante = Categoria::on($conexion)->select('id')
            ->where('categoria', 'FALTANTE')->get()->last();
        $faltante_id = $faltante->id;

        $sobrante = Categoria::on($conexion)->select('id')
            ->where('categoria', 'SOBRANTE')->get()->last();
        $sobrante_id = $sobrante->id;


        $lista_caja_cierres = $lista_caja_cierres->map(function ($row, $index) use (
            $conexion,
            $faltante_id,
            $sobrante_id
        ) {
            $fecha_apertura = substr($row->datos_apertura, 10, 19);
            $fecha_cierre = substr($row->datos_cierre, 10, 19);

            $comentario_apertura = $row->comentario_apertura == null ? '-' : $row->comentario_apertura;
            $comentario_cierre = $row->comentario_cierre == null ? '-' : $row->comentario_cierre;

            $faltante = Transaccion::on($conexion)->select(
                'concepto',
                'monto'
            )->where([
                ['caja_id', $row->id],
                ['categoria_id', $faltante_id]
            ])
                ->get()->last();

            $sobrante = Transaccion::on($conexion)->select(
                'concepto',
                'monto'
            )
                ->where([
                    ['caja_id', $row->id],
                    ['categoria_id', $sobrante_id]
                ])
                ->get()->last();

            $observacion = null;
            $detalle_observacion = null;

            if ($faltante != null) {
                $observacion = 'FALTANTE';
                $detalle_observacion = $faltante;
            } else if ($sobrante != null) {
                $observacion = 'SOBRANTE';
                $detalle_observacion = $sobrante;
            }

            return [
                'index' => $index,
                'id' => $row->id,
                'usuario' => $row->usuario,
                'nombres' => $row->nombres,
                'apellido_paterno' => $row->apellido_paterno,
                'apellido_materno' => $row->apellido_materno,
                'fecha_apertura' => date('d-m-Y H:m:i', strtotime($fecha_apertura)),
                'fecha_cierre' => date('d-m-Y H:m:i', strtotime($fecha_cierre)),
                'observacion' => $observacion,
                'detalle_observacion' => $detalle_observacion,
                'comentario_apertura' => $comentario_apertura,
                'comentario_cierre' => $comentario_cierre,
                'fecha_apertura_real' => date('d-m-Y H:m:i', strtotime($row->created_at)),
                'fecha_cierre_real' => date('d-m-Y H:m:i', strtotime($row->updated_at)),
            ];
        });



        return ['lista_caja_cierres' => $lista_caja_cierres];

        // dd($lista_caja_cierres);
    }
    public function exportar_cierres_caja(Request $request)
    {
        // Ordenando array de datos-------------------------------

        $tipo = $request->tipo;
        $data_usuario = [json_decode($request->data_usuario)];
        $data_sistema = [json_decode($request->data_sistema)];
        $data_real = [json_decode($request->data_real)];
        $data = json_decode($request->lista_operaciones);
        $data_subtotales = [json_decode($request->subtotales)];
        $data_total = [json_decode($request->totales)];
        $observacion = $request->observacion;

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptCierreCaja.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_operaciones = [];
        $lista_formatos_subtotales = [];
        $lista_formatos_totales = [];

        while ($celda <= 3) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_operacion = $sheet->getStyle($columna_1 . 9)->exportArray();
            $formato_subtotales = $sheet->getStyle($columna_1 . 10)->exportArray();
            $formato_total = $sheet->getStyle($columna_1 . 11)->exportArray();

            $lista_formatos_operaciones[] = $formato_operacion;
            $lista_formatos_subtotales[] = $formato_subtotales;
            $lista_formatos_totales[] = $formato_total;

            $celda++;
        }

        $sheet->removeRow(15);
        $sheet->removeRow(14);
        $sheet->removeRow(13);
        $sheet->removeRow(12);
        $sheet->removeRow(11);
        $sheet->removeRow(10);
        $sheet->removeRow(9);

        // Insertando datos -----------------------------

        // Datos de usuario y apertura ------------------------------
        $sheet->setCellValue('B3', $data_usuario[0]->usuario);

        $sheet->setCellValue('B5', $data_sistema[0]->apertura_sistema);
        $sheet->setCellValue('B6', $data_sistema[0]->cierre_sistema);

        $sheet->setCellValue('C5', $data_real[0]->apertura_real);
        $sheet->setCellValue('C6', $data_real[0]->cierre_real);

        // Datos de operaciones ------------------------------

        $indice = 9;

        foreach ($data as $item) {
            $columna_data = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_data) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_data) . $indice)->applyFromArray($lista_formatos_operaciones[$columna_data - 1]);
                $columna_data += 1;
            }
            $indice += 1;
        }

        foreach ($data_subtotales as $item) {
            $columna_subtotal = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_subtotal) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_subtotal) . $indice)->applyFromArray($lista_formatos_subtotales[$columna_subtotal - 1]);
                $columna_subtotal += 1;
            }
            $indice += 1;
        }

        foreach ($data_total as $item) {
            $columna_total = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_total) . $indice, $valor);
                $columna_total += 1;
            }
        }

        $columna_total = 1;
        foreach ($lista_formatos_totales as $valor) {
            $sheet->getStyle((new CreditosController)->num2char($columna_total) . $indice)->applyFromArray($valor);
            $columna_total += 1;
        }

        $rango =  'C' . $indice . ':' . 'D' . $indice;
        $sheet->mergeCells($rango);

        if ($observacion != null && $observacion != 'null') {
            $detalle_observacion = json_decode($request->detalle_observacion);


            $texto = $observacion . ': ' . $detalle_observacion->concepto . ' - S/ ' . round($detalle_observacion->monto, 2);
            $sheet->setCellValue('B' . $indice, $texto);

            $columna_total = 1;
            foreach ($lista_formatos_totales as $valor) {
                $sheet->getStyle((new CreditosController)->num2char($columna_total) . $indice)->applyFromArray($valor);
                $columna_total += 1;
            }

            $rango =  'B' . $indice . ':' . 'D' . $indice;
            $sheet->mergeCells($rango);
        }

        // Exportar para descarga-------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCierreCaja', 5);

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
    public function cierres_dia()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_CIERRES_DIA', 'CREDITOS_REPORTES');
            if ($band == 1) {



                return Inertia::render('Creditos/Reportes/Caja/cierres_dia');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar_cierres_dia(request $request)
    {
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;
        $conexion2 = 'solucion_records_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date($request->fecha_hasta);
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));


        $lista_cierres = DB::connection($conexion)->table('aplicacion_cierres as apl_cie')
            ->select(
                'usu.usuario',
                DB::raw("SUM(cue_rec.monto_final) as total_monto_final"),
                'apl_cie.datos_creacion',
                DB::raw("cue_rec.fecha_inicio as fecha_sistema"),
                'apl_cie.created_at as fecha_real',
                'apl_cie.id as id_cierre',

            )
            ->join('solucion_master.usuarios as usu', DB::raw("SUBSTR(apl_cie.datos_creacion,42,8)"), 'usu.dni')
            ->join("$conexion2.cuenta_usuarios_records as cue_rec", 'cue_rec.cierre_id', 'apl_cie.id')
            ->whereBetween(DB::raw("SUBSTR(apl_cie.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
            ->groupBy('cue_rec.cierre_id')
            ->orderby('fecha_sistema', 'desc')
            ->get();

        return ['lista_cierres' => $lista_cierres];

        // dd($lista_caja_cierres);
    }
    public function exportar_cierres(Request $request)
    {
        // return $request;
        // Ordenando array de datos-------------------------------

        $data = json_decode($request->lista_operaciones);
        $data_subtotales = [json_decode($request->subtotales)];
        $data_total = [json_decode($request->totales)];

        // return $data_total;

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptOperacionesCierreDia.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];
        $lista_formatos_subtotales = [];
        $lista_formatos_totales = [];


        while ($celda <= 3) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda = $sheet->getStyle($columna_1 . 4)->exportArray();
            $formato_subtotales = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_total = $sheet->getStyle($columna_1 . 7)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_subtotales[] = $formato_subtotales;
            $lista_formatos_totales[] = $formato_total;
            $celda++;
        }
        // $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);
        $sheet->removeRow(5);
        $sheet->removeRow(4);
        // return $lista_formatos_subtotales;
        // Insertando datos-----------------------------
        $indice = 4;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                $columna_2 += 1;
            }

            $indice += 1;
        }

        $sheet->insertNewRowBefore($indice + 2);


        foreach ($data_subtotales as $item) {
            $columna_subtotal = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_subtotal) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_subtotal) . $indice)->applyFromArray($lista_formatos_subtotales[$columna_subtotal - 1]);
                $columna_subtotal += 1;
            }
            $indice += 1;
        }


        // // -------------------------------------------------

        $sheet->insertNewRowBefore($indice + 1);


        foreach ($data_total as $item) {
            $columna_total = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_total) . $indice, $valor);
                $columna_total += 2;
            }
        }
        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }


        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptOperacionesCierreDia', 5);


        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function transferencias()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_TRANSFERENCIAS', 'CREDITOS_REPORTES');
            if ($band == 1) {

                $agencias = Agencia::all();
                $lista_usuarios = [];

                foreach ($agencias as $item) {
                    $conexion = 'master_' .  $item->id_agencia;
                    $id_agencia = $item->id_agencia;


                    $usuarios = CuentaUsuario::on($conexion)->from('cuenta_usuarios as cue_usu')->select(
                        'cue_usu.dni',
                        'usu.usuario',
                        'usu.habilitado',
                        DB::raw("$id_agencia as agencia_id")


                    )
                        ->where('cue_usu.con_cuenta', 1)
                        ->join('solucion_master.usuarios as usu', 'cue_usu.dni', 'usu.dni')
                        ->orderBy('usu.usuario', 'asc')
                        ->get();

                    foreach ($usuarios as $item) {
                        $lista_usuarios[] = $item;
                    }
                }

                // dd($lista_usuarios);



                return Inertia::render('Creditos/Reportes/Caja/transferencias', [
                    'usuarios' => $lista_usuarios
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar_transferencias(Request $request)
    {

        // dd($request);

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta =  $request->fecha_hasta;

        $de_estado_seleccionado = $request->de_estado_seleccionado;
        $a_estado_seleccionado = $request->a_estado_seleccionado;

        $de_usuario = $request->de_usuario;
        $a_usuario = $request->a_usuario;




        if ($de_estado_seleccionado == 'CUENTA') {



            if ($a_estado_seleccionado == 'CUENTA') {


                $rango = CuentaTransferencia::on($conexion)
                    ->select('id')
                    ->where('tipo', 'A_CUENTA')
                    ->whereBetween(DB::raw("SUBSTR(datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();


                $lista_transferencias = CuentaTransferencia::on($conexion)->from('cuenta_transferencias as cue_tra')
                    ->select(
                        'cue_tra.id',
                        'usu_1.usuario as usuario_emisor',
                        'usu_1.dni as dni_usuario_emisor',

                        'usu_2.usuario as usuario_receptor',
                        'usu_2.dni as dni_usuario_receptor',



                        'cue_tra.monto',
                        'cue_tra.estado',
                        'cue_tra.descripcion',

                        DB::raw("SUBSTR(cue_tra.datos_creacion,11,19) as fecha_envio"),
                        DB::raw("SUBSTR(cue_tra.datos_actualizacion,11,19) as fecha_recepcion"),

                        'cue_tra.comentario_rechazo'

                    )
                    ->join('cuenta_usuarios as cue_usu_1', 'cue_tra.remitente_id', 'cue_usu_1.id')
                    ->join('solucion_master.usuarios as usu_1', 'cue_usu_1.dni', 'usu_1.dni')

                    ->join('cuenta_usuarios as cue_usu_2', 'cue_tra.destinatario_id', 'cue_usu_2.id')
                    ->join('solucion_master.usuarios as usu_2', 'cue_usu_2.dni', 'usu_2.dni')

                    ->whereIn('cue_tra.id', $rango)

                    ->orderBy('fecha_envio', 'desc')
                    ->get();
            } else {
                $rango = CuentaTransferencia::on($conexion)
                    ->select('id')
                    ->where('tipo', 'A_CAJA')
                    ->whereBetween(DB::raw("SUBSTR(datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();


                $lista_transferencias = CuentaTransferencia::on($conexion)->from('cuenta_transferencias as cue_tra')
                    ->select(
                        'cue_tra.id',
                        'usu_1.usuario as usuario_emisor',
                        'usu_1.dni as dni_usuario_emisor',

                        'usu_2.usuario as usuario_receptor',
                        'usu_2.dni as dni_usuario_receptor',
                        'cue_tra.monto',
                        'cue_tra.estado',
                        'cue_tra.descripcion',

                        DB::raw("SUBSTR(cue_tra.datos_creacion,11,19) as fecha_envio"),
                        DB::raw("SUBSTR(cue_tra.datos_actualizacion,11,19) as fecha_recepcion"),

                        'cue_tra.comentario_rechazo'

                    )
                    ->join('cuenta_usuarios as cue_usu_1', 'cue_tra.remitente_id', 'cue_usu_1.id')
                    ->join('solucion_master.usuarios as usu_1', 'cue_usu_1.dni', 'usu_1.dni')

                    ->join('caja_registros as caj_reg', 'cue_tra.destinatario_id', 'caj_reg.id')
                    ->join('solucion_master.usuarios as usu_2', 'caj_reg.dni', 'usu_2.dni')

                    ->whereIn('cue_tra.id', $rango)
                    ->orderBy('fecha_envio', 'desc')
                    ->get();
            }
        }



        if ($de_estado_seleccionado == 'CAJA') {

            if ($a_estado_seleccionado == 'CUENTA') {




                $rango = CajaTransferencia::on($conexion)
                    ->select('id')
                    ->where('tipo', 'A_CUENTA')
                    ->whereBetween(DB::raw("SUBSTR(datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();


                $lista_transferencias = CajaTransferencia::on($conexion)->from('caja_transferencias as caj_tra')
                    ->select(
                        'caj_tra.id',
                        'usu_1.usuario as usuario_emisor',
                        'usu_1.dni as dni_usuario_emisor',

                        'usu_2.usuario as usuario_receptor',
                        'usu_2.dni as dni_usuario_receptor',
                        'caj_tra.monto',
                        'caj_tra.estado',
                        'caj_tra.descripcion',

                        DB::raw("SUBSTR(caj_tra.datos_creacion,11,19) as fecha_envio"),
                        DB::raw("SUBSTR(caj_tra.datos_actualizacion,11,19) as fecha_recepcion"),

                        'caj_tra.comentario_rechazo'

                    )
                    ->join('caja_registros as caj_reg', 'caj_tra.remitente_id', 'caj_reg.id')
                    ->join('solucion_master.usuarios as usu_1', 'caj_reg.dni', 'usu_1.dni')

                    ->join('cuenta_usuarios as cue_usu', 'caj_tra.destinatario_id', 'cue_usu.id')
                    ->join('solucion_master.usuarios as usu_2', 'cue_usu.dni', 'usu_2.dni')

                    ->whereIn('caj_tra.id', $rango)

                    ->orderBy('fecha_envio', 'desc')
                    ->get();
            } else {

                $rango = CajaTransferencia::on($conexion)
                    ->select('id')
                    ->where('tipo', 'A_CAJA')
                    ->whereBetween(DB::raw("SUBSTR(datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();


                $lista_transferencias = CajaTransferencia::on($conexion)->from('caja_transferencias as caj_tra')
                    ->select(
                        'caj_tra.id',
                        'usu_1.usuario as usuario_emisor',
                        'usu_1.dni as dni_usuario_emisor',

                        'usu_2.usuario as usuario_receptor',
                        'usu_2.dni as dni_usuario_receptor',
                        'caj_tra.monto',
                        'caj_tra.estado',
                        'caj_tra.descripcion',

                        DB::raw("SUBSTR(caj_tra.datos_creacion,11,19) as fecha_envio"),
                        DB::raw("SUBSTR(caj_tra.datos_actualizacion,11,19) as fecha_recepcion"),

                        'caj_tra.comentario_rechazo'

                    )
                    ->join('caja_registros as caj_reg_1', 'caj_tra.remitente_id', 'caj_reg_1.id')
                    ->join('solucion_master.usuarios as usu_1', 'caj_reg_1.dni', 'usu_1.dni')

                    ->join('caja_registros as caj_reg_2', 'caj_tra.destinatario_id', 'caj_reg_2.id')
                    ->join('solucion_master.usuarios as usu_2', 'caj_reg_2.dni', 'usu_2.dni')

                    ->whereIn('caj_tra.id', $rango)

                    ->orderBy('fecha_envio', 'desc')
                    ->get();
            }
        }


        if ($de_usuario == 'true') {


            $de_usuario_id = $request->de_usuario_id;

            $lista_transferencias = $lista_transferencias->where('dni_usuario_emisor', $de_usuario_id);
        }

        if ($a_usuario == 'true') {


            $a_usuario_id = $request->a_usuario_id;

            $lista_transferencias = $lista_transferencias->where('dni_usuario_receptor', $a_usuario_id);
        }
        $lista_transferencias = $lista_transferencias->values();


        // dd($lista_transferencias);

        return ['lista_transferencias' => $lista_transferencias];
    }


    public function exportar_transferencias(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $lista_transferencias = json_decode($request->lista_transferencias);
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;


        $de_estado_seleccionado = $request->de_estado_seleccionado;
        $a_estado_seleccionado = $request->a_estado_seleccionado;






        // dd($request);

        $data = [];
        $orden = 0;
        foreach ($lista_transferencias as $item) {
            $orden += 1;
            $object = (object)[
                'orden' => $orden,
                'emisor' =>  $item->usuario_emisor,
                'receptor' => $item->usuario_receptor,
                'monto' => $item->monto,
                'estado' => $item->estado,
                'descripcion' => $item->descripcion,
                'fecha_envio' => $item->fecha_envio,
                'fecha_confirmado' => $item->fecha_recepcion,
                'comentario_rechazo' => $item->comentario_rechazo,

            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $inputFileName = './report_templates/creditos/reportes/rptTransferencias.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
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

        $sheet->setCellValue('B2', 'TRANSFERENCIAS ' . $de_estado_seleccionado . ' - ' . $a_estado_seleccionado);

        $sheet->setCellValue('E' . $indice, $suma_acumulado);
        $sheet->setCellValue('J' . $indice, 'TOTAL ' . $orden . ' registro(s)');




        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptTransferencias', 5);


        // dd($spreadsheet);


        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
