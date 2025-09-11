<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Http\Controllers\General\PermisosController;
use App\Models\Logistica\Estado;
use Illuminate\Http\Request;

use Inertia\Inertia;

class LogisticaEstadoController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function estados()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ESTADOS',  'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $estados = Estado::all();
                return Inertia::render(
                    'Logistica/Mantenimiento/estados',
                    ['estados' => $estados]
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
        $estado = $request->estado;

        $estado_existe = Estado::select('id')->where('estado', $estado)->get();

        if ($modo == "EDITAR") {
            if (count($estado_existe) > 0) {
                if ($estado_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($estado_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)

    {
        $datos_registro = (new LogisticaController)->datos_registro();

        $modo = $request->modo;
        $estado = mb_strtoupper($request->estado);

        $descripcion = (new LogisticaController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Estado::create(array(
                'estado' =>  $estado,
                'descripcion' => $descripcion,
                'habilitado' => 1,
                'datos_creacion' => $datos_registro,
            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Estado::where('id', $id)
                ->update([
                    'estado' =>  $estado,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('log.man.estados');
    }
}
