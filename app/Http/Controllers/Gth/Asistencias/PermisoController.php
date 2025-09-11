<?php

namespace App\Http\Controllers\Gth\Asistencias;


use App\Http\Controllers\Gth\GthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;

use App\Models\Gth\Asistencias\Permiso;
use App\Models\Gth\Usuarios\usuario;


use App\Models\General\Agencia;
use Illuminate\Support\Facades\DB;

use App\Models\Gth\Asistencias\SolicitudFecha;


use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class PermisoController extends Controller
{


    public function permisos()
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


                return Inertia::render(
                    'Gth/Asistencias/permisos',
                    [
                        'usuarios' => $usuarios,
                        'agencias' => $agencias,
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




    public function listar_solicitud_permisos($fecha_desde,$fecha_hasta){



      $lista_permisos =  Permiso::from('solicitud_permisos as sol_per')
      ->select(

        'sol_per.id',
        'sol_per.fecha_permiso',
        DB::raw('DAYOFWEEK(sol_per.fecha_permiso) as dia_semana'),
        'sol_per.usuario_id',
        'sol_per.modo',
        DB::raw("DATE_FORMAT(sol_per.hora_inicio, '%h:%i %p') as hora_inicio"),
        'sol_per.hora_inicio as hora_inicio_n',
        DB::raw("DATE_FORMAT(sol_per.hora_fin, '%h:%i %p') as hora_fin"),
        'sol_per.hora_fin as hora_fin_n',
        'sol_per.hora_retorno',

        DB::raw("DATE_FORMAT(sol_per.hora_inicio, '%H') as hora_inicio_h"),

        DB::raw("DATE_FORMAT(sol_per.hora_retorno, '%h:%i %p') as hora_retorno_v"),


          'sol_per.tiempo',
          'sol_per.detalle',
          'sol_per.documento',
          'sol_per.comentario',

          'sol_per.goce',
          'sol_per.observacion',

          DB::raw('
          CASE
          WHEN sol_per.estado = "A" THEN "APROBADO"
          WHEN sol_per.estado = "P" THEN "PENDIENTE"
          WHEN sol_per.estado = "D" THEN "DENEGADO"
          WHEN sol_per.estado = "E" THEN "ELIMINADO"
          WHEN sol_per.estado = "I" THEN "NO VALIDADO"
          WHEN sol_per.estado = "V" THEN "VALIDADO"
          WHEN sol_per.estado = "U" THEN "UTILIZADO"
          WHEN sol_per.estado = "C" THEN "CANCELADO"

          ELSE 0
      END as estado'
      ),
      'sol_fec.fecha_solicitud',
          DB::raw("CONCAT(us.apellido_paterno, ' ', us.apellido_materno, ' ', us.nombres) AS nombre"),
          'us.usuario',
          'us_1.usuario as usuario_aprobador',
          'car.cargo as aprobador_cargo',
          DB::raw('
          CASE
          WHEN sol_per.estado = "A" OR sol_per.estado = "V" THEN 1
          ELSE 0
      END as estado_aprobacion'
      ),
          'us_2.usuario as usuario_validador',
          'ag.nombre as agencia'

       )
       ->join('solicitud_fechas as sol_fec','sol_fec.permiso_id','sol_per.id')
      ->join('usuarios as us','us.dni','sol_per.usuario_id')
      ->join('agencias as ag','us.agencia_id','ag.id_agencia')
      ->join('usuarios as us_1','us_1.dni','sol_per.aprobador_id')
      ->join('cargos as car','car.id','us_1.cargo_id')
      ->leftjoin('usuarios as us_2','us_2.dni','sol_per.validador_id')
      ->whereBetween(DB::raw("STR_TO_DATE(sol_fec.fecha_solicitud, '%Y-%m-%d')"), [$fecha_desde, $fecha_hasta])
      ->orderBy('sol_fec.fecha_solicitud', 'desc')
      ->get();

    //   dd($lista_permisos);
      return $lista_permisos;

    }

    public function validar_permiso(Request $request){

        $response = new \stdClass();

        $fecha_validacion = date("Y-m-d H:i:s");

        $permiso_id = $request->permiso_id;
        $validador_id = $request->validador_id;
        $observacion = strtoupper($request->observacion);
        $goce = $request->goce;

                $permiso = Permiso::where('id', $permiso_id)->update([

                    'goce' => $goce,
                    'observacion' => $observacion,
                    'validador_id' => $validador_id,
                    'estado' => 'V',

                ]);


                SolicitudFecha::where('permiso_id', $permiso_id)
                ->update([
                    'fecha_validacion' => $fecha_validacion
                ]);


                $response->success = true;

                // dd($response);

                return $response;

    }
    public function invalidar_permiso(Request $request){

        $response = new \stdClass();

        $fecha_validacion = date("Y-m-d H:i:s");


        $permiso_id = $request->permiso_id;
        $validador_id = $request->validador_id;
        $observacion = strtoupper($request->observacion);

                $permiso = Permiso::where('id', $permiso_id)->update([
                    'validador_id' => $validador_id,
                    'estado' => 'I',
                    'observacion' => $observacion,
                ]);

                SolicitudFecha::where('permiso_id', $permiso_id)
                ->update([
                    'fecha_validacion' => $fecha_validacion
                ]);


                $response->success = true;

                // dd($response);

                return $response;

    }

    public function cancelar_permiso(Request $request){


        $response = new \stdClass();

        $fecha_verificacion = date("Y-m-d H:i:s");



        $permiso_id = $request->permiso_id;
        $verificador_id = $request->verificador_id;



                $permiso = Permiso::where('id', $permiso_id)->update([
                    'estado' => 'C',


                ]);
                SolicitudFecha::where('permiso_id', $permiso_id)
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
        $permiso_filtrados = json_decode($request->permiso_filtrados);

        $agencia_sesion = session('id_agencia');

        // dd($fecha_desde,$fecha_hasta,$permiso_filtrados);
        $orden = 1;

        foreach ($permiso_filtrados as $item) {


                if($item->modo =="HORAS"){
                    if (preg_match('/(\d+)\s*horas?\s*y\s*(\d+)\s*minutos?/', $item->tiempo, $matches)) {
                        $horas = (int)$matches[1];
                        $minutos = (int)$matches[2];
                    };
                    $dias = '-';

                }else {
                    $horas ='-';
                    $minutos = '-';
                    $dias  =  0.5;
                }


            $object = (object)[
                'numero' => $orden,
                'agencia' => $item->agencia,


                'fecha_solicitud' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha_solicitud)),
                'estado' => $item->estado,
                'usuario' =>  $item->usuario,
                'fecha_permiso' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha_permiso)),
                'tipo' =>  $item->modo,
                'hora_inicio' =>  Date::dateTimeToExcel(Carbon::parse($item->hora_inicio)),
                'hora_fin' =>  Date::dateTimeToExcel(Carbon::parse($item->hora_fin)),
                'hora_retorno' => $item->hora_retorno != null ? Date::dateTimeToExcel(Carbon::parse($item->hora_retorno)):"-",

                'dias' => $dias,
                'horas' => $horas,
                'minutos' => $minutos,


                'usuario_aprobador' => $item->usuario_aprobador,
                'goce' => $item->goce == 1? "SI":"NO",
            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptPermisos');
        $spreadsheet = $reader->load("./report_templates/gth/rptPermisos.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezado

        $sheet->setCellValue('B1', (new GthController)->header_footer($agencia_sesion));
        $sheet->setCellValue('P2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

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
        $nombre_archivo = (new GthController)->concatenar_aleatorio('rptpermisos', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');


            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

            return ['path_xlsx' => $path_xlsx];

            // dd($nombre_archivo);

}




}
