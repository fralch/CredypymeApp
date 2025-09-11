<?php

namespace App\Http\Controllers\Gth\Mantenimiento;


use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\Mantenimiento\Planilla\SistPension;
use Illuminate\Http\Request;

use App\Http\Controllers\Gth\GthController;

use Inertia\Inertia;

class PlanillaSistPensioneController extends Controller
{

    public function sistema_pensiones()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PLANILLA_SISTEMA_PENSIONES', 'GTH_MANTENIMIENTO');

            if ($band == 1) {
                $sistema_pensiones = SistPension::all();
                return Inertia::render(
                    'Gth/Mantenimiento/Planilla/sistema_pensiones',
                    ['sistema_pensiones' => $sistema_pensiones]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }


    public function guardar_sistema_pension(Request $request)
    {
        $datos_registro = (new GthController)->datos_registro();

        $modo = $request->modo;
        $id_sis_pensiones = $request->id_sis_pensiones;
        $tipo = $request->tipo;
        $nombre = $request->nombre;
        $comision = $request->comision;

        if ($request->tipo_comision == '0') {
            $tipo_comision = NULL;
        } else {
            $tipo_comision = $request->tipo_comision;
        }


        if ($modo == 'NUEVO') {

            SistPension::create([
                'tipo' => $tipo,
                'nombre' => $nombre,
                'tipo_comision' => $tipo_comision,
                'porcentaje' => ($comision / 100),
                'datos_creacion' => $datos_registro
            ]);
        } else if ($modo == 'EDITAR') {
            SistPension::where(
                'id_sis_pensiones',
                $id_sis_pensiones
            )
                ->update([
                    'tipo' => $tipo,
                    'nombre' => $nombre,
                    'tipo_comision' => $tipo_comision,
                    'porcentaje' => ($comision / 100),
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return redirect()->route('gth.pla.sistema_pensiones');
    }
}
