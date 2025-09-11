<?php

namespace App\Http\Controllers\Creditos\Mantenimiento\Credito;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Mantenimiento\Credito\Estado;


use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CreditoEstadoController extends Controller
{
    public function estados()
    {
        $x = session()->all();
        $conexion = 'master_' .  $x['id_agencia'];

        if (empty($x['usuario_dni'])) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITO_ESTADOS', 'CREDITOS_MANTENIMIENTO');
            if ($band == 1) {
                $estados = Estado::on($conexion)->select()->get();
                return Inertia::render(
                    'Creditos/Mantenimiento/credito_estados',
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

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------
    public function listarEstados(Request $request)
    {
        // return $request;
        $id_agencia = $request->id_agencia;
        $conexion = 'master_' .  $id_agencia;
        $estados = Estado::on($conexion)->select()->get();
        return $estados;
    }

    public function verificar(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;


        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $estado = $request->estado;

        $estado_existe = Estado::on($conexion)->select('id')->where('estado', $estado)->get();

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
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;


        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));

        $modo = $request->modo;
        $estado = mb_strtoupper($request->estado);

        $descripcion = (new CreditosController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Estado::on($conexion)->create(
                [
                    'estado' => $estado,
                    'descripcion' => $descripcion,
                    'habilitado' => 1,
                    'datos_creacion' => $datos_registro,
                ]

            );
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Estado::on($conexion)->where('id', $id)
                ->update([
                    'estado' => $estado,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('man.cre_estados');
    }
    // --------------------------------------------------------------------------

}
