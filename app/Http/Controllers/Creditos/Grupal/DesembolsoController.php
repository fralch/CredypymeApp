<?php

namespace App\Http\Controllers\Creditos\Grupal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Negocio;
use App\Models\Creditos\Clientes\Grupo;
use App\Models\Creditos\Clientes\GrupoCliente;
use App\Models\Creditos\Grupal\Solicitud;
use App\Models\Creditos\Grupal\SolicitudCredito;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Caja\Desembolso;
use App\Models\Creditos\Caja\Transaccion;
use App\Models\Creditos\Clientes\Movimiento;
use App\Models\Creditos\Inversion\InversionMeta;
use App\Models\Creditos\Inversion\InversionMetaMovimiento;
use App\Models\Creditos\Mantenimiento\Credito\Producto;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Mantenimiento\Inversion\ProductosMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesembolsoController extends Controller
{
    protected $master_db;

    public function __construct()
    {
        $this->master_db = env('S_MASTER_DATABASE');
    }
    public function index(Request $request)
    {
        $grupo_id = $request->input('grupo_id');
        $agencia_id = $request->input('agencia_id');
        $grupo_solicitud_id = $request->input('grupo_solicitud_id');
        $grupo_desembolso_id = $request->input('grupo_desembolso_id');

        return Inertia('Creditos/Grupal/desembolso', [
            'grupo_id' => intval($grupo_id),
            'agencia_id' => intval($agencia_id),
            'grupo_solicitud_id' => intval($grupo_solicitud_id),
            'grupo_desembolso_id' => $grupo_desembolso_id ? intval($grupo_desembolso_id) : null,
        ]);
    }
    public function listar_datos(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $grupo_id = $request->input('grupo_id');
        $grupo_solicitud_id = $request->input('grupo_solicitud_id');;

        $conexion = 'master_' . $agencia_id;

        $datos_grupo = Grupo::on($conexion)
            ->from('grupos as gru')
            ->select(
                'gru.id',
                'gru.nombre',
                'gru.asesor_id',

                'usu.usuario as asesor'
            )
            ->join("$this->master_db.usuarios as usu", 'gru.asesor_id', 'usu.dni')
            ->where('gru.id', $grupo_id)
            ->get()->last();

        $grupo_clientes = SolicitudCredito::on($conexion)
            ->from('grupo_solicitud_creditos as gru_cre_sol')
            ->select(
                'gru_cre_sol.*',

                'cli_reg.agencia_id',
                DB::raw("CONCAT(cli_reg.apellido_paterno, ' ', cli_reg.apellido_materno, ' ', cli_reg.nombres) AS cliente"),

            )
            ->join('cliente_registros as cli_reg', 'gru_cre_sol.cliente_id', 'cli_reg.id')
            ->where('grupo_solicitud_id', $grupo_solicitud_id)
            ->orderBy('gru_cre_sol.id', 'asc')
            ->get();

        $grupo_solicitud = Solicitud::on($conexion)->find($grupo_solicitud_id);


        return response()->json([
            'success' => true,
            'datos_grupo' => $datos_grupo,
            'grupo_clientes' => $grupo_clientes,
            'grupo_solicitud' => $grupo_solicitud
        ], 200);
    }


    public function guardar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' . $agencia_id;

        $grupo_solicitud_id = $request->grupo_solicitud_id;
        $caja_id = $request->caja_id;
        $agencia_caja = $request->agencia_caja;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_desembolso = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $datos_solicitud = Solicitud::on($conexion)->find($grupo_solicitud_id);
        $datos_grupo = Grupo::on($conexion)->find($datos_solicitud->grupo_id);
        $grupo_creditos = SolicitudCredito::on($conexion)->where('grupo_solicitud_id', $grupo_solicitud_id)->get();

        $estado = Estado::on($conexion)->select('id')
            ->where('estado', 'DESEMBOLSADO')
            ->get()
            ->last();
        $estado_id = $estado->id;

        // Registro de la inversión para el crédito grupal
        $producto_meta = ProductosMeta::on($conexion)->select('id', 'valor_meta')->where('producto', 'CREDITO - GRUPAL')->get()->last();
        $inversion = InversionMeta::on($conexion)->create(
            [
                'agencia_id' => $agencia_id,
                'producto_meta_id' => $producto_meta->id,
                'cliente_id' => $grupo_creditos[0]->cliente_id, // Tomando el cliente responsable del grupo para registrar la inversión
                'comentario' => $datos_grupo->nombre,
                'valor_meta' => $producto_meta->valor_meta,
                'fecha_apertura' => $fecha_desembolso,
                'agencia_caja_apertura' => $agencia_caja,
                'caja_apertura' => $caja_id,
                'datos_creacion' => $datos_registro
            ]
        );

        $inversion_id = $inversion->id;

        foreach ($grupo_creditos as $item) {
            $grupo_credito_id = $item->id;
            $agencia_cliente = $item->agencia_cliente;
            $cliente_id = $item->cliente_id;
            $asesor_id = $datos_solicitud->asesor_id;
            $plazo = $datos_solicitud->plazo;
            $monto = $item->monto;
            $monto_retencion = $item->monto_retencion;
            $tasa_interes = $datos_solicitud->tasa_interes;
            $periodo_pago = $datos_solicitud->periodo_pago;
            $fecha_calculo = $datos_solicitud->fecha_aprobacion;
            $cuota = $item->cuota;

            $datos_credito = (object)[
                'plazo' => $plazo,
                'monto' => $monto,
                'tasa_interes' => $tasa_interes,
                'periodo_pago' => $periodo_pago,
                'con_dias_gracia' => false,
                'es_especial' => false,
            ];

            $datos_cuota = (new CreditosController)->calcular_cuota($datos_credito);

            $datos_desembolso = (object)[
                'monto' => $monto,
                'tasa_interes' => $tasa_interes,
                'fecha_desembolso' => $fecha_calculo,
                'periodo_pago' => $periodo_pago,
                'plazo' => $plazo,
                'dias_gracia' => 0,
                'es_especial' => false,
            ];

            $calendario = (new CreditosController)->calendario_con_redondeo($agencia_id, $datos_desembolso, $datos_cuota);

            $cuotas_pendientes = intval($plazo);
            $fecha_vencimiento = date("Y-m-d", strtotime($calendario[$cuotas_pendientes - 1]->fecha_pago));
            $saldo_total = floatval($cuota) * floatval($plazo);
            $capital_total = $monto;
            $interes_total = floatval($saldo_total) - floatval($capital_total);

            // Registro del crédito
            $credito = Credito::on($conexion)->create([
                'grupo_credito_id' => $grupo_credito_id,
                'fecha_desembolso' => $fecha_desembolso,
                'agencia_id' => $agencia_cliente,
                'cliente_id' => $cliente_id,
                'asesor_id' => $asesor_id,
                'cobrador_id' => $asesor_id,
                'saldo_total' => $saldo_total,
                'capital_total' => $capital_total,
                'interes_total' => $interes_total,
                'estado_id' => $estado_id,
                'cuota_actual' => 1,
                'cuotas_pendientes' => $cuotas_pendientes,
                'fecha_vencimiento' => $fecha_vencimiento,
                'datos_creacion' => $datos_registro
            ]);

            $credito_id = $credito->id;

            // Registro del detalle de cuotas

            $cronograma = array_map(function ($item) use ($credito_id, $datos_registro) {

                return [
                    'credito_id' => $credito_id,
                    'numero_cuota' => $item->orden,
                    'fecha_vencimiento' => date("Y-m-d", strtotime($item->fecha_pago)),
                    'cuota' => $item->monto_cuota,
                    'capital' => $item->monto_capital,
                    'interes' => $item->monto_interes,
                    'created_at' => json_decode($datos_registro)->fecha,
                    'updated_at' => json_decode($datos_registro)->fecha
                ];
            }, $calendario);

            Cuota::on($conexion)->insert($cronograma);

            // Registro del DESEMBOLSO

            $porcentaje_igv = 18;
            $interes_total = floatval($saldo_total) - floatval($capital_total);
            $importe_gravado = round(($interes_total) / (1 + ($porcentaje_igv / 100)), 2);
            $importe_igv = round($interes_total - $importe_gravado, 2);

            Desembolso::on($conexion)->create([
                'credito_id' => $credito_id,
                'monto' => $monto,
                'interes_total' => $interes_total,
                'porcentaje_igv' => 18,
                'importe_igv' => $importe_igv,
                'importe_gravado' => $importe_gravado,
                'agencia_caja' => $agencia_caja,
                'caja_id' => $caja_id,
                'emite_comprobante' => false,
                'modo_desembolso' => 'OFICINA',
                'datos_creacion' => $datos_registro
            ]);

            // Actualizando estado del credito de la solicitud

            SolicitudCredito::on($conexion)->where('id', $grupo_credito_id)->update([
                'estado_id' => $estado_id,
                'data_updated' => $datos_registro
            ]);

            // Guardando aporte de RETENCIÓN en INVERSIÓN META
            $cliente = Cliente::on($conexion)->find($cliente_id);
            $nombre_cliente = $cliente->apellido_paterno . ' ' . $cliente->apellido_materno . ' ' . $cliente->nombres;

            InversionMetaMovimiento::on($conexion)->create([
                'inversion_id' => $inversion_id,
                'tipo' => 'I',
                'monto' => $monto_retencion,
                'comentario' => "Retención por desembolso - $nombre_cliente",
                'agencia_caja' => $agencia_caja,
                'caja_id' => $caja_id,
                'datos_creacion' => $datos_registro
            ]);

            $inversion = InversionMeta::on($conexion)->find($inversion_id);
            // Actualizando saldo de la inversión meta
            $inversion->acumulado += $monto_retencion;
            $inversion->fecha_movimiento = $fecha_desembolso;
            $inversion->save();
        }

        // Actualizando el ESTADO de la SOLICITUD GRUPAL
        $grupo_solicitud = Solicitud::on($conexion)->find($grupo_solicitud_id);
        $grupo_solicitud->estado_id = $estado_id;
        $grupo_solicitud->fecha_desembolso = $fecha_desembolso;
        $grupo_solicitud->agencia_caja = $agencia_caja;
        $grupo_solicitud->caja_desembolso = $caja_id;
        $grupo_solicitud->data_updated = $datos_registro;
        $grupo_solicitud->save();

        return response()->json([
            'success' => true,
            'message' => 'Solicitud GRUPAL DESEMBOLSADA',
            'grupo_desembolso_id' => $grupo_solicitud_id
        ], 200);
    }

    public function desaprobar(Request $request) {}

    public function buscar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' . $agencia_id;

        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $estado_id = Estado::on($conexion)->where('estado', 'APROBADO')->value('id');

        $grupo_creditos = Solicitud::on($conexion)
            ->from('grupo_solicitudes as gru_sol')
            ->select(
                'gru_sol.id',
                'gru_sol.grupo_id',
                'gru_sol.plazo',
                'gru_sol.periodo_pago',
                'gru_sol.tasa_interes',
                'gru_sol.fecha_aprobacion',

                'gru.nombre as nombre_grupo',
                'usu_1.usuario as asesor',
                'usu_2.usuario as usuario_registro',

                DB::raw("SUM(gru_sol_cre.monto) as monto"),
            )

            ->join('grupos as gru', 'gru_sol.grupo_id', 'gru.id')
            ->join('grupo_solicitud_creditos as gru_sol_cre', 'gru_sol.id', 'gru_sol_cre.grupo_solicitud_id')
            ->join("$this->master_db.usuarios as usu_1", 'gru.asesor_id', 'usu_1.dni')
            ->join("$this->master_db.usuarios as usu_2", 'gru_sol.usuario_aprobacion', 'usu_2.dni')
            ->whereDate('gru_sol.fecha_aprobacion', $fecha_actual)
            ->where('gru_sol.estado_id', $estado_id)
            ->groupBy(
                'gru_sol.id',
                'gru_sol.grupo_id',
                'gru_sol.plazo',
                'gru_sol.periodo_pago',
                'gru_sol.tasa_interes',
                'gru_sol.fecha_aprobacion',
                'gru.nombre',
                'usu_1.usuario',
                'usu_2.usuario'
            )
            ->get();

        return response()->json([
            'success' => true,
            'lista_grupos_creditos' => $grupo_creditos
        ], 200);
    }

    public function exportar(Request $request)
    {

        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $solicitud_id = $request->solicitud_id;
        $grupo_id = $request->grupo_id;
        $conexion = 'master_' . $agencia_id;

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptFichaCreditoGrupal');
        $spreadsheet = $reader->load("./report_templates/reportes/creditos/rptFichaCredito.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Insertando valores

        $controller = new CreditosController();

        $solicitud = Solicitud::on($conexion)->find($solicitud_id);
        $cliente = Cliente::on($conexion)->find($solicitud->cliente_id);
        $negocio_cliente = Negocio::on($conexion)->where('cliente_id', $solicitud->cliente_id)->get()->last();
        $grupo = Grupo::on($conexion)->find($grupo_id);
        $asesor_grupo = $grupo->asesor;

        // Rellenando ENCABEZADO

        // Exportar para descarga-------------------------

        $nombre_archivo =  $controller->concatenar_aleatorio('rptFichaCreditoGrupal', 5);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }
}
