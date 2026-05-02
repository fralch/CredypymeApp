<?php

namespace Modules\Logistica\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Modules\Logistica\Presentation\Controllers\LogisticaController;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Condicion;
use Illuminate\Http\Request;

use Inertia\Inertia;

class LogisticaCondicionController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function condiciones()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CONDICIONES',  'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $condiciones = Condicion::all();
                return Inertia::render(
                    'Logistica/Mantenimiento/condiciones',
                    ['condiciones' => $condiciones]
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
        $condicion = $request->condicion;

        $condicion_existe = Condicion::select('id')->where('condicion', $condicion)->get();

        if ($modo == "EDITAR") {
            if (count($condicion_existe) > 0) {
                if ($condicion_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($condicion_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)

    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $condicion = mb_strtoupper($request->condicion);

        $descripcion = (new LogisticaController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Condicion::create(array(
                'condicion' => $condicion,
                'descripcion' => $descripcion,
                'habilitado' => 1,
                'datos_creacion' => $datos_registro,
            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Condicion::where('id', $id)
                ->update([
                    'condicion' => $condicion,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('log.man.condiciones');
    }
}
