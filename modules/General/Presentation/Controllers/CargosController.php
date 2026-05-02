<?php

namespace Modules\General\Presentation\Controllers;

use Modules\General\Infrastructure\Persistence\Eloquent\Usuarios_permiso;
use Modules\General\Infrastructure\Persistence\Eloquent\Permiso;
use App\Http\Controllers\Controller;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CargosController extends Controller
{
    // --------------------FUNCIÓN PARA VERIFICAR PERMISO-----------------------
    public function verificarPermiso($dni, $modulo, $area)
    {

        $band = 0;

        $id_permiso = Permiso::select('id')->where('modulo', $modulo)->where('area', $area)->get();

        $result = Usuarios_permiso::select('id')->where('usuarios_permisos.permiso_id', $id_permiso[0]['id'])->where('usuarios_permisos.usuario_id', $dni)->get();

        if (empty($result[0]['id'])) {
            $band = 0;
        } else {
            $band = 1;
        }

        return $band;
    }
    // --------------------------------------------------------------------------

    //------------------------FUNCIONES QUIE DEVUELVEN UNA VISTA---------------------
    public function listar_cargos()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            $band = $this->verificarPermiso($x['usuario_dni'], 'CARGOS', 'GENERAL_MANTENIMIENTO');

            if ($band == 1) {
                $cargos = Cargo::select(
                    'id',
                    'cargo',
                    'descripcion',
                    'created_at',
                    'habilitado',
                    'datos_creacion'
                )
                    ->orderBy('cargo', 'asc')->get();
                return Inertia::render('General/Mantenimiento/cargos', ['cargos' => $cargos]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }


    // ------------------------FUNCINES QUE NO DEVUELVEN VISTAS------------------------------

    public function verificar_cargo(Request $request)
    {
        // dd($request);
        $modo = $request->modo;
        $nombre_cargo = $request->nombre_cargo;
        $id_cargo = $request->id_cargo;
        $existe_cargo = cargo::select('id')->where([['cargo', $nombre_cargo]])
            ->get();
        if ($id_cargo == 0) {
            if (count($existe_cargo) == 0) {
                $resultado = "NO EXISTE;                                                                           ISTE";
            } else {
                $resultado = "EXISTE";
            }
        } else
        if ($id_cargo = !0) {
            if (count($existe_cargo) == 0) {
                $resultado = 'NO EXISTE';
            } else if ($existe_cargo[0]['id'] == $id_cargo) {
                $resultado = 'NO EXISTE';
            } else {
                $resultado = 'EXISTE';
            }
        }
        // dd($resultado);
        return $resultado;
    }

    public function guardar_cargo(Request $request)
    {

        $modo = $request->modo;
        $cargo = mb_strtoupper($request->cargo);
        $descripcion = mb_strtoupper($request->descripcion);

        $datos_registro = (new GeneralController)->datos_registro();
        // dd($descripcion_cargo);
        if ($modo == 'NUEVO') {

            cargo::create(array(
                'cargo' => $cargo,
                'descripcion' => $descripcion,
                'habilitado' => 1,
                'datos_creacion' => $datos_registro,
            ));
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            $habilitado = $request->habilitado;
            cargo::where('id', $id)
                ->update([
                    'cargo' => $cargo,
                    'descripcion' => $descripcion,
                    'habilitado' => $habilitado,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        // return $resultado;
        return redirect()->route('gen.car.general.listar_cargos');
    }
}
