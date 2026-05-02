<?php

namespace Modules\Gth\Presentation\Controllers\Asistencias;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Marcaje;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Tardanza;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Falta;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Justificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Gth\Presentation\Controllers\GthController;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Inertia\Inertia;

class AsistenciaGeneralController extends Controller
{

    public function general()
    {
        if (empty(session('usuario_dni'))) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso(session('usuario_dni'), 'REPORTE_GENERAL', 'GTH_ASISTENCIAS');
            if ($band == 1) {
                $usuarios = Usuario::from('usuarios as us')
                    ->select(
                        'us.dni',
                        'us.usuario',
                        'us.agencia_id',
                        'us.habilitado',
                        'age.nombre'
                    )
                    ->join('agencias as age', 'us.agencia_id', 'age.id_agencia')

                    ->orderBy('usuario', 'asc')
                    ->get();

                $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();



                return Inertia::render(
                    'Gth/Asistencias/reporte_general',
                    [
                        'usuarios' => $usuarios,
                        'agencias' => $agencias
                    ]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar(Request $request)
    {
        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;

        $faltas_justificacion = Falta::from('asistencia_faltas as af')
            ->select(
                'ag.nombre as nombre_agencia',
                'af.usuario_id',
                'us.apellido_paterno',
                'us.apellido_materno',
                'us.nombres',
                'af.fecha',
                'af.turno',
                DB::raw("'-' as hora_entrada"),
                DB::raw("'-' as hora_salida"),
                DB::raw("0 as minutos_tardanza"),
                DB::raw("IF(aj.justificacion is null,'FALTA','FALTA-JUSTIFICADA') as observacion"),
                DB::raw("IFNULL(aj.justificacion,'-') as justificacion")
            )
            ->join('usuarios as us', 'us.dni', 'af.usuario_id')
            ->join('agencias as ag', 'ag.id_agencia', 'us.agencia_id')
            ->leftjoin('asistencia_justificaciones as aj', 'aj.falta_id', 'af.id')

            ->where([
                [DB::raw("STR_TO_DATE(af.fecha, '%Y-%m-%d')"), '>=', [$f_desde]],
                [DB::raw("STR_TO_DATE(af.fecha, '%Y-%m-%d')"), '<=', [$f_hasta]],

            ]);


        return Marcaje::from('asistencia_marcajes as am')
            ->select(
                'ag.nombre as nombre_agencia',
                'am.usuario_id',
                'us.apellido_paterno',
                'us.apellido_materno',
                'us.nombres',
                DB::raw("STR_TO_DATE(am.hora_ingreso, '%Y-%m-%d') as fecha"),
                'am.turno',
                DB::raw("DATE_FORMAT(am.hora_ingreso,'%H:%i:%s') as hora_entrada"),
                DB::raw("DATE_FORMAT(am.hora_salida,'%H:%i:%s') as hora_salida"),
                DB::raw("IFNULL(at.minutos,0) as minutos_tardanza"),
                DB::raw("IF(at.minutos > 0 and at.justificado = 0,'TARDE',IF(at.justificado = 1,'TARDANZA-JUSTIFICADA','PUNTUAL')) as observacion"),
                DB::raw("IFNULL(aj.justificacion,'-') as justificacion")
            )
            ->join('usuarios as us', 'us.dni', 'am.usuario_id')
            ->join('agencias as ag', 'ag.id_agencia', 'us.agencia_id')
            ->leftjoin('asistencia_tardanzas as at', 'at.marcaje_id', 'am.id')

            ->leftjoin('asistencia_justificaciones as aj', 'aj.tardanza_id', 'at.id')
            ->where([
                [DB::raw("STR_TO_DATE(am.hora_ingreso, '%Y-%m-%d')"), '>=', [$f_desde]],
                [DB::raw("STR_TO_DATE(am.hora_ingreso, '%Y-%m-%d')"), '<=', [$f_hasta]],

            ])
            ->union($faltas_justificacion)
            ->orderBy('fecha', 'desc')
            ->get();
    }

    public function exportar_general(request $request)
    {

        // dd($request);
        $datos = json_decode($request->datos);
        $total_minutos = $request->total_minutos_total;



        // dd($datos);

        $data = [];

        $orden = 1;
        foreach ($datos as $item) {
            $object = (object)[
                'numero' => $orden,
                'dni' => $item->usuario_id,
                'colaborador' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'fecha_registro' => $item->fecha,
                'agencia' => $item->nombre_agencia,
                'turno' => $item->turno,
                'hora_entrada' => $item->hora_entrada,
                'hora_salida' => $item->hora_salida,
                'minutos_tardanza' => $item->minutos_tardanza,
                'observacion' => $item->observacion,
                'justificacion' => $item->justificacion,

            ];


            $orden += 1;
            $data[] = $object;
        }
        // dd($data);

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load("./report_templates/gth/rptAsistenciaGeneral.xlsx");
        $sheet = $spreadsheet->getActiveSheet();


        //    Obteniendo formatos-----------------------------
        $celda = 1;

        $lista_formato_celdas_1 = [];
        $lista_formato_celdas_2 = [];
        $lista_formato_totales = [];

        while ($celda <= 11) {
            $columna_1 = (new GthController)->num2char($celda);

            $formato_celdas_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celdas_2 = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_totales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formato_celdas_1[] = $formato_celdas_1;
            $lista_formato_celdas_2[] = $formato_celdas_2;
            $lista_formato_totales[] = $formato_totales;

            $celda++;
        }

        $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);

        // Insertando datos-----------------------------
        $indice = 5;
        $total = 0;

        foreach ($data as $item) {
            $columna_2 = 1;

            foreach ($item as $value) {
                $sheet->setCellValue((new GthController)->num2char($columna_2) . $indice, $value);
                if ($indice % 2 == 0) {
                    $sheet->getStyle((new GthController)->num2char($columna_2) . $indice)->applyFromArray($lista_formato_celdas_2[$columna_2 - 1]);
                } else {
                    $sheet->getStyle((new GthController)->num2char($columna_2) . $indice)->applyFromArray($lista_formato_celdas_1[$columna_2 - 1]);
                }
                $columna_2 += 1;
            }
            $indice += 1;
            $total += 1;
        }
        // dd($data);
        $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(9);

        $indice += 1;


        $sheet->setCellValue('J' . $indice, 'TOTAL ' . $total_minutos . ' minuto(s)');

        $sheet->setCellValue('K' . $indice, 'TOTAL');
        $sheet->setCellValue('L' . $indice,  $total . ' registro(s)');


        foreach ($lista_formato_totales as $key => $value) {
            $sheet->getStyle((new GthController)->num2char($key + 1) . $indice)->applyFromArray($value);
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
