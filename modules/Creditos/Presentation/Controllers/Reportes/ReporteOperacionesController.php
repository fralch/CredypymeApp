<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Creditos\Presentation\Controllers\Caja\CajaOperacionDiaController;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Envio;

use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Records\CuentaUsuarioRecord;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Movimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\AdelantoHaber;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transaccion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Desembolso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\ComisionPago;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMetaMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;



use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Illuminate\Support\Facades\DB;

use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transferencia as CajaTransferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Transferencia as CuentaTransferencia;


use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;





use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteOperacionesController extends Controller
{


    public function operaciones_mes()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_OPERACIONES_MES', 'CREDITOS_REPORTES');
            if ($band == 1) {



                return Inertia::render('Creditos/Reportes/Caja/operaciones_mes');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar_operaciones_mes(request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion1 = 'master_' .  $agencia_id;
        $conexion2 = 'solucion_master_' .  $agencia_id;
        $conexion3 = 'records_' .  $agencia_id;
        $conexion4 = 'solucion_records_' .  $agencia_id;

        $agencias = Agencia::all();

        $fecha_primer_dia = date("Y-m-d", strtotime($request->fecha_seleccionada));
        $fecha_ultimo_dia = date("Y-m-d", strtotime($request->fecha_seleccionada . "+ 1 month" . "- 1 days"));
        // $fecha_primer_dia= '2023-05-03';
        // $fecha_ultimo_dia= '2023-05-03';

        $cajas_cerradas = Caja::on($conexion1)->from('caja_registros as caj_reg')
            ->select('id')
            ->whereBetween(DB::raw("SUBSTR(datos_cierre,11,10)"), [$fecha_primer_dia, $fecha_ultimo_dia])
            ->orderby(DB::raw("SUBSTR(datos_cierre,11,10)"), 'asc')
            ->get();


        // dd($cajas_cerradas);

        $lista = [];


        foreach ($agencias as $item) {
            $conexion = 'master_' .  $item->id_agencia;

            $concepto =  "*** Pago de cuotas - " . $this->abreviacion_agencia($item->id_agencia);
            $pago_cuotas = PagoCuota::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto) as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->wherein('caja_id', $cajas_cerradas)
                ->where('agencia_caja', $agencia_id);

            if ($pago_cuotas->get()->count() > 0) {
                $lista[] = $pago_cuotas->get()[0];
            }

            $concepto =  "*** Pago de moras - " . $this->abreviacion_agencia($item->id_agencia);
            $pago_moras = PagoMora::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto) as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->wherein('caja_id', $cajas_cerradas)
                ->where('agencia_caja', $agencia_id);

            if ($pago_moras->get()->count() > 0) {
                $lista[] = $pago_moras->get()[0];
            }

            $concepto =  "*** Pago de notificaciones - " . $this->abreviacion_agencia($item->id_agencia);
            $pago_notificaciones = PagoNotificacion::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto) as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->wherein('caja_id', $cajas_cerradas)
                ->where('agencia_caja', $agencia_id);

            if ($pago_notificaciones->get()->count() > 0) {
                $lista[] = $pago_notificaciones->get()[0];
            }

            $concepto =  "*** Dscto de moras - " . $this->abreviacion_agencia($item->id_agencia);
            $descuento_moras = Credito::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(dscto_mora_cancelado) as egresos"),
                )
                ->wherein('caja_cancelado_id', $cajas_cerradas)
                ->where('agencia_caja_cancelado', $agencia_id);


            if ($descuento_moras->get()->count() > 0) {
                $lista[] = $descuento_moras->get()[0];
            }

            $concepto =  "*** Dscto de notificaciones - " . $this->abreviacion_agencia($item->id_agencia);
            $descuento_notificaciones = Credito::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(dscto_notificaciones_cancelado)  as egresos"),
                )
                ->wherein('caja_cancelado_id', $cajas_cerradas)
                ->where('agencia_caja_cancelado', $agencia_id);


            if ($descuento_notificaciones->get()->count() > 0) {
                $lista[] = $descuento_notificaciones->get()[0];
            }

            $concepto =  "*** Dscto de interes - " . $this->abreviacion_agencia($item->id_agencia);
            $descuento_interes = Credito::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(dscto_interes_cancelado)  as egresos"),
                )
                ->wherein('caja_cancelado_id', $cajas_cerradas)
                ->where('agencia_caja_cancelado', $agencia_id);

            if ($descuento_interes->get()->count() > 0) {
                $lista[] = $descuento_interes->get()[0];
            }

            $concepto =  "*** Adelanto de haberes - " . $this->abreviacion_agencia($item->id_agencia);
            $adelantos = AdelantoHaber::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(monto) as egresos"),
                )
                ->wherein('caja_id', $cajas_cerradas)
                ->where('agencia_caja', $agencia_id);

            if ($adelantos->get()->count() > 0) {
                $lista[] = $adelantos->get()[0];
            }



            $concepto =  "*** Desembolsos - " . $this->abreviacion_agencia($item->id_agencia);
            $desembolsos = Desembolso::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(monto) as egresos"),
                )
                ->wherein('caja_id', $cajas_cerradas)
                ->where('agencia_caja', $agencia_id);


            if ($desembolsos->get()->count() > 0) {
                $lista[] = $desembolsos->get()[0];
            }

            $concepto =  "*** Pago de comisiones - " . $this->abreviacion_agencia($item->id_agencia);
            $caja_comisiones_pagos = ComisionPago::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto)  as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->wherein('caja_id', $cajas_cerradas)
                ->where('agencia_caja', $agencia_id);

            if ($caja_comisiones_pagos->get()->count() > 0) {
                $lista[] = $caja_comisiones_pagos->get()[0];
            }

            $concepto =  "*** Transferencias a caja - " . $this->abreviacion_agencia($item->id_agencia);
            $transferencias_enviadas_caja = CajaTransferencia::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(monto)  as egresos"),
                )
                ->wherein('remitente_id', $cajas_cerradas)
                ->where('agencia_id', $agencia_id)
                ->where('tipo', 'A_CAJA')
                ->where('estado', 'CONFIRMADO');


            if ($transferencias_enviadas_caja->get()->count() > 0) {
                $lista[] = $transferencias_enviadas_caja->get()[0];
            }

            $concepto =  "*** Transferencias a cuenta - " . $this->abreviacion_agencia($item->id_agencia);
            $transferencias_enviadas_cuenta = CajaTransferencia::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0  as ingresos"),
                    DB::raw("SUM(monto)  as egresos"),
                )
                ->wherein('remitente_id', $cajas_cerradas)
                ->where('agencia_id', $agencia_id)
                ->where('tipo', 'A_CUENTA')
                ->where('estado', 'CONFIRMADO');


            if ($transferencias_enviadas_cuenta->get()->count() > 0) {
                $lista[] = $transferencias_enviadas_cuenta->get()[0];
            }

            $concepto =  "*** Transferencias de caja - " . $this->abreviacion_agencia($item->id_agencia);
            $transferencias_recibidas_caja = CajaTransferencia::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto)   as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->wherein('destinatario_id', $cajas_cerradas)
                ->where('agencia_id', $agencia_id)
                ->where('tipo', 'A_CAJA')
                ->where('estado', 'CONFIRMADO');


            if ($transferencias_recibidas_caja->get()->count() > 0) {
                $lista[] = $transferencias_recibidas_caja->get()[0];
            }

            $concepto =  "*** Transferencias de caja - " . $this->abreviacion_agencia($item->id_agencia);
            $transferencias_recibidas_cuenta = CuentaTransferencia::on($conexion)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto)   as ingresos"),
                    DB::raw("0 as egresos")
                )
                ->wherein('destinatario_id', $cajas_cerradas)
                ->where('agencia_id', $agencia_id)
                ->where('tipo', 'A_CAJA')
                ->where('estado', 'CONFIRMADO');



            if ($transferencias_recibidas_cuenta->get()->count() > 0) {
                $lista[] = $transferencias_recibidas_cuenta->get()[0];
            }

            $concepto_1 =  "*** Abono de inversion meta - " . $this->abreviacion_agencia($item->id_agencia);
            $inversion_meta_abono = InversionMetaMovimiento::on($conexion)
                ->select(
                    DB::raw("'$concepto_1'  as concepto"),
                    DB::raw("SUM(monto) as ingresos"),
                    DB::raw("0 as egresos")
                )
                ->wherein('caja_id', $cajas_cerradas)
                ->where('agencia_caja', $agencia_id)
                ->where('tipo', 'I');


            if ($inversion_meta_abono->get()->count() > 0) {
                $lista[] = $inversion_meta_abono->get()[0];
            }

            $concepto_2 =  "*** Retiro de inversion meta - " . $this->abreviacion_agencia($item->id_agencia);
            $inversion_meta_retiro = InversionMetaMovimiento::on($conexion)
                ->select(
                    DB::raw("'$concepto_2'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(monto) as egresos")
                )
                ->wherein('caja_id', $cajas_cerradas)
                ->where('agencia_caja', $agencia_id)
                ->where('tipo', 'E');

            if ($inversion_meta_retiro->get()->count() > 0) {
                $lista[] = $inversion_meta_retiro->get()[0];
            }
        }

        $concepto =  "*** Ingresos - " . $this->abreviacion_agencia($agencia_id);
        $transacciones_ingresos = Transaccion::on($conexion1)
            ->select(
                DB::raw("'$concepto'  as concepto"),
                DB::raw("SUM(monto) as ingresos"),
                DB::raw("0 as egresos"),
            )
            ->wherein('caja_id', $cajas_cerradas)
            ->where('tipo', 'I');


        if ($transacciones_ingresos->get()->count() > 0) {
            $lista[] = $transacciones_ingresos->get()[0];
        }

        $concepto =  "*** Egresos - " . $this->abreviacion_agencia($agencia_id);
        $transacciones_egresos = Transaccion::on($conexion1)
            ->select(
                DB::raw("'$concepto'  as concepto"),
                DB::raw("0 as ingresos"),
                DB::raw("SUM(monto) as egresos"),
            )
            ->wherein('caja_id', $cajas_cerradas)
            ->where('tipo', 'E');

        if ($transacciones_egresos->get()->count() > 0) {
            $lista[] = $transacciones_egresos->get()[0];
        }
        $lista_operaciones = [];
        foreach ($lista as $item) {
            if ($item->ingresos > 0 || $item->egresos > 0) {
                $lista_operaciones[] = $item;
            }
        }

        // dd($lista_operaciones);

        $pago_cuotas = (object) [
            'concepto' => '*** Pago de cuotas',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $pago_moras = (object) [
            'concepto' => '*** Pago de moras',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $pago_notificaciones = (object) [
            'concepto' => '*** Pago de notificaciones',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $descuento_moras = (object) [
            'concepto' => '*** Dscto de moras',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $descuento_notificaciones = (object) [
            'concepto' => '*** Dscto de notificaciones',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $descuento_interes = (object) [
            'concepto' => '*** Dscto de interes',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $adelanto_haberes = (object) [
            'concepto' => '*** Adelanto de haberes',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $ingresos = (object) [
            'concepto' => '*** Ingresos',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $egresos = (object) [
            'concepto' => '*** Egresos',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $desembolsos = (object) [
            'concepto' => '*** Desembolsos',
            'ingresos' => 0,
            'egresos' => 0
        ];
        $pago_comisiones = (object) [
            'concepto' => '*** Pago de comisiones',
            'ingresos' => 0,
            'egresos' => 0
        ];

        $abono_inversion = (object) [
            'concepto' => '*** Abono de inversion meta',
            'ingresos' => 0,
            'egresos' => 0
        ];

        $retiro_inversion = (object) [
            'concepto' => '*** Retiro de inversion meta',
            'ingresos' => 0,
            'egresos' => 0
        ];


        foreach ($lista_operaciones as $clave => $item2) {


            if (SUBSTR($item2->concepto, 0, 18) === '*** Pago de cuotas') {
                $pago_cuotas->ingresos += $item2->ingresos;
                $pago_cuotas->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 17) === '*** Pago de moras') {
                $pago_moras->ingresos += $item2->ingresos;
                $pago_moras->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 26) === '*** Pago de notificaciones') {
                $pago_notificaciones->ingresos += $item2->ingresos;
                $pago_notificaciones->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 18) === '*** Dscto de moras') {
                $descuento_moras->ingresos += $item2->ingresos;
                $descuento_moras->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 27) === '*** Dscto de notificaciones') {
                $descuento_notificaciones->ingresos += $item2->ingresos;
                $descuento_notificaciones->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 20) === '*** Dscto de interes') {
                $descuento_interes->ingresos += $item2->ingresos;
                $descuento_interes->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 23) === '*** Adelanto de haberes') {
                $adelanto_haberes->ingresos += $item2->ingresos;
                $adelanto_haberes->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 12) === '*** Ingresos') {
                $ingresos->ingresos += $item2->ingresos;
                $ingresos->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 11) === '*** Egresos') {

                $egresos->ingresos += $item2->ingresos;
                $egresos->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 15) === '*** Desembolsos') {
                $desembolsos->ingresos += $item2->ingresos;
                $desembolsos->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 22) === '*** Pago de comisiones') {
                $pago_comisiones->ingresos += $item2->ingresos;
                $pago_comisiones->egresos += $item2->egresos;
            }

            if (SUBSTR($item2->concepto, 0, 27) === '*** Abono de inversion meta') {
                $abono_inversion->ingresos += $item2->ingresos;
                $abono_inversion->egresos += $item2->egresos;
            }
            if (SUBSTR($item2->concepto, 0, 28) === '*** Retiro de inversion meta') {
                $retiro_inversion->ingresos += $item2->ingresos;
                $retiro_inversion->egresos += $item2->egresos;
            }
        }

        // dd($pago_comisiones,$abono_inversion,$retiro_inversion);

        $resultados = [];
        if ($pago_cuotas->ingresos > 0 || $pago_cuotas->egresos > 0) {
            $resultados[] = $pago_cuotas;
        }
        if ($pago_moras->ingresos > 0 || $pago_moras->egresos > 0) {
            $resultados[] = $pago_moras;
        }
        if ($pago_notificaciones->ingresos > 0 || $pago_notificaciones->egresos > 0) {
            $resultados[] = $pago_notificaciones;
        }
        if ($descuento_moras->ingresos > 0 || $descuento_moras->egresos > 0) {
            $resultados[] = $descuento_moras;
        }
        if ($descuento_notificaciones->ingresos > 0 || $descuento_notificaciones->egresos > 0) {
            $resultados[] = $descuento_notificaciones;
        }
        if ($descuento_interes->ingresos > 0 || $descuento_interes->egresos > 0) {
            $resultados[] = $descuento_interes;
        }
        if ($adelanto_haberes->ingresos > 0 || $adelanto_haberes->egresos > 0) {
            $resultados[] = $adelanto_haberes;
        }
        if ($ingresos->ingresos > 0 || $ingresos->egresos > 0) {
            $resultados[] = $ingresos;
        }
        if ($egresos->ingresos > 0 || $egresos->egresos > 0) {
            $resultados[] = $egresos;
        }
        if ($desembolsos->ingresos > 0 || $desembolsos->egresos > 0) {
            $resultados[] = $desembolsos;
        }
        if ($pago_comisiones->ingresos > 0 || $pago_comisiones->egresos > 0) {
            $resultados[] = $pago_comisiones;
        }
        if ($abono_inversion->ingresos > 0 || $abono_inversion->egresos > 0) {
            $resultados[] = $abono_inversion;
        }
        if ($retiro_inversion->ingresos > 0 || $retiro_inversion->egresos > 0) {
            $resultados[] = $retiro_inversion;
        }

        // dd($resultados);


        $cuentas_iniciales = DB::connection($conexion3)->table('cuenta_usuarios_records as cue_usu_reco')
            ->select(
                "cue_usu_reco.cuenta_id",
                'usu.usuario',
                "cue_usu_reco.monto_inicial",

            )
            ->join("$conexion2.cuenta_usuarios as cue_usu", 'cue_usu.id', '=', 'cue_usu_reco.cuenta_id')
            ->join('solucion_master.usuarios as usu', 'cue_usu.dni', 'usu.dni')
            ->where('cue_usu_reco.fecha_inicio', $fecha_primer_dia)
            ->get()->toArray();


        $cuentas_adicionales = DB::connection($conexion3)->table('cuenta_usuarios_records as cue_usu_reco')
            ->select(
                "cue_usu_reco.cuenta_id",
                'usu.usuario',

            )
            ->join("$conexion2.cuenta_usuarios as cue_usu", 'cue_usu.id', '=', 'cue_usu_reco.cuenta_id')
            ->join('solucion_master.usuarios as usu', 'cue_usu.dni', 'usu.dni')
            ->whereBetween('cue_usu_reco.fecha_inicio', [$fecha_primer_dia, $fecha_ultimo_dia])
            ->distinct('cuenta_id')
            ->get()->toArray();

        foreach ($cuentas_iniciales as  $item3) {
            $objeto_usuario = (object) [
                'concepto' => '*** Saldo inicial de: ' . $item3->usuario,
                'ingresos' => floatval($item3->monto_inicial),
                'egresos' => 0
            ];

            array_push($resultados, $objeto_usuario);
        }


        foreach ($cuentas_adicionales as  $item4) {

            $cont = 0;
            foreach ($cuentas_iniciales as  $item5) {


                if ($item4->cuenta_id == $item5->cuenta_id) {

                    $cont += 1;
                }
            }
            if ($cont == 0) {

                $objeto_usuario = (object) [
                    'concepto' => '*** Saldo inicial de: ' . $item4->usuario,
                    'ingresos' => 0,
                    'egresos' => 0
                ];
                array_push($resultados, $objeto_usuario);
            }
        }

        $monto_envio = Movimiento::on($conexion1)->from('cuenta_movimientos as cue_mov')
            ->select(
                DB::raw("SUM(cue_mov.monto) as monto_envio"),
            )

            ->whereBetween(DB::raw("SUBSTR(cue_mov.fecha_movimiento,1,10)"), [$fecha_primer_dia, $fecha_ultimo_dia])
            ->where('cue_mov.descripcion', 'ENVÍO A AGENCIA')
            ->get()->last();

        $monto_recepcion = Movimiento::on($conexion1)->from('cuenta_movimientos as cue_mov')
            ->select(
                DB::raw("SUM(cue_mov.monto) as monto_recepcion"),
            )

            ->whereBetween(DB::raw("SUBSTR(cue_mov.fecha_movimiento,1,10)"), [$fecha_primer_dia, $fecha_ultimo_dia])
            ->where('cue_mov.descripcion', 'RECEPCIÓN DE AGENCIA')
            ->get()->last();

        //  dd($monto_envio,$monto_recepcion);


        $envios_monto_ingreso = $monto_recepcion->monto_recepcion;
        $envios_monto_egreso = $monto_envio->monto_envio;

        if ($envios_monto_ingreso != null && $envios_monto_ingreso != 0) {

            $objeto_envios = (object) [
                'concepto' => '*** Recepciones de agencias ',
                'ingresos' => floatval($envios_monto_ingreso),
                'egresos' => 0,
            ];
            array_unshift($resultados, $objeto_envios);
        }
        if ($envios_monto_egreso != null && $envios_monto_egreso != 0) {

            $objeto_envios = (object) [
                'concepto' => '*** Envíos a agencias ',
                'ingresos' => 0,
                'egresos' => floatval($envios_monto_egreso),
            ];
            array_unshift($resultados, $objeto_envios);
        }

        //  $envios_monto_agencias = [];


        //  foreach ($agencias as $item) {
        //      $conexion4 = 'master_' .  $item->id_agencia;

        //      $envios_monto = Envio::on($conexion4)->from('cuenta_envios as cue_env')
        //      ->select(
        //          'cue_env.agencia_remitente_id',
        //          DB::raw("SUM(cue_env.monto) as monto"),
        //      )

        //      ->where([[DB::raw("SUBSTR(cue_env.datos_creacion,11,10)"),'>=', $fecha_primer_dia],[DB::raw("SUBSTR(cue_env.datos_creacion,11,10)"),'<=', $fecha_ultimo_dia],['cue_env.estado','CONFIRMADO'],['cue_env.agencia_remitente_id',$agencia_id]])
        //      ->orwhere([[DB::raw("SUBSTR(cue_env.datos_creacion,11,10)"),'>=', $fecha_primer_dia],[DB::raw("SUBSTR(cue_env.datos_creacion,11,10)"),'<=', $fecha_ultimo_dia],['cue_env.estado','CONFIRMADO'],['cue_env.agencia_destinatario_id',$agencia_id]])
        //      ->get()->last()->toArray();

        //      if($envios_monto['monto'] != null){

        //          $envios_monto_agencias[]=$envios_monto;

        //      }

        //  }

        //  $envios_monto_ingreso = 0;
        //  $envios_monto_egreso = 0;

        //  foreach($envios_monto_agencias as $item2){

        //      if($agencia_id != $item2['agencia_remitente_id']){

        //          $envios_monto_ingreso += $item2['monto'];
        //      } else{

        //          $envios_monto_egreso += $item2['monto'];
        //      }
        //  }

        //  if($envios_monto_ingreso != 0){

        //      $objeto_envios = (object) [
        //          'concepto' => '*** Recepciones de agencias ',
        //          'ingresos' => floatval($envios_monto_ingreso),
        //          'egresos' => 0,
        //      ];
        //      array_unshift($resultados, $objeto_envios);

        //  }
        //  if($envios_monto_egreso != 0){

        //      $objeto_envios = (object) [
        //          'concepto' => '*** Envíos a agencias ',
        //          'ingresos' => 0,
        //          'egresos' => floatval($envios_monto_egreso),
        //      ];
        //      array_unshift($resultados, $objeto_envios);

        //  }

        //  dd($resultados);

        return $resultados;
    }
    public function abreviacion_agencia($agencia_id)
    {

        $abreviacion = null;
        switch ($agencia_id) {
            case 1:
                $abreviacion = 'TMB';
                break;
            case 2:
                $abreviacion = 'HYO';
                break;
            case 3:
                $abreviacion = 'PMP';
                break;
            case 4:
                $abreviacion = 'HVC';
                break;
            case 5:
                $abreviacion = 'ADM';
                break;
            case 6:
                $abreviacion = 'CHI';
                break;
        }

        return $abreviacion;
    }



    public function exportar_operaciones_mes(Request $request)
    {
        // return $request;
        // Ordenando array de datos-------------------------------

        $data = json_decode($request->lista_operaciones);
        $data_subtotales = [json_decode($request->subtotales)];
        $data_total = [json_decode($request->totales)];

        // return $data_total;

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptOperacionesMes.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];
        $lista_formatos_subtotales = [];
        $lista_formatos_totales = [];


        while ($celda <= 3) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda = $sheet->getStyle($columna_1 . 4)->exportArray();
            $formato_subtotales = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_total = $sheet->getStyle($columna_1 . 7)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_subtotales[] = $formato_subtotales;
            $lista_formatos_totales[] = $formato_total;
            $celda++;
        }
        // $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);
        $sheet->removeRow(5);
        $sheet->removeRow(4);
        // return $lista_formatos_subtotales;
        // Insertando datos-----------------------------
        $indice = 4;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                $columna_2 += 1;
            }

            $indice += 1;
        }

        $sheet->insertNewRowBefore($indice + 2);


        foreach ($data_subtotales as $item) {
            $columna_subtotal = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_subtotal) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_subtotal) . $indice)->applyFromArray($lista_formatos_subtotales[$columna_subtotal - 1]);
                $columna_subtotal += 1;
            }
            $indice += 1;
        }


        // // -------------------------------------------------

        $sheet->insertNewRowBefore($indice + 1);


        foreach ($data_total as $item) {
            $columna_total = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_total) . $indice, $valor);
                $columna_total += 2;
            }
        }
        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }


        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptOperacionesMes', 5);


        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
