<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Mantenimiento\Credito\Tipo;
use App\Models\Creditos\Mantenimiento\Credito\Producto;
use App\Models\Creditos\Credito\Facturado;
use App\Models\Creditos\Mantenimiento\Facturacion\Limite;
use App\Models\Creditos\Mantenimiento\Facturacion\LimiteDetalle;
use App\Models\Creditos\Mantenimiento\Facturacion\Comprobante;

use App\Models\General\Agencia;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Caja\Desembolso;


use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;


class FacturacionController extends Controller
{


    public function verificar_facturacion($credito_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $resultado = 0;
        $datos_credito = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.capital_total',
                'cre_reg.interes_total',
                'cre_reg.cliente_id',

                'cre_apr.tipo_id',
                'cre_apr.producto_id',
            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->where('cre_reg.id', $credito_id)
            ->get()->last();

        // Filtro INTERÉS 0

        if ($datos_credito->interes_total == 0) {
            return  $resultado;
        }

        // Filtro MONTO DESEMBOLSO
        $monto_maximo = 3400;

        if ($datos_credito->capital_total >=  $monto_maximo) {
            return  $resultado;
        }
        // ---------------------------

        // Filtro NO REFINANCIADOS
        $tipo = Tipo::on($conexion)->select('id')->where('tipo', 'REFINANCIADO')->get()->last();
        $tipo_id = $tipo->id;

        if ($datos_credito->tipo_id == $tipo_id) {
            return  $resultado;
        }
        // ---------------------------

        // Filtro NO CONVENIO
        $producto = Producto::on($conexion)->select('id')->where('producto', 'CRÉDITO CONVENIO')->get()->last();

        if ($producto != null) {
            $producto_id = $producto->id;

            if ($datos_credito->producto_id == $producto_id) {
                return  $resultado;
            }
        }

        // ---------------------------

        // Filtro LÍMITE DIARIO
        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
        $limite_diario = LimiteDetalle::where('fecha', $fecha_actual)->get()->last();
        $limite_diario_detalle = json_decode($limite_diario->detalle);
        $monto_limite_agencia = 0;
        foreach ($limite_diario_detalle  as $item) {
            if ($item->agencia_id == $agencia_id) {
                $monto_limite_agencia = $item->monto_limite;
            }
        }

        $avance_factura_hoy = Desembolso::on($conexion)
            ->where(
                [
                    [
                        DB::raw("SUBSTR(datos_creacion ,11,10)"),
                        $fecha_actual
                    ],
                    ['emite_comprobante', 1]
                ]
            )
            ->sum('interes_total');

        $avance_factura_hoy += floatval($datos_credito->interes_total);

        if (round($avance_factura_hoy, 2) >= $monto_limite_agencia) {
            return  $resultado;
        }
        // ---------------------------

        // Parámetro GRUPO FACTURADOS DEL MES ANTERIOR

        $cliente_id = $datos_credito->cliente_id;

        $mes_actual = intval(date("m", strtotime($fecha_actual)));
        $año_actual = intval(date("Y", strtotime($fecha_actual)));

        $mes_anterior = date("m", strtotime($fecha_actual . "- 1 month"));
        $año_anterior = intval(date("Y", strtotime($fecha_actual . "- 1 month")));

        $año_mes_anterior =  $año_anterior . '-' . $mes_anterior;

        $condiciones = [
            ['cre_reg.cliente_id', $cliente_id],
            [DB::raw("SUBSTR(cre_fac.datos_creacion,11,7)",  $año_mes_anterior)]
        ];

        $empresa = Agencia::select('nueva_empresa')->where('id_agencia', $agencia_id)->get()->last();
        $nueva_empresa = $empresa->nueva_empresa;

        if ($nueva_empresa) {
            $condiciones[] = ['cre_fac.nueva_empresa', 1];
        }

        $facturado_mes_anterior = Facturado::on($conexion)->from('credito_facturados as cre_fac')
            ->select('cre_fac.id')
            ->join('caja_desembolsos as caj_des', 'cre_fac.desembolso_id', 'caj_des.id')
            ->join('credito_registros as cre_reg', 'caj_des.credito_id', 'cre_reg.id')
            ->where($condiciones)
            ->get();


        if (count($facturado_mes_anterior) > 0) {
            $datos_limite_actual = Limite::where([
                ['mes', $mes_actual],
                ['año', $año_actual]
            ])->get()->last();

            $datos_limite_anterior = Limite::where([
                ['mes', $mes_anterior],
                ['año', $año_anterior]
            ])->get()->last();

            $comprobantes_mes_anterior = $datos_limite_actual->comprobantes_mes_anterior;
            $comprobantes_emitidos_anterior = $datos_limite_anterior->comprobantes_emitidos;

            $nueva_cantidad =  $comprobantes_mes_anterior + 1;

            if ($comprobantes_emitidos_anterior == 0) {
                $resultado = 1;
            } else {
                $porcentaje_repetir = 0.4; //Establecido por CONTABILIDAD
                $total_comprobantes_repetir = round($porcentaje_repetir * floatval($comprobantes_emitidos_anterior), 0);

                if ($nueva_cantidad <= $total_comprobantes_repetir) {
                    $limite_id = $datos_limite_actual->id;

                    Limite::where('id', $limite_id)->update(['comprobantes_mes_anterior', $nueva_cantidad]);

                    $resultado = 1;
                }
            }
        } else {
            $resultado = 1;
        }

        return $resultado;

        // ---------------------------

    }

    // LOCAL

    public function facturar_local($datos_desembolso)
    {
        $agencia_id = intval($datos_desembolso->agencia_id);
        $conexion = 'master_' .  $agencia_id;

        $empresa = Agencia::select('nueva_empresa')->where('id_agencia', $agencia_id)->get()->last();
        $nueva_empresa = $empresa->nueva_empresa;

        $desembolso_id = $datos_desembolso->desembolso_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $desembolso = Desembolso::on($conexion)->from('caja_desembolsos as caj_des')
            ->select(
                'caj_des.id',
                'caj_des.credito_id',
                'caj_des.monto',
                'caj_des.datos_creacion',
                'caj_des.porcentaje_igv',
                'caj_des.interes_total',
                'caj_des.importe_igv',
                'caj_des.importe_gravado',
                'caj_des.emite_comprobante',
                'caj_des.facturado',

                'cre_reg.aprobacion_id',
                'cre_reg.agencia_id',

                'cli_reg.dni',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.direccion'
            )
            ->join('credito_registros as cre_reg', 'cre_reg.id', 'caj_des.credito_id')
            ->join('cliente_registros as cli_reg', 'cli_reg.id', 'cre_reg.cliente_id')
            ->where([['caj_des.id', $desembolso_id]])
            ->get()->last();

        //    -----------FACTURACION ------------
        $hoy = date("Y-m-d H:i:s");
        $nombre_cliente = $desembolso->apellido_paterno . " " . $desembolso->apellido_materno . " " . $desembolso->nombres;

        $serie = '';

        $comprobante = Comprobante::select('id')->where('comprobante', 'BOLETA')->get()->last();
        $comprobante_id = $comprobante->id;


        switch ($agencia_id) {

            case 1: //AGENCIA TAMBO:

                $serie = $comprobante_id == 1 ? 'FFF1' : 'BBB1';
                $token = "e77d106f3f144721bc6c0004714aca76314660cb61194d88b3cbc18b66e928e6";

                break;
            case 2: //AGENCIA HUANCAYO:

                $serie =  $comprobante_id == 1 ? 'FFF2' : 'BBB2';
                $token = "8015f6c9c0534f9f958d1dd685feb11962e57890aba44a38a7816cbdcb1b75ee";

                break;
            case 3: //AGENCIA PAMPAS:

                $serie =  $comprobante_id == 1 ? 'FFF3' : 'BBB3';
                $token = "9b0f17f77fa945ddabd983987fe856169de9ccef9b91465898a68bdb494a21c6";

                break;
            case 4: //AGENCIA HUANCAVELICA:

                $serie =  $comprobante_id == 1 ? 'FFF4' : 'BBB4';
                $token = "b9a6193f2d9d4d5dbffdaaae5271a2706cc0639049af42849bbcd5c2cf524e1f";

                break;
            case 6: //AGENCIA CHILCA:

                $serie =  $comprobante_id == 1 ? 'FFF6' : 'BBB6';
                $token = "8b788dac183f4313941f348c0c4149a53bfd41bf89cd42a084ee4192cc2c74fc";

                break;
        }


        $ruta = "https://api.nubefact.com/api/v1/957365d8-fc55-46fd-afbb-310406460b2a";


        $numeracion = [
            (object) ['agencia_id' => 1, 'ultimo' => 0],
            (object) ['agencia_id' => 2, 'ultimo' => 0],
            (object) ['agencia_id' => 3, 'ultimo' => 0],
            (object) ['agencia_id' => 4, 'ultimo' => 0],
            (object) ['agencia_id' => 5, 'ultimo' => 0],
            (object) ['agencia_id' => 6, 'ultimo' => 0],
        ];


        if ($nueva_empresa) {
            $ultimo_facturado = Facturado::on($conexion)->where([
                ['agencia_id', $agencia_id],
                ['nueva_empresa', 1]
            ])->get()->last();
        } else {
            $ultimo_facturado = Facturado::on($conexion)->where('agencia_id', $agencia_id)->get()->last();
        }


        $numero = 0;
        if ($ultimo_facturado == null) {
            foreach ($numeracion as $item) {
                if ($item->agencia_id == $agencia_id) {
                    $numero = intval($item->ultimo);
                }
            };
        } else {
            $numero = intval(json_decode($ultimo_facturado->datos_comprobante)->numero);
        }

        $numero += 1;

        $fecha = explode("-", substr($hoy, 0, 10));
        $importe_total = round($desembolso->interes_total, 2);
        $importe_igv = round($desembolso->importe_igv, 2);
        $importe_gravado =  round($desembolso->importe_gravado, 2);

        $data = array(
            "operacion"                => "generar_comprobante",
            "tipo_de_comprobante"               => $comprobante_id,
            "serie"                             => $serie,
            "numero"                => $numero,
            "sunat_transaction"            => "1",
            "cliente_tipo_de_documento"        => "1",
            "cliente_numero_de_documento"    => $desembolso->dni,
            "cliente_denominacion"              => $nombre_cliente,
            "cliente_direccion"                 => $desembolso->direccion,
            "cliente_email"                     => "",
            "cliente_email_1"                   => "",
            "cliente_email_2"                   => "",
            "fecha_de_emision"                  => date($fecha[2] . "-" . $fecha[1] . "-" . $fecha[0]),
            "fecha_de_vencimiento"              => "",
            "moneda"                            => "1",
            "tipo_de_cambio"                    => "",
            "porcentaje_de_igv"                 => strval($desembolso->porcentaje_igv),
            "descuento_global"                  => "",
            "descuento_global"                  => "",
            "total_descuento"                   => "",
            "total_anticipo"                    => "",
            "total_gravada"                     => strval($importe_gravado),
            "total_inafecta"                    => "",
            "total_exonerada"                   => "",
            "total_igv"                         => $importe_igv,
            "total_gratuita"                    => "",
            "total_otros_cargos"                => "",
            "total"                             => $importe_total,
            "percepcion_tipo"                   => "",
            "percepcion_base_imponible"         => "",
            "total_percepcion"                  => "",
            "total_incluido_percepcion"         => "",
            "detraccion"                        => "false",
            "observaciones"                     => "",
            "documento_que_se_modifica_tipo"    => "",
            "documento_que_se_modifica_serie"   => "",
            "documento_que_se_modifica_numero"  => "",
            "tipo_de_nota_de_credito"           => "",
            "tipo_de_nota_de_debito"            => "",
            "enviar_automaticamente_a_la_sunat" => "false",
            "enviar_automaticamente_al_cliente" => "false",
            "codigo_unico"                      => "",
            "condiciones_de_pago"               => "",
            "medio_de_pago"                     => "",
            "placa_vehiculo"                    => "",
            "orden_compra_servicio"             => "",
            "tabla_personalizada_codigo"        => "",
            "formato_de_pdf"                    => "",
            "items" => array(
                array(
                    "unidad_de_medida"          => "ZZ",
                    "codigo"                    => "001",
                    "descripcion"               => "INTERES COMPENSATORIO",
                    "cantidad"                  => "1",
                    "valor_unitario"            => strval($importe_gravado),
                    "precio_unitario"           => $importe_total,
                    "descuento"                 => "",
                    "subtotal"                  => strval($importe_gravado),
                    "tipo_de_igv"               => "1",
                    "igv"                       => $importe_igv,
                    "total"                     => $importe_total,
                    "anticipo_regularizacion"   => "false",
                    "anticipo_documento_serie"  => "",
                    "anticipo_documento_numero" => ""
                ),
            )
        );

        $data_json = json_encode($data);

        //Invocamos el servicio de NUBEFACT

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        curl_close($ch);
        #### LEER RESPUESTA DE NUBEFACT ####
        $respuesta_nubefact = json_decode($respuesta, true);

        if (isset($respuesta_nubefact['errors'])) {
            return $respuesta_nubefact['errors'];
        } else {
            Facturado::on($conexion)->create([
                'desembolso_id' => $desembolso_id,
                'agencia_id' => $agencia_id,
                'tipo_comprobante' => $comprobante_id,
                'nueva_empresa' => $nueva_empresa,
                'datos_comprobante' => json_encode($respuesta_nubefact),
                'datos_creacion' => $datos_registro
            ]);

            Desembolso::on($conexion)->where('id', $desembolso_id)
                ->update([
                    'facturado' => 1,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return true;
    }

    // PRODUCCIÓN

    public function facturar_production($datos_desembolso)
    {
        $agencia_id = intval($datos_desembolso->agencia_id);
        $conexion = 'master_' .  $agencia_id;

        $empresa = Agencia::select('nueva_empresa')->where('id_agencia', $agencia_id)->get()->last();
        $nueva_empresa = $empresa->nueva_empresa;

        $desembolso_id = $datos_desembolso->desembolso_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $desembolso = Desembolso::on($conexion)->from('caja_desembolsos as caj_des')
            ->select(
                'caj_des.id',
                'caj_des.credito_id',
                'caj_des.monto',
                'caj_des.datos_creacion',
                'caj_des.porcentaje_igv',
                'caj_des.interes_total',
                'caj_des.importe_igv',
                'caj_des.importe_gravado',
                'caj_des.emite_comprobante',
                'caj_des.facturado',

                'cre_reg.aprobacion_id',
                'cre_reg.agencia_id',

                'cli_reg.dni',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.direccion'
            )
            ->join('credito_registros as cre_reg', 'cre_reg.id', 'caj_des.credito_id')
            ->join('cliente_registros as cli_reg', 'cli_reg.id', 'cre_reg.cliente_id')
            ->where([['caj_des.id', $desembolso_id]])
            ->get()->last();

        //    -----------FACTURACION ------------
        $hoy = date("Y-m-d H:i:s");
        $nombre_cliente = $desembolso->apellido_paterno . " " . $desembolso->apellido_materno . " " . $desembolso->nombres;

        $serie = '';

        $comprobante = Comprobante::select('id')->where('comprobante', 'BOLETA')->get()->last();
        $comprobante_id = $comprobante->id;

        switch ($agencia_id) {

            case 1: //AGENCIA TAMBO:

                $serie = $comprobante_id == 1 ? 'FFF1' : 'BBB1';
                $token = "b83f416c92164eb290576217109e8d38ed781b8f754547e3aeac90c6cd372ddd";

                break;
            case 2: //AGENCIA HUANCAYO:

                $serie =  $comprobante_id == 1 ? 'FFF2' : 'BBB2';
                $token = "c479069d3e8845d99292037ccfd44010d555b75e8082429b81f9edf4ea14f304";

                break;

            case 3: //AGENCIA PAMPAS:

                $serie =  $comprobante_id == 1 ? 'FFF3' : 'BBB3';
                $token = "8f92dbd9eb8e4dd9a98decfe782a68db95f8d7572f10472e87d4384b59a78b5f";

                break;

            case 4: //AGENCIA HUANCAVELICA:

                $serie =  $comprobante_id == 1 ? 'FFF4' : 'BBB4';
                $token = "bfc6853f36fa43e181d07fca68d0fe3482a93e04baca48169f3e6465d0d854f2";

                break;

            case 5: //OF. ADMINISTRATIVA:

                $serie =  $comprobante_id == 1 ? 'FFF5' : 'BBB5';
                $token = "6309d2a198354c438971fd1bdd994494b75a0ed9c5174317829889dad912e201";

                break;

            case 6: //AGENCIA CHILCA:

                $serie =  $comprobante_id == 1 ? 'FFF6' : 'BBB6';
                $token = "50965ed974c54cba9e9066453bce8af94bdd6b25d26347a9a7f0ca2814983f0e";

                break;
        }

        $ruta = "https://api.nubefact.com/api/v1/0c3eb210-300d-4991-a1c9-deb8eb5cf45f";

        $numeracion = [
            (object) ['agencia_id' => 1, 'ultimo' => 0],
            (object) ['agencia_id' => 2, 'ultimo' => 0],
            (object) ['agencia_id' => 3, 'ultimo' => 0],
            (object) ['agencia_id' => 4, 'ultimo' => 0],
            (object) ['agencia_id' => 5, 'ultimo' => 0],
            (object) ['agencia_id' => 6, 'ultimo' => 0],
        ];

        if ($nueva_empresa) {
            $ultimo_facturado = Facturado::on($conexion)->where([
                ['agencia_id', $agencia_id],
                ['nueva_empresa', 1]
            ])->get()->last();
        } else {
            $ultimo_facturado = Facturado::on($conexion)->where('agencia_id', $agencia_id)->get()->last();
        }

        $numero = 0;
        if ($ultimo_facturado == null) {
            foreach ($numeracion as $item) {
                if ($item->agencia_id == $agencia_id) {
                    $numero = intval($item->ultimo);
                }
            };
        } else {
            $numero = intval(json_decode($ultimo_facturado->datos_comprobante)->numero);
        }

        $numero += 1;

        $fecha = explode("-", substr($hoy, 0, 10));
        $importe_total = round($desembolso->interes_total, 2);
        $importe_igv = round($desembolso->importe_igv, 2);
        $importe_gravado =  round($desembolso->importe_gravado, 2);

        $data = array(
            "operacion"                => "generar_comprobante",
            "tipo_de_comprobante"               => $comprobante_id,
            "serie"                             => $serie,
            "numero"                => $numero,
            "sunat_transaction"            => "1",
            "cliente_tipo_de_documento"        => "1",
            "cliente_numero_de_documento"    => $desembolso->dni,
            "cliente_denominacion"              => $nombre_cliente,
            "cliente_direccion"                 => $desembolso->direccion,
            "cliente_email"                     => "",
            "cliente_email_1"                   => "",
            "cliente_email_2"                   => "",
            "fecha_de_emision"                  => date($fecha[2] . "-" . $fecha[1] . "-" . $fecha[0]),
            "fecha_de_vencimiento"              => "",
            "moneda"                            => "1",
            "tipo_de_cambio"                    => "",
            "porcentaje_de_igv"                 => strval($desembolso->porcentaje_igv),
            "descuento_global"                  => "",
            "descuento_global"                  => "",
            "total_descuento"                   => "",
            "total_anticipo"                    => "",
            "total_gravada"                     => strval($importe_gravado),
            "total_inafecta"                    => "",
            "total_exonerada"                   => "",
            "total_igv"                         => $importe_igv,
            "total_gratuita"                    => "",
            "total_otros_cargos"                => "",
            "total"                             => $importe_total,
            "percepcion_tipo"                   => "",
            "percepcion_base_imponible"         => "",
            "total_percepcion"                  => "",
            "total_incluido_percepcion"         => "",
            "detraccion"                        => "false",
            "observaciones"                     => "",
            "documento_que_se_modifica_tipo"    => "",
            "documento_que_se_modifica_serie"   => "",
            "documento_que_se_modifica_numero"  => "",
            "tipo_de_nota_de_credito"           => "",
            "tipo_de_nota_de_debito"            => "",
            "enviar_automaticamente_a_la_sunat" => "false",
            "enviar_automaticamente_al_cliente" => "false",
            "codigo_unico"                      => "",
            "condiciones_de_pago"               => "",
            "medio_de_pago"                     => "",
            "placa_vehiculo"                    => "",
            "orden_compra_servicio"             => "",
            "tabla_personalizada_codigo"        => "",
            "formato_de_pdf"                    => "",
            "items" => array(
                array(
                    "unidad_de_medida"          => "ZZ",
                    "codigo"                    => "001",
                    "descripcion"               => "INTERES COMPENSATORIO",
                    "cantidad"                  => "1",
                    "valor_unitario"            => strval($importe_gravado),
                    "precio_unitario"           => $importe_total,
                    "descuento"                 => "",
                    "subtotal"                  => strval($importe_gravado),
                    "tipo_de_igv"               => "1",
                    "igv"                       => $importe_igv,
                    "total"                     => $importe_total,
                    "anticipo_regularizacion"   => "false",
                    "anticipo_documento_serie"  => "",
                    "anticipo_documento_numero" => ""
                ),
            )
        );

        $data_json = json_encode($data);

        //Invocamos el servicio de NUBEFACT

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        curl_close($ch);
        #### LEER RESPUESTA DE NUBEFACT ####
        $respuesta_nubefact = json_decode($respuesta, true);

        if (isset($respuesta_nubefact['errors'])) {
            dd($respuesta_nubefact['errors']);
        } else {
            Facturado::on($conexion)->create([
                'desembolso_id' => $desembolso_id,
                'agencia_id' => $agencia_id,
                'tipo_comprobante' => $comprobante_id,
                'nueva_empresa' => $nueva_empresa,
                'datos_comprobante' => json_encode($respuesta_nubefact),
                'datos_creacion' => $datos_registro
            ]);

            Desembolso::on($conexion)->where('id', $desembolso_id)
                ->update([
                    'facturado' => 1,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return true;
    }

    // PRODUCCION

    public function facturar_todos() //Para facturar mediante arrays
    {
        $lista = [];
        $lista[] = (object) ['nombres' => 'ESPINOZA CRISPIN DORIS LUZ', 'dni' => '41386245', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'VILLAVERDE CASTAÑEDA WILINTON', 'dni' => '45424552', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'GOMEZ CONDOR ADELA', 'dni' => '41264295', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'ESPINOZA CRISPIN DORIS LUZ', 'dni' => '41386245', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'CAPCHA ROMERO LIZ JANET', 'dni' => '48637766', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'PAUCARCHUCO SOTO ANTONIO FREDY', 'dni' => '42493456', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'FELIX CANCHARI RODRIGO', 'dni' => '45309266', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'GUERRERO MARTINEZ CHARLY SEBASTIAN', 'dni' => '44338739', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'GUTIERREZ ROJAS LURDES', 'dni' => '77053042', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'FLORES PECEROS MIGUEL', 'dni' => '47953548', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'ROJAS SHUAN YENNY YUDITH', 'dni' => '70149065', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'QUISPE PORRAS JAQUELINE ANA', 'dni' => '45928316', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'BUSTAMANTE ESPINOZA ANGGUE ZAID', 'dni' => '70667403', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'CAPCHA ROMERO LUIS MIGUEL', 'dni' => '45499242', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'CORDOVA BALBIN BERTHA', 'dni' => '20043858', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'HUARINGA MEZA CLARA LUZ', 'dni' => '62211325', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'JAIME ARAUCO ROSARIO ISABEL', 'dni' => '19852964', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'MENDOZA RODRIGUEZ FILOMENA', 'dni' => '23709498', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'OROZCO FABIAN ENA LUISA', 'dni' => '40462623', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'GAMBOA GALINDO MARIA JUANA', 'dni' => '07018316', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'QUISPE LEON JUSTINA VICTORIA', 'dni' => '23238346', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'CASQUI VILLALVA CINTYA', 'dni' => '46030516', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'QUINTO SANCHEZ INDIRA', 'dni' => '42749352', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'LAURA CCENTE JOSE CARLOS', 'dni' => '71808607', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'LUNA PRIVAT JOSE JUAN', 'dni' => '19810672', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'DE LA BREÑA REYMUNDO ROSI', 'dni' => '44478805', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'REYNOSO CAPCHA ELOY', 'dni' => '19882539', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];
        $lista[] = (object) ['nombres' => 'GOMEZ LIMAS JHESIKA', 'dni' => '43724402', 'direccion' => null, 'importe_total' => 333, 'importe_igv' => 50.7965, 'importe_gravado' => 282.2035, 'porcentaje_igv' => 18];

        //    -----------FACTURACION ------------
        $hoy = date("Y-m-d H:i:s");

        $numero = 662;

        foreach ($lista as $item) {
            $nombre_cliente = $item->nombres;

            $serie = '';

            $comprobante = Comprobante::select('id')->where('comprobante', 'BOLETA')->get()->last();
            $comprobante_id = $comprobante->id;

            $serie =  $comprobante_id == 1 ? 'FFF6' : 'BBB6';
            $token = "50965ed974c54cba9e9066453bce8af94bdd6b25d26347a9a7f0ca2814983f0e";

            $ruta = "https://api.nubefact.com/api/v1/0c3eb210-300d-4991-a1c9-deb8eb5cf45f";

            $numero += 1;

            $fecha = explode("-", substr($hoy, 0, 10));
            $importe_total = round($item->importe_total, 2);
            $importe_igv = round($item->importe_igv, 2);
            $importe_gravado =  $importe_total - $importe_igv;

            $data = array(
                "operacion"                => "generar_comprobante",
                "tipo_de_comprobante"               => $comprobante_id,
                "serie"                             => $serie,
                "numero"                => $numero,
                "sunat_transaction"            => "1",
                "cliente_tipo_de_documento"        => "1",
                "cliente_numero_de_documento"    => $item->dni,
                "cliente_denominacion"              => $nombre_cliente,
                "cliente_direccion"                 => $item->direccion,
                "cliente_email"                     => "",
                "cliente_email_1"                   => "",
                "cliente_email_2"                   => "",
                "fecha_de_emision"                  => date($fecha[2] . "-" . $fecha[1] . "-" . $fecha[0]),
                "fecha_de_vencimiento"              => "",
                "moneda"                            => "1",
                "tipo_de_cambio"                    => "",
                "porcentaje_de_igv"                 => strval($item->porcentaje_igv),
                "descuento_global"                  => "",
                "descuento_global"                  => "",
                "total_descuento"                   => "",
                "total_anticipo"                    => "",
                "total_gravada"                     => strval($importe_gravado),
                "total_inafecta"                    => "",
                "total_exonerada"                   => "",
                "total_igv"                         => $importe_igv,
                "total_gratuita"                    => "",
                "total_otros_cargos"                => "",
                "total"                             => $importe_total,
                "percepcion_tipo"                   => "",
                "percepcion_base_imponible"         => "",
                "total_percepcion"                  => "",
                "total_incluido_percepcion"         => "",
                "detraccion"                        => "false",
                "observaciones"                     => "",
                "documento_que_se_modifica_tipo"    => "",
                "documento_que_se_modifica_serie"   => "",
                "documento_que_se_modifica_numero"  => "",
                "tipo_de_nota_de_credito"           => "",
                "tipo_de_nota_de_debito"            => "",
                "enviar_automaticamente_a_la_sunat" => "false",
                "enviar_automaticamente_al_cliente" => "false",
                "codigo_unico"                      => "",
                "condiciones_de_pago"               => "",
                "medio_de_pago"                     => "",
                "placa_vehiculo"                    => "",
                "orden_compra_servicio"             => "",
                "tabla_personalizada_codigo"        => "",
                "formato_de_pdf"                    => "",
                "items" => array(
                    array(
                        "unidad_de_medida"          => "ZZ",
                        "codigo"                    => "001",
                        "descripcion"               => "POR LA VENTA DE CANASTA NAVIDEÑA",
                        "cantidad"                  => "1",
                        "valor_unitario"            => strval($importe_gravado),
                        "precio_unitario"           => $importe_total,
                        "descuento"                 => "",
                        "subtotal"                  => strval($importe_gravado),
                        "tipo_de_igv"               => "1",
                        "igv"                       => $importe_igv,
                        "total"                     => $importe_total,
                        "anticipo_regularizacion"   => "false",
                        "anticipo_documento_serie"  => "",
                        "anticipo_documento_numero" => ""
                    ),
                )
            );

            $data_json = json_encode($data);

            //Invocamos el servicio de NUBEFACT

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Authorization: Token token="' . $token . '"',
                    'Content-Type: application/json',
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            curl_close($ch);
            #### LEER RESPUESTA DE NUBEFACT ####
            $respuesta_nubefact = json_decode($respuesta, true);

            if (isset($respuesta_nubefact['errors'])) {
                dd($respuesta_nubefact);
            }
        }



        return redirect()->route('cre.index');
    }
}
