<?php

namespace Modules\Logistica\Presentation\Controllers\Activos;

use Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Tipo;
use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Illuminate\Http\Request;
use Modules\Logistica\Presentation\Controllers\LogisticaController;

use Inertia\Inertia;

class ActivoTipoController extends Controller
{
    public function tipos()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ACTIVOS_TIPOS',  'LOGISTICA_MANTENIMIENTO');
            if ($band == 1) {
                $tipos = Tipo::all();
                return Inertia::render(
                    'Logistica/Mantenimiento/activos_tipos',
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

    public function verificar(Request $request)
    {
        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $tipo = $request->tipo;
        $abreviacion = $request->abreviacion;

        $tipo_existe = Tipo::select('id')
            ->where('tipo', $tipo)
            ->orwhere('abreviacion', $abreviacion)
            ->get();
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
        $abreviacion = mb_strtoupper($request->abreviacion);

        $descripcion = (new LogisticaController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Tipo::create(array(
                'tipo' =>  $tipo,
                'abreviacion' =>  $abreviacion,
                'descripcion' => $descripcion,
                'habilitado' => 1,
                'datos_creacion' => $datos_registro,
            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Tipo::where('id', $id)->update([
                'tipo' =>  $tipo,
                'abreviacion' =>  $abreviacion,
                'descripcion' => $descripcion,
                'habilitado' => $habilitado,
                'datos_actualizacion' => $datos_registro,
            ]);
        }
        return redirect()->route('log.man.act_tipos');
    }
}
