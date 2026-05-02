<?php

namespace Modules\Creditos\Presentation\Controllers\Mantenimiento\Credito;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Garantia;


use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CreditoGarantiaController extends Controller
{
    public function garantias()
    {
        $x = session()->all();
        $conexion = 'master_' .  $x['id_agencia'];

        if (empty($x['usuario_dni'])) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITO_GARANTIAS', 'CREDITOS_MANTENIMIENTO');
            if ($band == 1) {
                $garantias = Garantia::on($conexion)->select()->get();
                return Inertia::render(
                    'Creditos/Mantenimiento/credito_garantias',
                    ['garantias' => $garantias]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------
    public function listarGarantias(Request $request)
    {
        // return $request;
        $id_agencia = $request->id_agencia;
        $conexion = 'master_' .  $id_agencia;
        $garantias = Garantia::on($conexion)->select()->get();
        return $garantias;
    }
    public function verificar(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $garantia = $request->garantia;

        $garantia_existe = Garantia::on($conexion)->select('id')->where('garantia', $garantia)->get();

        if ($modo == "EDITAR") {
            if (count($garantia_existe) > 0) {
                if ($garantia_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($garantia_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));

        $modo = $request->modo;
        $garantia = mb_strtoupper($request->garantia);
        $descripcion = mb_strtoupper($request->descripcion);
        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Garantia::on($conexion)->create(
                [
                    'garantia' => $garantia,
                    'descripcion' => $descripcion,
                    'habilitado' => 1,
                    'datos_creacion' => $datos_registro,
                ]

            );
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Garantia::on($conexion)->where('id', $id)
                ->update([
                    'garantia' => $garantia,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('man.cre_garantias');
    }
    // --------------------------------------------------------------------------
}
