<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;


use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Records\CreditoResumenRecord;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CentralRiesgo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Aprobacion;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteAprobacionController extends Controller
{

    public function aprobaciones_anuladas()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_ANULADOS', 'CREDITOS_REPORTES');

            if ($band == 1) {
                return Inertia::render('Creditos/Reportes/Creditos/creditos_anulados', []);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function obtener_anulaciones(Request $request)
    {
        // return $request; 
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        if ($agencia_id == 0 || $agencia_id == null) {
            return [];
        }

        return Aprobacion::on($conexion)->from('credito_aprobaciones as cre_apr')
            ->select(
                'cre_apr.id',
                'cre_apr.propuesta_id',
                'cre_apr.monto',
                'cre_apr.tasa_interes',
                'cre_apr.cuota',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.comentario_aprobacion',
                'cre_apr.fecha_aprobacion',
                'cre_apr.comisiones',
                'cre_apr.datos_creacion',
                'cre_apr.datos_actualizacion',
                'cre_apr.comentario_anulacion',

                'cre_pro.agencia_id',
                'cre_pro.cliente_id',
                'cre_pro.garantia_id',
                'cre_pro.comentario_garantia',
                'cre_pro.valor_garantia',
                'cre_pro.comentario_propuesta',

                'cli_reg.dni',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.codigo_expediente',
                'cli_reg.imagen_dni',
                'cli_reg.codigo_expediente',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_registro',

                'cre_gar.garantia'

            )
            ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('cliente_registros as cli_reg', 'cre_pro.cliente_id', 'cli_reg.id')
            ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->join('solucion_master.usuarios as us_2', DB::raw("SUBSTR(cre_apr.datos_actualizacion,42,8)"), 'us_2.dni')
            ->join('credito_garantias as cre_gar', 'cre_pro.garantia_id', 'cre_gar.id')
            ->whereBetween(DB::raw("SUBSTR(cre_apr.datos_actualizacion,11,10)"), [$fecha_desde, $fecha_hasta])
            ->where('cre_apr.estado_id', 6)
            ->get();
    }

    public function exportar_anulados(Request $request)
    {
        // return $request;
        // Ordenando array de datos-------------------------------

        $datos_tabla = json_decode($request->creditos_filtrados);
        $datos_tabla; 
        $data = [];
        $numero = 1;
        foreach ($datos_tabla as $item) {
            $object = (object)[
                'numero' => $numero,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'monto' => $item->monto,
                'plazo' => round($item->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($item->periodo_pago),
                'tasa_interes' => $item->tasa_interes,
                'usuario_asesor' => $item->usuario_asesor,
                'cuota' => $item->cuota,
                'fecha_aprobacion' => substr($item->datos_creacion,10,10),
                'fecha_anulacion' => substr($item->datos_actualizacion,10,10),
                'usuario_registro' => $item->usuario_registro,
                'comentario_anulacion' => $item->comentario_anulacion,

            ];
            $numero++;
            $data[] = $object;
        }

        // return $data; 
        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptCreditosAnulados.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_par = [];
        $lista_formatos_celdas_impar = [];
        $lista_formatos_celdas_total = [];


        while ($celda <= 11) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda_par = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_impar = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_celda_total = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formatos_celdas_par[] = $formato_celda_par;
            $lista_formatos_celdas_impar[] = $formato_celda_impar;
            $lista_formatos_celdas_total[] = $formato_celda_total;
            $celda++;
        }



        // Insertando datos-----------------------------
        $indice = 5;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                if ($item->numero % 2 == 0) {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_par[$columna_2 - 1]);
                }else{
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_impar[$columna_2 - 1]);
                }
                $columna_2 += 1;
            }

            $indice += 1;
        }
        $sheet->setCellValue((new CreditosController)->num2char(1) . $indice, "Total de registros: " . $indice - 5);

        $sheet->getStyle("B" . $indice.":"."L".$indice)->applyFromArray($lista_formatos_celdas_total[0]);
        
        

        // Exportar para descarga-------------------------
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $ret['data'] = base64_encode(ob_get_contents());
        ob_end_clean();

        return $ret['data'];
    }
}
