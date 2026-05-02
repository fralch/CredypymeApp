<?php

namespace Modules\Creditos\Presentation\Controllers\Mantenimiento\Credito;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Sector;


use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CreditoSectorController extends Controller
{



    public function sectores()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITO_SECTORES', 'CREDITOS_MANTENIMIENTO');
            if ($band == 1) {
                return Inertia::render(
                    'Creditos/Mantenimiento/credito_sectores'

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
    public function listar(Request $request)
    {
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;
        $sectores = Sector::on($conexion)->get();

        return ['sectores' => $sectores];
    }

    public function verificar(Request $request)

    {
        $resultado = "NO EXISTE";
        $agencia_id = $request->agencia_seleccionada;
        $modo = $request->modo;
        $id = $request->id;
        $sector = $request->sector;

        $conexion = 'master_' .  $agencia_id;
        $sector_existe = Sector::on($conexion)->select('id')->where('sector', $sector)->get();

        if ($modo == "EDITAR") {
            if (count($sector_existe) > 0) {
                if ($sector_existe[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($sector_existe) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)

    {
        $agencia_id = $request->agencia_seleccionada;
        $conexion = 'master_' .  $agencia_id;
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $modo = $request->modo;
        $sector = mb_strtoupper($request->sector);

        $descripcion = (new CreditosController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $habilitado = $request->habilitado;

        if ($modo == "NUEVO") {
            Sector::on($conexion)->create(
                [
                    'sector' => $sector,
                    'descripcion' => $descripcion,
                    'habilitado' => 1,
                    'datos_creacion' => $datos_registro,
                ]

            );
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Sector::on($conexion)->where('id', $id)
                ->update([
                    'sector' => $sector,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('man.cre_sectores');
    }
    // --------------------------------------------------------------------------

}
