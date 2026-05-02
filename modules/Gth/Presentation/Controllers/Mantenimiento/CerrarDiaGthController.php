<?php

namespace Modules\Gth\Presentation\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Presentation\Controllers\Planillas\PlanillaVacacionePeriodoController;
use Inertia\Inertia;


class CerrarDiaGthController extends Controller
{
    public function cierre_dia()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CIERRE_DIA', 'GTH_MANTENIMIENTO');
            if ($band == 1) {
                return Inertia::render('Gth/Mantenimiento/cierre_dia');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function fecha_larga_aplicacion()
    {
        $data = Datos_aplicacion::where('descripcion', 'FECHA_GTH')->get()->last();

        date_default_timezone_set("America/Lima");
        $hora = date('H:i:s');

        return $data->valorFecha . ' ' . $hora;
    }

    public function fecha_corta_aplicacion()
    {
        $data = Datos_aplicacion::where('descripcion', 'FECHA_GTH')->get()->last();

        return $data->valorFecha;
    }
    public function cerrar_dia()
    {
        $datos_sesion = session()->all();
        $fecha_actual_corta = date($this->fecha_corta_aplicacion());
        $fecha_actual_larga = date($this->fecha_larga_aplicacion());
        $fecha_nueva_corta = date("Y-m-d", strtotime($fecha_actual_corta . "+ 1 day"));

        $calcular_vacaciones = (new PlanillaVacacionePeriodoController)
            ->calcular_vacaciones(
                $fecha_actual_corta,
                $fecha_actual_larga,
                $fecha_nueva_corta,
                $datos_sesion
            );

        Datos_aplicacion::where('descripcion', 'FECHA_GTH')
            ->update(['valorFecha' => $fecha_nueva_corta]);



        return redirect()->route('gth.man.cierre_dia');
    }
}
