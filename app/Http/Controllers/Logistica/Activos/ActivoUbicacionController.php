<?php

namespace App\Http\Controllers\Logistica\Activos;

use App\Models\Logistica\Activos\Ubicacion;
use App\Models\General\Agencia;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use Illuminate\Http\Request;
use App\Http\Controllers\Logistica\LogisticaController;


use Inertia\Inertia;

class ActivoUbicacionController extends Controller
{
    public function ubicaciones()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ACTIVOS_UBICACIONES',  'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $ubicaciones =  Ubicacion::from('activo_ubicaciones as au')
                    ->select(
                        'au.id',
                        'au.ubicacion',
                        'au.abreviacion',
                        'au.descripcion',
                        'au.habilitado',
                        'au.agencia_id',
                        'ag.nombre as agencia',

                    )
                    ->join('agencias as ag', 'au.agencia_id', 'ag.id_agencia')->get();

                $agencias = Agencia::orderBy('nombre', 'asc')->get();

                return Inertia::render(
                    'Logistica/Mantenimiento/activos_ubicaciones',
                    [
                        'ubicaciones' => $ubicaciones,
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

    public function verificar(Request $request)
    {
        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $ubicacion = $request->ubicacion;
        $abreviacion = $request->abreviacion;
        $agencia_id = $request->agencia_id;

        $ubicacion_existe = Ubicacion::select('id')
            ->where([['ubicacion',  $ubicacion], ['agencia_id', $agencia_id]])
            ->orwhere([['abreviacion',  $abreviacion], ['agencia_id', $agencia_id]])
            ->get();

        if ($modo == "EDITAR") {
            if (count($ubicacion_existe) > 0) {
                if ($ubicacion_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($ubicacion_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;

        $ubicacion = mb_strtoupper($request->ubicacion);
        $abreviacion = mb_strtoupper($request->abreviacion);

        $descripcion = (new LogisticaController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;
        $agencia_id = $request->agencia_id;


        if ($modo == "NUEVO") {
            Ubicacion::create(array(
                'ubicacion' =>  $ubicacion,
                'abreviacion' =>  $abreviacion,
                'agencia_id' => $agencia_id,
                'descripcion' => $descripcion,
                'habilitado' => 1,
                'datos_creacion' => $datos_registro,

            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Ubicacion::where('id', $id)
                ->update([
                    'ubicacion' =>  $ubicacion,
                    'abreviacion' =>  $abreviacion,
                    'agencia_id' => $agencia_id,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('log.man.act_ubicaciones');
    }
}
