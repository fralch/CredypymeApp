<?php

namespace App\Http\Controllers\Gth\Asistencias;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\Asistencias\Tardanza;
use App\Models\General\Agencia;
use App\Models\Gth\Usuarios\Usuario;

use App\Models\Gth\Asistencias\Justificacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Gth\GthController;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Inertia\Inertia;

class TardanzaController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function tardanzas($modo)
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TARDANZAS', 'GTH_ASISTENCIAS');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_TARDANZAS', 'GTH_ASISTENCIAS');
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
                    'Gth/Asistencias/tardanzas',
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
    public function listar_tardanzas(Request $request)
    {
        $dni = $request->dni;
        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;

        if (!$dni) {
            return Tardanza::from('asistencia_tardanzas as at')
            ->select(
                'us.dni',
                'us.nombres',
                'us.apellido_paterno',
                'us.apellido_materno',
                'at.id',
                'am.hora_ingreso as fecha',
                'at.minutos',
                'am.turno',
                'us.agencia_id',
                'ag.nombre as nombre_agencia',
                'at.justificado',
            )

            ->join('asistencia_marcajes as am', 'am.id', 'at.marcaje_id')
            ->join('usuarios as us', 'am.usuario_id', '=', 'us.dni')
            ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
            ->where([['hora_ingreso', '<>', null], ['justificado', '=', 0]])
            ->whereBetween(DB::raw("STR_TO_DATE(am.hora_ingreso, '%Y-%m-%d')"), [$f_desde, $f_hasta])

            ->orderBy('hora_ingreso', 'desc')
            ->get();
        }else{
            return Tardanza::from('asistencia_tardanzas as at')
            ->select(
                'us.dni',
                'us.nombres',
                'us.apellido_paterno',
                'us.apellido_materno',
                'at.id',
                'am.hora_ingreso as fecha',
                'at.minutos',
                'am.turno',
                'us.agencia_id',
                'ag.nombre as nombre_agencia',
                'at.justificado',
                'am.foto'
            )

            ->join('asistencia_marcajes as am', 'am.id', 'at.marcaje_id')
            ->join('usuarios as us', 'am.usuario_id', '=', 'us.dni')
            ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
            ->where([['hora_ingreso', '<>', null], ['justificado', '=', 0]])
            ->whereBetween(DB::raw("STR_TO_DATE(am.hora_ingreso, '%Y-%m-%d')"), [$f_desde, $f_hasta])
            ->where('us.dni', $dni)
            ->orderBy('hora_ingreso', 'desc')
            ->get();
        }
        


        
    }

    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------

    public function justificar_tardanza(Request $request)
    {

        $datos_registro = (new GthController)->datos_registro();
        $id_tardanza = $request->id_tardanza;
        $justificacion = $request->justificacion;
        $justificacionm = strtoupper($justificacion);


        $datos_tardanza = Tardanza::select('asistencia_tardanzas.id', 'minutos', 'am.usuario_id', 'am.hora_ingreso')
            ->join('asistencia_marcajes as am', 'am.id', 'asistencia_tardanzas.marcaje_id')
            ->where('asistencia_tardanzas.id', $id_tardanza)->get()->last();



        $id_justificacion = Justificacion::create([

            'tardanza_id' => $id_tardanza,

            // 'dni' => $datos_tardanza->dni,
            // 'fecha' => $fecha_registro,
            // 'tipo' => 'Tardanza',

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

        Tardanza::where('id', $id_tardanza)
            ->update(['justificado' => 1]);

        return 'EXITO';
    }
    public function mis_tardanzas()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_TARDANZAS', 'GTH_ASISTENCIAS');
            if ($band == 1) {
                return Inertia::render('Gth/Asistencias/mis_tardanzas');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar_mis_tardanzas(Request $request)
    {
        $f_desde = $request->f_desde;
        $f_hasta = $request->f_hasta;
        $x = session()->all();
        $dni = $x['usuario_dni'];

        if (!$f_desde == null && !$f_hasta == null) {

            return Tardanza::from('asistencia_tardanzas as ta')
                ->select(
                    'am.usuario_id',
                    'us.nombres',
                    'us.apellido_paterno',
                    'us.apellido_materno',
                    'ta.id',
                    'am.hora_ingreso',
                    'ta.minutos',
                    'am.turno',
                    'us.agencia_id',
                    'ag.nombre as nombre_agencia'
                )
                ->join('asistencia_marcajes as am', 'ta.marcaje_id', '=', 'am.id')
                ->join('usuarios as us', 'am.usuario_id', '=', 'us.dni')
                ->join('agencias as ag', 'us.agencia_id', '=', 'ag.id_agencia')
                ->whereBetween(DB::raw("STR_TO_DATE(am.hora_ingreso, '%Y-%m-%d')"), [$f_desde, $f_hasta])
                ->where('am.usuario_id', $dni)
                ->get();
        }
    }
    public function exportar_tardanzas(request $request)
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
                'dni' => $item->dni,
                'colaborador' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'hora_ingreso' => $item->fecha,
                'minutos' => $item->minutos,
                'turno' => $item->turno,
                'nombre_agencia' => $item->nombre_agencia,


            ];


            $orden += 1;
            $data[] = $object;
        }
        // dd($data);

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load("./report_templates/gth/rptTardanzas.xlsx");
        $sheet = $spreadsheet->getActiveSheet();


        //    Obteniendo formatos-----------------------------
        $celda = 1;

        $lista_formato_celdas_1 = [];
        $lista_formato_celdas_2 = [];
        $lista_formato_totales = [];

        while ($celda <= 7) {
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



        $sheet->setCellValue('E' . $indice, 'TOTAL');
        $sheet->setCellValue('F' . $indice, $total_minutos . ' minuto(s)');
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
    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------

}
