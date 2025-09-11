<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;

use App\Models\Creditos\Caja\Caja;
use App\Models\Creditos\Caja\PagoNotificacion;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Session;

class CajaPagoNotificacionController extends Controller
{

    public function listar_pagos(Request $request)
    {
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;
        $main_db = 'solucion_master';

        $credito_id = $request->credito_id;

        $pagos =  PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
            ->select(
                'caj_pag_not.id',
                'caj_pag_not.agencia_caja',
                'caj_pag_not.caja_id',
                'caj_pag_not.datos_creacion',
                'caj_pag_not.monto',
                'caj_pag_not.comentario'
            )
            ->join('credito_notificaciones as cre_not', 'caj_pag_not.notificacion_id', 'cre_not.id')
            ->where('cre_not.credito_id', $credito_id)
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

            $item->usuario_caja = $datos_caja->usuario;
        }

        return $pagos;
    }
}
