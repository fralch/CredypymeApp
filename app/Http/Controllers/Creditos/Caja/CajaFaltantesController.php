<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\General\PermisosController;
use App\Models\Creditos\Mantenimiento\Transacciones\Categoria;
use App\Models\Creditos\Mantenimiento\Transacciones\Subcategoria;

use App\Models\Creditos\Mantenimiento\Transacciones\Comprobante;
use App\Models\General\AreaTrabajo;

use App\Models\General\Agencia;

use App\Models\Creditos\Caja\Transaccion;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class CajaFaltantesController extends Controller
{
    public function revision_faltantes()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'REVISION_FALTANTES', 'CREDITOS_CAJA');

            if ($band == 1) {


                // $agencias = Agencia::all();

                // $categorias = [];
                // $subcategorias = [];
                // $comprobantes = [];

                // foreach ($agencias as $item) {



                //     $agencia_id = $item->id_agencia;
                //     $conexion = 'master_' . $agencia_id;

                //     $lista_categorias = Categoria::on($conexion)
                //         ->select(
                //             'id',
                //             'categoria',
                //             DB::raw("$agencia_id as agencia_id")
                //         )
                //         ->get();

                //     $lista_subcategorias = SubCategoria::on($conexion)->from('transaccion_subcategorias as tra_sub')
                //         ->select(
                //             'tra_sub.id',
                //             'tra_sub.categoria_id',
                //             'tra_sub.subcategoria',
                //             DB::raw("$agencia_id as agencia_id")
                //         )
                //         ->get();


                //         $lista_comprobantes = Comprobante::on($conexion)->from('transaccion_comprobantes as tra_com')
                //         ->select(
                //             'tra_com.id',
                //             'tra_com.comprobante',
                //             DB::raw("$agencia_id as agencia_id")
                //         )
                //         ->get();


                //     foreach ($lista_categorias as $item) {
                //         $categorias[] = $item;
                //     }
                //     foreach ($lista_subcategorias as $item) {
                //         $subcategorias[] = $item;
                //     }
                //     foreach ($lista_comprobantes as $item) {
                //         $comprobantes[] = $item;
                //     }



                // }

                return Inertia::render('Creditos/Caja/revision_faltantes');
            } else {


                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function revision_faltantes_buscar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_desde =  date("Y-m-d", strtotime($fecha_desde));
        $fecha_hasta = $request->fecha_hasta;
        $fecha_hasta =  date("Y-m-d", strtotime($fecha_hasta));

        $categorias = Categoria::on($conexion)->select('id')
            ->whereIn('categoria', ['FALTANTE', 'SOBRANTE'])->get();

        $registros_faltantes = Transaccion::on($conexion)->from('transaccion_registros as tra_reg')
            ->select(
                'tra_reg.id',
                'tra_reg.regularizado',
                'tra_reg.monto',
                'tra_reg.tipo',
                'tra_reg.concepto',
                'us.usuario',
                'us.dni as usuario_id',
                'tra_reg.agencia_id as agencia_id_usu',
                'tra_reg.area_trabajo_id',
                'tra_reg.comprobante_id',
                DB::raw("STR_TO_DATE(SUBSTRING(tra_reg.datos_creacion,11,19), '%Y-%m-%d') as fecha_registro"),
                DB::raw("$agencia_id as agencia_id")

            )
            ->join('solucion_master.usuarios as us', 'tra_reg.usuario_id', 'us.dni')
            ->whereIn('tra_reg.categoria_id', $categorias)
            ->whereBetween(DB::raw("STR_TO_DATE(fecha_transaccion, '%Y-%m-%d')"), [$fecha_desde, $fecha_hasta])
            ->orderby('fecha_registro', 'desc')
            ->get();


        return $registros_faltantes;
    }

    public function revision_faltantes_guardar(Request $request)
    {

        $response = new \stdClass();

        $agencia_login = session('id_agencia');
        $conexion = 'master_' .  $agencia_login;

        $datos_registro = (new CreditosController)->datos_registro($agencia_login);
        $fecha_larga_aplicacion = (new CreditosController)->fecha_larga_aplicacion($agencia_login);
        $año = substr($fecha_larga_aplicacion, 0, 4);

        $frmDatosRegularizacion = json_decode($request->frmDatosRegularizacion);
        $agencia_id = $frmDatosRegularizacion->agencia_id;
        $tipo = $frmDatosRegularizacion->tipo;

        $usuario_id = $frmDatosRegularizacion->usuario_id;
        $caja_id = $frmDatosRegularizacion->caja_id;
        $tra_reg_id = $frmDatosRegularizacion->id;

        $monto = $frmDatosRegularizacion->monto;
        $concepto = mb_strtoupper($frmDatosRegularizacion->concepto);

        if ($tipo == "I") {

            $tipo_registro = "E";
            $categoria = "OTROS GASTOS";
            $subcategoria = "GASTO POR REGULARIZACIÓN DE SOBRANTE";
        } else if ($tipo == "E") {

            $tipo_registro = "I";
            $categoria = "INGRESOS NO REALES";
            $subcategoria = "REPOSICIÓN DE FALTANTES";
        }



        $categoria_id = Categoria::on($conexion)
            ->select(
                'id',
            )
            ->where('categoria', $categoria)->get()->last();


        $subcategoria_id = Subcategoria::on($conexion)
            ->select(
                'id',
            )
            ->where('subcategoria', $subcategoria)->get()->last();

        $comprobante_id = Comprobante::on($conexion)
            ->select(
                'id',
            )
            ->where('comprobante', "OTROS")->get()->last();


        $area_trabajo_id = AreaTrabajo::select(
            'id',
        )
            ->where('area', "ÁREA DE OPERACIONES")->get()->last();



        $transaccion = Transaccion::on($conexion)->create([
            'tipo' => $tipo_registro,
            'categoria_id' => $categoria_id->id,
            'subcategoria_id' => $subcategoria_id->id,
            'agencia_id' => $agencia_id,
            'usuario_id' => $usuario_id,
            'area_trabajo_id' => $area_trabajo_id->id,
            'comprobante_id' => $comprobante_id->id,
            'caja_id' => $caja_id,
            'monto' => $monto,
            'concepto' => $concepto,
            'fecha_transaccion' => $fecha_larga_aplicacion,
            'datos_creacion' => $datos_registro
        ]);



        $transaccion_id = $transaccion->id;


        $path_name = pathinfo($_FILES['documento']['name']);
        $extension = "." . $path_name['extension'];
        $nombre_archivo = $año . '_trans_' . $transaccion_id . $extension;
        $archivo = $_FILES['documento']['tmp_name'];
        $ruta = '/imagenes_server/creditos/caja/transacciones/' . $agencia_login . '/' . $año;
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_archivo;
        $calidad = 10;


        (new CreditosController)->compressImage($archivo, $ruta, $calidad);

        Transaccion::on($conexion)->where('id', $transaccion_id)
            ->update(['documento' => $nombre_archivo]);


        $conexion2 = 'master_' .  $agencia_id;

        Transaccion::on($conexion2)->where('id', $tra_reg_id)
            ->update(['regularizado' => 1, 'datos_actualizacion' => $datos_registro]);


        //    dd($nombre_archivo);



        $response->success = true;
        return $response;
    }


    public function revision_faltantes_exportar(Request $request)
    {

        $datos_recibidos = json_decode($request->datos_tabla);
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $tipo = $request->tipo;

        $data = [];

        $orden = 1;

        foreach ($datos_recibidos as $item) {
            $object = (object)[
                'numero' => $orden,
                'regularizado' => $item->regularizado == 1 ? "Si" : "No",
                'tipo' =>  $item->tipo == "I" ? "SOBRANTE" : "FALTANTE",
                'monto' => $item->monto,
                'descripcion' => $item->concepto,
                'fecha_registro' => $item->fecha_registro,
                'usuario' => $item->usuario,

            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName =  './report_templates/creditos/reportes/rptFaltantesSobrantes.xlsx';

        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];

        while ($celda <= 7) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;


            $celda++;
        }


        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('G2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-----------------------------
        $indice = 5;
        $total = 0;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);

                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_2 - 1]);


                $columna_2 += 1;
            }

            $indice += 1;
            $total += 1;
        }

        $indice += 1;

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptFaltantesSobrantes', 5);




        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        // dd($nombre_archivo);

        return ['path_xlsx' => $path_xlsx];
    }
}
