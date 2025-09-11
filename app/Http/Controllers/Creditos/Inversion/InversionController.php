<?php

namespace App\Http\Controllers\Creditos\Inversion;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;
use App\Models\Creditos\Inversion\InversionMeta;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InversionController extends Controller
{
    public function listar(Request $request)
    {
        $agencia_id = (new CreditosController)->verificar_nulo($request->agencia_id);

        if ($agencia_id == null) {
            return [];
        }

        $conexion = 'master_' .  $agencia_id;
        $texto_buscar = $request->texto_buscar;

        $columna1 = 'cre_reg.apellido_paterno, " ", cre_reg.apellido_materno, " ", cre_reg.nombres';
        $columna2 = 'inv_pro_met.producto';
        $operator = 'like';
        $search = "%$texto_buscar%";


        $inversion_meta = InversionMeta::on($conexion)->from('inversion_meta_registros as inv_met_reg')
            ->select(
                'cre_reg.id as cliente_id',
                'cre_reg.dni',
                'cre_reg.apellido_paterno',
                'cre_reg.apellido_materno',
                'cre_reg.nombres',

                'inv_met_reg.id',
                'inv_met_reg.fecha_apertura',
                'inv_met_reg.acumulado',

                'inv_pro_met.producto',
                DB::raw("'META' as tipo")

            )
            ->join('cliente_registros as cre_reg', 'cre_reg.id', 'inv_met_reg.cliente_id')
            ->join('inversion_productos_meta as inv_pro_met', 'inv_met_reg.producto_meta_id', 'inv_pro_met.id')
            ->where([
                ['inv_met_reg.fecha_cierre', null],
                [DB::raw("CONCAT($columna1)"), $operator, $search]
            ])
            ->orWhere([
                ['inv_met_reg.fecha_cierre', null],
                [$columna2, $operator, $search]
            ])
            ->get();

            // dd($inversion_meta);


        $inversiones = $inversion_meta;

        return $inversiones;
    }
}
