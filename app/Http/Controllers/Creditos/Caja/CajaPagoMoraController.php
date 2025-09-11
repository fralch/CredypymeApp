<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Caja\Caja;
use App\Models\Creditos\Caja\PagoCuota;
use App\Models\Creditos\Caja\PagoMora;

use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Gth\UsuariosAsistencia\Usuario;
use App\Models\General\Cargo;


use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Session;

class CajaPagoMoraController extends Controller
{

    public function listar_pagos(Request $request)
    {
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;
        $main_db = 'solucion_master';

        $credito_id = $request->credito_id;

        $pagos =  PagoMora::on($conexion)->from('caja_pago_moras as caj_pag_mor')
            ->select(
                'caj_pag_mor.id',
                'caj_pag_mor.agencia_caja',
                'caj_pag_mor.caja_id',
                'caj_pag_mor.datos_creacion',
                'caj_pag_mor.monto',
                'caj_pag_mor.comentario'
            )
            ->where('caj_pag_mor.credito_id', $credito_id)
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
