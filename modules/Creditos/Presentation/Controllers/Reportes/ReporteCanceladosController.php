<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;


use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Records\CreditoResumenRecord;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CentralRiesgo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteCanceladosController extends Controller
{

    public function cancelados_parcial()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_CANCELADOS_PARCIAL', 'CREDITOS_REPORTES');

            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ]);

                $usuarios = Usuario::select('dni', 'usuario', 'agencia_id', 'habilitado')
                    ->whereIn('cargo_id', $cargos)
                    ->get();

                return Inertia::render('Creditos/Reportes/Creditos/cancelados_parcial', [
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

    public function buscar_creditos_cancelados(Request $request)
    {
        // return $request;
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $filtro_usuario = $request->filtro_usuario;
        $asesores = json_decode($request->usuarios);

        $estado_credito = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO PARCIAL')->get()->last();
        $estado_id = $estado_credito->id;
        $rango = [];

        if ($filtro_usuario == "true") {
            $rango = Credito::on($conexion)
                ->select(
                    'id'
                )
                ->whereIn('asesor_id', $asesores)
                ->where('estado_id', $estado_id)
                ->whereBetween('fecha_ultimo_pago', [$fecha_desde, $fecha_hasta])
                ->get();
        }
        if ($filtro_usuario == "false") {

            $rango = Credito::on($conexion)
                ->select(
                    'id'
                )
                ->where('estado_id', $estado_id)
                ->whereBetween('fecha_ultimo_pago', [$fecha_desde, $fecha_hasta])
                ->get();
        }


        // return $rango;
        // $datos_busqueda=Credito::on($conexion)->from('credito_registros')
        // ->select()
        // ->join('credito_estados', 'credito_estados.id', '=', 'credito_registros.estado_id')
        // ->get();

        $datos_busqueda = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(

                'cre_reg.id',
                'cre_reg.asesor_id',
                'cre_reg.fecha_desembolso',
                'cre_reg.capital_total',
                'cre_reg.fecha_ultimo_pago',
                'cre_reg.fecha_hora_cancelado',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.notificaciones_pagado',
                'cre_reg.notificaciones_total',
                'cre_reg.saldo_total as deuda_total',
                'cre_reg.fecha_vencimiento',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.codigo_expediente',

                "cre_tipo.tipo",

                'us_1.usuario as usuario_asesor'
            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credito_tipos as cre_tipo', 'cre_apr.tipo_id', 'cre_tipo.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->whereIn('cre_reg.id', $rango)
            ->orderBy('cre_reg.id', 'desc')
            ->get();


        // dd($datos_busqueda);

        return $datos_busqueda;
    }
    public function exportar_cancelados(Request $request)
    {
        // return $request;
        // Ordenando array de datos-------------------------------

        $datos_tabla = json_decode($request->datos_tabla);

        $data = [];
        $numero = 1;
        foreach ($datos_tabla as $item) {
            $object = (object)[
                'numero' => $numero,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'asesor' => $item->usuario_asesor,
                'expediente' => $item->codigo_expediente,
                'fecha_desembolso' => $item->fecha_desembolso,
                'capital' => $item->capital_total,
                'ultimo_pago' => $item->fecha_ultimo_pago,
                'mora' => $item->mora_total,
                'deuda_mora' => ($item->mora_total - $item->mora_pagado),
                'notif_total' => $item->notificaciones_total,
                'deuda_notif' => ($item->notificaciones_total - $item->notificaciones_pagado),
                'tipo' => $item->tipo,
                'estado' => (strtotime($item->fecha_vencimiento) < strtotime($request->hoy) ? 'VENCIDO' : 'ACTIVO'),

            ];
            $numero++;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptCanceladosParcial.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];


        while ($celda <= 13) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda = $sheet->getStyle($columna_1 . 4)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $celda++;
        }



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


        // Exportar para descarga-------------------------
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $ret['data'] = base64_encode(ob_get_contents());
        ob_end_clean();

        return $ret['data'];
    }
}
