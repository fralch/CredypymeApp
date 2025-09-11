<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\General\Datos_aplicacion;
use App\Models\Creditos\Cuenta\CuentaUsuario;
use App\Models\Creditos\Caja\AdelantoHaber;
use App\Models\Creditos\Cuenta\Movimiento;
use App\Models\Creditos\Caja\Caja;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\General\Agencia;
use App\Models\General\Departamento;
use App\Models\General\Provincia;
use App\Models\General\Distrito;
use PhpOffice\PhpWord\TemplateProcessor;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CajaAdelantoHaberController extends Controller
{
    public function adelanto_haberes()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;
            $band = (new PermisosController)->verificarPermiso(
                $x['usuario_dni'],
                'ADELANTO_HABERES',
                'CREDITOS_CAJA'
            );

            if ($band == 1) {

                return Inertia::render('Creditos/Caja/adelanto_haberes');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar_recursos()
    {
        $usuarios = Usuario::from('usuarios as usu')->select(
            'usu.dni',
            'usu.usuario',
            'usu.nombres',
            'usu.apellido_paterno',
            'usu.apellido_materno',
            'usu.agencia_id',

            'dep.departamento',
            'pro.provincia',
            'dis.distrito',

            'age.nombre as agencia'
        )
            ->join('solucion_master.departamentos as dep', 'usu.departamento_id', 'dep.id')
            ->join('solucion_master.provincias as pro', 'usu.provincia_id', 'pro.id')
            ->join('solucion_master.distritos as dis', 'usu.distrito_id', 'dis.id')
            ->join('solucion_master.agencias as age', 'age.id_agencia', 'usu.agencia_id')
            ->where([
                ['usu.habilitado', 1],
                ['usuario_real', 1]
            ])
            ->orderBy('usuario', 'asc')
            ->get();

        return response()->json(['usuarios' => $usuarios]);
    }


    public function revisar(Request $request)
    {
        $usuario_id = $request->input('usuario_id');
        $agencia_id = $request->input('agencia_id');

        $conexion = 'master_' .  $agencia_id;

        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $mes = date("m", strtotime($fecha_actual));
        $año = date("Y", strtotime($fecha_actual));


        $registros = AdelantoHaber::on($conexion)->where([
            ['usuario_id', $usuario_id],
            [DB::raw("SUBSTR(datos_creacion,11,4)"), $año],
            [DB::raw("SUBSTR(datos_creacion,16,2)"), $mes]
        ])->get();

        $mes_cancelado = $registros->where('tipo', 'CANCELACION');

        $total_adelantos = $registros->sum('monto');

        if (count($mes_cancelado) > 0) {
            $mes_cancelado = true;
        } else {
            $mes_cancelado = false;
        }

        return response()->json([
            'total_adelantos' => $total_adelantos,
            'mes_cancelado' => $mes_cancelado
        ]);
    }

    public function registrar(Request $request)
    {
        $frmDatosAdelanto = json_decode($request->frmDatosAdelanto);
        $caja_id = $request->caja_id;

        $agencia_id = session('id_agencia');

        $usuario_agencia_id = $request->usuario_agencia_id;

        $conexion = 'master_' .  $usuario_agencia_id;


        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_larga_aplicacion = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $usuario_id = $frmDatosAdelanto->usuario_id;
        $tipo = $frmDatosAdelanto->tipo;
        $monto = $frmDatosAdelanto->monto;
        $descripcion = $frmDatosAdelanto->descripcion;

        $adelanto = AdelantoHaber::on($conexion)->create([
            'usuario_id' => $usuario_id,
            'tipo' => $tipo,
            'descripcion' => mb_strtoupper($descripcion),
            'monto' => $monto,
            'agencia_caja' => $agencia_id,
            'caja_id' => $caja_id,
            'fecha_adelanto' => $fecha_larga_aplicacion,
            'datos_creacion' => $datos_registro
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Operación exitosa',
            'adelanto_id' => $adelanto->id
        ], 200);
    }
    public function exportar(Request $request)
    {
        $reporte = $request->reporte;

        if ($reporte == 'voucher') {
            return $this->exportar_voucher($request);
        } elseif ($reporte == 'declaracion') {
            return $this->exportar_declaracion($request);
        }
    }


    public function exportar_voucher($request)
    {

        $adelanto_id = $request->adelanto_id;
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' . $agencia_id;

        $adelanto = AdelantoHaber::on($conexion)->find($adelanto_id);
        $datos_registro = json_decode($adelanto->datos_creacion);
        $colaborador = Usuario::find($adelanto->usuario_id);

        $fecha_actual = (new CreditosController)->fecha_larga_aplicacion(session('id_agencia'));

        // Ordenando array de datos-------------------------------

        $inputFileName = './report_templates/caja/reportes/vchAdelanto.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos-----------------------------

        if ($adelanto->tipo == 'ADELANTO') {
            $sheet->setCellValue("A3", 'ADELANTO DE HABERES');
        } else if ($adelanto->tipo == 'CANCELACION') {
            $sheet->setCellValue("A3", 'CANCELACIÓN DE HABERES');
        }

        $sheet->setCellValue("B5", $datos_registro->fecha);
        $nombre_colaborador = $colaborador->apellido_paterno . ' ' .
            $colaborador->apellido_materno . ' ' .
            $colaborador->nombres;
        $sheet->setCellValue("A7",  $nombre_colaborador);

        $sheet->setCellValue("A9", $adelanto->descripcion);
        $sheet->setCellValue("C10", $adelanto->monto);

        $sheet->setCellValue("A12", session('nombre_agencia'));
        $sheet->setCellValue("A13", $fecha_actual);

        $sheet->setCellValue("A14", session('usuario') . ' - ' . session('dispositivo')->nombre);

        // Exportar para descarga-------------------------

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('vchAdelanto', 5);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }
    public function exportar_declaracion($request)
    {
        $template = new TemplateProcessor(public_path('report_templates/caja/reportes/rptDeclaracionJurada.docx'));


        $agencia_id = $request->agencia_id;

        $conexion = 'master_' . $agencia_id;


        $adelanto = AdelantoHaber::on($conexion)->find($request->adelanto_id);


        $colaborador = Usuario::find($adelanto->usuario_id);
        $departamento = Departamento::find($colaborador->departamento_id);
        $provincia = Provincia::find($colaborador->provincia_id);
        $distrito = Distrito::find($colaborador->distrito_id);

        $agencia = Agencia::find(session('id_agencia'));
        $distrito_agencia = Distrito::find($agencia->distrito_id);

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.UTF-8');

        $fecha_adelanto =  Carbon::parse($adelanto->fecha_adelanto)->toDateString();
        $fecha_letras = Carbon::parse($fecha_adelanto);
        $fecha_letras = $fecha_letras->translatedFormat('j \d\e F \d\e\l Y');

        $data = [
            'solicitante' => $colaborador->nombre_completo,
            'dni_solicitante' => $colaborador->dni,
            'departamento' => $departamento->departamento,
            'provincia' => $provincia->provincia,
            'distrito' => $distrito->distrito,
            'fecha' => $fecha_adelanto,
            'descripcion' => $adelanto->descripcion,
            'importe' => 'S/ ' . $adelanto->monto,
            'importe_total' => 'S/ ' . $adelanto->monto,

            'dni_responsable' => null,
            'dni_solicitante' => $colaborador->dni,
            'dni_autorizacion' => null,

            'lugar_fecha' => $distrito_agencia->distrito . ', ' .  $fecha_letras
        ];

        foreach ($data as $mark => $value) {
            $template->setValue($mark, $value);
        }

        $documento = (new CreditosController)->concatenar_aleatorio('rptDeclaracionJurada', 5);

        $template->saveAs(public_path('temp_files/' . $documento . '.docx'));

        $docxFile = public_path('temp_files/' . $documento . '.docx');
        $pdfFile = public_path('temp_files/' . $documento . '.pdf');



        // Use OpenOffice to convert DOCX to PDF
        $command = env('LIBREOFFICE') . " --headless --convert-to pdf $docxFile --outdir " . dirname($pdfFile);

        shell_exec($command);

        // var_dump(shell_exec($command));


        $path_pdf = '/temp_files/' . $documento . '.pdf';
        // dd($path_pdf);
        // Return the path PDF file to the user
        return ['path_pdf' => $path_pdf];
    }
}
