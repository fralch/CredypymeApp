<?php

namespace App\Http\Controllers\Gth\Asistencias;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\Asistencias\Justificacion;
use App\Models\General\Agencia;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Gth\Usuarios\Usuario;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Gth\GthController;






class JustificacionController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function justificaciones($modo)
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'JUSTIFICACIONES', 'GTH_ASISTENCIAS');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_JUSTIFICACIONES', 'GTH_ASISTENCIAS');
            }
            if ($band == 1) {


                if ($modo == 'completo') {
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
                } elseif ($modo == 'personal') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->where('dni', session('usuario_dni'))

                        ->orderBy('usuario', 'asc')
                        ->get();


                    $agencias = Agencia::from('agencias as age')
                        ->select('age.id_agencia', 'age.nombre')
                        ->join('usuarios as us', 'us.agencia_id', 'age.id_agencia')
                        ->where('us.dni', session('usuario_dni'))->get();
                }


                return Inertia::render(
                    'Gth/Asistencias/justificaciones',
                    [
                        'modo' => $modo,
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


    public function listar_justificaciones(Request $request)
    {
        // dd($request);
        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;


        $justificacionesfaltastardanzas = Justificacion::from('asistencia_justificaciones as ju')
            ->select(
                'ju.id',
                'af.usuario_id',
                'ju.created_at as fechas',
                'af.turno',

                DB::raw("CONCAT('FECHA FALTA: ', af.fecha ,', JUSTIFICACIÓN: ',ju.justificacion) as justificacion"),

                'ju.documento',
                'us.nombres',
                'us.apellido_paterno',
                'us.apellido_materno',
                'us.agencia_id',
                'ag.nombre as nombre_agencia',
                DB::raw("'FALTA' as tipo")

            )
            ->join('asistencia_faltas as af', 'af.id', 'ju.falta_id')
            ->join('usuarios as us',  'af.usuario_id', 'us.dni')
            ->join('agencias as ag', 'us.agencia_id', 'ag.id_agencia')
            ->whereBetween(DB::raw("STR_TO_DATE(ju.created_at, '%Y-%m-%d')"), [$f_desde, $f_hasta]);

        return Justificacion::from('asistencia_justificaciones as ju')
            ->select(
                'ju.id',
                'am.usuario_id',
                'ju.created_at as fechas',
                'am.turno',
                DB::raw("CONCAT('FECHA MARCAJE: ', am.hora_ingreso ,', MINUTOS: ',at.minutos,', JUSTIFICACIÓN: ',ju.justificacion) as justificacion"),
                'ju.documento',
                'us.nombres',
                'us.apellido_paterno',
                'us.apellido_materno',
                'us.agencia_id',
                'ag.nombre as nombre_agencia',
                DB::raw("'TARDANZA' as tipo")
            )
            ->join('asistencia_tardanzas as at', 'ju.tardanza_id', 'at.id')
            ->join('asistencia_marcajes as am', 'at.marcaje_id', 'am.id')
            ->join('usuarios as us', 'am.usuario_id', '=', 'us.dni')
            ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
            ->whereBetween(DB::raw("STR_TO_DATE(ju.created_at, '%Y-%m-%d')"), [$f_desde, $f_hasta])
            ->union($justificacionesfaltastardanzas)
            ->orderBy('fechas', 'desc')
            ->get();

        // dd($justificaciones);
    }





    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------

    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------
    public function mis_justificaciones()

    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_JUSTIFICACIONES', 'GTH_ASISTENCIAS');
            if ($band == 1) {
                return Inertia::render('Gth/Asistencias/mis_justificaciones');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar_mis_justificaciones(Request $request)
    {

        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;
        $dni = $request->dni;

        if (!$f_desde == null && !$f_hasta == null) {
            $misjustificacionesfaltas = Justificacion::from('asistencia_justificaciones as ju')
                ->select(
                    'ju.id',
                    'af.usuario_id',
                    'ju.created_at as fechas',
                    'af.turno',
                    DB::raw("CONCAT('FECHA FALTA: ', af.fecha ,', JUSTIFICACIÓN: ',ju.justificacion) as justificacion"),
                    'ju.documento',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'us.agencia_id',
                    'ag.nombre as nombre_agencia',
                    DB::raw("'FALTA' as tipo")
                )
                ->join('asistencia_faltas as af', 'ju.falta_id', 'af.id')
                ->join('usuarios as us', 'af.usuario_id', '=', 'us.dni')
                ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                ->whereBetween(DB::raw("STR_TO_DATE(ju.created_at, '%Y-%m-%d')"), [$f_desde, $f_hasta])

                ->where('af.usuario_id', $dni);


            return Justificacion::from('asistencia_justificaciones as ju')
                ->select(
                    'ju.id',
                    'am.usuario_id',
                    'ju.created_at as fechas',
                    'am.turno',
                    DB::raw("CONCAT('FECHA MARCAJE: ', am.hora_ingreso ,', MINUTOS: ',at.minutos,', JUSTIFICACIÓN: ',ju.justificacion) as justificacion"),
                    'ju.documento',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'us.agencia_id',
                    'ag.nombre as nombre_agencia',
                    DB::raw("'TARDANZA' as tipo")
                )
                ->join('asistencia_tardanzas as at', 'ju.tardanza_id', 'at.id')
                ->join('asistencia_marcajes as am', 'at.marcaje_id', 'am.id')
                ->join('usuarios as us', 'am.usuario_id', '=', 'us.dni')
                ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                ->whereBetween(DB::raw("STR_TO_DATE(ju.created_at, '%Y-%m-%d')"), [$f_desde, $f_hasta])

                ->where('am.usuario_id', $dni)

                ->union($misjustificacionesfaltas)
                ->get();
        }
    }
    public function exportar_justificaciones(request $request)
    {
        // dd($request);
        $datos = json_decode($request->datos);


        // dd($datos);

        $data = [];

        $orden = 1;
        foreach ($datos as $item) {
            $object = (object)[
                'numero' => $orden,
                'dni' => $item->usuario_id,
                'colaborador' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'hora_ingreso' => $item->fechas,
                'nombre_agencia' => $item->nombre_agencia,
                'turno' => $item->turno,
                'justificacion' => $item->justificacion,
                'tipo' => $item->tipo,



            ];


            $orden += 1;
            $data[] = $object;
        }
        // dd($data);

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load("./report_templates/gth/rptJustificaciones.xlsx");
        $sheet = $spreadsheet->getActiveSheet();


        //    Obteniendo formatos-----------------------------
        $celda = 1;

        $lista_formato_celdas_1 = [];
        $lista_formato_celdas_2 = [];
        $lista_formato_totales = [];

        while ($celda <= 8) {
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



        $sheet->setCellValue('G' . $indice, 'TOTAL');
        $sheet->setCellValue('H' . $indice, 'TOTAL ' . $total . ' registro(s)');


        foreach ($lista_formato_totales as $key => $value) {
            $sheet->getStyle((new GthController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }


        // Exportar para descarga-------------------------

        // Exportar para descarga-------------------------
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $ret['data'] = base64_encode(ob_get_contents());
        ob_end_clean();

        return $ret['data'];
    }
}
