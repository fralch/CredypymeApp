<?php

namespace App\Http\Controllers\Creditos\Credito;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpWord\TemplateProcessor;


class CreditoDocumentoFinancieroController extends Controller
{

    public function documentos_financieros()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'DOCUMENTOS_FINANCIEROS', 'CREDITOS_CREDITO');
            if ($band == 1) {
                return Inertia::render('Creditos/Creditos/documentos_financieros');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }


    public function generar(Request $request)
    {
        $tipo = $request->tipo;
        $datos_documento = json_decode($request->datos_documento);

        $datos_empresa = (object)[
            'razon' => 'GRUPO CREDIPYME S.A.',
            'nombre' => 'CREDIPYME',
            'ruc' => '20611432616',
            'direccion' => 'Jr. Cuarzo Nro. 125 Urb. Millotingo',
            'distrito' => 'El Tambo',
            'provincia' => 'Huancayo',
        ];

        switch ($tipo) {

            case 'hoja_resumen':
                $documento = $this->generar_hoja_resumen($datos_empresa, $datos_documento);
                break;

            case 'pagare_fianza':
                $documento = $this->generar_pagare_fianza($datos_empresa, $datos_documento);
                break;

            case 'riesgo_credito':
                $documento = $this->generar_riesgo_credito($datos_empresa, $datos_documento);
                break;

            case 'contrato':
                $documento = $this->generar_contrato($datos_empresa, $datos_documento);
                break;
        }

        $docxFile = public_path('temp_files/' . $documento . '.docx');
        $pdfFile = public_path('temp_files/' . $documento . '.pdf');

        // Use OpenOffice to convert DOCX to PDF
        $command = env('LIBREOFFICE') . " --headless --convert-to pdf $docxFile --outdir " . dirname($pdfFile);
        shell_exec($command);

        $path_pdf = '/temp_files/' . $documento . '.pdf';
        // Return the path PDF file to the user
        return ['path_pdf' => $path_pdf];
    }

    public function generar_hoja_resumen($datos_empresa, $datos_documento)
    {
        $template = new TemplateProcessor(public_path('report_templates/creditos/clientes/rptHojaResumen.docx'));

        $data = [
            'codigo_seguimiento' => $datos_documento->codigo_seguimiento,
            'empresa_nombre' => $datos_empresa->nombre,

            'monto' => $datos_documento->monto,
            'tasa_interes' => $datos_documento->tasa_interes,
            'tasa_interes_moratoria' => $datos_documento->tasa_interes_moratoria,
            'monto_interes' => $datos_documento->monto_interes,
            'producto' => $datos_documento->producto,
            'tipo' => $datos_documento->tipo,
            'frecuencia_pago' => $datos_documento->frecuencia_pago,
            'numero_cuotas' => $datos_documento->numero_cuotas,
            'fecha_vencimiento' => $datos_documento->fecha_vencimiento,

            'fecha_lugar_elaboracion' => $datos_documento->fecha_lugar_elaboracion,
        ];

        foreach ($data as $mark => $value) {
            $template->setValue($mark, $value);
        }

        $documento = (new CreditosController)->concatenar_aleatorio('rptHojaResumen', 5);

        $template->saveAs(public_path('temp_files/' . $documento . '.docx'));

        return  $documento;
    }

    public function generar_pagare_fianza($datos_empresa, $datos_documento)
    {
        $template = new TemplateProcessor(public_path('report_templates/creditos/clientes/rptPagareFianza.docx'));

        $data = [
            'codigo_seguimiento' => $datos_documento->codigo_seguimiento,
            'empresa_razon' => $datos_empresa->razon,
            'empresa_nombre' => $datos_empresa->nombre,

            'fecha_lugar_elaboracion' => $datos_documento->fecha_lugar_elaboracion,

            'titular_nombres' => $datos_documento->titular_nombres,
            'titular_dni' => $datos_documento->titular_dni,
            'titular_direccion' => $datos_documento->titular_direccion,

            'pariente_nombres' => $datos_documento->pariente_nombres,
            'pariente_dni' => $datos_documento->pariente_dni,
            'pariente_direccion' => $datos_documento->pariente_direccion,

            'aval_nombres' => $datos_documento->aval_nombres,
            'aval_dni' => $datos_documento->aval_dni,
            'aval_direccion' => $datos_documento->aval_direccion,

            'pariente_aval_nombres' => $datos_documento->pariente_aval_nombres,
            'pariente_aval_dni' => $datos_documento->pariente_aval_dni,
            'pariente_aval_direccion' => $datos_documento->pariente_aval_direccion,
        ];

        foreach ($data as $mark => $value) {
            $template->setValue($mark, $value);
        }

        $documento = (new CreditosController)->concatenar_aleatorio('rptPagareFianza', 5);

        $template->saveAs(public_path('temp_files/' . $documento . '.docx'));

        return  $documento;
    }

    public function generar_riesgo_credito($datos_empresa, $datos_documento)
    {
        $template = new TemplateProcessor(public_path('report_templates/creditos/clientes/rptRiesgoCredito.docx'));

        $data = [

            'empresa_razon' => $datos_empresa->razon,
            'empresa_nombre' => $datos_empresa->nombre,
            'empresa_ruc' => $datos_empresa->ruc,
            'empresa_direccion' => $datos_empresa->direccion,

            'fecha_lugar_elaboracion' => $datos_documento->fecha_lugar_elaboracion,

            'titular_nombres' => $datos_documento->titular_nombres,
            'titular_dni' => $datos_documento->titular_dni,
            'titular_direccion' => $datos_documento->titular_direccion,
            'titular_distrito' => $datos_documento->titular_distrito,
            'titular_provincia' => $datos_documento->titular_provincia,
            'titular_departamento' => $datos_documento->titular_departamento
        ];

        foreach ($data as $mark => $value) {
            $template->setValue($mark, $value);
        }

        $documento = (new CreditosController)->concatenar_aleatorio('rptRiesgoCredito', 5);

        $template->saveAs(public_path('temp_files/' . $documento . '.docx'));

        return  $documento;
    }

    public function generar_contrato($datos_empresa, $datos_documento)
    {
        $template = new TemplateProcessor(public_path('report_templates/creditos/clientes/rptContratoCredito.docx'));

        $data = [
            'codigo_seguimiento' => $datos_documento->codigo_seguimiento,
            'empresa_razon' => $datos_empresa->razon,
            'empresa_nombre' => $datos_empresa->nombre,
            'empresa_ruc' => $datos_empresa->ruc,
            'empresa_direccion' => $datos_empresa->direccion,
            'empresa_distrito' => $datos_empresa->distrito,
            'empresa_provincia' => $datos_empresa->provincia,

            'fecha_lugar_elaboracion' => $datos_documento->fecha_lugar_elaboracion,

            'titular_nombres' => $datos_documento->titular_nombres,
            'titular_dni' => $datos_documento->titular_dni,
            'titular_direccion' => $datos_documento->titular_direccion,

            'pariente_nombres' => $datos_documento->pariente_nombres,
            'pariente_dni' => $datos_documento->pariente_dni,
            'pariente_direccion' => $datos_documento->pariente_direccion,

            'aval_nombres' => $datos_documento->aval_nombres,
            'aval_dni' => $datos_documento->aval_dni,
            'aval_direccion' => $datos_documento->aval_direccion,

            'pariente_aval_nombres' => $datos_documento->pariente_aval_nombres,
            'pariente_aval_dni' => $datos_documento->pariente_aval_dni,
            'pariente_aval_direccion' => $datos_documento->pariente_aval_direccion,
        ];

        foreach ($data as $mark => $value) {
            $template->setValue($mark, $value);
        }

        $documento = (new CreditosController)->concatenar_aleatorio('rptContratoCredito', 5);

        $template->saveAs(public_path('temp_files/' . $documento . '.docx'));

        return  $documento;
    }
}
