<?php

namespace Modules\Creditos\Presentation\Controllers\Caja;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Records\CuentaUsuarioRecord;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Envio;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Movimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\AdelantoHaber;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transaccion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Desembolso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\ComisionPago;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMetaMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Transferencia as CuentaTransferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transferencia as CajaTransferencia;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Comision;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;


use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CajaOperacionDiaController extends Controller
{
    //
    public function operaciones_dia()
    {
        $x = session()->all();


        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'OPERACIONES_DIA', 'CREDITOS_CAJA'); // no hacer merge

            if ($band == 1) {

                $agencias = Agencia::all();

                $cuentas = [];
                foreach ($agencias as $key => $item) {

                    $conexion = 'master_' .  $item->id_agencia;

                    $lista = CuentaUsuario::on($conexion)->from('cuenta_usuarios as cue_usu')
                        ->select(
                            'cue_usu.id',
                            'cue_usu.dni',
                            'cue_usu.monto',

                            'usu.usuario',
                            'usu.apellido_paterno',
                            'usu.apellido_materno',
                            'usu.nombres',
                            'usu.agencia_id'
                        )
                        ->join('solucion_master.usuarios as usu', 'cue_usu.dni', 'usu.dni')
                        ->where([['cue_usu.con_cuenta', 1], ['usu.habilitado', 1]])
                        ->get();

                    foreach ($lista as $key => $item_1) {
                        $cuentas[] = $item_1;
                    }
                }

                return Inertia::render('Creditos/Caja/operaciones_dia', ['cuentas' => $cuentas]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $cuentas = json_decode($request->cuentas);

        $fecha_corta_aplicacion = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $lista_operaciones = [];
        foreach ($cuentas as $item) {
            $datos_caja = Caja::on($conexion)
                ->select('id', 'dni', 'monto_apertura')
                ->where([
                    ['dni', $item->dni],
                    [DB::raw("SUBSTR(datos_apertura,11,10)"), $fecha_corta_aplicacion],
                    ['datos_cierre', null]
                ])
                ->get()->last();

            $operaciones = [];
            if ($datos_caja != null) {
                $operaciones = $this->listar_operaciones($datos_caja->id, $agencia_id);
            }

            foreach ($operaciones as $item_1) {
                $object = (object)[
                    'usuario_cuenta' => $item->dni,
                    'operaciones_caja' => $item_1
                ];
                $lista_operaciones[] = $object;
            }
        }

        return ['lista_operaciones' => $lista_operaciones];
    }


    public function listar_operaciones($caja_id, $agencia_id)
    {

        $conexion = 'master_' .  $agencia_id;

        $lista = [];

        $caja = Caja::on($conexion)->select(
            DB::raw("'*** APERTURA' as concepto"),
            'monto_apertura as ingresos',
            DB::raw("0 as egresos")
        )->where(
            'id',
            $caja_id
        );

        if ($caja->get()->count() > 0) {
            $lista[] = $caja->get()[0];
        }

        $agencias = Agencia::all();

        foreach ($agencias as $item) {
            $conexion1 = 'master_' .  $item->id_agencia;

            $concepto =  "*** Pago de cuotas - " . $this->abreviacion_agencia($item->id_agencia);
            $pago_cuotas = PagoCuota::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto) as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->where([
                    ['caja_id', $caja_id],
                    ['agencia_caja', $agencia_id],
                    ['pago_banco', 0]
                ])
                ->groupBy('caja_id');

            if ($pago_cuotas->get()->count() > 0) {
                $lista[] = $pago_cuotas->get()[0];
            }

            $concepto =  "*** Pago de moras - " . $this->abreviacion_agencia($item->id_agencia);
            $pago_moras = PagoMora::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto) as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->where([
                    ['caja_id', $caja_id],
                    ['agencia_caja', $agencia_id],
                    ['pago_banco', 0]
                ])->groupBy('caja_id');

            if ($pago_moras->get()->count() > 0) {
                $lista[] = $pago_moras->get()[0];
            }

            $concepto =  "*** Pago de notificaciones - " . $this->abreviacion_agencia($item->id_agencia);
            $pago_notificaciones = PagoNotificacion::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto) as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->where([
                    ['caja_id', $caja_id],
                    ['agencia_caja', $agencia_id],
                    ['pago_banco', 0]
                ])->groupBy('caja_id');

            if ($pago_notificaciones->get()->count() > 0) {
                $lista[] = $pago_notificaciones->get()[0];
            }

            $concepto =  "*** Dscto de moras - " . $this->abreviacion_agencia($item->id_agencia);
            $descuento_moras = Credito::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(dscto_mora_cancelado) as egresos")
                )
                ->where([
                    ['caja_cancelado_id', $caja_id],
                    ['agencia_caja_cancelado', $agencia_id],
                    ['pago_banco_cancelado', 0]
                ])->groupBy('caja_cancelado_id');

            if ($descuento_moras->get()->count() > 0) {
                $lista[] = $descuento_moras->get()[0];
            }

            $concepto =  "*** Dscto de notificaciones - " . $this->abreviacion_agencia($item->id_agencia);
            $descuento_notificaciones = Credito::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(dscto_notificaciones_cancelado)  as egresos")
                )
                ->where([
                    ['caja_cancelado_id', $caja_id],
                    ['agencia_caja_cancelado', $agencia_id],
                    ['pago_banco_cancelado', 0]
                ])->groupBy('caja_cancelado_id');

            if ($descuento_notificaciones->get()->count() > 0) {
                $lista[] = $descuento_notificaciones->get()[0];
            }

            $concepto =  "*** Dscto de interes - " . $this->abreviacion_agencia($item->id_agencia);
            $descuento_interes = Credito::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(dscto_interes_cancelado)  as egresos")
                )
                ->where([
                    ['caja_cancelado_id', $caja_id],
                    ['agencia_caja_cancelado', $agencia_id],
                    ['pago_banco_cancelado', 0]
                ])->groupBy('caja_cancelado_id');

            if ($descuento_interes->get()->count() > 0) {
                $lista[] = $descuento_interes->get()[0];
            }

            $concepto =  "*** Adelanto de haberes - " . $this->abreviacion_agencia($item->id_agencia);
            $adelantos = AdelantoHaber::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(monto) as egresos"),
                )
                ->where([
                    ['caja_id', $caja_id],
                    ['agencia_caja', $agencia_id]
                ])->groupBy('caja_id');

            if ($adelantos->get()->count() > 0) {
                $lista[] = $adelantos->get()[0];
            }



            $concepto =  "*** Desembolsos - " . $this->abreviacion_agencia($item->id_agencia);
            $desembolsos = Desembolso::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(monto) as egresos"),
                )
                ->where([
                    ['caja_id', $caja_id],
                    ['agencia_caja', $agencia_id],
                ])->groupBy('caja_id');

            if ($desembolsos->get()->count() > 0) {
                $lista[] = $desembolsos->get()[0];
            }

            $concepto =  "*** Pago de comisiones - " . $this->abreviacion_agencia($item->id_agencia);
            $caja_comisiones_pagos = ComisionPago::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto)  as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->where([
                    ['caja_id', $caja_id],
                    ['agencia_caja', $agencia_id],
                ])->groupBy('caja_id');

            if ($caja_comisiones_pagos->get()->count() > 0) {
                $lista[] = $caja_comisiones_pagos->get()[0];
            }

            $concepto =  "*** Transferencias a caja - " . $this->abreviacion_agencia($item->id_agencia);
            $transferencias_enviadas_caja = CajaTransferencia::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(monto)  as egresos"),
                )
                ->where([
                    ['remitente_id', $caja_id],
                    ['destinatario_id', '!=', $caja_id],
                    ['agencia_id', $agencia_id],
                    ['tipo', 'A_CAJA'],
                    ['estado', 'CONFIRMADO']
                ])->groupBy('remitente_id');

            if ($transferencias_enviadas_caja->get()->count() > 0) {
                $lista[] = $transferencias_enviadas_caja->get()[0];
            }

            $concepto =  "*** Transferencias a cuenta - " . $this->abreviacion_agencia($item->id_agencia);
            $transferencias_enviadas_cuenta = CajaTransferencia::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("0  as ingresos"),
                    DB::raw("SUM(monto)  as egresos"),
                )
                ->where([
                    ['remitente_id', $caja_id],
                    ['agencia_id', $agencia_id],
                    ['tipo', 'A_CUENTA'],
                    ['estado', 'CONFIRMADO']
                ])->groupBy('remitente_id');

            if ($transferencias_enviadas_cuenta->get()->count() > 0) {
                $lista[] = $transferencias_enviadas_cuenta->get()[0];
            }

            $concepto =  "*** Transferencias de caja - " . $this->abreviacion_agencia($item->id_agencia);
            $transferencias_recibidas_caja = CajaTransferencia::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto)   as ingresos"),
                    DB::raw("0 as egresos"),
                )
                ->where([
                    ['destinatario_id', $caja_id],
                    ['agencia_id', $agencia_id],
                    ['tipo', 'A_CAJA'],
                    ['estado', 'CONFIRMADO']
                ])->groupBy('destinatario_id');

            if ($transferencias_recibidas_caja->get()->count() > 0) {
                $lista[] = $transferencias_recibidas_caja->get()[0];
            }

            $concepto =  "*** Transferencias de caja - " . $this->abreviacion_agencia($item->id_agencia);
            $transferencias_recibidas_cuenta = CuentaTransferencia::on($conexion1)
                ->select(
                    DB::raw("'$concepto'  as concepto"),
                    DB::raw("SUM(monto)   as ingresos"),
                    DB::raw("0 as egresos")
                )
                ->where([
                    ['destinatario_id', $caja_id],
                    ['agencia_id', $agencia_id],
                    ['tipo', 'A_CAJA'],
                    ['estado', 'CONFIRMADO']
                ])->groupBy('destinatario_id');

            if ($transferencias_recibidas_cuenta->get()->count() > 0) {
                $lista[] = $transferencias_recibidas_cuenta->get()[0];
            }

            $concepto_1 =  "*** Abono de inversion meta - " . $this->abreviacion_agencia($item->id_agencia);
            $inversion_meta_abono = InversionMetaMovimiento::on($conexion1)
                ->select(
                    DB::raw("'$concepto_1'  as concepto"),
                    DB::raw("SUM(monto) as ingresos"),
                    DB::raw("0 as egresos")
                )
                ->where([
                    ['caja_id', $caja_id],
                    ['agencia_caja', $agencia_id],
                    ['tipo', 'I']

                ])
                ->whereNull('banco_id')
                ->groupBy('caja_id');

            if ($inversion_meta_abono->get()->count() > 0) {
                $lista[] = $inversion_meta_abono->get()[0];
            }

            $concepto_2 =  "*** Retiro de inversion meta - " . $this->abreviacion_agencia($item->id_agencia);
            $inversion_meta_retiro = InversionMetaMovimiento::on($conexion1)
                ->select(
                    DB::raw("'$concepto_2'  as concepto"),
                    DB::raw("0 as ingresos"),
                    DB::raw("SUM(monto) as egresos")
                )
                ->where([
                    ['caja_id', $caja_id],
                    ['agencia_caja', $agencia_id],
                    ['tipo', 'E']
                ])->groupBy('caja_id');

            if ($inversion_meta_retiro->get()->count() > 0) {
                $lista[] = $inversion_meta_retiro->get()[0];
            }
        }

        $concepto =  "*** Ingresos - " . $this->abreviacion_agencia($agencia_id);
        $transacciones_ingresos = Transaccion::on($conexion)
            ->select(
                DB::raw("'$concepto'  as concepto"),
                DB::raw("SUM(monto) as ingresos"),
                DB::raw("0 as egresos"),
            )
            ->where([
                ['caja_id', $caja_id],
                ['tipo', 'I']
            ])->groupBy('caja_id');

        if ($transacciones_ingresos->get()->count() > 0) {
            $lista[] = $transacciones_ingresos->get()[0];
        }

        $concepto =  "*** Egresos - " . $this->abreviacion_agencia($agencia_id);
        $transacciones_egresos = Transaccion::on($conexion)
            ->select(
                DB::raw("'$concepto'  as concepto"),
                DB::raw("0 as ingresos"),
                DB::raw("SUM(monto) as egresos"),
            )
            ->where([
                ['caja_id', $caja_id],
                ['tipo', 'E']
            ])->groupBy('caja_id');

        if ($transacciones_egresos->get()->count() > 0) {
            $lista[] = $transacciones_egresos->get()[0];
        }



        $lista_operaciones = [];
        foreach ($lista as $item) {
            if ($item->ingresos > 0 || $item->egresos > 0) {
                $lista_operaciones[] = $item;
            }
        }

        return $lista_operaciones;
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
    public function listar_operaciones_agencia($fecha, $agencia_id, $id_cierre)
    {
        $fecha_actual = date($fecha);
        $fecha_actual =  date("Y-m-d", strtotime($fecha_actual));
        $agencias = Agencia::all();

        $conexion = 'master_' .  $agencia_id;
        $conexion2 = 'solucion_records_' .  $agencia_id;

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

        $cajas_cerradas = Caja::on($conexion)->from('caja_registros as caj_reg')
            ->select('id')
            ->where(DB::raw("SUBSTR(datos_cierre,11,10)"),  $fecha_actual)
            ->get();

        // dd($cajas_cerradas);

        foreach ($cajas_cerradas as  $item) {

            $resultado = $this->listar_operaciones($item->id, $agencia_id);

            foreach ($resultado as $clave => $item2) {

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
        }

        //   dd($pago_comisiones,$abono_inversion,$retiro_inversion);

        $resultado = [];
        if ($pago_cuotas->ingresos > 0 || $pago_cuotas->egresos > 0) {
            $resultado[] = $pago_cuotas;
        }
        if ($pago_moras->ingresos > 0 || $pago_moras->egresos > 0) {
            $resultado[] = $pago_moras;
        }
        if ($pago_notificaciones->ingresos > 0 || $pago_notificaciones->egresos > 0) {
            $resultado[] = $pago_notificaciones;
        }
        if ($descuento_moras->ingresos > 0 || $descuento_moras->egresos > 0) {
            $resultado[] = $descuento_moras;
        }
        if ($descuento_notificaciones->ingresos > 0 || $descuento_notificaciones->egresos > 0) {
            $resultado[] = $descuento_notificaciones;
        }
        if ($descuento_interes->ingresos > 0 || $descuento_interes->egresos > 0) {
            $resultado[] = $descuento_interes;
        }
        if ($adelanto_haberes->ingresos > 0 || $adelanto_haberes->egresos > 0) {
            $resultado[] = $adelanto_haberes;
        }
        if ($ingresos->ingresos > 0 || $ingresos->egresos > 0) {
            $resultado[] = $ingresos;
        }
        if ($egresos->ingresos > 0 || $egresos->egresos > 0) {
            $resultado[] = $egresos;
        }
        if ($desembolsos->ingresos > 0 || $desembolsos->egresos > 0) {
            $resultado[] = $desembolsos;
        }
        if ($pago_comisiones->ingresos > 0 || $pago_comisiones->egresos > 0) {
            $resultado[] = $pago_comisiones;
        }
        if ($abono_inversion->ingresos > 0 || $abono_inversion->egresos > 0) {
            $resultado[] = $abono_inversion;
        }
        if ($retiro_inversion->ingresos > 0 || $retiro_inversion->egresos > 0) {
            $resultado[] = $retiro_inversion;
        }

        // dd($resultados);

        $lista_cierres = DB::connection($conexion)->table('aplicacion_cierres as apl_cie')
            ->select(
                'usu.usuario',
                "cue_rec.monto_inicial",
                'apl_cie.datos_creacion',
                DB::raw("SUBSTRING(apl_cie.datos_creacion,11,19) as fecha_sistema"),
                'apl_cie.created_at as fecha_real',
                'apl_cie.id as id_cierre',
                'cue_rec.fecha_cierre as fecha_cierre_cu',

            )
            ->join("$conexion2.cuenta_usuarios_records as cue_rec", 'cue_rec.cierre_id', 'apl_cie.id')
            ->join('cuenta_usuarios as cue_usu', 'cue_usu.id', '=', 'cue_rec.cuenta_id')
            ->join('solucion_master.usuarios as usu', 'cue_usu.dni', 'usu.dni')
            ->where('cue_rec.cierre_id', $id_cierre)
            ->where('cue_rec.monto_inicial', '>', 0)
            ->get();

        foreach ($lista_cierres as  $value) {
            $objeto_usuario = (object) [
                'concepto' => '*** Saldo inicial de: ' . $value->usuario,
                'ingresos' => floatval($value->monto_inicial),
                'egresos' => 0
            ];
            array_push($resultado, $objeto_usuario);
        }

        $envios_monto_agencias = [];


        // foreach ($agencias as $item) {
        //     $conexion3 = 'master_' .  $item->id_agencia;

        //     $envios_monto = Envio::on($conexion3)->from('cuenta_envios as cue_env')
        //     ->select(
        //         'cue_env.agencia_remitente_id',
        //         DB::raw("SUM(cue_env.monto) as monto"),
        //     )

        //     ->where([[DB::raw("SUBSTR(cue_env.datos_creacion,11,10)"), $fecha_actual],['cue_env.estado','CONFIRMADO'],['cue_env.agencia_remitente_id',$agencia_id]])
        //     ->orwhere([[DB::raw("SUBSTR(cue_env.datos_actualizacion,11,10)"), $fecha_actual],['cue_env.estado','CONFIRMADO'],['cue_env.agencia_destinatario_id',$agencia_id]])
        //     ->get()->last()->toArray();

        //     if($envios_monto['monto'] != null){

        //         $envios_monto_agencias[]=$envios_monto;

        //     }

        // }


        $monto_envio = Movimiento::on($conexion)->from('cuenta_movimientos as cue_mov')
            ->select(
                DB::raw("SUM(cue_mov.monto) as monto_envio"),
            )

            ->where([[DB::raw("SUBSTR(cue_mov.fecha_movimiento,1,10)"), $fecha_actual], ['cue_mov.descripcion', 'ENVÍO A AGENCIA']])
            ->get()->last();

        $monto_recepcion = Movimiento::on($conexion)->from('cuenta_movimientos as cue_mov')
            ->select(
                DB::raw("SUM(cue_mov.monto) as monto_recepcion"),
            )

            ->where([[DB::raw("SUBSTR(cue_mov.fecha_movimiento,1,10)"), $fecha_actual], ['cue_mov.descripcion', 'RECEPCIÓN DE AGENCIA']])
            ->get()->last();







        $envios_monto_ingreso = $monto_recepcion->monto_recepcion;
        $envios_monto_egreso = $monto_envio->monto_envio;

        // dd($envios_monto_ingreso,$envios_monto_egreso);

        //  $envios_monto_ingreso = 0;
        // $envios_monto_egreso = 0;

        // foreach($envios_monto_agencias as $item2){

        //     if($agencia_id != $item2['agencia_remitente_id']){

        //         $envios_monto_ingreso += $item2['monto'];
        //     } else{

        //         $envios_monto_egreso += $item2['monto'];
        //     }
        // }

        // if($envios_monto_ingreso != 0){

        //     $objeto_envios = (object) [
        //         'concepto' => '*** Recepciones de agencias ',
        //         'ingresos' => floatval($envios_monto_ingreso),
        //         'egresos' => 0,
        //     ];
        //     array_unshift($resultado, $objeto_envios);

        // }
        // if($envios_monto_egreso != 0){

        //     $objeto_envios = (object) [
        //         'concepto' => '*** Envíos a agencias ',
        //         'ingresos' => 0,
        //         'egresos' => floatval($envios_monto_egreso),
        //     ];
        //     array_unshift($resultado, $objeto_envios);

        // }

        //  dd($envios_monto_ingreso,$envios_monto_egreso);

        if ($envios_monto_ingreso != null && $envios_monto_ingreso != 0) {

            $objeto_envios = (object) [
                'concepto' => '*** Recepciones de agencias ',
                'ingresos' => floatval($envios_monto_ingreso),
                'egresos' => 0,
            ];
            array_unshift($resultado, $objeto_envios);
        }
        if ($envios_monto_egreso != null && $envios_monto_egreso != 0) {

            $objeto_envios = (object) [
                'concepto' => '*** Envíos a agencias ',
                'ingresos' => 0,
                'egresos' => floatval($envios_monto_egreso),
            ];
            array_unshift($resultado, $objeto_envios);
        }

        // dd($resultado);

        return $resultado;
    }
    public function exportar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $cuentas_filtradas = json_decode($request->cuentas_filtradas);
        $lista_operaciones = json_decode($request->lista_operaciones);
        $lista_subtotales = json_decode($request->lista_subtotales);
        $totales = json_decode($request->totales);

        $tipo = $request->tipo;

        // Ordenando array de datos-------------------------------
        $data = [];

        $orden = 1;
        foreach ($cuentas_filtradas as $item) {

            $lista_agrupada = (object)[
                'usuario_cuenta' => mb_strtoupper($item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres),

                'encabezados' => [null, 'CONCEPTO', 'INGRESOS', 'EGRESOS', null],
                'lista_operaciones' => [],
                'subtotales' => (object)[
                    'subtotal_ingresos' => 0,
                    'subtotal_egresos' => 0
                ]
            ];

            $operaciones_filtradas = array_filter($lista_operaciones, function ($var) use ($item) {
                return  $var->usuario_cuenta == $item->dni;
            });

            $subtotales_filtrados = array_filter($lista_subtotales, function ($var) use ($item) {
                return  $var->usuario_cuenta == $item->dni;
            });

            $orden = 1;

            $object = (object)[
                'orden' => $orden,
                'concepto' => '*** CUENTA PERSONAL',
                'ingresos' => $item->monto,
                'egresos' => 0
            ];
            $lista_agrupada->lista_operaciones[] = $object;
            $orden += 1;

            foreach ($operaciones_filtradas as $item_2) {
                $object = (object)[
                    'orden' => $orden,
                    'concepto' => $item_2->operaciones_caja->concepto,
                    'ingresos' => $item_2->operaciones_caja->ingresos,
                    'egresos' => $item_2->operaciones_caja->egresos
                ];
                $lista_agrupada->lista_operaciones[] = $object;
                $orden += 1;
            }

            foreach ($subtotales_filtrados as $item_2) {
                $lista_agrupada->subtotales->subtotal_ingresos = $item_2->subtotal_ingresos;
                $lista_agrupada->subtotales->subtotal_egresos = $item_2->subtotal_egresos;
            }

            $data[] = $lista_agrupada;
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("./report_templates/creditos/reportes/rptOperacionesDia.xlsx");
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptOperacionesDia.xlsx");

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));

        $celda = 1;
        $lista_formato_titulos = [];
        $lista_formato_celdas_1 = [];
        $lista_formato_celdas_2 = [];
        $lista_formato_subtotales = [];
        $lista_formato_totales = [];

        $formato_encabezado = $sheet->getStyle("B4")->exportArray();

        while ($celda <= 5) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_titulos = $sheet->getStyle($columna_1 . 5)->exportArray();
            $formato_celdas_1 = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_celdas_2 = $sheet->getStyle($columna_1 . 7)->exportArray();
            $formato_subtotales = $sheet->getStyle($columna_1 . 8)->exportArray();
            $formato_totales = $sheet->getStyle($columna_1 . 10)->exportArray();

            $lista_formato_titulos[] = $formato_titulos;
            $lista_formato_celdas_1[] = $formato_celdas_1;
            $lista_formato_celdas_2[] = $formato_celdas_2;
            $lista_formato_subtotales[] = $formato_subtotales;
            $lista_formato_totales[] = $formato_totales;

            $celda++;
        }

        $sheet->removeRow(10);
        $sheet->removeRow(9);
        $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);
        $sheet->removeRow(5);
        $sheet->removeRow(4);

        // Insertando datos-----------------------------
        $indice = 4;
        foreach ($data as $item) {
            $columna_2 = 1;
            $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $item->usuario_cuenta);
            $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($formato_encabezado);

            $rango =  'B' . $indice . ':' . 'F' . $indice;
            $sheet->mergeCells($rango);

            $indice += 1;

            foreach ($item->encabezados as $element) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $element);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formato_titulos[$columna_2 - 1]);
                $columna_2 += 1;
            }

            $indice += 1;
            $columna_2 = 1;
            foreach ($item->lista_operaciones as $valor) {
                foreach ($valor as $element) {
                    $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $element);
                    $columna_2 += 1;
                }
                $columna_2 = 1;

                $formato_celdas = $indice % 2 == 0 ? $lista_formato_celdas_1 : $lista_formato_celdas_2;

                foreach ($formato_celdas as $element_1) {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($element_1);
                    $columna_2 += 1;
                }

                $indice += 1;
                $columna_2 = 1;
            }

            $subtotal_ingresos = $item->subtotales->subtotal_ingresos;
            $subtotal_egresos = $item->subtotales->subtotal_egresos;
            $subtotal_saldo = $subtotal_ingresos - $subtotal_egresos;

            $sheet->setCellValue('C' . $indice, 'SUB TOTAL');
            $sheet->setCellValue('D' . $indice, $subtotal_ingresos);
            $sheet->setCellValue('E' . $indice, $subtotal_egresos);
            $sheet->setCellValue('F' . $indice, $subtotal_saldo);

            foreach ($lista_formato_subtotales as $element) {
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($element);
                $columna_2 += 1;
            }

            $columna_2 = 1;
            $indice += 2;
        }

        $total_ingresos = $totales->total_ingresos;
        $total_egresos = $totales->total_egresos;
        $total_saldo = $total_ingresos - $total_egresos;

        $sheet->setCellValue('C' . $indice, 'TOTAL');
        $sheet->setCellValue('D' . $indice, $total_ingresos);
        $sheet->setCellValue('E' . $indice, $total_egresos);
        $sheet->setCellValue('F' . $indice, $total_saldo);



        foreach ($lista_formato_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }


        $sheet->getHeaderFooter()->setOddFooter('&LFooter of the Document&RPage &P of &N');


        // Exportar para descarga-------------------------

        if ($tipo == 'XLSX') {

            $writer = new Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $ret['data'] = base64_encode(ob_get_contents());
            ob_end_clean();
        } else if ($tipo == 'PDF') {

            $spreadsheet->getDefaultStyle()->applyFromArray(
                [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'ffffff'],
                        ],

                    ]
                ]
            );

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);

            ob_start();
            $writer->save('php://output');

            $ret['data'] = base64_encode(ob_get_contents());
            ob_end_clean();
        }

        return $ret['data'];
    }
}
