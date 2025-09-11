<?php

namespace App\Http\Controllers\Gth\ColaboradorMes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gth\GthController;
use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\ColaboradorMes\Nivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ColaboradorMesNivelController extends Controller
{

    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------

    public function niveles()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'COLABORADOR_MES_NIVELES', 'GTH_MANTENIMIENTO');
            if ($band == 1) {
                return Inertia('Gth/Mantenimiento/ColaboradorMes/niveles');
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
    public function listar_recursos()
    {
        $niveles = Nivel::orderBy('nivel', 'asc')->get();
        return ['niveles' => $niveles];
    }
    public function verificar(Request $request)
    {
        $modo = $request->modo;
        $id = $request->id;
        $nivel = $request->nivel;

        $resultado = 'NO EXISTE';
        if ($modo == 'NUEVO') {
            $nivel_existe = Nivel::select('id')->where('nivel', $nivel)->get();
            if (count($nivel_existe) > 0) {
                $resultado = 'EXISTE';
            } else {
                $resultado = 'NO EXISTE';
            }
        } else if ($modo == 'EDITAR') {
            $nivel_existe = Nivel::select('id')->where('nivel', $nivel)->get();
            if (count($nivel_existe) > 0) {
                if ($nivel_existe[0]['id'] == $id) {
                    $resultado = 'NO EXISTE';
                } else {
                    $resultado = 'EXISTE';
                }
            } else {
                $resultado = 'NO EXISTE';
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $response = new \stdClass();
        $datos_registro = (new GthController)->datos_registro();
        $nivel = strtoupper($request->nivel);
        $habilitado = $request->habilitado;
        $lista_escalas = $request->lista_escalas;
        $modo = $request->modo;

        if ($habilitado == "false") {
            $habilitado = 0;
        }
        if ($habilitado == "true") {
            $habilitado = 1;
        }

        if ($modo == 'NUEVO') {
            Nivel::create(array(
                'nivel' => $nivel,
                'habilitado' => $habilitado,
                'descripcion' => $lista_escalas,
                'datos_creacion' => $datos_registro
            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Nivel::where('id', $id)->update([
                'nivel' => $nivel,
                'habilitado' => $habilitado,
                'descripcion' => $lista_escalas,
                'datos_actualizacion' => $datos_registro
            ]);
        }
        $response->success = true;

        return $response;
    }
}
