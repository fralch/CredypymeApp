<?php

namespace App\Http\Controllers\Creditos\Grupal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Negocio;
use App\Models\Creditos\Clientes\Grupo;
use App\Models\Creditos\Clientes\GrupoCliente;
use App\Models\Creditos\Creditos\Propuesta;
use App\Models\Creditos\Creditos\Credito;
use App\Models\Creditos\Grupal\Solicitud;

use App\Models\Creditos\Mantenimiento\Credito\Producto;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    protected $main_db;

    public function __construct()
    {
        $this->main_db = env('S_MASTER_DATABASE');
    }
    public function index(Request $request)
    {
        $modo = $request->input('modo');
        $grupo_id = $request->input('grupo_id');
        $agencia_id = $request->input('agencia_id');
        $grupo_solicitud_id = $request->input('grupo_solicitud_id');

        return Inertia('Creditos/Grupal/solicitud', [
            'modo' => $modo,
            'grupo_id' => intval($grupo_id),
            'agencia_id' => intval($agencia_id),
            'grupo_solicitud_id' => $grupo_solicitud_id ? intval($grupo_solicitud_id) : null,
        ]);
    }
    public function listar_datos(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $grupo_id = $request->input('grupo_id');
        $grupo_solicitud_id = $request->input('grupo_solicitud_id');

        $conexion = 'master_' . $agencia_id;

        $datos_grupo = Grupo::on($conexion)
            ->from('grupos as gru')
            ->select(
                'gru.id',
                'gru.nombre',
                'gru.asesor_id',

                'usu.usuario as asesor'
            )
            ->join("$this->main_db.usuarios as usu", 'gru.asesor_id', 'usu.dni')
            ->where('gru.id', $grupo_id)
            ->get()->last();

        $grupo_clientes = GrupoCliente::on($conexion)
            ->from('grupo_clientes as gru_cli')
            ->select(
                'gru_cli.id',
                'gru_cli.cliente_id',
                'gru_cli.responsable',

                DB::raw("CONCAT(cli_reg.apellido_paterno, ' ', cli_reg.apellido_materno, ' ', cli_reg.nombres) AS cliente"),
                DB::raw("ROUND(200,2) as monto"),
                DB::raw("0 as tasa_interes"),
                DB::raw("0 as cuota"),
                DB::raw("0 as monto_retencion")
            )
            ->join("cliente_registros as cli_reg", 'gru_cli.cliente_id', 'cli_reg.id')
            ->where('gru_cli.grupo_id', $grupo_id)
            ->orderByRaw('gru_cli.responsable IS NULL, gru_cli.responsable ASC')
            ->get();


        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);


        if ($grupo_solicitud_id) {

            $grupo_clientes = Solicitud::on($conexion)
                ->from('credito_solicitudes as cre_sol')
                ->select(
                    'cre_sol.*',
                    DB::raw("CONCAT(cli_reg.apellido_paterno, ' ', cli_reg.apellido_materno, ' ', cli_reg.nombres) AS cliente"),

                )
                ->join('cliente_registros as cli_reg', 'cre_sol.cliente_id', 'cli_reg.id')
                ->where('grupo_solicitud_id', $grupo_solicitud_id)
                ->get();
        }

        return response()->json([
            'success' => true,
            'datos_grupo' => $datos_grupo,
            'grupo_clientes' => $grupo_clientes,
            'fecha_actual' => $fecha_actual
        ], 200);
    }

    public function calcular_cronograma(Request $request)
    {

        $agencia_id = $request->input('agencia_id');
        $frmSolicitud = json_decode($request->input('frmSolicitud'));

        $plazo = $frmSolicitud->plazo;
        $periodo_pago = $frmSolicitud->periodo_pago;
        $tasa_interes = $frmSolicitud->tasa_interes;
        $fecha_solicitud = $frmSolicitud->fecha_solicitud;

        $grupo_clientes = $frmSolicitud->grupo_clientes;

        $datos_credito = (object)[
            'plazo' => $plazo,
            'periodo_pago' => $periodo_pago
        ];

        foreach ($grupo_clientes as  $item) {
            $datos_credito->monto = $item->monto;
            $datos_credito->tasa_interes = $tasa_interes;

            $datos_credito->con_dias_gracia = 0;
            $datos_credito->es_especial = false;

            $item->cuota = (new CreditosController)->calcular_cuota($datos_credito)->monto_cuota;
        }

        $datos_desembolso = (object)[
            'fecha_desembolso' => $fecha_solicitud,
            'periodo_pago' => $periodo_pago,
            'plazo' => $plazo,

        ];

        $datos_calendario = (new CreditosController)->calendario_sin_cuotas($agencia_id, $datos_desembolso);

        return response()->json([
            'success' => true,
            'cuotas_clientes' => $grupo_clientes,
            'datos_calendario' => $datos_calendario,
        ], 200);
    }
    public function verificar(Request $request)
    {

        $agencia_id = $request->input('agencia_id');
        $grupo_id = $request->input('grupo_id');

        $credito_observado = false;

        // Verificar si existe un crédito grupal VIGENTE 

        $conexion = 'main_' . $agencia_id;

        $estado = Estado::on($conexion)->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $creditos_vigentes = Solicitud::on($conexion)
            ->where([
                ['grupo_id', $grupo_id],
                ['estado_id', $estado_id]
            ])->get()->count();


        if ($creditos_vigentes > 0) {
            $credito_observado = true;
        }

        return response()->json(['credito_observado' => $credito_observado]);
    }
    public function guardar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'main_' . $agencia_id;

        $modo = $request->modo;
        $grupo_id = $request->grupo_id;
        $asesor_id = $request->asesor_id;
        $frmSolicitud = json_decode($request->frmSolicitud);

        $periodo_pago = $frmSolicitud->periodo_pago;
        $plazo = $frmSolicitud->plazo;
        $comentario_solicitud = mb_strtoupper($frmSolicitud->comentario_solicitud);
        $fecha_calculo = $frmSolicitud->fecha_calculo;
        $integrantes = $frmSolicitud->integrantes;

        $estado = Estado::on($conexion)
            ->select('id')
            ->where('estado', 'SOLICITADO')
            ->get()
            ->last();
        $estado_id = $estado->id;

        $producto = Producto::on($conexion)
            ->select('id')
            ->where('producto', 'CRÉDITO GRUPAL')
            ->get()
            ->last();
        $producto_id = $producto->id;

        $controller = (new CreditosController);
        $fecha_solicitud = $controller->fecha_larga_sistema($agencia_id);
        $datos_registro = $controller->datos_registro($agencia_id);

        $datos_grupo_solicitud = [
            'agencia_id' => $agencia_id,
            'grupo_id' => $grupo_id,
            'asesor_id' => $asesor_id,
            'estado_id' => $estado_id
        ];

        if ($modo == 'EDITAR_SOLICITUD') {

            $grupo_solicitud_id = $request->grupo_solicitud_id;
            $datos_grupo_solicitud['data_updated'] = $datos_registro;
            Solicitud::on($conexion)->where('id', $grupo_solicitud_id)->update($datos_grupo_solicitud);

            foreach ($integrantes as $item) {
                $datos = [
                    'asesor_id' => $asesor_id,
                    'monto' => floatval($item->monto),
                    'tasa_interes' => floatval($item->tasa_interes),
                    'plazo' => floatval($plazo),
                    'periodo_pago' => $periodo_pago,
                    'cuota' => floatval($item->cuota),
                    'ahorro_solidario' => floatval($item->ahorro_solidario),
                    'fecha_solicitud' =>  $fecha_solicitud,
                    'fecha_calculo' => $fecha_calculo,
                    'comentario_solicitud' => $comentario_solicitud,
                    'usuario_solicitud' => session('usuario_id'),
                    'data_updated' => $datos_registro
                ];
                Solicitud::on($conexion)->where('id', $item->id)->update($datos);
            }
        } else {
            $datos_grupo_solicitud['data_created'] = $datos_registro;
            $grupo_solicitud = Solicitud::on($conexion)->create($datos_grupo_solicitud);
            $grupo_solicitud_id = $grupo_solicitud->id;

            foreach ($integrantes as $item) {
                $datos = [
                    'cliente_id' => $item->cliente_id,
                    'asesor_id' => $asesor_id,
                    'grupo_solicitud_id' => $grupo_solicitud_id,
                    'monto' => floatval($item->monto),
                    'tasa_interes' => floatval($item->tasa_interes),
                    'plazo' => floatval($plazo),
                    'periodo_pago' => $periodo_pago,
                    'cuota' => floatval($item->cuota),
                    'producto_id' => $producto_id,
                    'ahorro_solidario' => floatval($item->ahorro_solidario),
                    'fecha_solicitud' =>  $fecha_solicitud,
                    'fecha_calculo' => $fecha_calculo,
                    'comentario_solicitud' => $comentario_solicitud,
                    'usuario_solicitud' => session('usuario_id'),
                    'estado_id' => $estado_id,
                    'data_created' => $datos_registro
                ];
                Solicitud::on($conexion)->create($datos);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitud registrada',
            'grupo_solicitud_id' => $grupo_solicitud_id
        ], 200);
    }

    public function buscar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'main_' . $agencia_id;

        $fecha_actual = (new CreditosController)->fecha_corta_sistema($agencia_id);

        $estado = Estado::on($conexion)->where('estado', 'SOLICITADO')->get()->last();
        $estado_id = $estado->id;

        $solicitudes = Solicitud::on($conexion)
            ->from('cliente_grupo_solicitudes as cli_gru_sol')
            ->select(
                'cli_gru_sol.id',
                'cli_gru_sol.grupo_id',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(cli_gru_sol.data_created, '$.fecha')) as fecha"),

                'cli_gru.nombre',

                'sed.sede',
                'usu.usuario as usuario_asesor'
            )
            ->join('cliente_grupos as cli_gru', 'cli_gru_sol.grupo_id', 'cli_gru.id')
            ->join("$this->main_db.sedes as sed", 'cli_gru.agencia_id', 'sed.id')
            ->join("$this->main_db.usuarios as usu", 'cli_gru.asesor_id', 'usu.id')
            ->whereDate(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(cli_gru_sol.data_created, '$.fecha'))"), $fecha_actual)
            ->where('estado_id', $estado_id)
            ->get();

        return response()->json([
            'solicitudes' => $solicitudes
        ], 200);
    }

    public function exportar(Request $request)
    {

        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $solicitud_id = $request->solicitud_id;
        $grupo_id = $request->grupo_id;
        $conexion = 'main_' . $agencia_id;

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
