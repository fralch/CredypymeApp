<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transaccion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Categoria;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Subcategoria;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Comprobante;


use Modules\General\Infrastructure\Persistence\Eloquent\AreaTrabajo;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Monolog\Handler\IFTTTHandler;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;


class ReporteTransaccionesController extends Controller
{
    public function transacciones($modo)
    {


        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_MIS_TRANSACCIONES', 'CREDITOS_REPORTES');
            }
            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_TRANSACCIONES', 'CREDITOS_REPORTES');
            }
            if ($band == 1) {

                $agencias = Agencia::all();

                $categorias = [];
                $subcategorias = [];
                $comprobantes = [];

                $usuarios_cuenta = [];
                $datos_usuario = [];

                foreach ($agencias as $item) {
                    $agencia_id = $item->id_agencia;
                    $conexion = 'master_' . $agencia_id;

                    $lista_categorias = Categoria::on($conexion)
                        ->select(
                            'id',
                            'categoria',
                            'tipo',
                            'habilitado',
                            DB::raw("$agencia_id as agencia_id")
                        )
                        ->orderBy('categoria', 'asc')
                        ->get();

                    $lista_subcategorias = SubCategoria::on($conexion)->from('transaccion_subcategorias as tra_sub')
                        ->select(
                            'tra_sub.id',
                            'tra_sub.categoria_id',
                            'tra_sub.subcategoria',
                            'tra_sub.habilitado',
                            'tra_cat.tipo',
                            DB::raw("$agencia_id as agencia_id")
                        )
                        ->join('transaccion_categorias as tra_cat', 'tra_sub.categoria_id', 'tra_cat.id')
                        ->orderBy('tra_sub.subcategoria', 'asc')
                        ->get();


                    $lista_comprobantes = Comprobante::on($conexion)->from('transaccion_comprobantes as tra_com')
                        ->select(
                            'tra_com.id',
                            'tra_com.comprobante',
                            'tra_com.habilitado',
                            DB::raw("$agencia_id as agencia_id")
                        )
                        ->get();


                    foreach ($lista_categorias as $item) {
                        $categorias[] = $item;
                    }
                    foreach ($lista_subcategorias as $item) {
                        $subcategorias[] = $item;
                    }
                    foreach ($lista_comprobantes as $item) {
                        $comprobantes[] = $item;
                    }
                }

                foreach ($agencias as $item) {
                    $agencia_id = $item->id_agencia;
                    $conexion = 'master_' . $agencia_id;


                    $lista_usuarios = CuentaUsuario::on($conexion)->from('cuenta_usuarios as us_cu')
                        ->select(
                            'us_cu.dni',
                            'us_cu.con_cuenta',
                            'us.usuario',
                            DB::raw("$agencia_id as agencia_id"),
                            'us.habilitado'

                        )

                        ->join('solucion_master.usuarios as us', 'us.dni', 'us_cu.dni')
                        ->orderBy('us.usuario', 'asc')->get();

                    foreach ($lista_usuarios as $item) {
                        $usuarios_cuenta[] = $item;
                    }
                }
                $datos_usuario = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )->where('dni', session('usuario_dni'))->get();

                $usuarios_edicion = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )->orderBy('usuario', 'asc')
                    ->get();




                $areas_trabajo = AreaTrabajo::where('habilitado', 1)->get();

                $agencias_totales = Agencia::select(

                    'nombre as agencia',
                    'id_agencia as id'
                )->get();


                return Inertia::render('Creditos/Reportes/Caja/transacciones', [
                    'modo' => $modo,
                    'usuarios_cuenta' => $usuarios_cuenta,
                    'usuarios_edicion' => $usuarios_edicion,
                    'datos_usuario' => $datos_usuario,
                    'categorias' => $categorias,
                    'subcategorias' => $subcategorias,
                    'areas_trabajo' => $areas_trabajo,
                    'comprobantes' => $comprobantes,
                    'agencias_totales' => $agencias_totales
                ]);
            } else {
                return redirect('/');
            }
        }
    }

    public function buscar(Request $request)
    {
        // dd($request);
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $tipo_transaccion = $request->tipo_transaccion;
        $filtro_usuario = $request->filtro_usuario;
        $por_categoria = $request->por_categoria;
        $por_subcategoria = $request->por_subcategoria;

        $filtros = [['tipo', $tipo_transaccion]];

        if ($por_categoria == 'true') {
            $categoria_id = $request->categoria_id;
            $filtros = [['tipo', $tipo_transaccion], ['categoria_id', $categoria_id]];
        }

        if ($por_subcategoria == 'true') {
            $subcategoria_id = $request->subcategoria_id;
            $filtros = [['tipo', $tipo_transaccion], ['subcategoria_id', $subcategoria_id]];
        }

        if ($filtro_usuario == 'true') {
            $usuarios = json_decode($request->usuarios_cuenta);
            $rango_registros = Transaccion::on($conexion)->from('transaccion_registros as tra_reg')
                ->select('tra_reg.id')
                ->join('solucion_master.usuarios as us', DB::raw("SUBSTRING(tra_reg.datos_creacion,42,8)"), 'us.dni')
                ->whereBetween('tra_reg.fecha_transaccion', [$fecha_desde, $fecha_hasta])
                ->where($filtros)
                ->whereIn("us.dni", $usuarios)
                ->get();
        } else {
            $rango_registros = Transaccion::on($conexion)
                ->select('id')
                ->whereBetween('fecha_transaccion', [$fecha_desde, $fecha_hasta])
                ->where($filtros)
                ->get();
        }

        $lista_transacciones = Transaccion::on($conexion)->from('transaccion_registros as tra_reg')
            ->select(
                'tra_reg.id',
                'tra_reg.tipo',
                'tra_reg.monto',
                'tra_reg.concepto',
                'tra_reg.documento',
                'tra_reg.fecha_transaccion',
                'tra_reg.agencia_id',
                'tra_reg.area_trabajo_id',
                'tra_reg.comprobante_id',


                'tra_cat.categoria',
                'tra_cat.id as categoria_id',
                'tra_cat.habilitado as categoria_hab',

                'tra_sub.subcategoria',
                'tra_sub.id as subcategoria_id',
                'tra_sub.habilitado as subcategoria_hab',

                'usu_1.usuario',
                'usu_1.dni as usuario_id',
                'usu_1.habilitado as usuario_hab',
                'usu_2.usuario as usuario_registro',

                'are_tra.area',
                'tra_com.comprobante',
                'tra_com.habilitado as comprobante_hab'
            )
            ->join('transaccion_categorias as tra_cat', 'tra_reg.categoria_id', 'tra_cat.id')
            ->join('transaccion_subcategorias as tra_sub', 'tra_reg.subcategoria_id', 'tra_sub.id')
            ->join('solucion_master.usuarios as usu_1', 'tra_reg.usuario_id', 'usu_1.dni')
            ->leftjoin('solucion_master.usuarios as usu_2', DB::raw("SUBSTRING(tra_reg.datos_creacion,42,8)"), 'usu_2.dni')
            ->join('solucion_master.areas_trabajo as are_tra', 'tra_reg.area_trabajo_id', 'are_tra.id')
            ->join('transaccion_comprobantes as tra_com', 'tra_reg.comprobante_id', 'tra_com.id')
            ->whereIn('tra_reg.id', $rango_registros)
            ->orderBy('id', 'desc')
            ->get();

        $totales = (object)[];

        $totales->total_monto = $lista_transacciones->sum('monto');

        return [
            'lista_transacciones' => $lista_transacciones,
            'totales' => $totales,
            'tipo_transaccion_actual' => $tipo_transaccion

        ];
    }

    public function transacciones_get(Request $request)
    {
        // return $request;
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $categoria = $request->sltCategoria;
        $subcategoria = $request->sltSubcategoria;
        $usuario = $request->sltUsuario;
        $tipo = $request->tipo;

        if ($usuario == 0 && $categoria == 0 && $subcategoria == 0) {
            $transacciones = Transaccion::on($conexion)->select(
                'transaccion_registros.id',
                'transaccion_registros.tipo',
                'transaccion_registros.monto',
                'transaccion_registros.concepto',
                'transaccion_registros.documento',
                'transaccion_registros.datos_creacion',
                'transaccion_categorias.categoria',
                'transaccion_subcategorias.subcategoria',
                'us.usuario',
                'transaccion_comprobantes.comprobante',
                'at.area'
            )
                ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_registros.categoria_id')
                ->join('transaccion_subcategorias', 'transaccion_subcategorias.id', 'transaccion_registros.subcategoria_id')
                ->join('transaccion_comprobantes', 'transaccion_comprobantes.id', 'transaccion_registros.comprobante_id')
                ->join('solucion_master.usuarios as us', 'us.dni', 'transaccion_registros.usuario_id')
                ->join('solucion_master.areas_trabajo as at', 'at.id', 'transaccion_registros.area_trabajo_id')
                ->where('transaccion_registros.tipo', $tipo)
                ->whereBetween(DB::raw("SUBSTR(transaccion_registros.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $transacciones;
        }

        if ($usuario != 0 && $categoria == 0 && $subcategoria == 0) {
            $transacciones = Transaccion::on($conexion)->select(
                'transaccion_registros.id',
                'transaccion_registros.tipo',
                'transaccion_registros.monto',
                'transaccion_registros.concepto',
                'transaccion_registros.documento',
                'transaccion_registros.datos_creacion',
                'transaccion_categorias.categoria',
                'transaccion_subcategorias.subcategoria',
                'us.usuario',
                'transaccion_comprobantes.comprobante',
                'at.area'
            )
                ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_registros.categoria_id')
                ->join('transaccion_subcategorias', 'transaccion_subcategorias.id', 'transaccion_registros.subcategoria_id')
                ->join('transaccion_comprobantes', 'transaccion_comprobantes.id', 'transaccion_registros.comprobante_id')
                ->join('solucion_master.usuarios as us', 'us.dni', 'transaccion_registros.usuario_id')
                ->join('solucion_master.areas_trabajo as at', 'at.id', 'transaccion_registros.area_trabajo_id')
                ->where('transaccion_registros.tipo', $tipo)
                ->where(DB::raw("SUBSTRING(transaccion_registros.datos_creacion,42,8)"), $usuario)
                ->whereBetween(DB::raw("SUBSTR(transaccion_registros.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $transacciones;
        }
        if ($categoria != 0 && $usuario == 0 && $subcategoria == 0) {
            $transacciones = Transaccion::on($conexion)->select(
                'transaccion_registros.id',
                'transaccion_registros.tipo',
                'transaccion_registros.monto',
                'transaccion_registros.concepto',
                'transaccion_registros.documento',
                'transaccion_registros.datos_creacion',
                'transaccion_categorias.categoria',
                'transaccion_subcategorias.subcategoria',
                'us.usuario',
                'transaccion_comprobantes.comprobante',
                'at.area'
            )
                ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_registros.categoria_id')
                ->join('transaccion_subcategorias', 'transaccion_subcategorias.id', 'transaccion_registros.subcategoria_id')
                ->join('transaccion_comprobantes', 'transaccion_comprobantes.id', 'transaccion_registros.comprobante_id')
                ->join('solucion_master.usuarios as us', 'us.dni', 'transaccion_registros.usuario_id')
                ->join('solucion_master.areas_trabajo as at', 'at.id', 'transaccion_registros.area_trabajo_id')
                ->where('transaccion_registros.tipo', $tipo)
                ->where('transaccion_registros.categoria_id', $categoria)
                ->whereBetween(DB::raw("SUBSTR(transaccion_registros.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $transacciones;
        }
        if ($subcategoria != 0 && $usuario == 0 && $categoria == 0) {
            $transacciones = Transaccion::on($conexion)->select(
                'transaccion_registros.id',
                'transaccion_registros.tipo',
                'transaccion_registros.monto',
                'transaccion_registros.concepto',
                'transaccion_registros.documento',
                'transaccion_registros.datos_creacion',
                'transaccion_categorias.categoria',
                'transaccion_subcategorias.subcategoria',
                'us.usuario',
                'transaccion_comprobantes.comprobante',
                'at.area'
            )
                ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_registros.categoria_id')
                ->join('transaccion_subcategorias', 'transaccion_subcategorias.id', 'transaccion_registros.subcategoria_id')
                ->join('transaccion_comprobantes', 'transaccion_comprobantes.id', 'transaccion_registros.comprobante_id')
                ->join('solucion_master.usuarios as us', 'us.dni', 'transaccion_registros.usuario_id')
                ->join('solucion_master.areas_trabajo as at', 'at.id', 'transaccion_registros.area_trabajo_id')
                ->where('transaccion_registros.tipo', $tipo)
                ->where('transaccion_registros.subcategoria_id', $subcategoria)
                ->whereBetween(DB::raw("SUBSTR(transaccion_registros.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $transacciones;
        }
        // // --------------------
        if ($usuario != 0 && $categoria != 0 && $subcategoria == 0) {
            $transacciones = Transaccion::on($conexion)->select(
                'transaccion_registros.id',
                'transaccion_registros.tipo',
                'transaccion_registros.monto',
                'transaccion_registros.concepto',
                'transaccion_registros.documento',
                'transaccion_registros.datos_creacion',
                'transaccion_categorias.categoria',
                'transaccion_subcategorias.subcategoria',
                'us.usuario',
                'transaccion_comprobantes.comprobante',
                'at.area'
            )
                ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_registros.categoria_id')
                ->join('transaccion_subcategorias', 'transaccion_subcategorias.id', 'transaccion_registros.subcategoria_id')
                ->join('transaccion_comprobantes', 'transaccion_comprobantes.id', 'transaccion_registros.comprobante_id')
                ->join('solucion_master.usuarios as us', 'us.dni', 'transaccion_registros.usuario_id')
                ->join('solucion_master.areas_trabajo as at', 'at.id', 'transaccion_registros.area_trabajo_id')
                ->where('transaccion_registros.tipo', $tipo)
                ->where(DB::raw("SUBSTRING(transaccion_registros.datos_creacion,42,8)"), $usuario)
                ->where('transaccion_registros.categoria_id', $categoria)
                ->whereBetween(DB::raw("SUBSTR(transaccion_registros.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $transacciones;
        }
        if ($usuario != 0 && $categoria == 0 && $subcategoria != 0) {
            $transacciones = Transaccion::on($conexion)->select(
                'transaccion_registros.id',
                'transaccion_registros.tipo',
                'transaccion_registros.monto',
                'transaccion_registros.concepto',
                'transaccion_registros.documento',
                'transaccion_registros.datos_creacion',
                'transaccion_categorias.categoria',
                'transaccion_subcategorias.subcategoria',
                'us.usuario',
                'transaccion_comprobantes.comprobante',
                'at.area'
            )
                ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_registros.categoria_id')
                ->join('transaccion_subcategorias', 'transaccion_subcategorias.id', 'transaccion_registros.subcategoria_id')
                ->join('transaccion_comprobantes', 'transaccion_comprobantes.id', 'transaccion_registros.comprobante_id')
                ->join('solucion_master.usuarios as us', 'us.dni', 'transaccion_registros.usuario_id')
                ->join('solucion_master.areas_trabajo as at', 'at.id', 'transaccion_registros.area_trabajo_id')
                ->where('transaccion_registros.tipo', $tipo)
                ->where(DB::raw("SUBSTRING(transaccion_registros.datos_creacion,42,8)"), $usuario)
                ->where('transaccion_registros.subcategoria_id', $subcategoria)
                ->whereBetween(DB::raw("SUBSTR(transaccion_registros.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $transacciones;
        }
        if ($usuario == 0 && $categoria != 0 && $subcategoria != 0) {
            $transacciones = Transaccion::on($conexion)->select(
                'transaccion_registros.id',
                'transaccion_registros.tipo',
                'transaccion_registros.monto',
                'transaccion_registros.concepto',
                'transaccion_registros.documento',
                'transaccion_registros.datos_creacion',
                'transaccion_categorias.categoria',
                'transaccion_subcategorias.subcategoria',
                'us.usuario',
                'transaccion_comprobantes.comprobante',
                'at.area'

            )
                ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_registros.categoria_id')
                ->join('transaccion_subcategorias', 'transaccion_subcategorias.id', 'transaccion_registros.subcategoria_id')
                ->join('transaccion_comprobantes', 'transaccion_comprobantes.id', 'transaccion_registros.comprobante_id')
                ->join('solucion_master.usuarios as us', 'us.dni', 'transaccion_registros.usuario_id')
                ->join('solucion_master.areas_trabajo as at', 'at.id', 'transaccion_registros.area_trabajo_id')
                ->where('transaccion_registros.tipo', $tipo)
                ->where('transaccion_registros.categoria_id', $categoria)
                ->where('transaccion_registros.subcategoria_id', $subcategoria)
                ->whereBetween(DB::raw("SUBSTR(transaccion_registros.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $transacciones;
        }

        // //------------------------------
        if ($usuario != 0 && $categoria != 0 && $subcategoria != 0) {
            $transacciones = Transaccion::on($conexion)->select(
                'transaccion_registros.id',
                'transaccion_registros.tipo',
                'transaccion_registros.monto',
                'transaccion_registros.concepto',
                'transaccion_registros.documento',
                'transaccion_registros.datos_creacion',
                'transaccion_categorias.categoria',
                'transaccion_subcategorias.subcategoria',
                'us.usuario',
                'transaccion_comprobantes.comprobante',
                'at.area'
            )
                ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_registros.categoria_id')
                ->join('transaccion_subcategorias', 'transaccion_subcategorias.id', 'transaccion_registros.subcategoria_id')
                ->join('transaccion_comprobantes', 'transaccion_comprobantes.id', 'transaccion_registros.comprobante_id')
                ->join('solucion_master.usuarios as us', 'us.dni', 'transaccion_registros.usuario_id')
                ->join('solucion_master.areas_trabajo as at', 'at.id', 'transaccion_registros.area_trabajo_id')
                ->where('transaccion_registros.tipo', $tipo)
                ->where(DB::raw("SUBSTRING(transaccion_registros.datos_creacion,42,8)"), $usuario)
                ->where('transaccion_registros.categoria_id', $categoria)
                ->where('transaccion_registros.subcategoria_id', $subcategoria)
                ->whereBetween(DB::raw("SUBSTR(transaccion_registros.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $transacciones;
        }
    }
    public function guardar_transacciones(Request $request)
    {

        $agencia_id = session('id_agencia');

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);


        $agencia_busqueda_edicion = $request->agencia_busqueda_edicion;
        $conexion = 'master_' .  $agencia_busqueda_edicion;

        $frmDatosTransaccion = json_decode($request->frmDatosTransaccion);
        $transaccion_id = $frmDatosTransaccion->id;

        $categoria_id = $frmDatosTransaccion->categoria_id;
        $subcategoria_id = $frmDatosTransaccion->subcategoria_id;
        $agencia_id = $frmDatosTransaccion->agencia_id;
        $usuario_id = $frmDatosTransaccion->usuario_id;
        $area_trabajo_id = $frmDatosTransaccion->area_trabajo_id;
        $comprobante_id = $frmDatosTransaccion->comprobante_id;
        $concepto = mb_strtoupper($frmDatosTransaccion->concepto);

        Transaccion::on($conexion)->where('id', $transaccion_id)
            ->update(
                [
                    'categoria_id' => $categoria_id,
                    'subcategoria_id' => $subcategoria_id,
                    'agencia_id' => $agencia_id,
                    'usuario_id' => $usuario_id,
                    'area_trabajo_id' => $area_trabajo_id,
                    'comprobante_id' => $comprobante_id,
                    'concepto' => $concepto,

                    'datos_actualizacion' => $datos_registro
                ]

            );


        // dd($request);




    }

    public function exportar_transacciones(Request $request)
    {
        // return $request;
        // Ordenando array de datos-------------------------------
        // dd($request);

        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $tipo = $request->tipo_transaccion_actual;



        $datos_tabla_transacciones = json_decode($request->lista_transacciones);
        $totales = $request->totales;

        $data = [];
        $orden = 0;

        foreach ($datos_tabla_transacciones as $item) {
            $orden += 1;

            $object = (object)[
                'orden' => $orden,

                'categoria' => $item->categoria,
                'subcategoria' => $item->subcategoria,
                'conceptop' => $item->concepto,
                'fecha_registro' => $item->fecha_transaccion,
                'usuario' => $item->usuario,
                'monto' => $item->monto,
                'comprobante' => $item->comprobante,
                'area' => $item->area,
                'usuario_registro' => $item->usuario_registro,

            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptTransacciones.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];


        while ($celda <= 10) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 6)->exportArray();

            $formato_totales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;

            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        $sheet->removeRow(8);
        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('J2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        if ($tipo == 'E') {

            $sheet->setCellValue('B2', 'TRANSACCIONES - EGRESOS');
        } else {

            $sheet->setCellValue('B2', 'TRANSACCIONES - INGRESOS');
        }

        // Insertando datos-----------------------------
        $indice = 5;
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
        }


        $indice += 1;


        $columna_total = 7;
        $sheet->setCellValue((new CreditosController)->num2char($columna_total) . $indice, $totales);


        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }


        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptTransacciones', 5);


        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function transacciones_reales()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {


            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CONTABILIDAD_TRANSACCIONES_REALES', 'CREDITOS_REPORTES');



            if ($band == 1) {

                $agencias = Agencia::all();

                $categorias = [];
                $subcategorias = [];


                foreach ($agencias as $item) {
                    $agencia_id = $item->id_agencia;
                    $conexion = 'master_' . $agencia_id;

                    $lista_categorias = Categoria::on($conexion)
                        ->select(
                            'id',
                            'categoria',
                            'tipo',
                            DB::raw("$agencia_id as agencia_id")
                        )
                        ->orderBy('categoria', 'asc')
                        ->get();

                    $lista_subcategorias = SubCategoria::on($conexion)->from('transaccion_subcategorias as tra_sub')
                        ->select(
                            'tra_sub.id',
                            'tra_sub.categoria_id',
                            'tra_cat.categoria',
                            'tra_sub.subcategoria',
                            'tra_cat.tipo',
                            DB::raw("$agencia_id as agencia_id")
                        )
                        ->join('transaccion_categorias as tra_cat', 'tra_sub.categoria_id', 'tra_cat.id')
                        ->orderBy('tra_sub.subcategoria', 'asc')
                        ->get();





                    foreach ($lista_categorias as $item) {
                        $categorias[] = $item;
                    }
                    foreach ($lista_subcategorias as $item) {
                        $subcategorias[] = $item;
                    }
                }

                // dd($lista_categorias);




                return Inertia::render('Creditos/Reportes/Contabilidad/transacciones_reales', [
                    'categorias' => $categorias,
                    'subcategorias' => $subcategorias,
                ]);
            } else {
                return redirect('/');
            }
        }
    }

    public function buscar_transacciones_reales(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        // dd($request);


        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));
        $tipo_transaccion = $request->tipo_transaccion;


        $rango_registros = Transaccion::on($conexion)
            ->select('id')
            ->whereBetween('fecha_transaccion', [$fecha_desde, $fecha_hasta])
            ->where('tipo', $tipo_transaccion)
            ->get();


        // dd($rango_registros);


        $lista_transacciones_reales = Transaccion::on($conexion)->from('transaccion_registros as tra_reg')
            ->select(
                'tra_reg.id',
                'tra_reg.es_real',
                'tra_cat.categoria',
                'tra_sub.subcategoria',
                'tra_reg.concepto',
                'tra_reg.fecha_transaccion',
                'usu_1.usuario as a_usuario',
                'tra_reg.monto',
                'are_tra.area',
                'usu_2.usuario as caja_usuario',


            )
            ->leftjoin('transaccion_categorias as tra_cat', 'tra_reg.categoria_id', 'tra_cat.id')
            ->leftjoin('transaccion_subcategorias as tra_sub', 'tra_reg.subcategoria_id', 'tra_sub.id')
            ->leftjoin('solucion_master.usuarios as usu_1', 'tra_reg.usuario_id', 'usu_1.dni')
            ->leftjoin('caja_registros as caj_reg', 'caj_reg.id', 'tra_reg.caja_id')
            ->leftjoin('solucion_master.usuarios as usu_2', 'caj_reg.dni', 'usu_2.dni')
            ->leftjoin('solucion_master.areas_trabajo as are_tra', 'tra_reg.area_trabajo_id', 'are_tra.id')
            ->whereIn('tra_reg.id', $rango_registros)
            ->where('tra_reg.es_real', 1)
            ->orderBy('fecha_transaccion', 'desc')
            ->get();

        $lista_transacciones_no_reales = Transaccion::on($conexion)->from('transaccion_registros as tra_reg')
            ->select(
                'tra_reg.id',
                'tra_reg.es_real',
                'tra_cat.categoria',
                'tra_sub.subcategoria',
                'tra_reg.concepto',
                'tra_reg.fecha_transaccion',
                'usu_1.usuario as a_usuario',
                'tra_reg.monto',
                'are_tra.area',
                'usu_2.usuario as caja_usuario',
            )
            ->leftjoin('transaccion_categorias as tra_cat', 'tra_reg.categoria_id', 'tra_cat.id')
            ->leftjoin('transaccion_subcategorias as tra_sub', 'tra_reg.subcategoria_id', 'tra_sub.id')
            ->leftjoin('solucion_master.usuarios as usu_1', 'tra_reg.usuario_id', 'usu_1.dni')
            ->leftjoin('caja_registros as caj_reg', 'caj_reg.id', 'tra_reg.caja_id')
            ->leftjoin('solucion_master.usuarios as usu_2', 'caj_reg.dni', 'usu_2.dni')
            ->leftjoin('solucion_master.areas_trabajo as are_tra', 'tra_reg.area_trabajo_id', 'are_tra.id')
            ->whereIn('tra_reg.id', $rango_registros)
            ->where('tra_reg.es_real', 0)
            ->orderBy('fecha_transaccion', 'desc')
            ->get();

        return [
            'lista_transacciones_reales' => $lista_transacciones_reales,
            'lista_transacciones_no_reales' => $lista_transacciones_no_reales,
        ];
    }

    public function enviar_transacciones_reales(Request $request)
    {


        $response = new \stdClass();

        $modo = $request->modo;
        $lista_transacciones_edicion = json_decode($request->lista_transacciones_edicion);

        // dd($lista_transacciones_edicion);

        $agencia_busqueda = $request->agencia_busqueda;
        $conexion = 'master_' .  $agencia_busqueda;

        $datos_registro = (new CreditosController)->datos_registro($agencia_busqueda);

        if ($modo == "A_NOREAL") {

            Transaccion::on($conexion)->whereIn('id', $lista_transacciones_edicion)
                ->update(
                    [
                        'es_real' => 0,
                        'datos_actualizacion' => $datos_registro
                    ]
                );
        };

        if ($modo == "A_REAL") {

            Transaccion::on($conexion)->whereIn('id', $lista_transacciones_edicion)
                ->update(
                    [
                        'es_real' => 1,
                        'datos_actualizacion' => $datos_registro
                    ]
                );
        };

        $response->success = true;
        return $response;
    }
}
