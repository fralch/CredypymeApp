<?php

namespace App\Http\Controllers\Logistica\Activos;


use App\Models\Logistica\Activos\Categoria;
use App\Models\Logistica\Activos\Nombre;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use Illuminate\Http\Request;
use App\Http\Controllers\Logistica\LogisticaController;

use Inertia\Inertia;

class ActivoNombreController extends Controller
{

    public function nombres()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ACTIVOS_NOMBRES',  'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $nombres = Nombre::from('activo_nombres as an')
                    ->select(
                        'an.id',
                        'an.nombre',
                        'an.categoria_id',
                        'ac.categoria',
                        'an.descripcion',
                        'an.habilitado'

                    )
                    ->join('activo_categorias as ac', 'an.categoria_id', 'ac.id')
                    ->get();

                $categorias = Categoria::select('id', 'categoria')->where('habilitado', 1)->get();
                return Inertia::render(
                    'Logistica/Mantenimiento/activos_nombres',
                    [
                        'nombres' => $nombres,
                        'categorias' => $categorias
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
        $nombre = $request->nombre_activo;

        $nombres_existe = Nombre::select('id')
            ->where('nombre',  $nombre)
            ->get();

        if ($modo == "EDITAR") {
            if (count($nombres_existe) > 0) {
                if ($nombres_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($nombres_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $nombre = mb_strtoupper($request->nombre);
        $categoria_id = $request->categoria_id;
        $descripcion = (new LogisticaController)->verificar_nulo($request->descripcion);

        if ($descripcion != null) {
            $descripcion = mb_strtoupper($descripcion);
        }

        $habilitado = $request->habilitado;


        if ($modo == "NUEVO") {
            Nombre::create(array(
                'nombre' =>  $nombre,
                'categoria_id' => $categoria_id,
                'habilitado' => 1,
                'descripcion' => $descripcion,
                'datos_creacion' => $datos_registro,

            ));
        } else if ($modo == "EDITAR") {
            $id = $request->id;
            Nombre::where('id', $id)
                ->update([
                    'nombre' =>  $nombre,
                    'categoria_id' => $categoria_id,
                    'habilitado' => $habilitado,
                    'descripcion' => $descripcion,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('log.man.act_nombres');
    }
}
