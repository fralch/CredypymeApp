<?php

namespace App\Http\Controllers\Creditos\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Facturado;
use App\Models\Creditos\Caja\Desembolso;
use App\Models\Creditos\Mantenimiento\Credito\Tipo;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Mantenimiento\TransaccionCategoria;
use App\Models\Creditos\Mantenimiento\Facturacion\Limite;
use App\Models\Creditos\Mantenimiento\Facturacion\LimiteDetalle;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class FacturacionLimiteController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function facturacion_dia()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'FACTURACION_DIA',  'CREDITOS_HERRAMIENTAS');

            if ($band == 1) {
                return Inertia::render(
                    'Creditos/Herramientas/facturacion_dia'
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function calcular_diario(Request $request)
    {
        $agencia_id = $request->agencia_id;

        if ($agencia_id == 0 || $agencia_id == null) {
            return [
                'total_facturado' => 0,
                'limite_maximo' => 0,
                'fecha_actual' => null
            ];
        }
        $conexion = 'master_' .  $agencia_id;

        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $total_facturado_hoy = Facturado::on($conexion)->from('credito_facturados as cre_fac')
            ->join('caja_desembolsos as caj_des', 'cre_fac.desembolso_id', 'caj_des.id')
            ->join('credito_registros as cre_reg', 'caj_des.credito_id', 'cre_reg.id')
            ->where(DB::raw("SUBSTR(cre_fac.datos_creacion ,11,10)"), $fecha_actual,)
            ->sum('caj_des.interes_total');

        $limite_hoy = LimiteDetalle::where('fecha', $fecha_actual)->get()->last();

        $monto_limite_agencia = 0;

        if ($limite_hoy != null) {
            $limite_detalle = json_decode($limite_hoy->detalle);
            foreach ($limite_detalle as $item) {

                if ($item->agencia_id == $agencia_id) {
                    $monto_limite_agencia = $item->monto_limite;
                }
            }
        }

        return [
            'total_facturado' => floatval($total_facturado_hoy),
            'limite_maximo' => $monto_limite_agencia,
            'fecha_actual' => $fecha_actual
        ];
    }

    public function limites()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'FACTURACION_LIMITES', 'CREDITOS_MANTENIMIENTO');

            if ($band == 1) {

                $limites = Limite::all();
                $años = Limite::select(DB::raw("DISTINCT(año) as año"))->get();
                // dd($años);
                return Inertia::render('Creditos/Mantenimiento/facturacion_limites', [
                    'limites' => $limites,
                    'años' => $años
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function verificar(Request $request)
    {
        $año = $request->año;
        $limites_existentes = Limite::where('año', $año)->get();

        $resultado = 'NO_EXISTE';
        if (count($limites_existentes) > 0) {
            $resultado = 'EXISTE';
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));
        $año = $request->año;
        $monto = $request->monto;

        $monto_mensual = $monto / 12;

        for ($i = 1; $i <= 12; $i++) {
            Limite::create([
                'año' => $año,
                'mes' => $i,
                'limite_ideal' => $monto_mensual,
                'limite_real' => $monto_mensual,
                'datos_creacion' => $datos_registro
            ]);
        }

        return redirect()->route('man.fac_limites');
    }

    public function generar_limite_diario($fecha_actual_corta, $fecha_nueva_corta, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $año_actual  = intval(date("Y", strtotime($fecha_nueva_corta)));
        $mes_actual  = intval(date("m", strtotime($fecha_nueva_corta)));

        $limite_mensual_actual = Limite::where([['año', $año_actual], ['mes', $mes_actual]])->get()->last();

        // Obtener total de créditos vigentes de la agencia del usuario
        $tipo = Tipo::on($conexion)->where('tipo', 'REFINANCIADO')->get()->last();
        $tipo_id = $tipo->id;

        $estado = Estado::on($conexion)->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $creditos_activos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select('cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->where([
                ['cre_reg.estado_id', $estado_id],
                ['cre_apr.tipo_id', '<>', $tipo_id]
            ])->get();
        // -----------------------------------

        // Porcentajes de desembolsos según día
        $porcentaje_segun_dia = [
            '1' => 0.16, //lunes
            '2' => 0.19, //martes
            '3' => 0.18, //miercoles
            '4' => 0.18, //jueves
            '5' => 0.19, //viernes
            '6' => 0.10,  //sabado
            '7' => 0        //domingo
        ];
        // -----------------------------------

        // Obtener días operativos del mes
        $domingos = 0;
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_actual, $año_actual);
        for ($i = 1; $i <= $dias_mes; $i++) {
            if (date('N', strtotime($año_actual . '-' . $mes_actual . '-' . $i)) == 7)
                $domingos++;
        }
        $dias_operativos = intval($dias_mes) - intval($domingos);
        $dia_semana_nuevo = date('N', strtotime($fecha_nueva_corta));
        // ---------------------------

        // Calcular el restante del limite del dia anterior
        $restante = 0;
        // $limite_dia_anterior = LimiteDetalle::where('fecha', $fecha_actual_corta)->get()->last();

        // if ($limite_dia_anterior != null) {
        //     $limite_dia_anterior = json_decode($limite_dia_anterior->detalle);

        //     $limite_anterior = [];
        //     foreach ($limite_dia_anterior as $item) {
        //         if ($item->agencia_id == session('id_agencia')) {
        //             $limite_anterior[] = $item;
        //         }
        //     }

        //     if (count($limite_anterior) > 0) {
        //         $monto_limite_anterior = $limite_anterior[0]->monto_limite;
        //         $facturado_dia_anterior = Facturado::from('credito_facturados as cre_fac')
        //             ->join('caja_desembolsos as caj_des', 'cre_fac.desembolso_id', 'caj_des.id')
        //             ->join('credito_registros as cre_reg', 'caj_des.credito_id', 'cre_reg.id')
        //             ->where([
        //                 [DB::raw("STR_TO_DATE(SUBSTRING(cre_fac.datos_creacion ,11,10), '%Y-%m-%d')"), $fecha_actual_corta],
        //                 ['cre_reg.agencia_id', session('id_agencia')]
        //             ])
        //             ->sum('caj_des.interes_total');

        //         $restante = round(floatval($monto_limite_anterior) - floatval($facturado_dia_anterior), 2);
        //     }
        // }

        // ---------------------------

        // Verificar si existe limites del día nuevo
        $limite_diario_actual = LimiteDetalle::where('fecha', $fecha_nueva_corta)->get()->last();
        // -----------------------------------


        if ($limite_diario_actual == null) {

            if ($dia_semana_nuevo == 7) {
                $porcentaje_limite = 0;
                $monto_limite_dia =  0;
            } else {
                $porcentaje_limite = 1;
                $monto_limite = $limite_mensual_actual->limite_real;
                $monto_limite_corresponde = round(floatval($porcentaje_limite) * floatval($monto_limite), 2);

                // Calcular monto límite del día de la agencia
                $monto_limite_semanal = (floatval($monto_limite_corresponde) / floatval($dias_operativos)) * 6;
                $porcentaje_dia  = $porcentaje_segun_dia[$dia_semana_nuevo];
                $monto_limite_dia =  round($porcentaje_dia * $monto_limite_semanal, 2);
                // ---------------------------
            }

            $lista_detalles = [];

            if ($agencia_id != 5) {

                if (in_array($agencia_id, [2, 3])) {
                    $lista_detalles[] = [
                        'agencia_id' => $agencia_id,
                        'cantidad_creditos' => count($creditos_activos),
                        'porcentaje_limite' => round($porcentaje_limite / 7.8, 2),
                        'monto_limite' => round($monto_limite_dia / 7.8, 2)
                    ];
                } else {
                    $lista_detalles[] = [
                        'agencia_id' => $agencia_id,
                        'cantidad_creditos' => count($creditos_activos),
                        'porcentaje_limite' => round($porcentaje_limite / 3.9, 2),
                        'monto_limite' => round($monto_limite_dia / 3.9, 2)
                    ];
                }
            } else {
                $lista_detalles[] = [
                    'agencia_id' => $agencia_id,
                    'cantidad_creditos' => 0,
                    'porcentaje_limite' => 0,
                    'monto_limite' => 0
                ];
            }


            LimiteDetalle::create([
                'limite_id' => $limite_mensual_actual->id,
                'fecha' => $fecha_nueva_corta,
                'detalle' => json_encode($lista_detalles),
                'datos_creacion' => $datos_registro
            ]);
        } else {

            $limite_actual_detalle = json_decode($limite_diario_actual->detalle);

            // Calculando totales de creditos
            $total_creditos = 0;
            foreach ($limite_actual_detalle as $item) {
                $total_creditos += intval($item->cantidad_creditos);
            }
            $total_creditos += intval(count($creditos_activos));
            // ---------------------------

            $lista_detalles = [];

            // Actualizando porcentajes y montos limites de cada agencia
            foreach ($limite_actual_detalle as $item) {

                if ($dia_semana_nuevo == 0) {
                    $nuevo_porcentaje = 0;
                    $nuevo_monto_limite =  0;
                } else {
                    $nuevo_porcentaje = round(intval($item->cantidad_creditos) / intval($total_creditos), 2);
                    $monto_limite = $limite_mensual_actual->limite_real;
                    $monto_limite_corresponde = round(floatval($nuevo_porcentaje) * floatval($monto_limite), 2);

                    // Calcular monto límite del día de la agencia
                    $monto_limite_semanal = (floatval($monto_limite_corresponde) / floatval($dias_operativos)) * 6;
                    $porcentaje_dia  = $porcentaje_segun_dia[$dia_semana_nuevo];
                    $nuevo_monto_limite =  round($porcentaje_dia * $monto_limite_semanal, 2);
                }
                // ---------------------------

                if (in_array($item->agencia_id, [2, 3])) {
                    $lista_detalles[] = [
                        'agencia_id' => $item->agencia_id,
                        'cantidad_creditos' => $item->cantidad_creditos,
                        'porcentaje_limite' => round($nuevo_porcentaje / 7.8, 2),
                        'monto_limite' => round($nuevo_monto_limite /  7.8, 2)
                    ];
                } else {
                    $lista_detalles[] = [
                        'agencia_id' => $item->agencia_id,
                        'cantidad_creditos' => $item->cantidad_creditos,
                        'porcentaje_limite' => round($nuevo_porcentaje / 3.9, 2),
                        'monto_limite' => round($nuevo_monto_limite /  3.9, 2)
                    ];
                }
            }
            // ---------------------------

            // Insertando nuevo limite de agencia actual
            if ($dia_semana_nuevo == 0) {
                $porcentaje_limite = 0;
                $monto_limite_dia =  0;
            } else {
                $porcentaje_limite = round(count($creditos_activos) / $total_creditos, 2);
                $monto_limite = $limite_mensual_actual->limite_real;
                $monto_limite_corresponde = round(floatval($porcentaje_limite) * floatval($monto_limite), 2);
                // Calcular monto límite del día de la agencia
                $monto_limite_semanal = (floatval($monto_limite_corresponde) / floatval($dias_operativos)) * 6;
                $porcentaje_dia  = $porcentaje_segun_dia[$dia_semana_nuevo];
                $monto_limite_dia =  round(($porcentaje_dia * $monto_limite_semanal) + $restante, 2);
                // ---------------------------
            }

            if (
                $agencia_id != 5
            ) {
                if (in_array($agencia_id, [2, 3])) {
                    $lista_detalles[] = [
                        'agencia_id' => $agencia_id,
                        'cantidad_creditos' => count($creditos_activos),
                        'porcentaje_limite' => round($porcentaje_limite / 7.8, 2),
                        'monto_limite' => round($monto_limite_dia / 7.8, 2)
                    ];
                } else {
                    $lista_detalles[] = [
                        'agencia_id' => $agencia_id,
                        'cantidad_creditos' => count($creditos_activos),
                        'porcentaje_limite' => round($porcentaje_limite / 3.9, 2),
                        'monto_limite' => round($monto_limite_dia / 3.9, 2)
                    ];
                }
            } else {
                $lista_detalles[] = [
                    'agencia_id' => $agencia_id,
                    'cantidad_creditos' => 0,
                    'porcentaje_limite' => 0,
                    'monto_limite' => 0
                ];
            }


            LimiteDetalle::where('id', $limite_diario_actual->id)->update([
                'detalle' => json_encode($lista_detalles),
                'datos_actualizacion' => $datos_registro
            ]);
            // ---------------------------
        }

        return true;
    }
}
