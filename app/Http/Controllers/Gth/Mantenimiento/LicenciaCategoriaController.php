<?php

namespace App\Http\Controllers\Gth\Mantenimiento;


use App\Http\Controllers\Gth\GthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;

use App\Models\Gth\Mantenimiento\Solicitudes\LicenciaCategoria;

use App\Models\General\Agencia;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;
use Illuminate\Http\Request;

class LicenciaCategoriaController extends Controller
{



    public function listar_categorias()
    {
        $categorias = LicenciaCategoria::orderBy('abreviacion', 'asc')->get();

        // dd($categorias);
        return ['categorias' => $categorias];
    }




    // LICENCIA CATEGORIAS -----------------------------------------------------------

    public function licencia_categorias()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'LICENCIA_CATEGORIAS', 'GTH_MANTENIMIENTO');
            if ($band == 1) {
                return Inertia('Gth/Mantenimiento/Solicitud/licencia_categorias');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function verificar_categorias(Request $request)
    {
        $modo = $request->modo;
        $id = $request->id;
        $abreviacion = $request->abreviacion;

        $resultado = 'NO EXISTE';
        if ($modo == 'NUEVO') {
            $categoria_existe = LicenciaCategoria::select('id')->where('abreviacion', $abreviacion)->get();
            if (count($categoria_existe) > 0) {
                $resultado = 'EXISTE';
            } else {
                $resultado = 'NO EXISTE';
            }
        } else if ($modo == 'EDITAR') {
            $categoria_existe = LicenciaCategoria::select('id')->where('abreviacion', $abreviacion)->get();
            if (count($categoria_existe) > 0) {
                if ($categoria_existe[0]['id'] == $id) {
                    $resultado = 'NO EXISTE';
                } else {
                    $resultado = 'EXISTE';
                }
            } else {
                $resultado = 'NO EXISTE';
            }
        }
        // dd($resultado);
        return $resultado;
    }

    public function guardar_categorias(Request $request)
    {

        $response = new \stdClass();
        $datos_registro = (new GthController)->datos_registro();
        $abreviacion = strtoupper($request->abreviacion);
        $nombre = strtoupper($request->nombre);
        $habilitado = $request->habilitado;
        $modo = $request->modo;

        if ($habilitado == "false") {
            $habilitado = 0;
        }
        if ($habilitado == "true") {
            $habilitado = 1;
        }

        if ($modo == 'NUEVO') {
            LicenciaCategoria::create(array(
                'nombre' => $nombre,
                'abreviacion' => $abreviacion,
                'datos_creacion' => $datos_registro
            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            LicenciaCategoria::where('id', $id)->update([
                'nombre' => $nombre,
                'abreviacion' => $abreviacion,
                'habilitado' => $habilitado,

                'datos_actualizacion' => $datos_registro
            ]);
        }

        $response->success = true;

        // dd($response);

        return $response;
    }


}
