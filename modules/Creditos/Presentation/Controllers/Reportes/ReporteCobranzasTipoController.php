<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Tipo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Producto;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Departamento;
use Modules\General\Infrastructure\Persistence\Eloquent\Provincia;
use Modules\General\Infrastructure\Persistence\Eloquent\Distrito;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Desembolso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\ComisionPago;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Comision;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Carbon\Carbon;

class ReporteCobranzasTipoController extends Controller
{
    public function cobranzas_tipo()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_COBRANZAS_TIPO', 'CREDITOS_REPORTES');

            if ($band == 1) {
                return Inertia('Creditos/Reportes/Caja/cobranzas_tipo');
            } else {
                return redirect('/');
            }
        }
    }

    public function listar_recursos(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' . $agencia_id;

        $tipos = Tipo::on($conexion)->where('habilitado', 1)->orderBy('tipo', 'asc')->get();
        $productos = Producto::on($conexion)->where('habilitado', 1)->orderBy('producto', 'asc')->get();

        return response()->json([
            'tipos' => $tipos,
            'productos' => $productos
        ]);
    }

    public function buscar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = date("Y-m-d", strtotime($request->input('fecha_hasta') . "+ 1 days"));

        $esquema = Cliente::on($conexion)->select(
            DB::raw("0 as pago_id"),
            DB::raw("0 as credito_id"),
            DB::raw("0 as aprobacion_id"),
            DB::raw("0 as numero_cuota"),
            DB::raw("null as cliente_id"),
            DB::raw("null as fecha_pago_credito"),
            DB::raw("null as fecha_pago"),
            DB::raw("null as fecha_pago_corta"),
            DB::raw("null as agencia_caja"),
            DB::raw("null as caja_id"),
            DB::raw("null as asesor_id"),

            DB::raw("0 as capital"),
            DB::raw("0 as interes"),
            DB::raw("0 as redondeo"),
            DB::raw("0 as moras"),
            DB::raw("0 as notificaciones"),
            DB::raw("0 as dscto_mora"),
            DB::raw("0 as dscto_notificaciones"),
            DB::raw("0 as dscto_interes"),
            DB::raw("null as comentario"),
        )->take(1);

        $rango = PagoCuota::on($conexion)->select('id')->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_cuotas = PagoCuota::on($conexion)->from('caja_pago_cuotas as caj_pag_cuo')
                ->select(
                    'caj_pag_cuo.id as pago_id',
                    'cre_reg.id as credito_id',
                    'cre_reg.aprobacion_id',
                    'caj_pag_cuo.numero_cuota',
                    'cre_reg.cliente_id',
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_cuo.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_cuo.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_cuo.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_cuo.agencia_caja',
                    'caj_pag_cuo.caja_id',
                    'cre_reg.asesor_id',

                    'caj_pag_cuo.capital_pagado as capital',
                    'caj_pag_cuo.interes_pagado as interes',
                    'caj_pag_cuo.redondeo_pagado as redondeo',
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    'caj_pag_cuo.comentario',
                )
                ->join('credito_registros as cre_reg', 'caj_pag_cuo.credito_id', 'cre_reg.id')
                ->whereIn('caj_pag_cuo.id', $rango);
        } else {
            $pago_cuotas = [];
        }

        $rango = PagoMora::on($conexion)->select('id')->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_moras = PagoMora::on($conexion)->from('caja_pago_moras as caj_pag_mor')
                ->select(
                    'caj_pag_mor.id as pago_id',
                    'cre_reg.id as credito_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    'cre_reg.cliente_id',
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_mor.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_mor.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_mor.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_mor.agencia_caja',
                    'caj_pag_mor.caja_id',
                    'cre_reg.asesor_id',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    'caj_pag_mor.monto as moras',
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    'caj_pag_mor.comentario',
                )
                ->join('credito_registros as cre_reg', 'caj_pag_mor.credito_id', 'cre_reg.id')
                ->whereIn('caj_pag_mor.id', $rango);
        } else {
            $pago_moras = [];
        }

        $rango = PagoNotificacion::on($conexion)->select('id')->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_notificaciones = PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
                ->select(
                    'caj_pag_not.id as pago_id',
                    'cre_reg.id as credito_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    'cre_reg.cliente_id',
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_not.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_not.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_not.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_not.agencia_caja',
                    'caj_pag_not.caja_id',
                    'cre_reg.asesor_id',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    'caj_pag_not.monto as notificaciones',
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    'caj_pag_not.comentario',
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
            ->whereBetween('fecha_hora_cancelado', [$fecha_desde, $fecha_hasta])
            ->get();

        if (count($rango) > 0) {
            $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
            $estado_id = $estado->id;

            $descuentos = Credito::on($conexion)->from('credito_registros as cre_reg')
                ->select(
                    'cre_reg.id as pago_id',
                    'cre_reg.id as credito_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    'cre_reg.cliente_id',
                    DB::raw("CONCAT(cre_reg.id,'-',cre_reg.fecha_hora_cancelado)as fecha_pago_credito"),
                    'cre_reg.fecha_hora_cancelado as fecha_pago',
                    DB::raw("SUBSTR(cre_reg.fecha_hora_cancelado,1,10) as fecha_pago_corta"),
                    'cre_reg.agencia_caja_cancelado as agencia_caja',
                    'cre_reg.caja_cancelado_id as caja_id',
                    'cre_reg.asesor_id',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    'cre_reg.dscto_mora_cancelado as dscto_mora',
                    'cre_reg.dscto_notificaciones_cancelado as dscto_notificaciones',
                    'cre_reg.dscto_interes_cancelado as dscto_interes',
                    'cre_reg.comentario_cancelado as comentario'
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

        $lista = DB::connection($conexion)->table(DB::raw("({$esquema->toSql()}) as pagos"))
            ->mergeBindings($esquema->getQuery())
            ->join('credito_aprobaciones as cre_apr', 'pagos.aprobacion_id', 'cre_apr.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('credito_productos as cre_pro', 'cre_apr.producto_id', 'cre_pro.id')
            ->join('cliente_registros as cli_reg', 'pagos.cliente_id', 'cli_reg.id')
            ->join('solucion_master.usuarios as usu', 'pagos.asesor_id', 'usu.dni')
            ->select(
                'pagos.*',

                'cre_apr.numero_credito',
                'cre_apr.tipo_id',
                'cre_apr.producto_id',
                'cre_apr.es_especial',

                'cre_tip.tipo',
                'cre_pro.producto',

                'cli_reg.numero_expediente',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',

                'usu.usuario as usuario_asesor'
            )
            ->orderBy('pagos.fecha_pago', 'desc')
            ->orderBy('pagos.numero_cuota', 'asc')
            ->get();

        $lista = $lista->groupBy('fecha_pago_credito')->map(function ($row) {

            $cliente = $row[0]->apellido_paterno . ' ' . $row[0]->apellido_materno . ' ' . $row[0]->nombres;

            $total_pago = floatval($row->sum('capital'))  +
                floatval($row->sum('interes'))  +
                floatval($row->sum('redondeo'))  +
                floatval($row->sum('moras'))  +
                floatval($row->sum('notificaciones'))  -
                floatval($row->sum('dscto_mora'))  -
                floatval($row->sum('dscto_notificaciones'))  -
                floatval($row->sum('dscto_interes'));

            return (object)[
                'credito_id' => $row[0]->credito_id,
                'numero_expediente' =>  $row[0]->numero_expediente,
                'numero_credito' =>  $row[0]->numero_credito,
                'numero_cuota' =>  $row->max('numero_cuota'),
                'cliente' =>  $cliente,
                'fecha_pago' =>  $row[0]->fecha_pago,
                'fecha_pago_corta' =>  $row[0]->fecha_pago_corta,
                'agencia_caja' =>  $row[0]->agencia_caja,
                'caja_id' =>  $row[0]->caja_id,
                'asesor_id' =>  $row[0]->asesor_id,
                'usuario_asesor' => $row[0]->usuario_asesor,
                'tipo_id' => $row[0]->tipo_id,
                'tipo' => $row[0]->tipo,
                'producto_id' => $row[0]->producto_id,
                'producto' => $row[0]->producto,
                'es_especial' => $row[0]->es_especial,
                'capital' => $row->sum('capital'),
                'interes' => $row->sum('interes'),
                'redondeo' => $row->sum('redondeo'),
                'moras' => $row->sum('moras'),
                'notificaciones' => $row->sum('notificaciones'),
                'dscto_mora' => $row->sum('dscto_mora'),
                'dscto_notificaciones' => $row->sum('dscto_notificaciones'),
                'dscto_interes' => $row->sum('dscto_interes'),
                'total_pago' => $total_pago,
                'comentario' => $row->max('comentario')
            ];
        });


        $lista_cobranzas = [];

        $lista = $lista->groupBy('agencia_caja');

        foreach ($lista as  $agencia_caja =>  $value) {

            if ($agencia_caja != 0 && $agencia_caja != null) {

                // Construir conexión dinámica
                $conexion_caja = 'master_' . $agencia_caja;

                // Obtener los caja_id únicos
                $caja_ids = $value->pluck('caja_id')->unique();

                // Obtener los datos de caja_registros
                $cajas  = Caja::on($conexion_caja)
                    ->from('caja_registros as caj_reg')
                    ->select('usu.usuario', 'caj_reg.dni', 'caj_reg.id')
                    ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                    ->whereIn('caj_reg.id', $caja_ids)
                    ->get()->keyBy('id');
                // Indexar por id para acceso rápido

                // Obtener datos de agencia (una sola consulta por agencia)
                $datos_agencia = Agencia::find($agencia_caja);

                // Asignar datos a los registros
                foreach ($value as $item) {
                    $datos_caja = $cajas[$item->caja_id] ?? null;

                    if ($datos_caja) {
                        // Convertir a array para poder fusionarlo
                        $datos_iniciales = (array) $item;

                        $datos_iniciales['usuario_caja'] = $datos_caja->usuario ?? null;
                        $datos_iniciales['usuario_agencia'] = $datos_agencia->nombre ?? null;
                        $datos_iniciales['dni_caja'] = $datos_caja->dni ?? null;

                        $lista_cobranzas[] = $datos_iniciales;
                    }
                }
            }
        }

        $lista_cobranzas = collect($lista_cobranzas)->sortByDesc('fecha_pago')->values();

        return response()->json([
            'lista_cobranzas' => $lista_cobranzas
        ]);
    }

    public function exportar(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $cobranzas = json_decode($request->lista_cobranzas);

        foreach ($cobranzas as $item) {

            $object = (object)[
                'expediente' => $item->numero_expediente . '-' . $item->numero_credito,
                'numero_cuota' => $item->numero_cuota,
                'cliente' =>  $item->cliente,
                'asesor' =>  $item->usuario_asesor,
                'fecha_pago' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha_pago)),
                'capital' => $item->capital,
                'interes' => $item->interes,
                'redondeo' => $item->redondeo,
                'moras' => $item->moras,
                'notificaciones' => $item->notificaciones,
                'dscto_mora' => $item->dscto_mora,
                'dscto_notificaciones' => $item->dscto_notificaciones,
                'dscto_interes' => $item->dscto_interes,
                'total' => $item->total_pago,
                'comentario' => $item->comentario,
                'caja' => $item->usuario_caja,
                'agencia' => $item->usuario_agencia,
                'tipo' => $item->tipo,
                'producto' => $item->producto,
                'especial' => $item->es_especial === 1 ? 'SI' : 'NO'
            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptCobranzasTipo');
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptCobranzasTipo.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Rellenando TÍTULO y ENCABEZADOS ---------------
        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        // $titulo = 'MOVIMIENTOS EN CUENTA - ' . $detalle_cuenta->usuario;
        // $sheet->setCellValue('B2', $titulo);
        $sheet->setCellValue('U2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando valores
        $indice = 5;
        $controller = new CreditosController();

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
        $nombre_archivo = $controller->concatenar_aleatorio('rptCobranzasTipo', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
