<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Models\Logistica\Suministros\Tipo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuministroTipoController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function tipos()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'SUMINISTROS_TIPOS', 'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $tipos = Tipo::all();
                return Inertia::render(
                    'Logistica/Mantenimiento/suministros_tipos',
                    ['tipos' => $tipos]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------
    public function verificar(Request $request)

    {
        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $tipo = $request->tipo;

        $tipo_existe = Tipo::select('id')->where('tipo', $tipo)->get();

        if ($modo == "EDITAR") {
            if (count($tipo_existe) > 0) {
                if ($tipo_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($tipo_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)

    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $tipo = mb_strtoupper($request->tipo);

        $descripcion = (new LogisticaController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Tipo::create(
                [
                    'tipo' => $tipo,
                    'descripcion' => $descripcion,
                    'habilitado' => 1,
                    'datos_creacion' => $datos_registro,
                ]

            );
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Tipo::where('id', $id)
                ->update([
                    'tipo' => $tipo,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('log.man.sum_tipos');
    }
    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------
}
