<?php

namespace Modules\Logistica\Presentation\Controllers\Activos;

use Modules\Logistica\Infrastructure\Persistence\Eloquent\Responsable;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Estado;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Condicion;

use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Tipo;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Ubicacion;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Nombre;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Activo;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Logistica\Presentation\Controllers\LogisticaController;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ActivoInventarioController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function inventario()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            //$band = 0;
            // if ($tipo_modulo == 'LOCAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'INVENTARIO_LOCAL',  'LOGISTICA_ACTIVOS');
            // } else if ($tipo_modulo == 'GENERAL') {
            //     $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'INVENTARIO_GENERAL',  'LOGISTICA_ACTIVOS');
            // }
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'INVENTARIO',  'LOGISTICA_ACTIVOS');
            //dd($result);
            // foreach ($result as $k => $v){
            //     $band = $v;
            //     $acceso_agencias = $aea['acceso_agencias'];
            // }
            if ($band == 1) {

                // $acceso_agencias = [];
                // foreach($result['acceso_agencias'] as $key){
                //     $acceso_agencias[] = $key->agencia_id;
                // }
                //$acceso_agencias = $result['acceso_agencias'];
                //dd($acceso_agencias);
                //if ($tipo_modulo == 'LOCAL') {

                // } else {
                //     $agencias = Agencia::from('agencias as ag')
                //         ->select(
                //             'ag.id_agencia',
                //             'ag.nombre as nombre',
                //         )
                //         ->orderBy('ag.nombre', 'asc')
                //         ->get();
                // }

                $tipos = Tipo::select(
                    'id',
                    'tipo',
                    'abreviacion'
                )
                    ->where('habilitado', 1)->get();

                $ubicaciones = Ubicacion::from('activo_ubicaciones as act_ubi')
                    ->select(
                        'act_ubi.id',
                        'act_ubi.ubicacion',
                        'act_ubi.abreviacion',
                        'act_ubi.agencia_id',
                        'ag.nombre as agencia'
                    )
                    ->join('agencias as ag', 'act_ubi.agencia_id', 'ag.id_agencia')
                    ->where('act_ubi.habilitado', 1)
                    ->get();

                $nombres = Nombre::from('activo_nombres as act_nom')
                    ->select(
                        'act_nom.id',
                        'act_nom.nombre',
                        'act_cat.vida_util'
                    )
                    ->join('activo_categorias as act_cat', 'act_nom.categoria_id', 'act_cat.id')
                    ->where('act_nom.habilitado', 1)
                    ->get();


                $usuarios = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id'
                )
                    ->where('habilitado', 1)
                    ->orderBy('usuario', 'asc')->get();

                $responsables = Responsable::from('logistica_responsables as log_res')
                    ->select(
                        'log_res.id',
                        'log_res.responsable',
                        'log_res.abreviacion',
                        'log_res.agencia_id',
                        'log_res.usuario_id',
                        'log_res.encargado_agencia',

                        'us.usuario'
                    )
                    ->join('usuarios as us', 'log_res.usuario_id', 'us.dni')
                    ->where('log_res.habilitado', 1)
                    ->get();

                $condiciones = Condicion::select(
                    'id',
                    'condicion'
                )
                    ->where('habilitado', 1)
                    ->orderBy('id', 'asc')
                    ->get();

                return Inertia::render(
                    'Logistica/Activos/inventario',
                    [
                        //'tipo_modulo' => $tipo_modulo,

                        'tipos' => $tipos,
                        'nombres' => $nombres,
                        'ubicaciones' => $ubicaciones,
                        'usuarios' => $usuarios,
                        'responsables' => $responsables,
                        'condiciones' => $condiciones,
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

    public function listar($agencia_id, $estado)
    {
        $operador = '>';
        $estado_id = 0;

        if ($estado != 'TODOS') {
            $operador = '=';

            $estado_id = Estado::select('id')->where('estado', $estado)->get()->last();
            $estado_id = $estado_id->id;
        };

        $activos = Activo::from('activo_inventario as act_inv')
            ->select(
                'act_inv.id',
                'act_inv.agencia_id',
                'act_inv.nombre_id',
                'act_inv.codigo',
                'act_inv.tipo_id',
                'act_inv.responsable_id',
                'act_inv.ubicacion_id',
                'act_inv.descripcion',
                'act_inv.cantidad',
                'act_inv.marca',
                'act_inv.modelo',
                'act_inv.placa',
                'act_inv.caracteristicas',
                'act_inv.color',
                'act_inv.condicion_id',
                'act_inv.estado_id',
                DB::raw("STR_TO_DATE(act_inv.fecha_compra, '%Y-%m-%d') as fecha_compra"),
                'act_inv.valor_compra',
                'act_inv.vida_util',
                'act_inv.porcentaje_depreciacion',
                'act_inv.valor_actual',
                'act_inv.fecha_ultimo_inventario',
                'act_inv.datos_creacion',
                'act_inv.datos_actualizacion',
                'act_inv.created_at',
                'act_inv.updated_at',

                'ag_1.nombre as agencia',
                'act_nom.nombre',
                'act_tip.tipo',
                'act_tip.abreviacion as abreviacion_tipo',
                'log_res.responsable',
                'log_res.abreviacion as abreviacion_responsable',
                'us.usuario as usuario_responsable',
                'act_ubi.ubicacion',
                'act_ubi.abreviacion as abreviacion_ubicacion',
                'ag_2.nombre as agencia_ubicacion',

                'log_est.estado',
                'log_con.condicion',
                DB::raw("ROUND(act_inv.valor_actual,2) as valor_venta")
            )
            ->join('agencias as ag_1', 'act_inv.agencia_id', 'ag_1.id_agencia')
            ->join('activo_nombres as act_nom', 'act_inv.nombre_id', 'act_nom.id')
            ->join('activo_tipos as act_tip', 'act_inv.tipo_id', 'act_tip.id')
            ->join('logistica_responsables as log_res', 'act_inv.responsable_id', 'log_res.id')
            ->join('usuarios as us', 'log_res.usuario_id', 'us.dni')
            ->join('activo_ubicaciones as act_ubi', 'act_inv.ubicacion_id', 'act_ubi.id')
            ->join('agencias as ag_2', 'act_ubi.agencia_id', 'ag_2.id_agencia')
            ->join('logistica_estados as log_est', 'act_inv.estado_id', 'log_est.id')
            ->join('logistica_condiciones as log_con', 'act_inv.condicion_id', 'log_con.id')
            ->where([['act_inv.agencia_id', $agencia_id], ['act_inv.estado_id', $operador, $estado_id]])
            ->get();

        return $activos;
    }

    public function editar(Request $request)
    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $tipo_modulo = $request->tipo_modulo;
        $activo_id = $request->activo_id;
        $nombre_id = $request->nombre_id;
        $descripcion = mb_strtoupper($request->descripcion);
        $codigo = json_decode($request->codigo)[0]->codigo;
        $marca = (new LogisticaController)->verificar_nulo($request->marca);
        if (!$marca == null) {
            $marca = mb_strtoupper($marca);
        }

        $modelo = (new LogisticaController)->verificar_nulo($request->modelo);
        if (!$modelo == null) {
            $modelo = mb_strtoupper($modelo);
        }

        $placa = (new LogisticaController)->verificar_nulo($request->placa);
        if (!$placa == null) {
            $placa = mb_strtoupper($placa);
        }

        $caracteristicas = (new LogisticaController)->verificar_nulo($request->caracteristicas);
        if (!$caracteristicas == null) {
            $caracteristicas = mb_strtoupper($caracteristicas);
        }

        $condicion_id = $request->condicion_id;
        $color = mb_strtoupper($request->color);

        Activo::where('id', $activo_id)->update([
            'nombre_id' => $nombre_id,
            'descripcion' => $descripcion,
            'codigo' => $codigo,
            'marca' => $marca,
            'modelo' => $modelo,
            'placa' => $placa,
            'caracteristicas' => $caracteristicas,
            'condicion_id' => $condicion_id,
            'color' => $color,
            'datos_actualizacion' => $datos_registro,
        ]);
        return redirect()->route('log.act.inventario', $tipo_modulo);
    }

    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    public function obtener_ultimo()
    {
        $ultimo = Activo::latest('id')->first();
        if ($ultimo == null) {
            $ultimo = 0;
        } else {
            $ultimo = $ultimo->id;
        }
        return $ultimo;
    }
    // --------------------------------------------------------------------------
}
