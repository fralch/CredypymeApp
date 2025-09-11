<?php

namespace App\Http\Controllers\Gth\Asistencias;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gth\GthController;

use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Gth\Asistencias\Marcaje;
use App\Models\Gth\Asistencias\Tardanza;
use App\Models\Gth\Mantenimiento\Asistencia\Horario;
use App\Models\General\Agencia;
use App\Models\Gth\Usuarios\UsuarioHorario;
use App\Models\Gth\Asistencias\TokenAsistencia;
use App\Models\Aplicacion\VersionesAplicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;



class AsistenciaController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA-----

    public function validar_asistencia()
    {
        return Inertia::render('Asistencia/validacion_token');
    }

    public function marcado_asistencia()
    {
        date_default_timezone_set("America/Lima");
        $fecha_servidor = date('Y-m-d h:i:s');
        return Inertia::render('Asistencia/marcado_asistencia',  ['fecha_servidor' => $fecha_servidor]);
    }

    public function asistencias($modo)
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ASISTENCIAS', 'GTH_ASISTENCIAS');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_ASISTENCIAS', 'GTH_ASISTENCIAS');
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
                    'Gth/Asistencias/asistencias',
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

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------

    // public function validar_token(Request $request)
    // {

    //     $token_u = $request->token;
    //     $token_s = TokenAsistencia::all()->last()['token'];

    //     if ($token_s == $token_u) {
    //         session(['asistencia_token' => TRUE]);
    //         return 'CORRECTO';
    //     } else {
    //         session(['asistencia_token' => FALSE]);
    //         return 'INCORRECTO';
    //     }
    // }

    public function verificar_usuario_asistencia(Request $request)
    {
        $resultado = '';

        $dni = $request->dni;

        $usuario_existe = Usuario::select('dni')->where('dni',  $dni)->get();

        if (count($usuario_existe) == 0) {
            $resultado = 'NO EXISTE';
        } else {

            date_default_timezone_set("America/Lima");
            $fechaNow = date('Y-m-d');
            $hora = (int) date('H');
            $min = (int) date('i');
            $fecha_dia = date('l');

            $hora_form = $hora . ":" . $min . ":00";
            $fecha_actual_hora_min =  strtotime($hora_form);

            $min_adic_falta = 60;

            $datos_horario = Horario::from('asistencia_horarios as h')
                ->select(
                    'h.hora_entrada_mañana',
                    'h.hora_entrada_tarde',
                    'h.hora_salida_mañana',
                    'h.hora_salida_tarde',
                    'h.hora_entrada_mañana_s',
                    'h.hora_salida_mañana_s',

                    'h.tolerancia',
                    'uh.tolerancia_personal'
                )
                ->join('asistencia_usuarios_horarios as uh', 'h.id', '=', 'uh.horario_id')
                ->where('uh.usuario_id', $dni)->get();

            if ($fecha_dia != "Saturday") {

                $salida_hora_min_mañ = strtotime($datos_horario[0]['hora_salida_mañana']);
                $salida_hora_min_tar = strtotime($datos_horario[0]['hora_salida_tarde']);

                $tolerancia = $datos_horario[0]['tolerancia'];
                $tolerancia_personal = $datos_horario[0]['tolerancia_personal'];

                $entrada_hora_min_mañ = strtotime($datos_horario[0]['hora_entrada_mañana'] . "+ $tolerancia min" . "+ $tolerancia_personal min" . "+ $min_adic_falta min");
                $entrada_hora_min_tar = strtotime($datos_horario[0]['hora_entrada_tarde'] . "+ $tolerancia min" . "+ $tolerancia_personal min" . "+ $min_adic_falta min");

                // El marcaje se habilita 10 minutos antes de la hora de ingreso
                $minimo_ingreso_maniana = strtotime($datos_horario[0]['hora_entrada_mañana'] . "- 10 min");
                $minimo_ingreso_tarde = strtotime($datos_horario[0]['hora_entrada_tarde'] . "- 10 min");

                //ingreso mañanas
                if ($fecha_actual_hora_min < $salida_hora_min_mañ) {
                    //---------------------
                    $verificarmarcado = Marcaje::where('usuario_id', $dni)
                        ->where('hora_ingreso', 'like', '%' . $fechaNow . '%')
                        ->where('turno', 'ingreso_mañana')
                        ->get();
                    $verificarmarcajehabilitado = UsuarioHorario::where('usuario_id', $dni)
                        ->where('marca_asistencia', 1)
                        ->get();

                    if (count($verificarmarcado) != 0) {
                        $resultado = 'SI MARCADO';
                    } else {
                        $resultado = 'NO MARCADO';
                    }

                    if ($fecha_actual_hora_min > $entrada_hora_min_mañ) {

                        $resultado = 'FUERA DE TIEMPO';
                    }
                    if (count($verificarmarcajehabilitado) == 0) {
                        $resultado = 'NO HABILITADO';
                    }

                    if ($fecha_actual_hora_min < $minimo_ingreso_maniana) {

                        $resultado = 'FUERA DE HORARIO';
                    }
                    //ingreso tardes


                } else if ($fecha_actual_hora_min > $salida_hora_min_mañ and $fecha_actual_hora_min < $salida_hora_min_tar) {
                    $verificarmarcado = Marcaje::where('usuario_id', $dni)
                        ->where('hora_ingreso', 'like', '%' . $fechaNow . '%')
                        ->where('turno', 'ingreso_tarde')
                        ->get();
                    $verificarmarcajehabilitado = UsuarioHorario::where('usuario_id', $dni)
                        ->where('marca_asistencia', 1)
                        ->get();


                    if (count($verificarmarcado) != 0) {
                        $resultado = 'SI MARCADO';
                    } else {
                        $resultado = 'NO MARCADO';
                    }

                    if ($fecha_actual_hora_min > $entrada_hora_min_tar) {
                        $resultado = 'FUERA DE TIEMPO';
                    }
                    if (count($verificarmarcajehabilitado) == 0) {
                        $resultado = 'NO HABILITADO';
                    }

                    if ($fecha_actual_hora_min < $minimo_ingreso_tarde) {

                        $resultado = 'FUERA DE HORARIO';
                    }
                    //-----------------------------------
                } else if ($fecha_actual_hora_min > $salida_hora_min_tar) {
                    $resultado = 'FUERA DE HORARIO';
                }
            } else {

                $tolerancia = $datos_horario[0]['tolerancia'];
                $tolerancia_personal = $datos_horario[0]['tolerancia_personal'];

                $salida_hora_min_mañ_s = strtotime($datos_horario[0]['hora_salida_mañana_s']);
                $entrada_hora_min_mañ_s = strtotime($datos_horario[0]['hora_entrada_mañana_s'] . "+ $tolerancia min" . "+ $tolerancia_personal min" . "+ $min_adic_falta min");

                // El marcaje se habilita 10 minutos antes de la hora de ingreso
                $minimo_ingreso_maniana_s = strtotime($datos_horario[0]['hora_entrada_mañana_s'] . "- 10 min");

                if ($fecha_actual_hora_min < $salida_hora_min_mañ_s) {
                    //---------------------
                    $verificarmarcado = Marcaje::where('usuario_id', $dni)
                        ->where('hora_ingreso', 'like', '%' . $fechaNow . '%')
                        ->where('turno', 'ingreso_mañana')
                        ->get();
                    $verificarmarcajehabilitado = UsuarioHorario::where('usuario_id', $dni)
                        ->where('marca_asistencia', 1)
                        ->get();

                    if (count($verificarmarcado) != 0) {
                        $resultado = 'SI MARCADO';
                    } else
                        $resultado = 'NO MARCADO';

                    if ($fecha_actual_hora_min > $entrada_hora_min_mañ_s) {
                        $resultado = 'FUERA DE TIEMPO';
                    }
                    if (count($verificarmarcajehabilitado) == 0) {
                        $resultado = 'NO HABILITADO';
                    }

                    if ($fecha_actual_hora_min < $minimo_ingreso_maniana_s) {

                        $resultado = 'FUERA DE HORARIO';
                    }
                } else if ($fecha_actual_hora_min > $salida_hora_min_mañ_s) {
                    $resultado = 'FUERA DE HORARIO';
                }
            }
        }

        return $resultado;
    }


    public function registrar_asistencia(Request $request)
    {

        //------------------------------

        // $resultado = 'ERROR';
        //fecha real
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        // $fechaActual = session('fecha_ingreso');
        $fechaCortaActual = date('Y-m-d');
        $hora = (int) date('H');
        $min = (int) date('i');
        // $fecha_dc = date_create("now");
        $fecha_dia = date('l');
        //---------------------------------------------

        $dni = (int)$request->dni;
        $fotosave = $request->foto;
        $coordenadas = $request->coordenadas;

        $imagenCodificadaLimpia = str_replace("data:image/jpeg;base64,", "", $fotosave);
        $imagenDecodificada = base64_decode($imagenCodificadaLimpia);
        //---------------------

        //--------------
        $hora_form = $hora . ":" . $min . ":00";
        $fecha_actual_hora_min =  strtotime($hora_form);

        $datos_horario = Horario::from('asistencia_horarios as h')
            ->select(
                'h.hora_entrada_mañana',
                'h.hora_entrada_tarde',
                'h.hora_salida_mañana',
                'h.hora_salida_tarde',
                'h.hora_entrada_mañana_s',
                'h.hora_salida_mañana_s',
                'h.tolerancia',
                'uh.tolerancia_personal'
            )
            ->join('asistencia_usuarios_horarios as uh', 'h.id', '=', 'uh.horario_id')
            ->join('usuarios as us', 'uh.usuario_id', '=', 'us.dni')
            ->where('uh.usuario_id', $dni)->get();


        if ($fecha_dia != "Saturday") {

            $tolerancia = $datos_horario[0]['tolerancia'];
            $tolerancia_personal = $datos_horario[0]['tolerancia_personal'];

            $entrada_hora_min_mañ = strtotime($datos_horario[0]['hora_entrada_mañana'] . "+ $tolerancia min" . "+ $tolerancia_personal min");
            $salida_hora_min_mañ = strtotime($datos_horario[0]['hora_salida_mañana']);
            $hora_salida_mañana = $datos_horario[0]['hora_salida_mañana'];
            $entrada_hora_min_tar = strtotime($datos_horario[0]['hora_entrada_tarde'] . "+ $tolerancia min" . "+ $tolerancia_personal min");
            $salida_hora_min_tar = strtotime($datos_horario[0]['hora_salida_tarde']);
            $hora_salida_tarde = $datos_horario[0]['hora_salida_tarde'];

            //añadir min y seg aleatorios
            $fecha_hora_salida = $fechaCortaActual . " " . $hora_salida_mañana;
            $min_aleatorios = rand(0, 5);
            $seg_aleatorios = rand(0, 60);
            $suma_resta = rand(0, 1);
            if ($suma_resta == 0) {
                $signo = "+";
            } else {
                $signo = "-";
            }
            $nueva_fecha_añadir_min = strtotime($signo . $min_aleatorios . "minute", strtotime($fecha_hora_salida));
            $nueva_fecha_añadir_seg = strtotime('+' . $seg_aleatorios . "second", $nueva_fecha_añadir_min);
            $nueva_fecha_hora_salida = date('Y-m-d H:i:s', $nueva_fecha_añadir_seg);

            // Para turno mañana
            if ($fecha_actual_hora_min < $salida_hora_min_mañ) {
                // dd($nueva_fecha_hora_salida);
                $id_entrada_salida = Marcaje::insertGetId([
                    'hora_ingreso' => $fechaActual,
                    'turno' => 'ingreso_mañana',
                    'usuario_id' => $dni,
                    'hora_salida' => $nueva_fecha_hora_salida,
                    'coordenadas' => $coordenadas
                ]);



                $nombreImagenGuardada =  "asist_" . $id_entrada_salida . "_" . $dni . ".png";

                $ruta = '/imagenes_server/gth/asistencias';
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombreImagenGuardada;
                file_put_contents($ruta, $imagenDecodificada);

                Marcaje::where('id', $id_entrada_salida)
                    ->update(['foto' => $nombreImagenGuardada]);

                // Registro de tardanzas
                if ($fecha_actual_hora_min > $entrada_hora_min_mañ) {
                    $minutos_tarde = ($fecha_actual_hora_min - $entrada_hora_min_mañ) / 60;
                    Tardanza::insert([

                        'marcaje_id' => $id_entrada_salida,
                        'minutos' => $minutos_tarde,

                    ]);
                }
                return redirect()->route('gth.asi.marcado_asistencia');
                // $resultado = 'EXITOSO';


                //ingreso tarde
            } else if ($fecha_actual_hora_min > $salida_hora_min_mañ and $fecha_actual_hora_min < $salida_hora_min_tar) {

                $fecha_hora_salida = $fechaCortaActual . " " . $hora_salida_tarde;
                $min_aleatorios = rand(0, 5);
                $seg_aleatorios = rand(0, 60);
                $suma_resta = rand(0, 1);
                if ($suma_resta == 0) {
                    $signo = "+";
                } else {
                    $signo = "-";
                }
                $nueva_fecha_añadir_min = strtotime($signo . $min_aleatorios . "minute", strtotime($fecha_hora_salida));
                $nueva_fecha_añadir_seg = strtotime('+' . $seg_aleatorios . "second", $nueva_fecha_añadir_min);
                $nueva_fecha_hora_salida = date('Y-m-d H:i:s', $nueva_fecha_añadir_seg);


                $id_entrada_salida = Marcaje::insertGetId(
                    [
                        'hora_ingreso' => $fechaActual,
                        'turno' => 'ingreso_tarde',
                        'usuario_id' => $dni,
                        'hora_salida' => $nueva_fecha_hora_salida,
                        'coordenadas' => $coordenadas
                    ]
                );

                $nombreImagenGuardada =  "asist_" . $id_entrada_salida . "_" . $dni . ".png";

                $ruta = '/imagenes_server/gth/asistencias';
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombreImagenGuardada;
                file_put_contents($ruta, $imagenDecodificada);

                Marcaje::where('id', $id_entrada_salida)->update(['foto' => $nombreImagenGuardada]);

                // Registro de tardanzas
                if ($fecha_actual_hora_min > $entrada_hora_min_tar) {
                    $minutos_tarde = ($fecha_actual_hora_min - $entrada_hora_min_tar) / 60;
                    Tardanza::insert([

                        'marcaje_id' => $id_entrada_salida,

                        'minutos' => $minutos_tarde,



                    ]);
                }

                return redirect()->route('gth.asi.marcado_asistencia');
            }
        } else {

            $tolerancia = $datos_horario[0]['tolerancia'];
            $tolerancia_personal = $datos_horario[0]['tolerancia_personal'];
            $entrada_hora_min_mañ = strtotime($datos_horario[0]['hora_entrada_mañana_s'] . "+ $tolerancia min");
            $salida_hora_min_mañ = strtotime($datos_horario[0]['hora_salida_mañana_s']);
            $hora_salida = $datos_horario[0]['hora_salida_mañana'];

            //añadir min y seg aleatorios
            $fecha_hora_salida = $fechaCortaActual . " " . $hora_salida;
            $min_aleatorios = rand(0, 5);
            $seg_aleatorios = rand(0, 60);
            $suma_resta = rand(0, 1);
            if ($suma_resta == 0) {
                $signo = "+";
            } else {
                $signo = "-";
            }
            $nueva_fecha_añadir_min = strtotime($signo . $min_aleatorios . "minute", strtotime($fecha_hora_salida));
            $nueva_fecha_añadir_seg = strtotime('+' . $seg_aleatorios . "second", $nueva_fecha_añadir_min);
            $nueva_fecha_hora_salida = date('Y-m-d H:i:s', $nueva_fecha_añadir_seg);


            // Ingreso mañana
            $id_entrada_salida = Marcaje::insertGetId([
                'hora_ingreso' => $fechaActual,
                'turno' => 'ingreso_mañana',
                'usuario_id' => $dni,
                'hora_salida' => $nueva_fecha_hora_salida,
                'coordenadas' => $coordenadas
            ]);
            $nombreImagenGuardada =  "asis_marc_" . $id_entrada_salida . "_" . $dni . ".png";

            $ruta = '/imagenes_server/gth/asistencias';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombreImagenGuardada;
            file_put_contents($ruta, $imagenDecodificada);

            Marcaje::where('id', $id_entrada_salida)->update(['foto' => $nombreImagenGuardada]);

            // Registro de tardanzas
            if ($fecha_actual_hora_min > $entrada_hora_min_mañ) {
                $minutos_tarde = ($fecha_actual_hora_min - $entrada_hora_min_mañ) / 60;
                Tardanza::insert([
                    'marcaje_id' => $id_entrada_salida,
                    'minutos' => $minutos_tarde,

                ]);
            }
            return redirect()->route('gth.asi.marcado_asistencia');
        }

        return redirect()->route('gth.asi.marcado_asistencia');
    }

    // public function agregartolerancia(Request $request)
    // {
    //     // return $request;
    //     $dni = $request->dni;
    //     $tolerancia = $request->minutos;

    //     UsuarioHorario::where('usuario_id', $dni)
    //         ->update(['tolerancia_personal' =>  $tolerancia]);

    //     return redirect('/controles_tolerancias');
    // }

    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    public function listar_asistencias(Request $request)
    {

        // dd($request);

        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;


        return Marcaje::from('asistencia_marcajes as am')
            ->select(
                'am.id',
                'am.usuario_id',
                'us.nombres',
                'us.apellido_paterno',
                'us.apellido_materno',
                'us.agencia_id',
                'ag.nombre as nombre_agencia',
                'am.hora_ingreso',
                'am.turno',
                'am.foto',
            )
            ->join('usuarios as us', 'am.usuario_id', '=', 'us.dni')
            ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
            ->whereBetween(DB::raw("STR_TO_DATE(am.hora_ingreso, '%Y-%m-%d')"), [$f_desde, $f_hasta])
            ->orderBy('hora_ingreso', 'desc')
            ->get();
    }



    // --------------------------------------------------------------------------
    public function fecha_servidor(Request $request)
    {
        return Marcaje::from('asistencia_marcajes as es')->select(DB::raw('now()'))->get()->last()['now()'];
    }

    public function mis_asistencias()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_ASISTENCIAS', 'GTH_ASISTENCIAS');
            if ($band == 1) {
                return Inertia::render('Gth/Asistencias/mis_asistencias');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    // API consulta para la app movil
    public function listar_mis_asistencias(Request $request)
    {
        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;
        $dni = $request->dni;



        return Marcaje::from('asistencia_marcajes as am')
            ->select(
                'am.id',
                'am.usuario_id',
                'us.nombres',
                'us.apellido_paterno',
                'us.apellido_materno',
                'us.agencia_id',
                'ag.nombre as nombre_agencia',
                'am.hora_ingreso',
                'am.turno',
                'am.foto',
            )
            ->join('usuarios as us', 'am.usuario_id', '=', 'us.dni')
            ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
            ->whereBetween(DB::raw("STR_TO_DATE(am.hora_ingreso, '%Y-%m-%d')"), [$f_desde, $f_hasta])
            ->where('am.usuario_id', $dni)
            ->orderBy('hora_ingreso', 'desc')
            ->get();
    }

    public function exportar_asistencias(request $request)
    {

        // dd($request);
        $datos = json_decode($request->datos);

        // dd($datos);

        $data = [];

        $orden = 1;
        foreach ($datos as $item) {
            $object = (object)[
                'numero' => $orden,
                'usuario_id' => $item->usuario_id,
                'colaborador' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'hora_ingreso' => $item->hora_ingreso,

                'nombre_agencia' => $item->nombre_agencia,
                'turno' => $item->turno,


            ];


            $orden += 1;
            $data[] = $object;
        }
        // dd($data);

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load("./report_templates/gth/rptAsistencias.xlsx");
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
        $sheet->setCellValue('G' . $indice, $total . ' registro(s)');

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

    public function versiones_aplicacion()
    {
        // controlador para versiones de la app android  otra vez 
        $versiones = VersionesAplicacion::all()->last();
        return $versiones;
    }
}
