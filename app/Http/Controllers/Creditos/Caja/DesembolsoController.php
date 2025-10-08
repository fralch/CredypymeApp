<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\Creditos\Caja\FacturacionController;

use App\Models\General\Agencia;
use App\Models\Creditos\Credito\Aprobacion;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Caja\Desembolso;
use App\Models\Creditos\Caja\ComisionPago;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Mantenimiento\Credito\Comision;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class DesembolsoController extends Controller
{
    //
    public function desembolso($aprobacion_id, $agencia_id)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'DESEMBOLSO', 'CREDITOS_CAJA');
            if ($band == 1) {

                $conexion = 'master_' .  $agencia_id;

                $datos_aprobacion =  Aprobacion::on($conexion)
                    ->from('credito_aprobaciones as cre_apr')
                    ->select(
                        'cre_apr.id',
                        'cre_apr.propuesta_id',
                        'cre_apr.monto',
                        'cre_apr.tasa_interes',
                        'cre_apr.cuota',
                        'cre_apr.plazo',
                        'cre_apr.periodo_pago',
                        'cre_apr.es_especial',
                        'cre_apr.mora_adicional',
                        'cre_apr.con_dias_gracia',
                        'cre_apr.dias_gracia_ci',
                        'cre_apr.dias_gracia_si',
                        'cre_apr.comentario_aprobacion',
                        'cre_apr.fecha_aprobacion',
                        'cre_apr.comisiones',
                        'cre_apr.considerado_uno',
                        'cre_apr.datos_creacion',

                        'cre_pro.agencia_id',
                        'cre_pro.cliente_id',
                        'cre_pro.garantia_id',
                        'cre_pro.comentario_garantia',
                        'cre_pro.valor_garantia',
                        'cre_pro.comentario_propuesta',

                        'cli_reg.dni',
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cli_reg.codigo_expediente',
                        'cli_reg.codigo_expediente_2',
                        'cli_reg.imagen_dni',
                        'cli_reg.asesor_id',

                        'us_1.usuario as usuario_asesor',
                        'us_2.usuario as usuario_registro',

                        'cre_gar.garantia',
                        'cre_tip.tipo'

                    )->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
                    ->join('cliente_registros as cli_reg', 'cre_pro.cliente_id', 'cli_reg.id')
                    ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
                    ->join('solucion_master.usuarios as us_2', DB::raw("SUBSTR(cre_apr.datos_creacion,42,8)"), 'us_2.dni')
                    ->join('credito_garantias as cre_gar', 'cre_pro.garantia_id', 'cre_gar.id')
                    ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                    ->where([['cre_apr.id', $aprobacion_id]])
                    ->get()->last();

                // Verificar si el crédito ya está DESEMBOLSADO

                $credito =  Credito::on($conexion)
                    ->where('aprobacion_id', $aprobacion_id)
                    ->get()->last();

                if ($credito != null) {

                    $credito_id = $credito->id;
                    $datos_desembolso = Credito::on($conexion)
                        ->from('credito_registros as cre_reg')
                        ->select(
                            'cre_apr.monto',
                            'cre_apr.tasa_interes',
                            'cre_apr.cuota',
                            'cre_apr.plazo',
                            'cre_apr.periodo_pago',
                            'cre_apr.numero_credito',
                            'cre_apr.numero_credito_2',
                            'cre_apr.con_dias_gracia',
                            'cre_apr.dias_gracia_ci',
                            'cre_apr.dias_gracia_si',
                            'cre_apr.codigo_seguimiento',
                            'cre_apr.codigo_seguimiento_2',

                            'cre_reg.fecha_desembolso',

                            'caj_des.id as desembolso_id',
                            'caj_des.interes_total',
                            'caj_des.emite_comprobante',
                            'caj_des.facturado',
                            'caj_des.modo_desembolso',
                            'caj_des.datos_creacion',

                            'cre_tip.tipo',

                            'cre_fac.id as facturado_id',
                            'cre_fac.datos_comprobante'
                        )
                        ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
                        ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                        ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                        ->leftjoin('credito_facturados as cre_fac', 'cre_fac.desembolso_id', 'caj_des.id')
                        ->where('cre_reg.id', $credito_id)
                        ->get()->last();

                    $datos_titular = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                        ->select(
                            'cli_reg.id',
                            'cli_reg.nombres',
                            'cli_reg.apellido_paterno',
                            'cli_reg.apellido_materno',
                            'cli_reg.codigo_expediente',
                            'cli_reg.codigo_expediente_2',

                            'cli_neg.nombre as negocio_nombre',
                            'cli_neg.actividad as negocio_actividad',
                        )
                        ->join('credito_registros as cre_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                        ->join('cliente_negocios as cli_neg', 'cli_reg.id', 'cli_neg.cliente_id')
                        ->where('cre_reg.id', $credito_id)
                        ->get()->last();

                    $cliente_id = $datos_titular->id;

                    $datos_pariente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                        ->select(
                            'cli_reg.dni',
                            'cli_reg.apellido_paterno',
                            'cli_reg.apellido_materno',
                            'cli_reg.nombres',
                            'cli_reg.central_riesgo',

                            'cli_par.id',
                            'cli_par.pariente_id',
                            'cli_par.parentesco'
                        )
                        ->join('cliente_parientes as cli_par', 'cli_reg.id', 'cli_par.pariente_id')
                        ->where([
                            ['cli_par.cliente_id', $cliente_id],
                            ['cli_par.vinculado', 1]
                        ])->get()->last();

                    $datos_aval = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                        ->select(
                            'cli_reg.dni',
                            'cli_reg.apellido_paterno',
                            'cli_reg.apellido_materno',
                            'cli_reg.nombres',
                            'cli_reg.central_riesgo',
                            'cli_reg.direccion',
                            'cli_reg.referencia_direccion',
                            'cli_reg.telefonos',

                            'dep.departamento',
                            'pro.provincia',
                            'dis.distrito',

                            'cli_ava.id',
                            'cli_ava.aval_id',
                            'cli_ava.aval_id'
                        )
                        ->join('cliente_avales as cli_ava', 'cli_reg.id', 'cli_ava.aval_id')
                        ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id',  'dep.id')
                        ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id',  'pro.id')
                        ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id',  'dis.id')
                        ->where([
                            ['cli_ava.cliente_id', $cliente_id],
                            ['cli_ava.vinculado', 1]
                        ])->get()->last();

                    if ($datos_aval != null) {
                        $datos_pariente_aval =  Cliente::on($conexion)->from('cliente_registros as cli_reg')
                            ->select(
                                'cli_reg.dni',
                                'cli_reg.apellido_paterno',
                                'cli_reg.apellido_materno',
                                'cli_reg.nombres',
                                'cli_reg.central_riesgo',

                                'cli_par.id',
                                'cli_par.pariente_id',
                                'cli_par.parentesco'
                            )
                            ->join('cliente_parientes as cli_par', 'cli_reg.id', 'cli_par.pariente_id')
                            ->where([
                                ['cli_par.cliente_id', $datos_aval->aval_id],
                                ['cli_par.vinculado', 1]
                            ])->get()->last();
                    } else {
                        $datos_pariente_aval = null;
                    }
                    $datos_desembolso_comisiones = Desembolso::on($conexion)->from('caja_desembolsos as caj_des')
                        ->select(
                            'caj_des.id',

                            'caj_com_pag.id',
                            'caj_com_pag.comision_id',
                            'caj_com_pag.monto',

                            'cre_com.comision'
                        )
                        ->leftjoin('caja_comisiones_pagos as caj_com_pag', 'caj_com_pag.desembolso_id', 'caj_des.id')
                        ->join('credito_comisiones as cre_com', 'caj_com_pag.comision_id', 'cre_com.id')
                        ->where('caj_des.credito_id', $credito_id)
                        ->get();
                } else {
                    $credito_id = null;
                    $datos_desembolso = null;
                    $datos_desembolso_comisiones = [];
                    $datos_titular = null;
                    $datos_pariente = null;
                    $datos_aval = null;
                    $datos_pariente_aval = null;
                }

                $comisiones = Comision::on($conexion)->where('habilitado', 1)->get();

                return Inertia::render('Creditos/Caja/desembolso', [
                    'agencia_id' =>  intval($agencia_id),
                    'aprobacion_id' => intval($aprobacion_id),
                    'credito_id' => intval($credito_id),
                    'datos_aprobacion' => $datos_aprobacion,
                    'datos_desembolso' => $datos_desembolso,
                    'datos_titular' => $datos_titular,
                    'datos_pariente' => $datos_pariente,
                    'datos_aval' => $datos_aval,
                    'datos_pariente_aval' => $datos_pariente_aval,
                    'datos_desembolso_comisiones' => $datos_desembolso_comisiones,
                    'comisiones' => $comisiones

                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function guardar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_larga_aplicacion = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $datos_aprobacion = json_decode($request->datos_aprobacion);
        $caja_id = intval($request->caja_id);
        $modo_desembolso = $request->modo_desembolso;

        $aprobacion_id = $datos_aprobacion->id;

        $cliente_id = $datos_aprobacion->cliente_id;
        $asesor_id = $datos_aprobacion->asesor_id;
        $comisiones = json_decode($request->lista_comisiones);

        $plazo = $datos_aprobacion->plazo;
        $monto = $datos_aprobacion->monto;
        $tasa_interes = $datos_aprobacion->tasa_interes;
        $periodo_pago = $datos_aprobacion->periodo_pago;
        $con_dias_gracia = $datos_aprobacion->con_dias_gracia;
        $dias_gracia_ci = $datos_aprobacion->dias_gracia_ci;
        $dias_gracia_si = $datos_aprobacion->dias_gracia_si;
        $fecha_desembolso = $datos_aprobacion->fecha_aprobacion;
        $es_especial = filter_var($request->es_especial, FILTER_VALIDATE_BOOLEAN);

        $datos_credito = (object)[
            'plazo' => $plazo,
            'monto' => $monto,
            'tasa_interes' => $tasa_interes,
            'periodo_pago' => $periodo_pago,
            'con_dias_gracia' => $con_dias_gracia,
            'dias_gracia_ci' => $dias_gracia_ci,
            'es_especial' => $es_especial,
        ];

        $datos_cuota = (new CreditosController)->calcular_cuota($datos_credito);

        $datos_desembolso = (object)[
            'monto' => $monto,
            'tasa_interes' => $tasa_interes,
            'fecha_desembolso' => $fecha_desembolso,
            'periodo_pago' => $periodo_pago,
            'plazo' => $plazo,
            'dias_gracia' => intval($dias_gracia_ci) + intval($dias_gracia_si)
        ];

        $calendario_con_redondeo = (new CreditosController)->calendario_con_redondeo($agencia_id, $datos_desembolso, $datos_cuota);

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;


        if ($datos_aprobacion->periodo_pago != 'PAGO_UNICO') {
            $cuotas_pendientes = intval($plazo);
            $fecha_vencimiento = date("Y-m-d", strtotime($calendario_con_redondeo[$cuotas_pendientes - 1]->fecha_pago));
            $saldo_total = floatval($datos_cuota->monto_cuota) * floatval($plazo);
        } else {
            $cuotas_pendientes = 1;
            $fecha_vencimiento = date("Y-m-d", strtotime($calendario_con_redondeo[0]->fecha_pago));
            $saldo_total = floatval($datos_cuota->monto_cuota);
        }

        $capital_total = $monto;
        $interes_total = floatval($saldo_total) - floatval($capital_total);

        $nueva_empresa = 1;

        // Registro del crédito
        $credito = Credito::on($conexion)->create([
            'aprobacion_id' => $aprobacion_id,
            'fecha_desembolso' => $fecha_larga_aplicacion,
            'agencia_id' => $agencia_id,
            'cliente_id' => $cliente_id,
            'asesor_id' => $asesor_id,
            'cobrador_id' => $asesor_id,
            'saldo_total' => $saldo_total,
            'capital_total' => $capital_total,
            'interes_total' => $interes_total,
            'estado_id' => $estado_id,
            'nueva_empresa' => $nueva_empresa,
            'cuotas_pendientes' => $cuotas_pendientes,
            'fecha_vencimiento' => $fecha_vencimiento,

            'datos_creacion' => $datos_registro
        ]);

        $credito_id = $credito->id;

        // Registro del detalle de cuotas
        $cronograma_con_redondeo = array_map(function ($item) use ($credito_id) {

            date_default_timezone_set("America/Lima");

            return [
                'credito_id' => $credito_id,
                'numero_cuota' => $item->orden,
                'fecha_vencimiento' => date("Y-m-d", strtotime($item->fecha_pago)),
                'cuota' => $item->monto_cuota,
                'capital' => $item->monto_capital,
                'interes' => $item->monto_interes,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')

            ];
        }, $calendario_con_redondeo);

        Cuota::on($conexion)->insert($cronograma_con_redondeo);

        // Registro del desembolso
        $porcentaje_igv = 18;
        $interes_total = floatval($saldo_total) - floatval($capital_total);
        $importe_gravado = round(($interes_total) / (1 + ($porcentaje_igv / 100)), 2);
        $importe_igv = round($interes_total - $importe_gravado, 2);

        $emite_comprobante = (new FacturacionController)->verificar_facturacion($credito_id, $agencia_id);

        $desembolso = Desembolso::on($conexion)->create([
            'credito_id' => $credito_id,
            'monto' => $monto,
            'interes_total' => $interes_total,
            'porcentaje_igv' => $porcentaje_igv,
            'importe_igv' => $importe_igv,
            'importe_gravado' => $importe_gravado,
            'agencia_caja' => session('id_agencia'),
            'caja_id' => $caja_id,
            'emite_comprobante' => $emite_comprobante,
            'modo_desembolso' => $modo_desembolso,
            'datos_creacion' => $datos_registro
        ]);

        $desembolso_id = $desembolso->id;

        // Registro del pago de comisiones
        foreach ($comisiones as $item) {

            ComisionPago::on($conexion)->create([
                'desembolso_id' => $desembolso_id,
                'comision_id' => $item->comision_id,
                'monto' => $item->monto_cobrar,
                'agencia_caja' => session('id_agencia'),
                'caja_id' => $caja_id,
                'fecha_pago' => $fecha_larga_aplicacion,
                'datos_creacion' => $datos_registro
            ]);
        }

        // Verificar si el DESEMBOLSO es a DOMICILIO

        if ($modo_desembolso == 'DOMICILIO') {
            $comision_domicilio = filter_var($request->comision_domicilio, FILTER_VALIDATE_BOOLEAN);
            // Verifica si hay algún cobro por el DESEMBOLSO A DOMICILIO

            if ($comision_domicilio) {

                $monto_comision = floatval($request->monto_comision);

                $comision = Comision::on($conexion)
                    ->where('comision', 'DESEMBOLSO A DOMICILIO')
                    ->get()->last();

                $comision_id = $comision->id;

                ComisionPago::on($conexion)->create([
                    'desembolso_id' => $desembolso_id,
                    'comision_id' => $comision_id,
                    'monto' => $monto_comision,
                    'agencia_caja' => session('id_agencia'),
                    'caja_id' => $caja_id,
                    'fecha_pago' => $fecha_larga_aplicacion,
                    'datos_creacion' => $datos_registro
                ]);
            }
        }

        // Actualizando estado de la aprobación

        Aprobacion::on($conexion)->where('id', $aprobacion_id)->update([
            'estado_id' => $estado_id,
            'datos_actualizacion' => $datos_registro
        ]);

        if ($emite_comprobante == 1) {

            $datos_desembolso = (object)[
                'agencia_id' => $agencia_id,
                'desembolso_id' => $desembolso_id
            ];

            $enviroment = getenv('APP_ENV');

            if ($enviroment == 'development') {
                $facturado = (new FacturacionController)->facturar_local($datos_desembolso);
            } else if ($enviroment == 'production') {
                $facturado = (new FacturacionController)->facturar_production($datos_desembolso);
            }
        }

        return  response()->json([
            'message' => 'Desembolso realizado CORRECTAMENTE.',
            'credito_id' => $credito_id
        ], 201);
    }

    public function imprimir_cronograma(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $cronograma = json_decode($request->cronograma);
        $datos_titular = json_decode($request->datos_titular);
        $datos_credito = json_decode($request->datos_credito);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load("./report_templates/caja/reportes/rptCronograma.xlsx");

        $sheet = $spreadsheet->getActiveSheet();

        $datos_agencia = Agencia::select(
            'celular',
            'nombre',
            'cuentas'
        )
            ->where('id_agencia', $agencia_id)
            ->get()->last();

        $texto = null;

        $sheet->setCellValue('E4', $datos_credito->fecha_desembolso);
        $sheet->setCellValue('E5', $datos_credito->codigo_seguimiento);

        $sheet->setCellValue('D7', $datos_titular->titular);
        $sheet->setCellValue('D8', $datos_titular->pariente == null ? '-' : $datos_titular->pariente);
        $sheet->setCellValue('D9', $datos_titular->aval == null ? '-' : $datos_titular->aval);
        $sheet->setCellValue('D10', $datos_titular->pariente_aval == null ? '-' : $datos_titular->pariente_aval);
        $sheet->setCellValue('D11', $datos_titular->negocio);
        $sheet->setCellValue('D13', $datos_titular->asesor);
        $sheet->setCellValue('D14', $datos_credito->monto);
        $sheet->setCellValue('D15', $datos_credito->plazo);
        $sheet->setCellValue('D16', $datos_credito->periodo_pago);
        $sheet->setCellValue('D17', $datos_credito->cuota);
        $sheet->setCellValue('D18', $datos_credito->tipo);
        $sheet->setCellValue('D19', $datos_credito->tasa_interes / 100);

        // Datos informativos-----------------------------

        if ($datos_agencia->celular != null) {
            $texto = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $texto->createText('¿Alguna duda?, llámanos o escríbenos al Whatsapp ');
            $numero = $texto->createTextRun($datos_agencia->celular);
            $numero->getFont()->setBold(true);
            $sheet->setCellValue('K3', $texto);
        }

        if ($datos_agencia->cuentas != null) {
            $cuentas = json_decode($datos_agencia->cuentas);

            $fila = 7;
            $sheet->setCellValue('I6', '*** El depósito de sus cuotas a estas cuentas solo será en casos de EMERGENCIA:');
            foreach ($cuentas as  $value) {
                $sheet->setCellValue('I' . $fila, $value->banco);
                $sheet->getStyle('I' . $fila)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_DOTTED)
                    ->setColor(new Color('#BCBCBC'));
                $sheet->setCellValue('J' . $fila, $value->titular);
                $sheet->getStyle('J' . $fila)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_DOTTED)
                    ->setColor(new Color('#BCBCBC'));
                $sheet->setCellValue('L' . $fila, $value->cuenta);
                $sheet->getStyle('L' . $fila)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_DOTTED)
                    ->setColor(new Color('#BCBCBC'));
                $sheet->setCellValue('M' . $fila, 'CUENTA');
                $sheet->getStyle('M' . $fila)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_DOTTED)
                    ->setColor(new Color('#BCBCBC'));
                $fila += 1;
                $sheet->setCellValue('J' . $fila, $value->titular);
                $sheet->getStyle('J' . $fila)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_DOTTED)
                    ->setColor(new Color('#BCBCBC'));
                $sheet->setCellValue('L' . $fila, $value->cci);
                $sheet->getStyle('L' . $fila)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_DOTTED)
                    ->setColor(new Color('#BCBCBC'));
                $sheet->setCellValue('M' . $fila, 'CCI');
                $sheet->getStyle('M' . $fila)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_DOTTED)
                    ->setColor(new Color('#BCBCBC'));
                $fila += 1;
            }
        }

        // Datos de las cuotas-----------------------------

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 12) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celdas = $sheet->getStyle($columna_1 . 23)->exportArray();

            $lista_formatos_celdas[] = $formato_celdas;

            $celda++;
        }

        $formato_total_capital = $sheet->getStyle('E24')->exportArray();;
        $formato_total_interes = $sheet->getStyle('F24')->exportArray();;

        $sheet->removeRow(24);

        // Insertando datos-----------------------------

        $fila = 23;
        $columna = 1;
        foreach ($cronograma as $value) {
            $sheet->setCellValue('B' . $fila, $value->fecha_pago);
            $sheet->setCellValue('C' . $fila, $value->orden);
            $sheet->setCellValue('D' . $fila, $value->monto_cuota);
            $sheet->setCellValue('E' . $fila, $value->monto_capital);
            $sheet->setCellValue('F' . $fila, $value->monto_interes);
            $sheet->setCellValue('G' . $fila, $value->saldo_pagar < 0 ? 0 : $value->saldo_pagar);

            foreach ($lista_formatos_celdas as $item) {
                $sheet->getStyle((new CreditosController)->num2char($columna) . $fila)->applyFromArray($item);
                $columna += 1;
            }

            $sheet->getRowDimension($fila)->setRowHeight(25);

            $columna = 1;
            $fila += 1;
            $sheet->insertNewRowBefore($fila + 1);
        }

        $sheet->setCellValue('E' . $fila, $datos_credito->monto);
        $sheet->getStyle('E' . $fila)->applyFromArray($formato_total_capital);
        $sheet->setCellValue('F' . $fila, $datos_credito->interes_total);
        $sheet->getStyle('F' . $fila)->applyFromArray($formato_total_interes);

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCronograma', 5);


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';
        return ['path_pdf' => $path_pdf];
    }

    public function generar_voucher(Request $request)
    {
        // dd($request);
        $concepto = $request->concepto;

        // Ordenando array de datos-------------------------------
        $agencia_id = session('id_agencia');
        $celular_agencia = Agencia::select('celular')
            ->where('id_agencia', $agencia_id)
            ->get()->last();
        if ($concepto == 'DESEMBOLSO') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
            $spreadsheet = $reader->load("./report_templates/caja/reportes/vchDesembolso.xlsx");

            $sheet = $spreadsheet->getActiveSheet();

            // Insertando datos-----------------------------

            $datos_desembolso = json_decode($request->datos_desembolso);
            $datos_titular = json_decode($request->datos_titular);

            $fecha_desembolso = $datos_desembolso->fecha_desembolso;
            $cliente = $datos_titular->apellido_paterno . ' ' . $datos_titular->apellido_materno . ' ' . $datos_titular->nombres;
            $monto = $datos_desembolso->monto;

            $sheet->setCellValue("A3", $request->titulo);
            $sheet->setCellValue("B5", $fecha_desembolso);
            $sheet->setCellValue("A7", $cliente);
            $sheet->setCellValue("C8", $monto);

            $texto = null;

            if ($celular_agencia->celular != null) {
                $texto = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $texto->createText('¿Alguna duda?, llámanos o escríbenos al Whatsapp ');
                $numero = $texto->createTextRun($celular_agencia->celular);
                $numero->getFont()->setBold(true);
                $numero->getFont()->setSize(7);


                $sheet->setCellValue('A11', $texto);
                $sheet->setCellValue('A12', 'AGENCIA ' . $request->agencia);
                $sheet->setCellValue('A13', $fecha_desembolso);
                $sheet->setCellValue('A14', $request->usuario . ' - ' . $request->dispositivo);
            } else {
                $sheet->setCellValue('A11', 'AGENCIA ' . $request->agencia);
                $sheet->setCellValue('A12', $fecha_desembolso);
                $sheet->setCellValue('A13', $request->usuario . ' - ' . $request->dispositivo);
                $sheet->setCellValue('A14', null);
            }

            $nombre_archivo = (new CreditosController)->concatenar_aleatorio('vchDesembolso', 5);


            // dd($concepto);
        } elseif ($concepto == 'COMISION') {

            // dd($request);


            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
            $spreadsheet = $reader->load("./report_templates/caja/reportes/vchComision.xlsx");

            $sheet = $spreadsheet->getActiveSheet();

            // Insertando datos-----------------------------
            $datos_comision = json_decode($request->datos_comision);
            $datos_creacion = json_decode($request->datos_creacion);

            $fecha_desembolso = $datos_creacion->fecha;
            $cliente = $datos_comision->cliente;

            $sheet->setCellValue("A3", $request->titulo);
            $sheet->setCellValue("B5", $fecha_desembolso);
            $sheet->setCellValue("A7", $cliente);
            $sheet->setCellValue("A9", 'COMISIÓN POR ' . $datos_comision->tipo);
            $sheet->setCellValue("C10", $datos_comision->monto);

            $texto = null;

            if ($celular_agencia->celular != null) {
                $texto = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $texto->createText('¿Alguna duda?, llámanos o escríbenos al Whatsapp ');
                $numero = $texto->createTextRun($celular_agencia->celular);
                $numero->getFont()->setBold(true);
                $numero->getFont()->setSize(7);


                $sheet->setCellValue('A13', $texto);
                $sheet->setCellValue('A14', 'AGENCIA ' . $request->agencia);
                $sheet->setCellValue('A15', $fecha_desembolso);
                $sheet->setCellValue('A16', $request->usuario . ' - ' . $request->dispositivo);
            } else {
                $sheet->setCellValue('A13', 'AGENCIA ' . $request->agencia);
                $sheet->setCellValue('A14', $fecha_desembolso);
                $sheet->setCellValue('A15', $request->usuario . ' - ' . $request->dispositivo);
                $sheet->setCellValue('A16', null);
            }
            $nombre_archivo = (new CreditosController)->concatenar_aleatorio('vchComision', 5);
        }


        // Exportar para descarga-------------------------

        $spreadsheet->getDefaultStyle()->applyFromArray(
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'ffffff'],
                    ],

                ],
            ]
        );


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';


        // dd($nombre_archivo );

        return ['path_pdf' => $path_pdf];
    }

    public function listar(Request $request)
    {
        $agencia_id = (new CreditosController)->verificar_nulo($request->agencia_id);

        if ($agencia_id == null) {
            return [];
        }

        $estado = Estado::select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        return Credito::from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',

                'caj_des.monto',
                'caj_des.datos_creacion',

                'cre_apr.plazo',
                'cre_apr.periodo_pago',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.codigo_expediente',

                'age.nombre as agencia',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_registro'
            )
            ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('agencias as age', 'cre_pro.agencia_id', 'age.id_agencia')
            ->join('usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->join('usuarios as us_2', DB::raw("SUBSTRING(cre_apr.datos_creacion,42,8)"), 'us_2.dni')
            ->join('credito_estados as cre_est', 'cre_apr.estado_id', 'cre_est.id')
            ->where([
                ['cre_reg.estado_id', $estado_id],
                ['cre_reg.agencia_id', $agencia_id]
            ])
            ->get();
    }

    public function buscar(Request $request)
    {

        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;
        $main_db = 'solucion_master';

        $texto_buscar = $request->texto_buscar;
        $tipo_filtro = $request->tipo_filtro;

        $columna = "";
        $operator = 'like';
        $search = "%$texto_buscar%";

        if ($tipo_filtro == 'apellidos_nombres') {
            $columna = 'cli_reg.apellido_paterno, " ", cli_reg.apellido_materno, " ", cli_reg.nombres';
        } else if ($tipo_filtro == 'dni') {
            $columna = 'cli_reg.dni';
        } else if ($tipo_filtro == 'expediente') {
            $columna = 'cli_reg.codigo_expediente';
        }

        $cancelados_parcial = $request->cancelados_parcial;
        if ($cancelados_parcial == 'true') {
            $lista_estados = ['DESEMBOLSADO', 'CANCELADO PARCIAL'];
        } else {
            $lista_estados = ['DESEMBOLSADO'];
        }

        $estados = Estado::on($conexion)->select('id')->whereIn('estado', $lista_estados)->get();


        return Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.dias_atraso',

                'caj_des.monto',
                'caj_des.datos_creacion',

                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.cuota',
                'cre_apr.numero_credito',

                'cli_reg.id as cliente_id',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.numero_expediente',

                'age.nombre as agencia',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_registro',

                'cre_est.estado',
                'cre_tip.tipo'
            )
            ->join('caja_desembolsos as caj_des', 'caj_des.credito_id', 'cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join("$main_db.agencias as age", 'cre_pro.agencia_id', 'age.id_agencia')
            ->join("$main_db.usuarios as us_1", 'cli_reg.asesor_id', 'us_1.dni')
            ->leftjoin("$main_db.usuarios as us_2", DB::raw("SUBSTR(cre_apr.datos_creacion,42,8)"), 'us_2.dni')
            ->join('credito_estados as cre_est', 'cre_apr.estado_id', 'cre_est.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->whereIn('cre_reg.estado_id', $estados)
            ->where(
                DB::raw("CONCAT($columna)"),
                $operator,
                $search,
            )
            ->get();
    }
}
