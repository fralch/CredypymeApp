<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;

use App\Models\Gth\Usuarios\Usuario;
use App\Models\General\Agencia;
use App\Models\Logistica\Suministros\Suministro;
use App\Models\Logistica\Suministros\Proveedor;
use App\Models\Logistica\Suministros\Tipo;
use App\Models\Logistica\Suministros\Medicion;
use App\Models\Logistica\Estado;
use App\Models\Logistica\Condicion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

use Inertia\Inertia;

class SuministroAlmacenController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function almacen()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ALMACEN',  'LOGISTICA_SUMINISTROS');
            if ($band == 1) {
                return Inertia(
                    'Logistica/Suministros/almacen',
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar_recursos(Request $request)
    {
        $usuarios = Usuario::select('usuario', 'dni', 'agencia_id')
            ->where([
                ['habilitado', 1],
                ['usuario_real', 1]
            ])
            ->orderBy('usuario', 'asc')->get();

        $estados = Estado::select('id', 'estado')
            ->where('habilitado',  1)
            ->whereIn('estado', ['DISPONIBLE', 'AGOTADO'])
            ->orderby('estado', 'asc')
            ->get();

        $condiciones = Condicion::select('id', 'condicion')
            ->where('habilitado',  1)
            ->get();

        $tipos = Tipo::select('id', 'tipo')
            ->where('habilitado', 1)
            ->get();

        $proveedores = Proveedor::select('id', 'proveedor')
            ->where('habilitado',  1)
            ->orderby('proveedor', 'asc')
            ->get();

        $mediciones = Medicion::select('id', 'medicion', 'escala')
            ->where('habilitado',  1)
            ->orderby('medicion', 'asc')
            ->get();

        return response()->json([
            'usuarios' => $usuarios,
            'estados' => $estados,
            'condiciones' => $condiciones,
            'tipos' => $tipos,
            'proveedores' => $proveedores,
            'mediciones' => $mediciones
        ]);
    }

    public function buscar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $estado = $request->input('estado');

        $operador = '>';
        $estado_id = 0;

        if ($estado != 'TODOS') {
            $operador = '=';

            $estado_id = Estado::select('id')->where('estado', $estado)->get()->last();
            $estado_id = $estado_id->id;
        };

        $lista_suministros = Suministro::withoutGlobalScopes()
            ->from('suministro_almacen as sum_alm')
            ->select(
                'sum_alm.id',
                'sum_alm.agencia_id',
                'sum_alm.codigo',
                'sum_alm.suministro',
                'sum_alm.marca',
                'sum_alm.detalle',
                'sum_alm.tipo_id',
                'sum_alm.proveedor_id',
                'sum_alm.condicion_id',
                'sum_alm.clasificacion',
                'sum_alm.cantidad',
                'sum_alm.medicion_id',
                'sum_alm.valor_unitario',
                'sum_alm.estado_id',
                DB::raw("
    IF(sum_alm.datos_creacion IS NULL OR sum_alm.datos_creacion = '', NULL, 
        JSON_UNQUOTE(JSON_EXTRACT(sum_alm.datos_creacion, '$.fecha'))
    ) as fecha_compra
"),
                DB::raw("(sum_alm.cantidad * sum_alm.valor_unitario) as valor_total"),

                'sum_tip.tipo',

                'log_con.condicion',
                'log_est.estado',
                'log_med.escala',
                'log_med.medicion',

                'age.nombre as agencia',

                "log_med.escala as cantidad_envio",
                "log_med.escala as cantidad_asignar",
                "log_med.escala as cantidad_vender",
                "sum_alm.valor_unitario as valor_venta",

            )
            ->join('suministro_tipos as sum_tip', 'sum_alm.tipo_id', 'sum_tip.id')
            ->join('logistica_condiciones as log_con', 'sum_alm.condicion_id',  'log_con.id')
            ->join('logistica_estados as log_est', 'sum_alm.estado_id',  'log_est.id')
            ->leftjoin('logistica_mediciones as log_med', 'sum_alm.medicion_id',  'log_med.id')
            ->join('agencias as age', 'sum_alm.agencia_id', 'age.id_agencia')
            ->where([
                ['sum_alm.agencia_id', $agencia_id],
                ['sum_alm.estado_id', $operador, $estado_id]
            ])
            ->whereNull('deleted_at')
            ->orderby('sum_alm.suministro', 'asc')
            ->get();

        return response()->json(['lista_suministros' => $lista_suministros]);
    }

    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------


    public function editar(Request $request)
    {
        $datos_registro = (new LogisticaController)->datos_registro();
        $tipo_modulo = $request->tipo_modulo;
        $suministro_id = $request->suministro_id;
        $suministro = mb_strtoupper($request->suministro);
        $marca = mb_strtoupper($request->marca);
        $detalle = mb_strtoupper($request->detalle);
        $proveedor_id = $request->proveedor_id;
        $condicion_id = $request->condicion_id;

        Suministro::where('id', $suministro_id)
            ->update([
                'suministro' => $suministro,
                'marca' => $marca,
                'detalle' => $detalle,
                'proveedor_id' => $proveedor_id,
                'condicion_id' => $condicion_id,
                'datos_actualizacion' => $datos_registro
            ]);

        return redirect()->route('log.sum.almacen', $tipo_modulo);
    }

    public function eliminar($id)
    {
        $datos_registro = (new LogisticaController)->datos_registro();

        Suministro::where('id', $id)
            ->update([
                'datos_actualizacion' => $datos_registro
            ]);
        Suministro::find($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Suministro eliminado.'
        ], 200);
    }

    public function exportar(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;

        $suministros = json_decode($request->lista_suministros);
        $orden = 1;
        foreach ($suministros as $item) {

            $object = (object)[
                'orden' => $orden,
                'estado' => $item->estado,
                'agencia' => $item->agencia,
                'suministro' => $item->suministro,
                'codigo' => $item->codigo,
                'tipo' => $item->tipo,
                'marca' => $item->marca,
                'detalle' => $item->detalle,
                'condicion' => $item->condicion,
                'cantidad' => floatval($item->cantidad),
                'medicion' => $item->medicion,
                'valor_unitario' => floatval($item->valor_unitario),
                'valor_total' => floatval($item->valor_total),
                'fecha_compra' => $item->fecha_compra ? Date::dateTimeToExcel(Carbon::parse($item->fecha_compra)) : null,
                'clasificacion' => $item->clasificacion,
            ];

            $data[] = $object;

            $orden++;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptSuministrosAlmacen');
        $spreadsheet = $reader->load("./report_templates/logistica/reportes/rptSuministrosAlmacen.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezado

        $sheet->setCellValue('B1', (new LogisticaController)->header_footer($agencia_id));

        // Insertando valores

        $indice = 5;
        $controller = new LogisticaController();

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
        $nombre_archivo = (new LogisticaController)->concatenar_aleatorio('rptSuministrosAlmacen', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');

        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    // -----------------------------------APIS-----------------------------------

    public function obtener_codigo(Request $request)
    {
        $tipo_id = $request->tipo_id;
        $agencia_id = $request->agencia_id;

        $cantidad = Suministro::where([
            ['agencia_id', $agencia_id],
            ['tipo_id', $tipo_id]
        ])->count();

        return response()->json(['cantidad' => $cantidad]);
    }
    // --------------------------------------------------------------------------

}
