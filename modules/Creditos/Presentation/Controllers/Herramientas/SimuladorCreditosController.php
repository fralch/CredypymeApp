<?php

namespace Modules\Creditos\Presentation\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Tipo;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SimuladorCreditosController extends Controller
{
    public function simuladorCreditos()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'SIMULADOR_CREDITOS', 'CREDITOS_HERRAMIENTAS');
            if ($band == 1) {
                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;

                $tipos = Tipo::on($conexion)->where('habilitado', 1)->orderBy('tipo', 'asc')->get();
                return Inertia::render('Creditos/Herramientas/simulador_creditos', [
                    'agencia_id' => intval($agencia_id),
                    'tipos' => $tipos
                ]);
            } else {
                return redirect('/');
            }
        }
    }

    public function rangos()
    {
        $agencia_id = session('id_agencia');
        $conexion = 'master_' .  $agencia_id;

        $comision = DB::connection($conexion)->table('credito_comisiones')
            ->select('id')
            ->where('comision', 'RIESGO CREDITICIO')->get()->last();
        $comision_id = $comision->id;

        $rango_comisiones = DB::connection($conexion)->table('credito_comisiones_rangos')
            ->select(
                'monto_desde',
                'monto_hasta',
                'monto_cobrar'
            )
            ->where('comision_id', $comision_id)->get();

        return ['rango_comisiones' => $rango_comisiones];
    }
}
