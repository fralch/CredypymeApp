<?php

namespace Modules\Gth\Presentation\Controllers\ColaboradorMes;

use Modules\Gth\Presentation\Controllers\GthController;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Equipo;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Examen;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\EquipoIntegrante;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\Evaluacion;
use Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes\EvaluacionPregunta;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ColaboradorMesSeguimientoController extends Controller
{

    public function seguimiento()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'SEGUIMIENTO_EVALUACION', 'GTH_COLABORADOR_MES');
            if ($band == 1) {
                return Inertia::render('Gth/ColaboradorMes/seguimiento_evaluaciones');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar(request $request)
    {
        $fecha_seleccionada = $request->fecha_seleccionada;

        $anio_seleccionado = intval(date("Y", strtotime($fecha_seleccionada)));
        $mes_seleccionado = intval(date("m", strtotime($fecha_seleccionada)));

        $lista_evaluaciones =  EvaluacionPregunta::from('colaboradormes_evaluaciones as col_eva')
            ->select(
                'age.nombre as agencia',
                'usu.usuario as evaluador',

                'col_exa.examen',
                'col_eva.culminado',
                'col_eva.updated_at as fecha'

            )
            ->join('usuarios as usu', 'usu.dni', 'col_eva.evaluador_id')
            ->join('agencias as age', 'age.id_agencia', 'usu.agencia_id')
            ->join('colaboradormes_examenes as col_exa', 'col_exa.id', 'col_eva.examen_id')
            ->where([
                ['col_eva.año', $anio_seleccionado],
                ['col_eva.mes', $mes_seleccionado]
            ])
            ->groupBy('col_eva.equipo_id', 'col_eva.evaluador_id')
            ->orderby('evaluador', 'desc')
            ->get();

        return ['lista_evaluaciones' => $lista_evaluaciones];
    }
}
