<?php

namespace App\Http\Controllers\Gth\Asistencias;


use App\Http\Controllers\Gth\GthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;

use App\Models\Gth\Asistencias\Licencia;
use App\Models\Gth\Asistencias\Permiso;
use App\Models\Gth\Usuarios\usuario;
use App\Models\Gth\Mantenimiento\Solicitudes\LicenciaCategoria;

use App\Models\Gth\Asistencias\SolicitudFecha;
use App\Models\General\Feriado;



use App\Models\General\Agencia;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;


class LicenciaController extends Controller
{


    public function licencias()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'LICENCIAS', 'GTH_ASISTENCIAS');
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
                $categorias = LicenciaCategoria::where('habilitado', 1)->get();

                $año_actual = date("Y");

                $feriados = Feriado::select('fecha')->whereYear('fecha', $año_actual )->pluck('fecha')->toArray();

                // dd($feriados);


                return Inertia::render(
                    'Gth/Asistencias/licencias',
                    [
                        'usuarios' => $usuarios,
                        'agencias' => $agencias,
                        'categorias' => $categorias,
                        'feriados' => $feriados
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

    public function listar_solicitud_licencias($fecha_desde,$fecha_hasta){



      $lista_licencias =  Licencia::from('solicitud_licencias as sol_lic')
      ->select(

        'sol_lic.id',
          'sol_lic.fecha_inicio',
          'sol_lic.fecha_fin',
          'sol_lic.fecha_retorno',
          'sol_lic.dias',
          'sol_lic.detalle',
          'sol_lic.documento',
          'sol_lic.goce',
          'sol_lic.comentario',
          'sol_lic.observacion',

          DB::raw('
          CASE
          WHEN sol_lic.estado = "A" THEN "APROBADO"
          WHEN sol_lic.estado = "P" THEN "PENDIENTE"
          WHEN sol_lic.estado = "D" THEN "DENEGADO"
          WHEN sol_lic.estado = "E" THEN "ELIMINADO"
          WHEN sol_lic.estado = "I" THEN "NO VALIDADO"
          WHEN sol_lic.estado = "V" THEN "VALIDADO"
          WHEN sol_lic.estado = "U" THEN "UTILIZADO"
          WHEN sol_lic.estado = "C" THEN "CANCELADO"

          ELSE 0
      END as estado'
      ),
          'sol_fec.fecha_solicitud',
          'lic_cat.id as categoria_id',
          'lic_cat.nombre as categoria_nombre',
          'lic_cat.abreviacion as categoria_abreviacion',
          'us.usuario',

          DB::raw("CONCAT(us.apellido_paterno, ' ', us.apellido_materno, ' ', us.nombres) AS nombre"),

          'us_1.usuario as usuario_aprobador',
          'car.cargo as aprobador_cargo',

          DB::raw('
          CASE
          WHEN sol_lic.estado = "A" OR sol_lic.estado = "V" THEN 1
          ELSE 0
      END as estado_aprobacion'
      ),

          'us_2.usuario as usuario_validador',
          'ag.nombre as agencia'

       )
       ->join('solicitud_fechas as sol_fec','sol_fec.licencia_id','sol_lic.id')
      ->join('usuarios as us','us.dni','sol_lic.usuario_id')
      ->join('agencias as ag','us.agencia_id','ag.id_agencia')
      ->join('usuarios as us_1','us_1.dni','sol_lic.aprobador_id')
      ->join('cargos as car','car.id','us_1.cargo_id')
      ->leftjoin('usuarios as us_2','us_2.dni','sol_lic.validador_id')
      ->join('licencia_categorias as lic_cat','lic_cat.id','sol_lic.categoria_id')
      ->whereBetween(DB::raw("STR_TO_DATE(sol_fec.fecha_solicitud, '%Y-%m-%d')"), [$fecha_desde, $fecha_hasta])
      ->orderBy('sol_fec.fecha_solicitud', 'desc')
      ->get();




      return $lista_licencias;

    }

    public function validar_licencia(Request $request){

        $response = new \stdClass();

        $fecha_validacion = date("Y-m-d H:i:s");



        $licencia_id = $request->licencia_id;
        $categoria_id = $request->categoria_id;
        $validador_id = $request->validador_id;
        $dias = $request->dias;
        $observacion = strtoupper($request->observacion);
        $goce = $request->goce;


                $licencia = Licencia::where('id', $licencia_id)->update([
                    'categoria_id' => $categoria_id,
                    'goce' => $goce,
                    'dias' => $dias,
                    'observacion' => $observacion,
                    'validador_id' => $validador_id,
                    'estado' => 'V',

                ]);

                SolicitudFecha::where('licencia_id', $licencia_id)
                ->update([
                    'fecha_validacion' => $fecha_validacion
                ]);


                $response->success = true;

                return $response;



    }

    public function invalidar_licencia(Request $request){

        $fecha_validacion = date("Y-m-d H:i:s");


        $response = new \stdClass();


        $licencia_id = $request->licencia_id;
        $validador_id = $request->validador_id;
        $observacion = strtoupper($request->observacion);

                $licencia = Licencia::where('id', $licencia_id)->update([
                    'validador_id' => $validador_id,
                    'estado' => 'I',
                    'observacion' => $observacion
                ]);


                SolicitudFecha::where('licencia_id', $licencia_id)
                ->update([
                    'fecha_validacion' => $fecha_validacion
                ]);


                $response->success = true;

                return $response;

    }

    public function verificar_licencia(Request $request){

        $response = new \stdClass();

        $fecha_verificacion = date("Y-m-d");

        // dd($request);



        $licencia_id = $request->licencia_id;
        $dias = $request->dias;
        $fecha_retorno = $request->fecha_retorno;
        $verificador_id = $request->verificador_id;
        $fecha_inicio = $request->fecha_inicio;

        if($fecha_verificacion >= $fecha_inicio){

                $licencia = Licencia::where('id', $licencia_id)->update([
                    'dias' => $dias,
                    'fecha_retorno' => $fecha_retorno,
                    'estado' => 'U',

                ]);

                SolicitudFecha::where('licencia_id', $licencia_id)
                ->update([
                    'fecha_verificacion' => $fecha_verificacion,
                    'usuario_ver' => $verificador_id
                ]);


                $response->success = true;

            } else {

                $response->success = "fecha";
            }

            // dd($response);+

                return $response;



    }

    public function cancelar_licencia(Request $request){


        $response = new \stdClass();

        $fecha_verificacion = date("Y-m-d H:i:s");



        $licencia_id = $request->licencia_id;
        $verificador_id = $request->verificador_id;



                $licencia = Licencia::where('id', $licencia_id)->update([
                    'estado' => 'C',


                ]);
                SolicitudFecha::where('licencia_id', $licencia_id)
                ->update([
                    'fecha_verificacion' => $fecha_verificacion,
                    'usuario_ver' => $verificador_id
                ]);

                $response->success = true;

                return $response;


    }

    public function exportar(Request $request){

                // Ordenando array de datos-------------------------------


                $fecha_desde = $request->fecha_desde;
                $fecha_hasta = $request->fecha_hasta;
                $licencia_filtradas = json_decode($request->licencia_filtradas);

                $agencia_sesion = session('id_agencia');

                // dd($fecha_desde,$fecha_hasta,$licencia_filtradas);
                $orden = 1;

                foreach ($licencia_filtradas as $item) {

                    $object = (object)[
                        'numero' => $orden,
                        'agencia' => $item->agencia,

                        'fecha_solicitud' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha_solicitud)),
                        'estado' => $item->estado,
                        'usuario' =>  $item->usuario,
                        'categoria_abreviacion' =>  $item->categoria_abreviacion,
                        'fecha_inicio' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha_inicio)),
                        'fecha_fin' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha_fin)),
                        'fecha_retorno' => $item->fecha_retorno != null ? Date::dateTimeToExcel(Carbon::parse($item->fecha_retorno)):"-",
                        'dias' => $item->dias,
                        'usuario_aprobador' => $item->usuario_aprobador,
                        'goce' => $item->goce == 1? "SI":"NO",
                    ];
                    $orden += 1;
                    $data[] = $object;
                }

                // Leer Plantilla-------------------------
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $reader->setLoadSheetsOnly('rptLicencias');
                $spreadsheet = $reader->load("./report_templates/gth/rptLicencias.xlsx");
                $sheet = $spreadsheet->getActiveSheet();

                // Encabezado

                $sheet->setCellValue('B1', (new GthController)->header_footer($agencia_sesion));
                $sheet->setCellValue('M2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

                // Insertando valores

                $indice = 5;
                $controller = new GthController();

                foreach ($data as $item) {
                    $columna = 1;
                    foreach ($item as $valor) {
                        $columnaLetra = $controller->num2char($columna);
                        $cell = $columnaLetra . $indice;

                        $sheet->setCellValue($cell, $valor);

                        $columna++;
                    }
                    $indice++;
                }

                // Exportar para descarga-------------------------
                $nombre_archivo = (new GthController)->concatenar_aleatorio('rptLicencias', 5);
                $writer = new Xlsx($spreadsheet);
                $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');


                    $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

                    return ['path_xlsx' => $path_xlsx];

                    // dd($nombre_archivo);

    }

    public function exportar_general(Request $request){

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

    $lista_permisos =  Permiso::from('solicitud_permisos as sol_per')
      ->select(
        'us.usuario',
        'ag.nombre as agencia',
        DB::raw('
        CASE
        WHEN sol_per.goce = 1 THEN "SI"
        WHEN sol_per.goce = 0 THEN "NO"
        END as goce'
    ),
        'sol_per.tiempo',

       )
       ->join('solicitud_fechas as sol_fec','sol_fec.permiso_id','sol_per.id')
       ->join('usuarios as us','us.dni','sol_per.usuario_id')
       ->join('agencias as ag','us.agencia_id','ag.id_agencia')
      ->whereBetween(DB::raw("STR_TO_DATE(sol_fec.fecha_solicitud, '%Y-%m-%d')"), [$fecha_desde, $fecha_hasta])
      ->where('sol_per.estado','U')
      ->get();

$datosProcesados = $lista_permisos->map(function ($item) {

    preg_match('/([0-9.]+)\s*dias?/i', $item->tiempo, $matchDias);
    preg_match('/([0-9]+)\s*horas?/i', $item->tiempo, $matchHoras);
    preg_match('/([0-9]+)\s*minutos?/i', $item->tiempo, $matchMinutos);

    $item->dias = isset($matchDias[1]) ? (float) $matchDias[1] : 0;
    $item->horas = isset($matchHoras[1]) ? (int) $matchHoras[1] : 0;
    $item->minutos = isset($matchMinutos[1]) ? (int) $matchMinutos[1] : 0;

    return $item;

});

$lista_permisos = $datosProcesados
->groupBy(function ($item) {
    return $item->usuario . '_' . $item->goce;
})
->map(function ($grupo) {
    $primer = $grupo->first();

    return [
        'usuario' => $primer->usuario,
        'agencia'       => $primer->agencia,
        'goce'       => $primer->goce,
        'total_dias' => $grupo->sum('dias'),
        'total_horas' => $grupo->sum('horas'),
        'total_minutos' => $grupo->sum('minutos'),
        'total_permisos' => $grupo->count(),
    ];
})
->values();

      $lista_licencias =  Licencia::from('solicitud_licencias as sol_lic')
      ->select(
        'us.usuario',
        'ag.nombre as agencia',
        DB::raw('
        CASE
        WHEN sol_lic.goce = 1 THEN "SI"
        WHEN sol_lic.goce = 0 THEN "NO"
        END as goce'
    ),
    DB::raw('SUM(sol_lic.dias) as total_dias'),
    DB::raw('0 as total_horas'),
    DB::raw('0 as total_minutos'),

     DB::raw('COUNT(*) as total_licencias')

       )
       ->join('solicitud_fechas as sol_fec','sol_fec.licencia_id','sol_lic.id')
      ->join('usuarios as us','us.dni','sol_lic.usuario_id')
      ->join('agencias as ag','us.agencia_id','ag.id_agencia')
      ->whereBetween(DB::raw("STR_TO_DATE(sol_fec.fecha_solicitud, '%Y-%m-%d')"), [$fecha_desde, $fecha_hasta])
      ->where('sol_lic.estado','U')
      ->groupBy('usuario')
      ->groupBy('sol_lic.goce')
      ->get()
      ->toArray();

      $lista_licencias = collect($lista_licencias);

$permisosIndexados = $lista_permisos->keyBy(function ($item) {
    return $item['usuario'] . '_' . $item['goce'];
});

$licenciasIndexadas = $lista_licencias->keyBy(function ($item) {
    return $item['usuario'] . '_' . $item['goce'];
});

$clavesUnidas = $permisosIndexados->keys()->merge($licenciasIndexadas->keys())->unique();

$resultadoFinal = $clavesUnidas->map(function ($clave) use ($permisosIndexados, $licenciasIndexadas) {
    $permiso = $permisosIndexados->get($clave);
    $licencia = $licenciasIndexadas->get($clave);

    return [
        'agencia'         => $permiso['agencia'] ?? $licencia['agencia'] ?? '',
        'usuario'         => $permiso['usuario'] ?? $licencia['usuario'] ?? '',
        'goce'            => $permiso['goce'] ?? $licencia['goce'] ?? '',
        'total_dias'      => ($permiso['total_dias'] ?? 0) + ($licencia['total_dias'] ?? 0),
        'total_horas'     => $permiso['total_horas'] ?? 0,
        'total_minutos'   => $permiso['total_minutos'] ?? 0,
        'total_permisos'  => $permiso['total_permisos'] ?? 0,
        'total_licencias' => $licencia['total_licencias'] ?? 0,
    ];
});
$resultadoFinal = $resultadoFinal->sort(function ($a, $b) {
    $agenciaCompare = strcmp($a['agencia'], $b['agencia']);
    if ($agenciaCompare !== 0) {
        return $agenciaCompare;
    }

    $usuarioCompare = strcmp($a['usuario'], $b['usuario']);
    if ($usuarioCompare !== 0) {
        return $usuarioCompare;
    }

    return strcmp($b['goce'], $a['goce']);
})->values();


$agencia_sesion = session('id_agencia');

$orden = 1;

foreach ($resultadoFinal as $item) {

    $object = (object)[
        'numero' => $orden,
        'agencia'         => $item['agencia'],
        'usuario'         => $item['usuario'],
        'goce'            => $item['goce'],
        'total_dias'      => $item['total_dias'],
        'total_horas'     => $item['total_horas'],
        'total_minutos'   => $item['total_minutos'],
        'total_permiso'   => $item['total_permisos'],
        'total_licencias' => $item['total_licencias'],


    ];
    $orden += 1;
    $data[] = $object;
}

// Leer Plantilla-------------------------
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$reader->setLoadSheetsOnly('rptSolicitudesGeneral');
$spreadsheet = $reader->load("./report_templates/gth/rptSolicitudesGeneral.xlsx");
$sheet = $spreadsheet->getActiveSheet();

// Encabezado

$sheet->setCellValue('B1', (new GthController)->header_footer($agencia_sesion));
$sheet->setCellValue('I2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

// Insertando valores

$indice = 5;
$controller = new GthController();

foreach ($data as $item) {
    $columna = 1;
    foreach ($item as $valor) {
        $columnaLetra = $controller->num2char($columna);
        $cell = $columnaLetra . $indice;

        $sheet->setCellValue($cell, $valor);

        $columna++;
    }
    $indice++;
}

// Exportar para descarga-------------------------
$nombre_archivo = (new GthController)->concatenar_aleatorio('rptSolicitudesGeneral', 5);
$writer = new Xlsx($spreadsheet);
$writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');


    $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

    return ['path_xlsx' => $path_xlsx];

// dd($resultadoFinal);


    }


}
