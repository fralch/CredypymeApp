<?php

namespace Modules\Gth\Presentation\Controllers\Asistencias;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Falta;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Justificacion;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Marcaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Gth\Presentation\Controllers\GthController;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class FaltaController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function faltas($modo)
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'FALTAS', 'GTH_ASISTENCIAS');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_FALTAS', 'GTH_ASISTENCIAS');
            }
            if ($band == 1) {

                $this->generar_faltas();

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
                    'Gth/Asistencias/faltas',
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

    // --------------------------------------------------------------------------
    public function listar_faltas(Request $request)
    {
        $this->generar_faltas();

        // dd($request);
        $dni = $request->dni;
        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;

        if (!$dni) {
            return Falta::from('asistencia_faltas as af')
                ->select(
                    'af.id',
                    'af.fecha',
                    'af.usuario_id',
                    'af.turno',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'us.agencia_id',
                    'ag.nombre as nombre_agencia'
                )
                ->join('usuarios as us', 'af.usuario_id', '=', 'us.dni')
                ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                ->where([['af.fecha', '<>', null], ['justificado', '=', 0]])
                ->whereBetween('af.fecha', [$f_desde, $f_hasta])
                ->orderBy('af.fecha', 'desc')

                ->get();
        } else {
            return Falta::from('asistencia_faltas as af')
                ->select(
                    'af.id',
                    'af.fecha',
                    'af.usuario_id',
                    'af.turno',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'us.agencia_id',
                    'ag.nombre as nombre_agencia'
                )
                ->join('usuarios as us', 'af.usuario_id', '=', 'us.dni')
                ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                ->where([['af.fecha', '<>', null], ['justificado', '=', 0]])
                ->whereBetween('af.fecha', [$f_desde, $f_hasta])
                ->where('us.dni', $dni)
                ->orderBy('af.fecha', 'desc')
                ->get();
        }
    }
    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------

    public function justificar_faltas(Request $request)
    {
        // dd($request);
        $datos_registro = (new GthController)->datos_registro();

        $id_falta = $request->id_falta;
        $justificacion = $request->justificacion;
        $justificacionm = strtoupper($justificacion);

        // $justificacion = "(fecha: " . $datos_falta->fecha . " / " . "turno: " . $datos_falta->turno . ") " . $justificacion;



        $id_justificacion = Justificacion::create([

            'falta_id' => $id_falta,

            'justificacion' => $justificacionm,

            'datos_creacion' => $datos_registro


        ]);
        $id_justificacion = $id_justificacion->id;

        if ($request->hasFile('documento')) {

            $path_name = pathinfo($_FILES['documento']['name']);
            $extension = "." . $path_name['extension'];
            $nombreDocumento = "asis_just_" . $id_justificacion . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/gth/justificaciones';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombreDocumento;
            move_uploaded_file($archivo, $ruta);
            Justificacion::where('id', $id_justificacion)
                ->update(['documento' => $nombreDocumento]);
        }

        Falta::where('id', $id_falta)
            ->update(['justificado' => 1]);

        return 'EXITO';
    }
    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    public function generar_faltas()
    {

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');
        $fecha_dia = date('l');
        $hora_dia = date('H:i');

        $hora = (int) date('H');
        $min = (int) date('i');

        $min_adic_falta = 60;


        $hora_form = $hora . ":" . $min . ":00";
        $fecha_actual_hora_min =  strtotime($hora_form);

        if ($hora_dia >= '08:27' && $hora_dia < '12:00') {
            if ($fecha_dia != "Saturday" && $fecha_dia != "Sunday") {
                $usuarios_mañana = Usuario::from('usuarios as us')
                    ->select('us.dni as dni')
                    ->join('asistencia_usuarios_horarios as uh', 'us.dni', '=', 'uh.usuario_id')
                    ->join('asistencia_horarios as ah', 'uh.horario_id', '=', 'ah.id')
                    ->where([['us.habilitado', 1], ['ah.hora_entrada_mañana', '<>', 'null'], ['uh.marca_asistencia', 1]])
                    ->get();


                $marcaron_asistencia = Marcaje::select('usuario_id as dni')
                    ->where([[DB::raw("STR_TO_DATE(hora_ingreso, '%Y-%m-%d')"), $fechaActual], ['turno', 'ingreso_mañana']])
                    ->get();

                $array1 = array();
                $array2 = array();

                foreach ($usuarios_mañana as  $value) {
                    $array1[] = $value;
                }

                foreach ($marcaron_asistencia as  $value) {
                    $array2[] = $value;
                }

                $no_marcaron = array_diff($array1, $array2);

                foreach ($no_marcaron as $value) {

                    $datos_usuario = Usuario::from('usuarios as us')
                        ->select(
                            'us.dni',
                            'ah.hora_entrada_mañana',
                            'ah.tolerancia',
                            'uh.tolerancia_personal'
                        )
                        ->join('asistencia_usuarios_horarios as uh', 'us.dni', '=', 'uh.usuario_id')
                        ->join('asistencia_horarios as ah', 'uh.horario_id', '=', 'ah.id')
                        ->where([['us.habilitado', 1], ['ah.hora_entrada_mañana', '<>', 'null'], ['usuario_id', $value->dni]])
                        ->get();


                    $tolerancia = $datos_usuario[0]->tolerancia;
                    $tolerancia_personal = $datos_usuario[0]->tolerancia_personal;

                    $faltas_mañana = Falta::select('usuario_id')
                        ->where([
                            [DB::raw("STR_TO_DATE(fecha, '%Y-%m-%d')"), $fechaActual],
                            ['turno', 'ingreso_mañana'],
                            ['usuario_id', $value->dni]
                        ])
                        ->get()->last();

                    if ($faltas_mañana == null) {

                        $entrada_hora_min_mañ = strtotime($datos_usuario[0]['hora_entrada_mañana']  . "+ $tolerancia min" . "+ $tolerancia_personal min" . "+ $min_adic_falta min");
                        if ($fecha_actual_hora_min > $entrada_hora_min_mañ) {
                            Falta::insert(['fecha' => $fechaActual, 'usuario_id' => $value->dni, 'turno' => 'ingreso_mañana']);
                        }
                    }
                }
            } else if ($fecha_dia == "Saturday") {

                $usuarios_mañana = Usuario::from('usuarios as us')
                    ->select('us.dni as dni')
                    ->join('asistencia_usuarios_horarios as uh', 'us.dni', '=', 'uh.usuario_id')
                    ->join('asistencia_horarios as ah', 'uh.horario_id', '=', 'ah.id')
                    ->where([['us.habilitado', 1], ['ah.hora_entrada_mañana_s', '<>', 'null'], ['uh.marca_asistencia', 1]])
                    ->get();


                $marcaron_asistencia = Marcaje::select('usuario_id as dni')
                    ->where([[DB::raw("STR_TO_DATE(hora_ingreso, '%Y-%m-%d')"), $fechaActual], ['turno', 'ingreso_mañana']])
                    ->get();

                $array1 = array();
                $array2 = array();
                foreach ($usuarios_mañana as  $value) {
                    $array1[] = $value;
                }

                foreach ($marcaron_asistencia as  $value) {
                    $array2[] = $value;
                }
                $no_marcaron = array_diff($array1, $array2);

                foreach ($no_marcaron as $value) {

                    $datos_usuario = Usuario::from('usuarios as us')
                        ->select(
                            'us.dni',
                            'ah.hora_entrada_mañana_s',
                            'ah.tolerancia',
                            'uh.tolerancia_personal'
                        )
                        ->join('asistencia_usuarios_horarios as uh', 'us.dni', '=', 'uh.usuario_id')
                        ->join('asistencia_horarios as ah', 'uh.horario_id', '=', 'ah.id')
                        ->where([['us.habilitado', 1], ['usuario_id', $value->dni]])
                        ->get();

                    $tolerancia = $datos_usuario[0]->tolerancia;
                    $tolerancia_personal = $datos_usuario[0]->tolerancia_personal;

                    $faltas_mañana = Falta::select('usuario_id')
                        ->where([
                            [DB::raw("STR_TO_DATE(fecha, '%Y-%m-%d')"), $fechaActual],
                            ['turno', 'ingreso_mañana'],
                            ['usuario_id', $value->dni]
                        ])
                        ->get()->last();

                    if ($faltas_mañana == null) {
                        $entrada_hora_min_mañ_s = strtotime($datos_usuario[0]['hora_entrada_mañana_s']  . "+ $tolerancia min" . "+ $tolerancia_personal min" . "+ $min_adic_falta min");

                        if ($fecha_actual_hora_min > $entrada_hora_min_mañ_s) {
                            Falta::insert(['fecha' => $fechaActual, 'usuario_id' => $value->dni, 'turno' => 'ingreso_mañana']);
                        }
                    }
                }
            }
        } else if ($hora_dia > '15:12' && $hora_dia < '18:30') {

            if ($fecha_dia != "Saturday" && $fecha_dia != "Sunday") {

                $usuarios_tarde = Usuario::from('usuarios as us')
                    ->select('us.dni as dni')
                    ->join('asistencia_usuarios_horarios as uh', 'us.dni', '=', 'uh.usuario_id')
                    ->join('asistencia_horarios as ah', 'uh.horario_id', '=', 'ah.id')
                    ->where([['us.habilitado', 1], ['ah.hora_entrada_tarde', '<>', 'null'], ['uh.marca_asistencia', 1]])
                    ->get();



                $marcaron_asistencia = Marcaje::select('usuario_id as dni')
                    ->where([[DB::raw("STR_TO_DATE(hora_ingreso, '%Y-%m-%d')"), $fechaActual], ['turno', 'ingreso_tarde']])
                    ->get();

                $array1 = array();
                $array2 = array();
                foreach ($usuarios_tarde as  $value) {
                    $array1[] = $value;
                }

                foreach ($marcaron_asistencia as  $value) {
                    $array2[] = $value;
                }
                $no_marcaron = array_diff($array1, $array2);

                foreach ($no_marcaron as $value) {

                    $datos_usuario = Usuario::from('usuarios as us')
                        ->select(
                            'us.dni',
                            'ah.hora_entrada_tarde',
                            'ah.tolerancia',
                            'uh.tolerancia_personal'
                        )
                        ->join('asistencia_usuarios_horarios as uh', 'us.dni', '=', 'uh.usuario_id')
                        ->join('asistencia_horarios as ah', 'uh.horario_id', '=', 'ah.id')
                        ->where([['us.habilitado', 1], ['usuario_id', $value->dni]])
                        ->get();



                    $tolerancia = $datos_usuario[0]->tolerancia;
                    $tolerancia_personal = $datos_usuario[0]->tolerancia_personal;


                    $faltas_tarde = Falta::select('usuario_id')
                        ->where([
                            [DB::raw("STR_TO_DATE(fecha, '%Y-%m-%d')"), $fechaActual],
                            ['turno', 'ingreso_tarde'],
                            ['usuario_id', $value->dni]
                        ])
                        ->get()->last();

                    if ($faltas_tarde == null) {
                        $entrada_hora_min_tar = strtotime($datos_usuario[0]['hora_entrada_tarde']  . "+ $tolerancia min" . "+ $tolerancia_personal min" . "+ $min_adic_falta min");

                        if ($fecha_actual_hora_min > $entrada_hora_min_tar) {
                            Falta::insert(['fecha' => $fechaActual, 'usuario_id' => $value->dni, 'turno' => 'ingreso_tarde']);
                        }
                    }
                }
            }
        }
    }
    public function mis_faltas()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_FALTAS', 'GTH_ASISTENCIAS');
            if ($band == 1) {
                return Inertia::render('Gth/Asistencias/mis_faltas');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar_mis_faltas(Request $request)
    {
        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;
        $x = session()->all();
        $dni = $x['usuario_dni'];

        if (!$f_desde == null && !$f_hasta == null) {
            return Falta::from('asistencia_faltas as fa')
                ->select(
                    'fa.id',
                    'fa.fecha',
                    'fa.usuario_id',
                    'fa.turno',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'us.agencia_id',
                    'ag.nombre as nombre_agencia'
                )
                ->join('usuarios as us', 'fa.usuario_id', '=', 'us.dni')
                ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                ->whereBetween('fa.fecha', [$f_desde, $f_hasta])

                ->where([['fecha', '<>', null], ['justificado', '=', 0], ['fa.usuario_id', $dni]])->get();
        }
    }
    public function exportar_faltas(request $request)
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
                'hora_ingreso' => $item->fecha,
                'turno' => $item->turno,
                'nombre_agencia' => $item->nombre_agencia,


            ];


            $orden += 1;
            $data[] = $object;
        }
        // dd($data);

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load("./report_templates/gth/rptFaltas.xlsx");
        $sheet = $spreadsheet->getActiveSheet();


        //    Obteniendo formatos-----------------------------
        $celda = 1;

        $lista_formato_celdas_1 = [];
        $lista_formato_celdas_2 = [];
        $lista_formato_totales = [];

        while ($celda <= 6) {
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



        $sheet->setCellValue('F' . $indice, 'TOTAL');
        $sheet->setCellValue('G' . $indice, 'TOTAL ' . $total . ' registro(s)');


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
