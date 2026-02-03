<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\General\SesionController;
use App\Http\Controllers\Gth\Usuarios\UsuarioController;
use App\Http\Controllers\Creditos\Mantenimiento\FacturacionLimiteController;

use App\Models\General\Feriado;
use App\Models\General\Datos_aplicacion;
use App\Models\General\Agencia;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Aprobacion;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use IntlDateFormatter;
use IntlCalendar;
use Inertia\Inertia;

class CreditosController extends Controller
{


    public function index()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PRINCIPAL', 'CREDITOS');
            if ($band == 1) {
                $agencias = Agencia::select('id_agencia', 'nombre')->get();
                return Inertia::render('Creditos/principal', ['agencias' => $agencias]);
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }

    public function fecha_larga_aplicacion($agencia_id)
    {

        $conexion = 'master_' .  $agencia_id;
        $main_db = 'solucion_master';

        $data = Datos_aplicacion::on($conexion)->where([
            ['descripcion', 'FECHA_CREDITOS'],
        ])->get()->last();

        date_default_timezone_set("America/Lima");
        $hora = date('H:i:s');

        return $data->valor_fecha . ' ' . $hora;
    }

    public function fecha_corta_aplicacion($agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;
        $main_db = 'solucion_master';

        $data = Datos_aplicacion::on($conexion)->where([
            ['descripcion', 'FECHA_CREDITOS']

        ])->get()->last();

        return $data->valor_fecha;
    }

    public function verificar_nulo($value)
    {
        if (is_null($value) || empty($value) || $value == 'null' || trim($value) == '') {
            return null;
        }
        return trim($value);
    }

    public function datos_registro($agencia_id)
    {
        $fecha = $this->fecha_larga_aplicacion($agencia_id);
        $usuario = session('usuario_dni');
        $nombre_dispositivo = session('dispositivo')->nombre;
        $tipo_dispositivo = session('dispositivo')->tipo;

        $datos_registro = (object)[
            'fecha' => $fecha,
            'usuario' => $usuario,
            'nombre_dispositivo' => $nombre_dispositivo,
            'tipo_dispositivo' => $tipo_dispositivo
        ];

        return json_encode($datos_registro);
    }

    public function cierre_dia()
    {
        $x = session()->all();


        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CIERRE_DIA', 'CREDITOS_CAJA');

            if ($band == 1) {
                $sesiones = (new SesionController)->usuarios_online();
                return Inertia::render('Creditos/Caja/cierre_dia', ['sesiones' => $sesiones]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }



    public function cronograma_sin_redondeo(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $plazo = $request->plazo;
        $monto = $request->monto;
        $tasa_interes = $request->tasa_interes;
        $periodo_pago = $request->periodo_pago;
        $con_dias_gracia = filter_var($request->con_dias_gracia, FILTER_VALIDATE_BOOLEAN);
        $fecha_desembolso = $request->fecha_desembolso;
        $es_especial = filter_var($request->es_especial, FILTER_VALIDATE_BOOLEAN);

        if ($con_dias_gracia) {
            $dias_gracia_ci = intval($request->dias_gracia_ci);
            $dias_gracia_si = intval($request->dias_gracia_si);
        } else {
            $dias_gracia_ci = 0;
            $dias_gracia_si = 0;
        }

        $datos_credito = (object)[
            'plazo' => $plazo,
            'monto' => $monto,
            'tasa_interes' => $tasa_interes,
            'periodo_pago' => $periodo_pago,
            'con_dias_gracia' => $con_dias_gracia,
            'dias_gracia_ci' => $dias_gracia_ci,
            'es_especial' => $es_especial
        ];

        $datos_desembolso = (object)[
            'monto' => $monto,
            'tasa_interes' => $tasa_interes,
            'fecha_desembolso' => $fecha_desembolso,
            'periodo_pago' => $periodo_pago,
            'plazo' => $plazo,
            'dias_gracia' => $dias_gracia_ci + $dias_gracia_si,
        ];

        $datos_cuota = $this->calcular_cuota($datos_credito);
        $datos_calendario = $this->calendario_con_redondeo($agencia_id, $datos_desembolso, $datos_cuota);

        return [
            'datos_cuota' => $datos_cuota,
            'datos_calendario' => $datos_calendario,

        ];
    }
    public function cronograma_con_redondeo(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $plazo = $request->plazo;
        $monto = $request->monto;
        $tasa_interes = $request->tasa_interes;
        $periodo_pago = $request->periodo_pago;
        $fecha_desembolso = $request->fecha_desembolso;

        $datos_credito = (object)[
            'plazo' => $plazo,
            'monto' => $monto,
            'tasa_interes' => $tasa_interes,
            'periodo_pago' => $periodo_pago
        ];

        $datos_desembolso = (object)[
            'monto' => $monto,
            'tasa_interes' => $tasa_interes,
            'fecha_desembolso' => $fecha_desembolso,
            'periodo_pago' => $periodo_pago,
            'plazo' => $plazo
        ];

        $datos_cuota = $this->calcular_cuota($datos_credito);

        $datos_calendario = $this->calendario_con_redondeo($agencia_id, $datos_desembolso, $datos_cuota);

        return [

            'datos_cuota' => $datos_cuota,
            'datos_calendario' => $datos_calendario,

        ];
    }

    public function calcular_cuota($datos_credito)
    {

        $plazo = $datos_credito->plazo;
        $monto = $datos_credito->monto;
        $tasa_interes = $datos_credito->tasa_interes;
        $periodo_pago = $datos_credito->periodo_pago;
        $con_dias_gracia = filter_var($datos_credito->con_dias_gracia, FILTER_VALIDATE_BOOLEAN);
        $es_especial = filter_var($datos_credito->es_especial, FILTER_VALIDATE_BOOLEAN);

        $interes_interno = round($monto * ($tasa_interes / 100), 2);
        $total_dias = 0;

        if ($periodo_pago == 'PAGO_UNICO') {
            $tasa_interes_total = ($plazo / 26) * ($tasa_interes / 100);
            $interes_total  = round($monto * $tasa_interes_total, 2);
            $interes_diario = round($interes_total / $plazo, 2);
            $total_pagar = round($this->redondeo_especial($monto + $interes_total), 2);

            $cuota_redondeada = $total_pagar;
        } else {
            if ($periodo_pago == 'DIARIO') {
                $interes_diario = round($interes_interno / 26, 2, PHP_ROUND_HALF_DOWN);
                $total_dias = $plazo * 1;
            } else if ($periodo_pago == 'SEMANAL') {
                $interes_diario = round($interes_interno / 24, 2, PHP_ROUND_HALF_DOWN);
                $total_dias = $plazo * 6;
            } else if ($periodo_pago == 'QUINCENAL') {
                $interes_diario = round($interes_interno / 30, 2, PHP_ROUND_HALF_DOWN);
                $total_dias = $plazo * 15;
            } else if ($periodo_pago == 'MENSUAL') {
                $interes_diario = round($interes_interno / 30, 2, PHP_ROUND_HALF_DOWN);
                $total_dias = $plazo * 30;
            }

            // Sólo aplica para CRÉDITOS ESPECIALES
            // if ($es_especial) {
            //     $interes_diario = round($interes_interno / 20, 2, PHP_ROUND_HALF_DOWN);
            // }

            $interes_adicional = 0;
            if ($con_dias_gracia) {
                $interes_adicional = $datos_credito->dias_gracia_ci * $interes_diario;
            }

            $interes_semi_completo = ($interes_diario * $total_dias) + $interes_adicional;
            $total_interes = $interes_semi_completo;
            $total_pagar = round($this->redondeo_especial($monto + $total_interes), 2);
            $cuota_redondeada = round($this->redondeo_especial($total_pagar / $plazo), 2) + $this->redondeo_especial($interes_adicional / $plazo, 2);
        }

        return (object)[
            'monto_cuota' => $cuota_redondeada
        ];
    }


    public function nombre_dia($fecha)
    {

        switch (date('w', $fecha)) {
            case 0:
                return "Domingo";
                break;
            case 1:
                return "Lunes";
                break;
            case 2:
                return "Martes";
                break;
            case 3:
                return "Miércoles";
                break;
            case 4:
                return "Jueves";
                break;
            case 5:
                return "Viernes";
                break;
            case 6:
                return "Sábado";
                break;
        }
    }
    public function nombre_mes($numero_mes)
    {

        switch ($numero_mes) {

            case 1:
                return "ENERO";
                break;
            case 2:
                return "FEBRERO";
                break;
            case 3:
                return "MARZO";
                break;
            case 4:
                return "ABRIL";
                break;
            case 5:
                return "MAYO";
                break;
            case 6:
                return "JUNIO";
                break;
            case 7:
                return "JULIO";
                break;
            case 8:
                return "AGOSTO";
                break;
            case 9:
                return "SETIEMBRE";
                break;
            case 10:
                return "OCTUBRE";
                break;
            case 11:
                return "NOVIEMBRE";
                break;
            case 12:
                return "DICIEMBRE";
                break;
        }
    }

    public function redondeo_especial($valor)
    {

        $valor = round($valor, 2);
        $band = false;
        $parte_entera = '';
        $parte_decimal = '';



        for ($i = 0; $i < strlen((string)$valor); $i++) {
            if (((string)$valor)[$i] == '.') {
                $band = true;
            }

            if ($band) {
                $parte_decimal = $parte_decimal . ((string)$valor)[$i];
            } else {
                $parte_entera = $parte_entera . ((string)$valor)[$i];
            }
        }

        if ($parte_decimal != '') {
            $parte_decimal = substr($parte_decimal, 1);
            if (strlen((string)$parte_decimal) == 2) {
                if (intval($parte_decimal) < 90) {
                    while (intval($parte_decimal) % 10 <> 0) {
                        $parte_decimal += 1;
                    }
                } else {
                    $parte_decimal = 0;
                    $parte_entera += 1;
                }
            }
        } else {
            $parte_decimal = '00';
        }

        return floatval($parte_entera . '.' . $parte_decimal);
    }

    public function calendario_con_redondeo($agencia_id, $datos_desembolso, $datos_cuota)
    {
        $monto = floatval($datos_desembolso->monto);
        $plazo = intval($datos_desembolso->plazo);
        $fecha_desembolso = $datos_desembolso->fecha_desembolso;
        $periodo_pago = $datos_desembolso->periodo_pago;
        $dias_gracia = intval($datos_desembolso->dias_gracia);

        $monto_cuota = floatval($datos_cuota->monto_cuota);

        $total_pagar = $monto_cuota * $plazo;
        $total_interes = $total_pagar - $monto;
        $sumatoria_cuotas = ($plazo * ($plazo + 1)) / 2;
        $interes_cuota = $total_interes / $sumatoria_cuotas;

        $lista_feriados = Feriado::where('agencias', 'like', '%' . $agencia_id . '%')->get();

        $feriados = [];
        foreach ($lista_feriados as $item) {
            $feriados[] = $item->fecha;
        }

        $fecha_pago = date("Y-m-d", strtotime($fecha_desembolso));

        if ($dias_gracia > 0) {
            for ($i = 1; $i <= $dias_gracia; $i++) {
                $fecha_pago = date("Y-m-d", strtotime($fecha_pago . "+1 days"));
                $dia_pago = $this->nombre_dia(strtotime($fecha_pago));
                while ($dia_pago == 'Domingo' || in_array(date("Y-m-d", strtotime($fecha_pago)), $feriados)) {
                    $fecha_pago = date("d-m-Y", strtotime($fecha_pago . "+ 1 days"));
                    $dia_pago = $this->nombre_dia(strtotime($fecha_pago));
                }
            }
        }

        $calendario = [];
        $interes_acumulado = 0;

        switch ($periodo_pago) {

            case 'DIARIO':
                for ($i = 1; $i <= $plazo; $i++) {
                    $orden = $i;
                    $fecha_pago = date("d-m-Y", strtotime($fecha_pago . "+ 1 days"));
                    $dia_pago = $this->nombre_dia(strtotime($fecha_pago));

                    while ($dia_pago == 'Domingo' || in_array(date("Y-m-d", strtotime($fecha_pago)), $feriados)) {
                        $fecha_pago = date("d-m-Y", strtotime($fecha_pago . "+ 1 days"));
                        $dia_pago = $this->nombre_dia(strtotime($fecha_pago));
                    }

                    if ($i < $plazo) {
                        $monto_interes = round($interes_cuota * ($plazo - ($i - 1)), 2);
                        $interes_acumulado += $monto_interes;
                    } else if ($i == $plazo) {
                        $monto_interes = round($total_interes - $interes_acumulado, 2);
                    }

                    $monto_capital = round($monto_cuota - $monto_interes, 2);
                    $monto = round($monto - $monto_capital, 2);

                    $cuota = (object)[
                        'orden' => $orden,
                        'fecha_pago' => $fecha_pago,
                        'dia_pago' => $dia_pago,
                        'monto_cuota' => round($monto_cuota, 2),
                        'monto_capital' => round($monto_capital, 2),
                        'monto_interes' => round($monto_interes, 2),
                        // 'monto_redondeo' => round($monto_redondeo, 2),
                        'saldo_pagar' => round($monto, 2)
                    ];

                    $calendario[] = $cuota;
                }

                break;
            case 'SEMANAL':
                for ($i = 1; $i <= $plazo; $i++) {
                    $orden = $i;
                    $fecha_pago = date("d-m-Y", strtotime($fecha_pago . "+ 1 week"));
                    $dia_pago = $this->nombre_dia(strtotime($fecha_pago));


                    if (
                        $dia_pago == 'Domingo' ||
                        in_array(date("Y-m-d", strtotime($fecha_pago)), $feriados)
                    ) {

                        $fecha_pago_2 = $fecha_pago;
                        $dia_pago_2 = $dia_pago;


                        while (
                            $dia_pago_2 == 'Domingo' ||
                            in_array(date("Y-m-d", strtotime($fecha_pago_2)), $feriados)
                        ) {
                            $fecha_pago_2 = date("d-m-Y", strtotime($fecha_pago_2 . "+ 1 days"));
                            $dia_pago_2 = $this->nombre_dia(strtotime($fecha_pago_2));
                        }

                        if ($i < $plazo) {
                            $monto_interes = round($interes_cuota * ($plazo - ($i - 1)), 2);
                            $interes_acumulado += $monto_interes;
                        } else if ($i == $plazo) {
                            $monto_interes = round($total_interes - $interes_acumulado, 2);
                        }

                        $monto_capital = round($monto_cuota - $monto_interes, 2);
                        $monto = round($monto - $monto_capital, 2);

                        $cuota = (object)[
                            'orden' => $orden,
                            'fecha_pago' => $fecha_pago_2,
                            'dia_pago' => $dia_pago_2,
                            'monto_cuota' => round($monto_cuota, 2),
                            'monto_capital' => round($monto_capital, 2),
                            'monto_interes' => round($monto_interes, 2),
                            // 'monto_redondeo' => round($monto_redondeo, 2),
                            'saldo_pagar' => round($monto, 2)
                        ];
                    } else {

                        if ($i < $plazo) {
                            $monto_interes = round($interes_cuota * ($plazo - ($i - 1)), 2);
                            $interes_acumulado += $monto_interes;
                        } else if ($i == $plazo) {
                            $monto_interes = round($total_interes - $interes_acumulado, 2);
                        }

                        $monto_capital = round($monto_cuota - $monto_interes, 2);
                        $monto = round($monto - $monto_capital, 2);

                        $cuota = (object)[
                            'orden' => $orden,
                            'fecha_pago' => $fecha_pago,
                            'dia_pago' => $dia_pago,
                            'monto_cuota' => round($monto_cuota, 2),
                            'monto_capital' => round($monto_capital, 2),
                            'monto_interes' => round($monto_interes, 2),
                            // 'monto_redondeo' => round($monto_redondeo, 2),
                            'saldo_pagar' => round($monto, 2)
                        ];
                    }

                    $calendario[] = $cuota;
                }
                break;
            case 'QUINCENAL':
                for ($i = 1; $i <= $plazo; $i++) {
                    $orden = $i;

                    $fecha_pago = date("d-m-Y", strtotime($fecha_pago . "+ 15 days"));
                    $dia_pago = $this->nombre_dia(strtotime($fecha_pago));

                    if (
                        $dia_pago == 'Domingo' ||
                        in_array(date("Y-m-d", strtotime($fecha_pago)), $feriados)
                    ) {

                        $fecha_pago_2 = $fecha_pago;
                        $dia_pago_2 = $dia_pago;

                        while (
                            $dia_pago_2 == 'Domingo' ||
                            in_array(date("Y-m-d", strtotime($fecha_pago_2)), $feriados)
                        ) {
                            $fecha_pago_2 = date("d-m-Y", strtotime($fecha_pago_2 . "+ 1 days"));
                            $dia_pago_2 = $this->nombre_dia(strtotime($fecha_pago_2));
                        }

                        if ($i < $plazo) {
                            $monto_interes = round($interes_cuota * ($plazo - ($i - 1)), 2);
                            $interes_acumulado += $monto_interes;
                        } else if ($i == $plazo) {
                            $monto_interes = round($total_interes - $interes_acumulado, 2);
                        }

                        $monto_capital = round($monto_cuota - $monto_interes, 2);
                        $monto = round($monto - $monto_capital, 2);

                        $cuota = (object)[
                            'orden' => $orden,
                            'fecha_pago' => $fecha_pago_2,
                            'dia_pago' => $dia_pago_2,
                            'monto_cuota' => round($monto_cuota, 2),
                            'monto_capital' => round($monto_capital, 2),
                            'monto_interes' => round($monto_interes, 2),
                            // 'monto_redondeo' => round($monto_redondeo, 2),
                            'saldo_pagar' => round($monto, 2)
                        ];
                    } else {

                        if ($i < $plazo) {
                            $monto_interes = round($interes_cuota * ($plazo - ($i - 1)), 2);
                            $interes_acumulado += $monto_interes;
                        } else if ($i == $plazo) {
                            $monto_interes = round($total_interes - $interes_acumulado, 2);
                        }

                        $monto_capital = round($monto_cuota - $monto_interes, 2);
                        $monto =  round($monto - $monto_capital, 2);

                        $cuota = (object)[
                            'orden' => $orden,
                            'fecha_pago' => $fecha_pago,
                            'dia_pago' => $dia_pago,
                            'monto_cuota' => round($monto_cuota, 2),
                            'monto_capital' => round($monto_capital, 2),
                            'monto_interes' => round($monto_interes, 2),
                            // 'monto_redondeo' => round($monto_redondeo, 2),
                            'saldo_pagar' => round($monto, 2)
                        ];
                    }

                    $calendario[] = $cuota;
                }
                break;
            case 'PAGO_UNICO':

                $orden = 1;

                $fecha_pago = date("d-m-Y", strtotime($fecha_pago . "+ " . $plazo . " days"));

                $dia_pago = $this->nombre_dia(strtotime($fecha_pago));

                while (
                    $dia_pago == 'Domingo' ||
                    in_array(date("Y-m-d", strtotime($fecha_pago)), $feriados)
                ) {
                    $fecha_pago = date("d-m-Y", strtotime($fecha_pago . "+ 1 days"));
                    $dia_pago = $this->nombre_dia(strtotime($fecha_pago));
                }

                $monto_interes = round($monto_cuota - $monto, 2);
                $monto_capital = $monto;

                $cuota = (object)[
                    'orden' => $orden,
                    'fecha_pago' => $fecha_pago,
                    'dia_pago' => $dia_pago,
                    'monto_cuota' => round($monto_cuota, 2),
                    'monto_capital' => round($monto_capital, 2),
                    'monto_interes' => round($monto_interes, 2),
                    // 'monto_redondeo' => round($monto_redondeo, 2),
                    'saldo_pagar' => round($monto, 2)
                ];

                $calendario[] = $cuota;

                break;
            case 'MENSUAL':

                for ($i = 1; $i <= $plazo; $i++) {
                    $orden = $i;
                    $fecha_pago = date("d-m-Y", strtotime($fecha_pago . "+ 30 days"));
                    $dia_pago = $this->nombre_dia(strtotime($fecha_pago));

                    if (
                        $dia_pago == 'Domingo' ||
                        in_array(date("Y-m-d", strtotime($fecha_pago)), $feriados)
                    ) {

                        $fecha_pago_2 = $fecha_pago;
                        $dia_pago_2 = $dia_pago;

                        while (
                            $dia_pago_2 == 'Domingo' ||
                            in_array(date("Y-m-d", strtotime($fecha_pago_2)), $feriados)
                        ) {
                            $fecha_pago_2 = date("d-m-Y", strtotime($fecha_pago_2 . "+ 1 days"));
                            $dia_pago_2 = $this->nombre_dia(strtotime($fecha_pago_2));
                        }

                        if ($i < $plazo) {
                            $monto_interes = round($interes_cuota * ($plazo - ($i - 1)), 2);
                            $interes_acumulado += $monto_interes;
                        } else if ($i == $plazo) {
                            $monto_interes = round($total_interes - $interes_acumulado, 2);
                        }

                        $monto_capital = round($monto_cuota - $monto_interes, 2);
                        $monto = round($monto - $monto_capital, 2);

                        $cuota = (object)[
                            'orden' => $orden,
                            'fecha_pago' => $fecha_pago_2,
                            'dia_pago' => $dia_pago_2,
                            'monto_cuota' => round($monto_cuota, 2),
                            'monto_capital' => round($monto_capital, 2),
                            'monto_interes' => round($monto_interes, 2),
                            // 'monto_redondeo' => round($monto_redondeo, 2),
                            'saldo_pagar' => round($monto, 2)
                        ];
                    } else {

                        if ($i < $plazo) {
                            $monto_interes = round($interes_cuota * ($plazo - ($i - 1)), 2);
                            $interes_acumulado += $monto_interes;
                        } else if ($i == $plazo) {
                            $monto_interes = round($total_interes - $interes_acumulado, 2);
                        }

                        $monto_capital = round($monto_cuota - $monto_interes, 2);
                        $monto = round($monto - $monto_capital, 2);

                        $cuota = (object)[
                            'orden' => $orden,
                            'fecha_pago' => $fecha_pago,
                            'dia_pago' => $dia_pago,
                            'monto_cuota' => round($monto_cuota, 2),
                            'monto_capital' => round($monto_capital, 2),
                            'monto_interes' => round($monto_interes, 2),
                            // 'monto_redondeo' => round($monto_redondeo, 2),
                            'saldo_pagar' => round($monto, 2)
                        ];
                    }

                    $calendario[] = $cuota;
                }
                break;
        }

        // dd(json_encode($calendario));

        return $calendario;
    }

    public function compressImage($archivo, $ruta, $calidad)
    {

        // Obtenemos la información de la imagen
        $imgInfo = getimagesize($archivo);

        $mime = $imgInfo['mime'];

        // Creamos una imagen
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($archivo);
                break;
            case 'image/png':
                $image = imagecreatefrompng($archivo);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($archivo);
                break;
            default:
                $image = imagecreatefromjpeg($archivo);
        }

        // Guardamos la imagen
        imagejpeg($image, $ruta, $calidad);

        // Devolvemos la imagen comprimida
        return true;
    }

    function num2char($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->num2char($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    public function periodo_medicion($periodo_pago)
    {
        $medicion = 'días';
        switch ($periodo_pago) {

            case 'DIARIO':
                $medicion = 'día(s)';
                break;
            case 'SEMANAL':
                $medicion = 'semana(s)';
                break;
            case 'QUINCENAL':
                $medicion = 'quincena(s)';
                break;
            case 'MENSUAL':
                $medicion = 'mes(es)';
                break;
        }

        return $medicion;
    }

    public function frecuencia_pago($periodo_pago)
    {

        if ($periodo_pago  == 'DIARIO') {
            return 1;
        } else if ($periodo_pago == 'MENSUAL') {
            return 30;
        } else if ($periodo_pago == 'PAGO_UNICO') {
            return 1;
        } else if ($periodo_pago == 'QUINCENAL') {
            return 15;
        } else if ($periodo_pago == 'SEMANAL') {
            return 7;
        } else {
            return 0;
        }
    }


    public function header_footer($agencia_id)
    {
        $fecha_actual = $this->fecha_larga_aplicacion($agencia_id);
        $usuario = session('usuario');
        $nombre_dispositivo = session('dispositivo')->nombre;
        $tipo_dispositivo = session('dispositivo')->tipo;

        return $fecha_actual . ' - ' . $usuario . ' - ' . $nombre_dispositivo . ' - ' . $tipo_dispositivo;
    }

    public function concatenar_aleatorio($string, $characters)
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $rango = 36;
        $cantidad = $characters;

        $aleatorio = '_';
        for ($i = 0; $i < $cantidad; $i++) {
            $caracter_aleatorio =  $caracteres[mt_rand(0, $rango - 1)];
            $aleatorio .= $caracter_aleatorio;
        }

        return $string . $aleatorio;
    }

    public function fecha_texto($fecha_corta)
    {
        ini_set('date.timezone', 'America/Lima');
        ini_set('intl.default_locale', 'es_PE');

        $fecha = IntlCalendar::fromDateTime($fecha_corta, 'es_PE');
        $fecha =  IntlDateFormatter::formatObject(
            $fecha,
            IntlDateFormatter::TRADITIONAL
        );
        $fecha =  explode(',', $fecha);
        $fecha = $fecha[0] . ',' . $fecha[1];

        return $fecha_corta;
    }

    public function formato_moneda($monto, $numero_decimales, $moneda)
    {
        $monto_redondeado =  number_format((float)round($monto, $numero_decimales), $numero_decimales, '.', ',');
        $resultado = null;
        if ($moneda == 'PEN') {
            $resultado = 'S/ ' . $monto_redondeado;
        } elseif ($moneda == 'USD') {
            $resultado = '$' . $monto_redondeado;
        }
        return $resultado;
    }

    // public function convertir_pdf($path_file, $file_name)
    // {
    //     try {
    //         $myTask = new OfficepdfTask('project_public_9a70a24464d44dec092caab7186491dd_am47C0c732efe0ed4f4c7bbb61ffb73ce3a42', 'secret_key_6d3540eb19f8aa4bdb7f594ed5adc8c3_kM7zUcda5b7b2b044997d14a85f3a0c7429da');

    //         $file = $myTask->addFile($_SERVER['DOCUMENT_ROOT'] . $path_file);
    //         $myTask->execute();
    //         $myTask->download($_SERVER['DOCUMENT_ROOT'] . '/temp_files');

    //         $path_pdf = '/temp_files/' . $file_name . '.pdf';
    //     } catch (\Exception $e) {
    //         $path_pdf = null;
    //     }

    //     return $path_pdf;
    // }

    public function eliminar_archivo(Request $request)
    {
        $source_file = $request->ruta;
        Storage::disk('public')->delete($source_file);
    }

    public function agencia_abreviacion($agencia_id)
    {
        switch ($agencia_id) {
            case 1:
                return 'TMB';
                break;
            case 2:
                return 'HYO';
                break;
            case 3:
                return 'PAM';
                break;
            case 4:
                return 'HVC';
                break;
            case 5:
                return 'ADM';
                break;
            case 6:
                return 'CHI';
                break;
        }
    }

    public function corregir()
    {

        $agencias = Agencia::all();

        // foreach ($agencias as $agencia) {
        // $conexion = 'master_' .  $agencia->id_agencia;
        $conexion = 'master_1';

        $estado = Estado::on($conexion)->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $aprobaciones = Aprobacion::on($conexion)
            ->select('id')
            ->whereIn('periodo_pago', ['MENSUAL'])
            ->get();

        // $fecha_corta = '2026-01-26';

        $creditos = Credito::on($conexion)->where('estado_id', $estado_id)
            ->whereIn('aprobacion_id', $aprobaciones)
            // ->where('fecha_vencimiento', '>=', $fecha_corta)
            ->get();

        foreach ($creditos as $credito) {
            $cuotas_vencidas = Cuota::on($conexion)->where([
                ['credito_id', $credito->id],
                ['dias_atraso', '>', '0']
            ])->get();

            $total_mora = 5 * count($cuotas_vencidas);

            $credito = Credito::on($conexion)->find($credito->id);

            $capital = floatval($credito->capital_total) - floatval($credito->capital_pagado);
            $interes = floatval($credito->interes_total) - floatval($credito->interes_pagado);
            $redondeo = floatval($credito->redondeo_total) - floatval($credito->redondeo_pagado);
            $mora = $total_mora - floatval($credito->mora_pagado);
            $notificaciones = floatval($credito->notificaciones_total) - floatval($credito->notificaciones_pagado);

            $saldo_total = $capital + $interes + $redondeo + $mora + $notificaciones;

            $credito->mora_total = $total_mora;
            $credito->saldo_total = $saldo_total;
            $credito->save();
        }
        // }


        return response()->json([
            'success' => true,
            'message' => 'Proceso de corrección finalizado.'
        ], 200);
    }
}
