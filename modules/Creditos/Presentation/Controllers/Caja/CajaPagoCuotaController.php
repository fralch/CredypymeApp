<?php

namespace Modules\Creditos\Presentation\Controllers\Caja;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Gth\Infrastructure\Persistence\Eloquent\UsuariosAsistencia\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;

use Illuminate\Http\Request;

class CajaPagoCuotaController extends Controller
{

    public function listar_pagos(Request $request)
    {
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;
        $main_db = 'solucion_master';

        $credito_id = $request->credito_id;

        $pagos = PagoCuota::on($conexion)->from('caja_pago_cuotas as caj_pag_cuo')
            ->select(
                'caj_pag_cuo.id',
                'caj_pag_cuo.agencia_caja',
                'caj_pag_cuo.caja_id',
                'caj_pag_cuo.monto',
                'caj_pag_cuo.numero_cuota',
                'caj_pag_cuo.numero_recibo',
                'caj_pag_cuo.comentario',
                'caj_pag_cuo.datos_creacion'
            )
            ->where('caj_pag_cuo.credito_id', $credito_id)
            ->get();

        foreach ($pagos as $item) {

            $conexion = 'master_' .  $item->agencia_caja;
            $main_db = 'solucion_master';

            $datos_caja = Caja::on($conexion)->from('caja_registros as caj_reg')
                ->select('usu.usuario')
                ->join("$main_db.usuarios as usu", 'caj_reg.dni', 'usu.dni')
                ->where('caj_reg.id', $item->caja_id)
                ->get()
                ->last();

            if ($datos_caja) {
                $item->usuario_caja = $datos_caja->usuario;
            } else {
                $item->usuario_caja = '-';
            }
        }

        return $pagos;
    }
}
