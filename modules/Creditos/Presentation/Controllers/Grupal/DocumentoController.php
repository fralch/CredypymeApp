<?php

namespace Modules\Creditos\Presentation\Controllers\Grupal;


use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Aprobacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Grupal\Solicitud;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Grupal\SolicitudCredito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Propuesta;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

use NumberToWords\NumberToWords;
use PhpOffice\PhpWord\TemplateProcessor;


class DocumentoController extends Controller
{

    protected $main_db;

    public function __construct()
    {
        $this->main_db = env('S_MASTER_DATABASE');
    }
    public function index()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'DOCUMENTOS', 'CREDITOS_GRUPAL');
            if ($band == 1) {
                return Inertia::render('Creditos/Grupal/documentos');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar_creditos(Request $request)
    {
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;

        $estado_id  = Estado::on($conexion)->where('estado', 'APROBADO')->value('id');

        $lista_creditos = Solicitud::on($conexion)
            ->from('grupo_solicitudes as gru_sol')
            ->select(
                'gru_sol.id as grupo_solicitud_id',
                'gru_sol.grupo_id',
                'gru_sol.fecha_aprobacion',
                DB::raw("CAST(gru_sol.plazo AS UNSIGNED) AS plazo"),
                'gru_sol.periodo_pago',

                DB::raw("SUM(gru_sol_cre.monto) as monto"),

                'gru.nombre as nombre_grupo',

                'usu.usuario as usuario_asesor'
            )
            ->join('grupo_solicitud_creditos as gru_sol_cre', 'gru_sol.id', 'gru_sol_cre.grupo_solicitud_id')
            ->join('grupos as gru', 'gru.id', 'gru_sol.grupo_id')
            ->join("$this->main_db.usuarios as usu", 'usu.dni', 'gru_sol.asesor_id')
            ->where('gru_sol.estado_id', $estado_id)
            ->groupBy('gru_sol_cre.grupo_solicitud_id')
            ->orderBy('gru_sol.fecha_aprobacion', 'desc')
            ->get();

        // dd($lista_creditos);

        return response()->json([
            'success' => true,
            'lista_creditos' => $lista_creditos
        ], 200);
    }

    public function generar(Request $request)
    {

        $tipo = $request->tipo;
        $datos_credito = json_decode($request->datos_credito);
        $datos_credito->agencia_id = $request->agencia_id;

        $datos_empresa = (object)[
            'nombre' => 'CREDIPYME HUANCA',
            'razon' => 'CREDIPYME HUANCA S.A.C.',
            'ruc' => '20614827239',
            'pagina' => 'www.credipymehuanca.com.pe',
            'direccion' => 'PROLONGACION HUANUCO N°317 – HUANCAYO- HUANCAYO -JUNIN',
            'gerente_nombre' => 'HUBER ALVAREZ APONTE',
            'gerente_dni' => '41188332'
        ];

        switch ($tipo) {

            case 'pagare':
                $resultado = $this->generar_pagare($datos_empresa, $datos_credito);

                break;
            case 'contrato':
                $resultado = $this->generar_contrato($datos_empresa, $datos_credito);
                break;
        }

        $resultado = $resultado->getData(true);

        $docxFile = public_path('temp_files/' . $resultado['documento'] . '.docx');
        $pdfFile = public_path('temp_files/' . $resultado['documento'] . '.pdf');

        $command = env('LIBREOFFICE') . " --headless --convert-to pdf $docxFile --outdir " . dirname($pdfFile);
        shell_exec($command);

        $path_pdf = '/temp_files/' . $resultado['documento'] . '.pdf';

        return response()->json([
            'success' => true,
            'path_pdf' => $path_pdf,
            'message' => '¡LISTO!'
        ], 200);
    }


    public function generar_pagare($datos_empresa, $datos_documento)
    {

        $template = new TemplateProcessor(public_path('report_templates/creditos/grupales/rptPagare.docx'));

        $grupo_solicitud_id = $datos_documento->grupo_solicitud_id;
        $agencia_id = $datos_documento->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_clientes = SolicitudCredito::on($conexion)
            ->from('grupo_solicitud_creditos as gru_sol_cre')
            ->select(
                DB::raw("CONCAT(cli_reg.apellido_paterno, ' ', cli_reg.apellido_materno, ' ', cli_reg.nombres) as cliente"),
                'cli_reg.dni as dni',
                'cli_reg.direccion as direccion',

                'dis.distrito as distrito'
            )

            ->join('cliente_registros as cli_reg', 'cli_reg.id', 'gru_sol_cre.cliente_id')
            ->join("$this->main_db.distritos as dis", 'dis.id', 'cli_reg.distrito_id')
            ->where('gru_sol_cre.grupo_solicitud_id', $grupo_solicitud_id)
            ->get();

        $data = [
            'empresa_razon' => $datos_empresa->razon,
            'empresa_ruc' => $datos_empresa->ruc,
            'empresa_direccion' => $datos_empresa->direccion,
            'empresa_gerente' => $datos_empresa->gerente_nombre,
            'empresa_gerente_dni' => $datos_empresa->gerente_dni,
        ];

        $template->cloneBlock('block_clientes', count($datos_clientes), true, true);
        $template->cloneBlock('block_firmas', count($datos_clientes), true, true);

        foreach ($data as $mark => $value) {
            $template->setValue($mark, $value);
        }

        foreach ($datos_clientes as  $index => $item) {

            $i = $index + 1;
            $template->setValue("cliente_numero#{$i}", $i);
            $template->setValue("cliente_nombres#{$i}", $item->cliente);
            $template->setValue("cliente_dni#{$i}", $item->dni);
            $template->setValue("cliente_direccion#{$i}", $item->direccion . ' - ' . $item->distrito);

            $template->setValue("nombre_r1#{$i}", $datos_clientes[0]->cliente);
            $template->setValue("dni_r1#{$i}", $datos_clientes[0]->dni);
        }

        $documento = (new CreditosController)->concatenar_aleatorio('rptPagare', 5);

        $template->saveAs(public_path('temp_files/' . $documento . '.docx'));

        return response()->json([
            'success' => true,
            'documento' => $documento,

        ], 200);
    }

    public function generar_contrato($datos_empresa, $datos_documento)
    {
        $template = new TemplateProcessor(public_path('report_templates/creditos/grupales/rptContrato.docx'));

        $grupo_solicitud_id = $datos_documento->grupo_solicitud_id;
        $agencia_id = $datos_documento->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $datos_clientes = SolicitudCredito::on($conexion)
            ->from('grupo_solicitud_creditos as gru_sol_cre')
            ->select(
                DB::raw("CONCAT(cli_reg.apellido_paterno, ' ', cli_reg.apellido_materno, ' ', cli_reg.nombres) as cliente"),
                'cli_reg.dni as dni',
            )
            ->join('cliente_registros as cli_reg', 'cli_reg.id', 'gru_sol_cre.cliente_id')
            ->where('gru_sol_cre.grupo_solicitud_id', $grupo_solicitud_id)
            ->get();

        $data = [
            'empresa_razon' => $datos_empresa->razon,
            'empresa_nombre' => $datos_empresa->nombre,
            'empresa_pagina' => $datos_empresa->pagina,
            'fecha_corta' => $fecha_corta
        ];

        $template->cloneBlock('block_clientes', count($datos_clientes), true, true);

        foreach ($data as $mark => $value) {
            $template->setValue($mark, $value);
        }

        foreach ($datos_clientes as  $index => $item) {

            $i = $index + 1;
            $template->setValue("cliente#{$i}", $item->cliente);
            $template->setValue("dni#{$i}", $item->dni);
        }

        $documento = (new CreditosController)->concatenar_aleatorio('rptContrato', 5);

        $template->saveAs(public_path('temp_files/' . $documento . '.docx'));

        return response()->json([
            'success' => true,
            'documento' => $documento,
        ], 200);
    }
}
