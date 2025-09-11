<?php

namespace App\Http\Controllers\Logistica\Suministros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Models\Logistica\Suministros\Medicion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuministroMedicionController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function mediciones()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'SUMINISTROS_MEDICIONES', 'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $mediciones = Medicion::all();
                return Inertia::render(
                    'Logistica/Mantenimiento/suministros_mediciones',
                    ['mediciones' => $mediciones]
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
        $medida = $request->medida;

        $medida_existe = Medicion::select('id')->where('medicion', $medida)->get();

        if ($modo == "EDITAR") {
            if (count($medida_existe) > 0) {
                if ($medida_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($medida_existe) > 0) {
                $resultado = "EXISTE";
            }
        }

        // dd($resultado);
        return $resultado;
    }

    public function guardar(Request $request)

    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $medida = mb_strtoupper($request->medida);

        $escala = (new LogisticaController)->verificar_nulo($request->escala);
        if ($escala != null) {
            $escala = mb_strtoupper($request->escala);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Medicion::create(
                [
                    'medicion' => $medida,
                    'escala' => $escala,
                    'habilitado' => 1,
                    'datos_creacion' => $datos_registro,
                ]

            );
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Medicion::where('id', $id)
                ->update([
                    'medicion' => $medida,
                    'escala' => $escala,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('log.man.sum_mediciones');
    }
    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------
}
