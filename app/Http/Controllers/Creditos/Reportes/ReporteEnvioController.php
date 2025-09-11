<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;
use App\Models\General\Agencia;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Cuenta\Envio;

use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\General\Datos_aplicacion;
use DOTNET;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ReporteEnvioController extends Controller
{

    public function envios_agencias()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_ENVIOS_AGENCIAS', 'CREDITOS_REPORTES');
            

            if ($band == 1) {
                                 
                return Inertia::render('Creditos/Reportes/Caja/envios_agencias'
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
   
    public function buscar_envios_agencias(Request $request)
    {

        // dd($request);

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $lista_envios_agencia = Envio::on($conexion)->from('cuenta_envios as cue_env')
            ->select(
                'cue_env.id',
                'cue_env.agencia_remitente_id',
                'age_1.nombre as agencia_envio',
                'usu_1.usuario as usuario_envio',

                'age_2.nombre as agencia_recepcion',
                'cue_env.agencia_destinatario_id',

                'cue_env.monto',
                'cue_env.estado',
                'cue_env.concepto',
                DB::raw("SUBSTR(cue_env.datos_creacion,11,19) as fecha_envio"),
                DB::raw("SUBSTR(cue_env.datos_actualizacion,11,19) as fecha_recepcion"),
                'cue_env.tipo',
                'usu_3.usuario as usuario_gestion',
                'ent.nombre as entidad',
                'cue_env.comprobante_envio',
                'cue_env.comprobante_recepcion',
                'cue_env.comentario_rechazo',

            )

            ->join('solucion_master.agencias as age_1', 'age_1.id_agencia', 'cue_env.agencia_remitente_id')
            ->join('cuenta_usuarios as cue_usu_1', 'cue_usu_1.id', 'cue_env.remitente_id')
            ->join('solucion_master.usuarios as usu_1', 'usu_1.dni', 'cue_usu_1.dni')

            ->join('solucion_master.agencias as age_2', 'age_2.id_agencia', 'cue_env.agencia_destinatario_id')

            ->join('solucion_master.usuarios as usu_3', 'usu_3.dni', 'cue_env.usuario_gestion_id')
            ->leftjoin('solucion_master.entidades as ent', 'ent.id', 'cue_env.entidad_id')

            ->whereBetween(DB::raw("SUBSTR(cue_env.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
            ->orderBy('fecha_envio', 'desc')
            ->get();

    $lista_envios = [];

    
    foreach ($lista_envios_agencia as $item) {

        $tabla = 'solucion_master_' .  $item->agencia_destinatario_id;

        $envio = Envio::on($conexion)->from('cuenta_envios as cue_env')
            ->select(
                'cue_env.id',
                'usu.usuario as usuario_recepcion',
         
            )
            ->join("$tabla.cuenta_usuarios as cue_usu", 'cue_env.destinatario_id', "cue_usu.id")
            ->join('solucion_master.usuarios as usu', 'usu.dni', 'cue_usu.dni')
            ->where('cue_env.id', $item->id)
            ->get()->last();

             $envio = $envio->toArray();

        $lista_envios[] = $envio;
    }

    $lista_envios_agencia = $lista_envios_agencia->toArray();
  
    $lista_envios_agencia_completo = [];

    foreach ($lista_envios_agencia as $item) {

        foreach ($lista_envios as  $item2) {

            if ($item['id'] == $item2['id']) {

                $value = $item2['usuario_recepcion'];
                $item['usuario_recepcion'] = $value;

                $lista_envios_agencia_completo[]= $item;
            }

        }
    }
    

        return ['lista_envios_agencia' => $lista_envios_agencia_completo];
    }
  
  
 

    public function exportar_envios_agencias(Request $request)
    {
        // Ordenando array de datos-------------------------------

       

        $datos_recibidos =   json_decode($request->datos_tabla);
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $data = [];
        $orden = 1;

        foreach ($datos_recibidos as $item) {
            $object = (object)[

                'numero' => $orden,
                'agencia_envio' => $item->agencia_envio,
                'usuario_envio' => $item->usuario_envio,
                'agencia_recepcion' => $item->agencia_recepcion,
                'usuario_recepcion' => $item->usuario_recepcion,
                'monto' => $item->monto,
                'estado' => $item->estado,
                'concepto' => $item->concepto,
                'fecha_envio' => $item->fecha_envio,
                'fecha_recepcion' => $item->fecha_recepcion,
                'tipo' => $item->tipo,
                'usuario_gestion' => $item->usuario_gestion,
                'entidad' => $item->entidad,
                'comentario_rechazo' => $item->comentario_rechazo,
                

            ];
            $orden += 1;
            $data[] = $object;
        }

             // Leer Plantilla-------------------------
             $inputFileName = './report_templates/creditos/reportes/rptEnviosAgencia.xlsx';

             $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
             $spreadsheet = $reader->load($inputFileName);
             $sheet = $spreadsheet->getActiveSheet();
     
     
             // Obteniendo formatos-----------------------------
             $celda = 1;
             $lista_formatos_celdas_1 = [];
             $lista_formatos_celdas_2 = [];
     
             while ($celda <= 15) {
                 $columna_1 = (new CreditosController)->num2char($celda);
     
                 $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
                 $formato_celda_2 = $sheet->getStyle($columna_1 . 6)->exportArray();
     
                 $formato_total = $sheet->getStyle($columna_1 . 8)->exportArray();
     
                 $lista_formatos_celdas_1[] = $formato_celda_1;
                 $lista_formatos_celdas_2[] = $formato_celda_2;
                 $lista_formatos_totales[] = $formato_total;
     
     
                 $celda++;
             }
             $sheet->removeRow(5);
             $sheet->removeRow(6);
              
             // Encabezado
     
             $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
             $sheet->setCellValue('O2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);
     
             // Insertando datos-----------------------------
             $indice = 5;
             $total = 0;
             $suma_acumulado = 0;
             foreach ($data as $item) {
                 $columna_2 = 1;
                 foreach ($item as $valor) {
                     $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                     if ($indice % 2 == 0) {
                         $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_2[$columna_2 - 1]);
                     } else {
                         $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_2 - 1]);
                     }
     
                     $columna_2 += 1;
                 }
     
                 $indice += 1;
                 $total += 1;
                 $suma_acumulado = $suma_acumulado + $item->monto;

             }
            //  dd($indice);
             $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(7);
     
             $indice += 1;

             $sheet->setCellValue('G' . $indice, $suma_acumulado);
             $sheet->setCellValue('O' . $indice,'Total '. $total . ' registro(s)');

             foreach ($lista_formatos_totales as $key => $value) {
                 $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
             }

             $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptEnviosAgencia', 5);
          
             $writer = new Xlsx($spreadsheet);
             $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
             $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';
     
             return ['path_xlsx' => $path_xlsx];
    }

}
