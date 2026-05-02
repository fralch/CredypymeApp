<?php

namespace Modules\Logistica\Presentation\Controllers\Activos;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Compra;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\CompraDetalle;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Estado;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Http\Controllers\Controller;
use Modules\Logistica\Presentation\Controllers\LogisticaController;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Activo;

use Illuminate\Support\Facades\DB;

class ActivoCompraController extends Controller
{
    public function historial_compras()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_COMPRAS_LOCAL',  'LOGISTICA_ACTIVOS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_COMPRAS_GENERAL',  'LOGISTICA_ACTIVOS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'HISTORIAL_COMPRAS',  'LOGISTICA_ACTIVOS');

            if ($band == 1) {

                return Inertia::render(
                    'Logistica/Activos/Historial/historial_compras',
                    []
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function comprar(Request $request)
    {

        // dd($request);
        $datos_registro = (new LogisticaController)->datos_registro();
        $tipo_modulo = $request->tipo_modulo;
        $canasta = json_decode($request->canasta);
        $usuario_compra = $request->usuario_compra;

        $compra = Compra::create(array(
            'agencia_id' => 5, //La agencia por defecto es Administrativa, su id = 5
            'usuario_compra' => $usuario_compra,
            'datos_creacion' => $datos_registro
        ));

        $compra_id = $compra->id;

        $estado = Estado::select('id')->where('estado', 'DISPONIBLE')->get()->last();

        $estado_id = $estado->id;
        $path_name = pathinfo($_FILES['documento']['name']);
        $extension = "." . $path_name['extension'];
        $nombre_documento = "act_compra_" . $compra_id . $extension;
        $archivo = $_FILES['documento']['tmp_name'];
        $ruta = '/imagenes_server/logistica/activos/compras';
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
        move_uploaded_file($archivo, $ruta);

        Compra::where('id', $compra_id)
            ->update(['documento' => $nombre_documento]);

        foreach ($canasta as $item) {

            $agencia_id = 5;
            $nombre_id = $item->nombre_id;
            $codigo = $item->codigo->codigo;
            $tipo_id = $item->tipo_id;
            $responsable_id = $item->responsable_id;
            $ubicacion_id = $item->ubicacion_id;
            $descripcion = mb_strtoupper($item->descripcion);
            $cantidad = $item->cantidad;
            $marca = (new LogisticaController)->verificar_nulo($item->marca);
            if (!$marca == null) {
                $marca = mb_strtoupper($marca);
            }

            $modelo = (new LogisticaController)->verificar_nulo($item->modelo);
            if (!$modelo == null) {
                $modelo = mb_strtoupper($modelo);
            }

            $placa = (new LogisticaController)->verificar_nulo($item->placa);
            if (!$placa == null) {
                $placa = mb_strtoupper($placa);
            }

            $caracteristicas = (new LogisticaController)->verificar_nulo($item->caracteristicas);
            if (!$caracteristicas == null) {
                $caracteristicas = mb_strtoupper($caracteristicas);
            }

            $color = mb_strtoupper($item->color);
            $condicion_id = $item->condicion_id;

            $fecha_compra = $item->fecha_compra;
            $valor_compra = $item->valor_compra;
            $igv = $item->igv;
            $vida_util = $item->vida_util;
            $porcentaje_depreciacion = $item->depreciacion;

            $activo = Activo::create(
                [
                    'agencia_id' => $agencia_id,
                    'nombre_id' => $nombre_id,
                    'codigo' => $codigo,
                    'tipo_id' => $tipo_id,
                    'responsable_id' => $responsable_id,
                    'ubicacion_id' => $ubicacion_id,
                    'descripcion' => $descripcion,
                    'cantidad' => $cantidad,
                    'marca' => $marca,
                    'modelo' => $modelo,
                    'placa' => $placa,
                    'caracteristicas' => $caracteristicas,
                    'color' => $color,
                    'condicion_id' => $condicion_id,
                    'estado_id' => $estado_id,
                    'fecha_compra' => $fecha_compra,
                    'valor_compra' => $valor_compra,
                    'vida_util' => $vida_util,
                    'porcentaje_depreciacion' => $porcentaje_depreciacion,
                    'valor_actual' => $valor_compra,
                    'datos_creacion' => $datos_registro
                ]
            );

            $activo_id = $activo->id;

            CompraDetalle::create([
                'compra_id' => $compra_id,
                'activo_id' => $activo_id,
                'cantidad' => $cantidad,
                'valor_unitario' => $valor_compra,
                'igv' => $igv,
                'datos_creacion' => $datos_registro
            ]);
        }

        return redirect()->route('log.act.inventario', $tipo_modulo);
    }

    public function buscar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $lista_compras = Compra::from('activo_compras as act_com')
            ->select(
                'act_com.id',
                'act_com.agencia_id',
                'act_com.usuario_compra',
                'act_com.documento',
                'act_com.datos_creacion',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_compra',
                'us_2.usuario as usuario_registro'
            )
            ->join('agencias as ag', 'act_com.agencia_id',  'ag.id_agencia')
            ->join('usuarios as us_1', 'act_com.usuario_compra',  'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(act_com.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['act_com.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_com.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_com.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])

            ->get();

        $lista_compras_detalles = CompraDetalle::from('activo_compras_detalles as act_com_det')
            ->select(
                'act_com_det.id',
                'act_com_det.compra_id',
                'act_com_det.activo_id',
                'act_com_det.cantidad',
                'act_com_det.valor_unitario',
                'act_com_det.igv',

                'act_com.agencia_id',
                'act_com.usuario_compra',
                'act_com.documento',
                'act_com.datos_creacion',

                'act_inv.codigo',
                'act_inv.descripcion',

                'act_inv.marca',
                'act_inv.modelo',
                'act_inv.placa',
                'act_inv.caracteristicas',
                'act_inv.color',

                'log_con.condicion',

                'ag.nombre as agencia',
                'us_1.usuario as usuario_compra',
                'us_2.usuario as usuario_registro'
            )
            ->join('activo_compras as act_com', 'act_com_det.compra_id',  'act_com.id')
            ->join('activo_inventario as act_inv', 'act_com_det.activo_id',  'act_inv.id')
            ->join('logistica_condiciones as log_con', 'act_inv.condicion_id',  'log_con.id')
            ->join('agencias as ag', 'act_com.agencia_id',  'ag.id_agencia')
            ->join('usuarios as us_1', 'act_com.usuario_compra',  'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(act_com.datos_creacion,42,8)"), 'us_2.dni')
            ->where([
                ['act_com.agencia_id', $agencia_id],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_com.datos_creacion,11,19), '%Y-%m-%d')"), '>=', $fecha_desde],
                [DB::raw("STR_TO_DATE(SUBSTRING(act_com.datos_creacion,11,19), '%Y-%m-%d')"), '<=', $fecha_hasta]
            ])
            ->get();

        return [
            'lista_compras' => $lista_compras,
            'lista_compras_detalles' => $lista_compras_detalles
        ];
    }
}
