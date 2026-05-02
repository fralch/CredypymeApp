<?php

namespace Modules\Creditos\Presentation\Controllers\Grupal;

use App\Http\Controllers\Controller;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Negocio;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Grupo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\GrupoCliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Grupal\Solicitud;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Grupal\SolicitudCredito;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Producto;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpWord\TemplateProcessor;

class AprobacionController extends Controller
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
        $grupo_aprobacion_id = $request->input('grupo_aprobacion_id');

        return Inertia('Creditos/Grupal/aprobacion', [
            'grupo_id' => intval($grupo_id),
            'agencia_id' => intval($agencia_id),
            'grupo_solicitud_id' => intval($grupo_solicitud_id),
            'grupo_aprobacion_id' => $grupo_aprobacion_id ? intval($grupo_aprobacion_id) : null,
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

    public function aprobar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' . $agencia_id;

        $grupo_solicitud_id = $request->grupo_solicitud_id;
        $frmSolicitud = json_decode($request->frmSolicitud);

        $periodo_pago = $frmSolicitud->periodo_pago;
        $plazo = $frmSolicitud->plazo;
        $tasa_interes = floatval($frmSolicitud->tasa_interes);
        $tasa_retencion = floatval($frmSolicitud->tasa_retencion);

        $grupo_clientes = $frmSolicitud->grupo_clientes;

        $estado_id = Estado::on($conexion)
            ->select('id')
            ->where('estado', 'APROBADO')
            ->value('id');

        $controller = (new CreditosController);
        $fecha_aprobacion = $controller->fecha_larga_aplicacion($agencia_id);
        $datos_registro = $controller->datos_registro($agencia_id);

        $datos_grupo_solicitud = [
            'plazo' => $plazo,
            'periodo_pago' => $periodo_pago,
            'tasa_interes' => $tasa_interes,
            'tasa_retencion' => $tasa_retencion,
            'estado_id' => $estado_id,
            'fecha_aprobacion' => $fecha_aprobacion,
            'usuario_aprobacion' => session('usuario_dni'),
            'data_updated' => $datos_registro
        ];

        Solicitud::on($conexion)->where('id', $grupo_solicitud_id)->update($datos_grupo_solicitud);

        foreach ($grupo_clientes as $item) {

            $monto_retencion = ($tasa_retencion / 100) * floatval($item->monto);
            $datos = [
                'monto' => floatval($item->monto),
                'monto_retencion' => floatval($monto_retencion),
                'cuota' => floatval($item->cuota),
                'estado_id' => $estado_id,
                'data_updated' => $datos_registro
            ];
            SolicitudCredito::on($conexion)->where('id', $item->id)->update($datos);
        }

        return response()->json([
            'success' => true,
            'message' => 'Aprobación registrada'
        ], 200);
    }

    public function desaprobar(Request $request) {}

    public function buscar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' . $agencia_id;

        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $estado_id = Estado::on($conexion)->where('estado', 'APROBADO')->value('id');

        $solicitudes = Solicitud::on($conexion)
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
            'lista_grupos_creditos' => $solicitudes
        ], 200);
    }

    public function generar_ficha(Request $request)
    {

        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' . $agencia_id;

        $datos_grupo = json_decode($request->datos_grupo);
        $frmSolicitud = json_decode($request->frmSolicitud);

        $monto_total = 0;

        foreach ($frmSolicitud->grupo_clientes as $item) {
            $monto_total += floatval($item->monto);
        }

        $template = new TemplateProcessor(public_path('report_templates/creditos/grupales/rptFichaCredito.docx'));

        $grupo_clientes = $frmSolicitud->grupo_clientes;

        $data = [
            'tipo_documento' => 'APROBACIÓN',
            'nombre_grupo' => $datos_grupo->nombre,
            'fecha' => $frmSolicitud->fecha_solicitud,
            'asesor' => $datos_grupo->asesor,
            'monto_total' => number_format((float) $monto_total, 2, '.', ','),

        ];

        $dni_presidente = Cliente::on($conexion)->where('id', $grupo_clientes[0]->cliente_id)->value('dni');
        $plazo_periodo = $frmSolicitud->plazo . ' ' . (new CreditosController)->periodo_medicion($frmSolicitud->periodo_pago);

        $template->cloneBlock('block_clientes', count($grupo_clientes), true, true);

        foreach ($data as $mark => $value) {
            $template->setValue($mark, $value);
        }

        foreach ($grupo_clientes as  $index => $item) {
            $cliente = Cliente::on($conexion)
                ->from('cliente_registros as cli_reg')
                ->select(
                    'cli_reg.apellido_paterno',
                    'cli_reg.apellido_materno',
                    'cli_reg.nombres',
                    'cli_reg.dni',
                    'cli_reg.direccion',
                    'cli_reg.referencia_direccion',
                    'cli_reg.telefonos',

                    'dep.departamento',
                    'prov.provincia',
                    'dis.distrito'
                )
                ->join("$this->master_db.departamentos as dep", 'cli_reg.departamento_id', 'dep.id')
                ->join("$this->master_db.provincias as prov", 'cli_reg.provincia_id', 'prov.id')
                ->join("$this->master_db.distritos as dis", 'cli_reg.distrito_id', 'dis.id')
                ->where('cli_reg.id', $item->cliente_id)
                ->first();

            $telefonos = json_decode($cliente->telefonos);

            $negocio = Negocio::on($conexion)->where([
                ['cliente_id', $item->cliente_id],
                ['vinculado', 1]
            ])->first();

            $i = $index + 1;
            $template->setValue("cliente_cargo#{$i}", ($i == 1) ? 'PRESIDENTE' : ($i == 2 ? 'TESORERO' : 'MIEMBRO DEL GRUPO'));
            $template->setValue("apellido_paterno#{$i}", $cliente->apellido_paterno);
            $template->setValue("apellido_materno#{$i}", $cliente->apellido_materno);
            $template->setValue("nombres#{$i}", $cliente->nombres);
            $template->setValue("dni#{$i}", $cliente->dni);
            $template->setValue("cargo#{$i}", ($i == 1) ? 'PRESIDENTE' : ($i == 2 ? 'TESORERO' : '-'));
            $template->setValue("monto#{$i}", number_format((float) $item->monto, 2, '.', ','));
            $template->setValue("localidad#{$i}", $cliente->departamento . ' - ' . $cliente->provincia . ' - ' . $cliente->distrito);
            $template->setValue("direccion#{$i}", $cliente->direccion);
            $template->setValue("referencia_direccion#{$i}", $cliente->referencia_direccion);
            $template->setValue("telefonos#{$i}", $telefonos->t1 . ' / ' . ($telefonos->t2 ?? '-') . ' / ' . ($telefonos->t3 ?? '-'));
            $template->setValue("direccion_negocio#{$i}", $negocio ? $negocio->direccion : '-');

            $template->setValue("tipo_documento#{$i}", 'APROBACIÓN');
            $template->setValue("asesor#{$i}", $datos_grupo->asesor);
            $template->setValue("monto#{$i}", number_format((float) $item->monto, 2, '.', ','));
            $template->setValue("plazo_periodo#{$i}", $plazo_periodo);
            $template->setValue("cuota#{$i}", number_format((float) $item->cuota, 2, '.', ','));
            $template->setValue("tasa#{$i}", number_format((float) $frmSolicitud->tasa_interes, 2, '.', ','));

            $template->setValue("dni_r1#{$i}", $dni_presidente);
        }

        $documento = (new CreditosController)->concatenar_aleatorio('rptFichaAprobacion', 5);

        $template->saveAs(public_path('temp_files/' . $documento . '.docx'));

        $docxFile = public_path('temp_files/' . $documento . '.docx');
        $pdfFile = public_path('temp_files/' . $documento . '.pdf');

        // Use OpenOffice to convert DOCX to PDF
        $command = env('LIBREOFFICE') . " --headless --convert-to pdf $docxFile --outdir " . dirname($pdfFile);
        shell_exec($command);

        $path_pdf = '/temp_files/' . $documento . '.pdf';
        // Return the path PDF file to the user

        return response()->json([
            'success' => true,
            'path_pdf' => $path_pdf,
            'message' => 'Ficha de APROBACIÓN generada'
        ], 200);
    }
}
