<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\AdelantoHaber;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Compromiso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Notificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\NotificacionTipo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Desembolso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Aprobacion;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;


use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function GuzzleHttp\Promise\each;

class ReporteCompromisoNotificacionesController extends Controller
{
    public function compromisosNotificaciones($modo)
    {
        $x = session()->all();


        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_COMPROMISOS_NOTIFICACIONES', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MIS_COMPROMISOS_NOTIFICACIONES', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {


                $lista_cargos = ['ASESOR DE NEGOCIOS'];

                $cargos = Cargo::whereIn('cargo', $lista_cargos)->get();

                $cargos_id = [];

                foreach ($cargos as $item) {
                    $cargos_id[] = $item->id;
                };

                if ($modo == 'personal') {
                    $asesores = Usuario::where('dni', session('usuario_dni'))
                        ->get();
                } else {
                    $asesores = Usuario::where([
                        ['habilitado', 1]
                    ])
                        ->whereIn('cargo_id', $cargos_id)
                        ->where('cargo_id', $cargos_id)
                        ->orderBy('usuario', 'asc')
                        ->get();
                }

                $usuarios = Usuario::select('usuario', 'dni', 'agencia_id')
                    ->where('habilitado', 1)
                    ->orderBy('usuario', 'asc')
                    ->get();


                return Inertia::render('Creditos/Reportes/Creditos/compromisos_notificaciones', [
                    'modo' => $modo,
                    'asesores' => $asesores,
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
        $fecha_hasta = $request->fecha_hasta;

        $mas_filtros = filter_var($request->mas_filtros, FILTER_VALIDATE_BOOLEAN);

        $rango_n = Notificacion::on($conexion)
            ->select('id')
            ->whereBetween(
                DB::raw("SUBSTR(datos_creacion,11,10)"),
                [$fecha_desde, $fecha_hasta]
            )
            ->get()->toArray();

        $rango_c = Compromiso::on($conexion)
            ->select('id')
            ->whereBetween(
                DB::raw("SUBSTR(datos_creacion,11,10)"),
                [$fecha_desde, $fecha_hasta]
            )
            ->get()->toArray();

        $condiciones = [];

        if ($mas_filtros) {
            $tipo_filtro = $request->tipo_filtro;
            if ($tipo_filtro == 'asesor') {
                $asesor_id = $request->asesor_id;
                $condiciones[] = ['usu.dni', $asesor_id];
            } else if ($tipo_filtro == 'usuario') {
                $usuario_registro = $request->usuario_registro;
                $condiciones[] = ['usu_2.dni', $usuario_registro];
            }
        }

        $lista_notificaciones = [];
        $lista_compromisos = [];

        foreach (array_chunk($rango_n, 500) as $row) {
            $notificaciones = Notificacion::on($conexion)->from('credito_notificaciones as cre_not')
                ->select(
                    'cli_reg.codigo_expediente',
                    'cli_reg.apellido_paterno',
                    'cli_reg.apellido_materno',
                    'cli_reg.nombres',
                    'cli_reg.direccion',
                    'cre_reg.capital_total',
                    'cre_apr.plazo',
                    'cre_apr.periodo_pago',
                    'cre_apr.numero_credito',
                    'cre_not_tip.tipo',
                    'cre_not.monto',
                    'cre_not.datos_creacion',
                    'cre_reg.dias_atraso',
                    'caj_des.datos_creacion as fecha_desembolso',
                    'cre_apr.cuota as monto_cuota',
                    'cre_reg.cuotas_pendientes as CuXpag',
                    'cre_reg.cuotas_vencidas as CuVenc',
                    'cre_reg.monto_vencido as MCuVenc',
                    'cre_reg.mora_pagado as mora_acumulado',
                    'cre_reg.saldo_total',
                    'cre_reg.fecha_ultimo_pago',
                    'usu.usuario as usuario_asesor',
                    'usu_2.usuario as usuario_registro'
                )
                ->join('credito_registros as cre_reg', 'cre_reg.id', 'cre_not.credito_id')
                ->join('cliente_registros as cli_reg', 'cli_reg.id', 'cre_reg.cliente_id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                ->join('credito_notificaciones_tipos as cre_not_tip', 'cre_not_tip.id', 'cre_not.tipo_id')
                ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
                ->join('solucion_master.usuarios as usu', 'cre_reg.asesor_id', 'usu.dni')
                ->join('solucion_master.usuarios as usu_2', DB::raw("SUBSTRING(cre_not.datos_creacion,42,8)"), 'usu_2.dni')
                ->whereIn('cre_not.id', $row)
                ->where($condiciones)
                ->get();

            foreach ($notificaciones as $value) {
                $lista_notificaciones[] = $value;
            }
        }

        foreach (array_chunk($rango_c, 500) as $row) {
            $compromisos = Compromiso::on($conexion)->from('credito_compromisos as cre_com')
                ->select(
                    'cli_reg.codigo_expediente',
                    'cli_reg.apellido_paterno',
                    'cli_reg.apellido_materno',
                    'cli_reg.nombres',
                    'cre_apr.numero_credito',
                    'cre_com.compromiso',
                    'cre_com.datos_creacion',
                    'cre_com.fecha_hora_visita',
                    'cre_com.fecha_vencimiento',
                    'usu_2.usuario as usuario_registro',
                    'usu.usuario as usuario_asesor',
                    DB::raw("(SELECT (COUNT(cre_reg_1.id))
            FROM credito_registros cre_reg_1
            INNER JOIN cliente_registros cli_reg_1 on cre_reg_1.cliente_id=cli_reg_1.id
            WHERE cre_reg.cliente_id = cli_reg_1.id
            GROUP BY cli_reg_1.id
             ) AS cantidad_compromisos"),
                )
                ->join('credito_registros as cre_reg', 'cre_reg.id', 'cre_com.credito_id')
                ->join('cliente_registros as cli_reg', 'cli_reg.id', 'cre_reg.cliente_id')
                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')

                ->join('solucion_master.usuarios as usu', 'cre_reg.asesor_id', 'usu.dni')
                ->join('solucion_master.usuarios as usu_2', DB::raw("SUBSTRING(cre_com.datos_creacion,42,8)"), 'usu_2.dni')
                ->whereIn('cre_com.id', $row)
                ->where($condiciones)
                ->get();

            foreach ($compromisos as $value) {
                $lista_compromisos[] = $value;
            }
        }

        return [
            'lista_notificaciones' => $lista_notificaciones,
            'lista_compromisos' => $lista_compromisos
        ];
    }
    public function exportar_notificaciones(Request $request)
    {
        $tipo = $request->tipo;
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        // Ordenando array de datos-------------------------------
        $datos_recibidos =   $tipo == 'notificaciones'
            ? json_decode($request->lista_notificaciones)
            : json_decode($request->lista_compromisos);

        $data = [];

        $orden = 1;
        if ($tipo == 'notificaciones') {
            foreach ($datos_recibidos as $item) {
                $object = (object)[

                    'orden' => $orden,
                    'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                    'expediente' => $item->codigo_expediente . ' - ' . $item->numero_credito,
                    'asesor' => $item->usuario_asesor,
                    'capital' => $item->capital_total,
                    'plazo' => $item->plazo,
                    'atraso' => $item->dias_atraso,
                    'couta' => $item->CuXpag,
                    'cuota_vencida' => $item->CuVenc,
                    'tipo_notificacion' => $item->tipo,
                    'monto' => $item->monto,
                    'fecha' => json_decode($item->datos_creacion)->fecha,
                    'usuario_registro' => $item->usuario_registro,
                    'mora_acum' => $item->mora_acumulado,
                    'saldo_total' => $item->saldo_total,
                    'ultimo_pago' => $item->fecha_ultimo_pago,
                    'direccion' => $item->direccion,
                ];
                $orden += 1;
                $data[] = $object;
            }
        }
        if ($tipo == 'compromisos') {
            foreach ($datos_recibidos as $item) {
                $object = (object)[
                    'orden' => $orden,
                    'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                    'expediente' => $item->codigo_expediente . ' - ' . $item->numero_credito,
                    'comentario' => $item->compromiso,
                    'fecha_registro' => $item->fecha_hora_visita,
                    'usuario_registro' => $item->usuario_registro,
                    'fecha_vencimiento' => $item->fecha_vencimiento,
                    'asesor' => $item->usuario_asesor,
                    'cantidad' => $item->cantidad_compromisos,
                ];
                $orden += 1;
                $data[] = $object;
            }
        }


        // Leer Plantilla-------------------------
        if ($tipo == 'notificaciones') {
            $inputFileName = './report_templates/creditos/reportes/rptNotificaciones.xlsx';
        } else {

            $inputFileName = './report_templates/creditos/reportes/rptCompromisos.xlsx';
        }



        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();


        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];
        $numero_celdas = $tipo == 'notificaciones' ? 17 : 9;


        while ($celda <= $numero_celdas) {
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
        if ($tipo == 'notificaciones') {
            $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
            $sheet->setCellValue('R2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);
        } else {
            $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
            $sheet->setCellValue('I2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);
        }


        // Insertando datos-----------------------------
        $indice = 5;
        // $suma_total_pago = 0;

        // $suma_total_en_cuotas = 0;
        // $suma_total_en_moras = 0;
        // $suma_total_en_notificaciones = 0;


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
            // $suma_total_pago = $suma_total_pago + $item->total_pago;

            // $suma_total_en_cuotas = $suma_total_en_cuotas + $item->en_cuotas;
            // $suma_total_en_moras = $suma_total_en_moras + $item->en_moras;
            // $suma_total_en_notificaciones = $suma_total_en_notificaciones + $item->en_notificaciones;
        }
        $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(7);

        $indice += 1;

        // $sheet->setCellValue('G' . $indice, $suma_total_pago);

        // $sheet->setCellValue('H' . $indice, $suma_total_en_cuotas);
        // $sheet->setCellValue('I' . $indice, $suma_total_en_moras);
        // $sheet->setCellValue('J' . $indice, $suma_total_en_notificaciones);


        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }
        if ($tipo == 'notificaciones') {
            $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptNotificaciones', 5);
        } else {

            $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCompromisos', 5);
        }


        // dd($spreadsheet);


        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
