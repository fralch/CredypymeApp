<?php

namespace App\Http\Controllers\General;

use App\Models\General\Usuarios_permiso;
use App\Models\General\Permiso;
use App\Models\General\AreaTrabajo;
use App\Models\General\Cargo;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AreaTrabajoController extends Controller
{
    //
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

    public function areas_trabajo()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = $this->verificarPermiso($x['usuario_dni'], 'AREAS_TRABAJO', 'GENERAL_MANTENIMIENTO');
            if ($band == 1) {
                $areas_trabajo = AreaTrabajo::all();

                return Inertia::render(
                    'General/Mantenimiento/areas_trabajo',
                    [
                        'areas_trabajo' => $areas_trabajo,
                    ]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function validarExiste(Request $request)
    {

        $validar_duplicados =  AreaTrabajo::where('area', $request->area)->get();

        if (count($validar_duplicados)) {
            return 'EXISTE';
        } else {
            return 'NO_EXISTE';
        }
    }

    public function guardarArea(Request $request)
    {
        // return $request;
        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));

        if ($request->modo == 1) {
            AreaTrabajo::create([
                'area' => strtoupper($request->area),
                'descripcion' => strtoupper($request->descripcion),
                'habilitado' => 1,
                'datos_creacion' => $datos_registro
            ]);
        } elseif ($request->modo == 0) {
            AreaTrabajo::where('id', $request->id)
                ->update([
                    'area' => strtoupper($request->area),
                    'descripcion' => strtoupper($request->descripcion),
                    'habilitado' => $request->habilitado,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return redirect()->route('gen.man.areas_trabajo');
    }
}
