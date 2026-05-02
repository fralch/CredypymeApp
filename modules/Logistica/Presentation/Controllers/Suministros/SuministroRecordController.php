<?php

namespace Modules\Logistica\Presentation\Controllers\Suministros;

use App\Http\Controllers\Controller;
use Modules\Logistica\Presentation\Controllers\LogisticaController;

use Modules\Logistica\Infrastructure\Persistence\Eloquent\Suministros\Suministro;
use Modules\Logistica\Infrastructure\Persistence\Eloquent\Suministros\Records\SuministroRecord;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;

class SuministroRecordController extends Controller
{
    public function cierre_dia($cierre_id)
    {

        $suministros = Suministro::all()->map(function (Suministro $suministros) use ($cierre_id) {
            date_default_timezone_set("America/Lima");
            return [
                'agencia_id' => $suministros->agencia_id,
                'suministro_id' => $suministros->id,
                // 'codigo' => $suministros->codigo,
                // 'suministro' => $suministros->suministro,
                // 'tipo_id' => $suministros->tipo_id,
                // 'proveedor_id' => $suministros->proveedor_id,
                // 'condicion_id' => $suministros->condicion_id,
                'cantidad' => $suministros->cantidad,
                'valor_unitario' => $suministros->valor_unitario,
                'estado_id' => $suministros->estado_id,

                // 'datos_actualizacion_origen' => $suministros->datos_actualizacion,
                // 'updated_at_origen' => $suministros->updated_at,

                'cierre_id' => $cierre_id,
                'created_at' => date('Y-m-d H:i:s')
            ];
        });

        $suministros = $suministros->toArray();

        SuministroRecord::insert($suministros);

        return 'SUCCESS';
    }
}
