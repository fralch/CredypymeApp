<?php

namespace Modules\Creditos\Presentation\Controllers\Credito;

use App\Http\Controllers\Controller;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Illuminate\Http\Request;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Pariente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Aval;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Aprobacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Desembolso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\ComisionPago;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Comision;

use Inertia\Inertia;

class CronogramaController extends Controller
{
    public function copia_cronograma($cliente_id, $credito_id, $agencia_id)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'COPIA_CRONOGRAMA', 'CREDITOS_CREDITO');
            if ($band == 1) {
                $conexion = 'master_' .  $agencia_id;

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
                    ->join('cliente_negocios as cli_neg', 'cli_reg.id', 'cli_neg.cliente_id')
                    ->where('cli_reg.id', $cliente_id)
                    ->get()->last();

                $cliente_id = $datos_titular->id;

                $datos_desembolso = Desembolso::on($conexion)
                    ->where('credito_id', $credito_id)
                    ->get()->last();

                $datos_credito = Credito::on($conexion)->from('credito_registros as cre_reg')
                    ->select(
                        'cre_reg.fecha_desembolso',

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
                        'cre_apr.pago_oficina',
                        'cre_apr.codigo_seguimiento',
                        'cre_apr.codigo_seguimiento_2',

                        'cre_prop.agencia_pariente',
                        'cre_prop.pariente_id',
                        'cre_prop.agencia_aval',
                        'cre_prop.aval_id',
                        'cre_prop.agencia_pariente_aval',
                        'cre_prop.pariente_aval_id',

                        'cre_tip.tipo',

                        'cre_reg.interes_total',
                        'cre_reg.dias_atraso',
                        'cre_reg.datos_creacion',

                        'cre_sec.sector',
                        'cre_pro.producto',
                    )
                    ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                    ->join('credito_propuestas as cre_prop', 'cre_apr.propuesta_id', 'cre_prop.id')
                    ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                    ->join('credito_sectores as cre_sec', 'cre_apr.sector_id', 'cre_sec.id')
                    ->join('credito_productos as cre_pro', 'cre_apr.producto_id', 'cre_pro.id')
                    ->where('cre_reg.id', $credito_id)
                    ->get()->last();

                $comision_desembolso = ComisionPago::on($conexion)->from('caja_comisiones_pagos as caj_com_pag')
                    ->select(

                        'caj_com_pag.id',
                        'caj_com_pag.comision_id',
                        'caj_com_pag.monto',

                        'cre_com.comision'
                    )
                    ->join('credito_comisiones as cre_com', 'caj_com_pag.comision_id', 'cre_com.id')
                    ->where('caj_com_pag.desembolso_id', $datos_desembolso->id)
                    ->where('caj_com_pag.comision_id', 1)
                    ->get();


                $comision_riesgo = ComisionPago::on($conexion)->from('caja_comisiones_pagos as caj_com_pag')
                    ->select(

                        'caj_com_pag.id',
                        'caj_com_pag.comision_id',
                        'caj_com_pag.monto',

                        'cre_com.comision'
                    )
                    ->join('credito_comisiones as cre_com', 'caj_com_pag.comision_id', 'cre_com.id')
                    ->where('caj_com_pag.desembolso_id', $datos_desembolso->id)
                    ->where('caj_com_pag.comision_id', 2)
                    ->get();

                $comision_domicilio = ComisionPago::on($conexion)->from('caja_comisiones_pagos as caj_com_pag')
                    ->select(

                        'caj_com_pag.id',
                        'caj_com_pag.comision_id',
                        'caj_com_pag.monto',

                        'cre_com.comision'
                    )
                    ->join('credito_comisiones as cre_com', 'caj_com_pag.comision_id', 'cre_com.id')
                    ->where('caj_com_pag.desembolso_id', $datos_desembolso->id)
                    ->where('caj_com_pag.comision_id', 3)
                    ->get();

                $analista = Credito::on($conexion)->from('credito_registros as cre_reg')
                    ->select('us.usuario')
                    ->join('solucion_master.usuarios as us', 'us.dni', 'cre_reg.asesor_id')
                    ->where('cre_reg.id', $credito_id)
                    ->get()->last();

                $cuotas_credito = Cuota::on($conexion)->from('credito_cuotas as cre_cuo')
                    ->where('cre_cuo.credito_id', $credito_id)
                    ->get();

                if ($datos_credito->pariente_id != null && $datos_credito->agencia_pariente != null) {

                    $conexion_pariente = 'master_' . $datos_credito->agencia_pariente;

                    $datos_pariente = Cliente::on($conexion_pariente)
                        ->select(
                            'id as pariente_id',
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres'
                        )
                        ->where('id', $datos_credito->pariente_id)
                        ->get()->last();
                } else {
                    $datos_pariente = null;
                }

                if ($datos_credito->aval_id != null && $datos_credito->agencia_aval != null) {

                    $conexion_aval = 'master_' . $datos_credito->agencia_aval;

                    $datos_aval = Cliente::on($conexion_aval)
                        ->select(
                            'id as aval_id',
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres'
                        )
                        ->where('id', $datos_credito->aval_id)
                        ->get()->last();
                } else {
                    $datos_aval = null;
                }

                if ($datos_credito->pariente_aval_id != null && $datos_credito->agencia_pariente_aval != null) {

                    $conexion_pariente_aval = 'master_' . $datos_credito->agencia_pariente_aval;

                    $datos_pariente_aval = Cliente::on($conexion_pariente_aval)
                        ->select(
                            'id as pariente_aval_id',
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres'
                        )
                        ->where('id', $datos_credito->pariente_aval_id)
                        ->get()->last();
                } else {
                    $datos_pariente_aval = null;
                }

                return Inertia::render('Creditos/Creditos/copia_cronograma', [
                    'agencia_id' => intval($agencia_id),
                    'datos_titular' => $datos_titular,
                    'cuotas_credito' => $cuotas_credito,
                    'datos_credito' => $datos_credito,
                    'datos_desembolso' => $datos_desembolso,
                    'comision_desembolso' => $comision_desembolso,
                    'comision_riesgo' => $comision_riesgo,
                    'comision_domicilio' => $comision_domicilio,
                    'analista' => $analista->usuario,
                    'datos_pariente' => $datos_pariente,
                    'datos_aval' => $datos_aval,
                    'datos_pariente_aval' => $datos_pariente_aval,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)
                    ->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function verificar_caja(Request $request)
    {
        $agencia_caja = $request->input('agencia_caja');
        $caja_id = $request->input('caja_id');

        $conexion = 'master_' . $agencia_caja;
        $datos_caja = Caja::on($conexion)->find($caja_id);

        $estado = 'CAJA_CERRADA';

        if ($datos_caja->datos_cierre == null) {
            $estado = 'CAJA_ABIERTA';
        }

        return response()->json(['estado' => $estado]);
    }

    public function cambiar_modo(Request $request)
    {
        $agencia_credito = $request->agencia_credito;
        $agencia_caja = $request->agencia_caja;
        $caja_id = $request->caja_id;
        $desembolso_id = $request->desembolso_id;
        $frmCambioModo = json_decode($request->frmCambioModo);

        $conexion_credito =  'master_' . $agencia_credito;
        $datos_registro = (new CreditosController)->datos_registro($agencia_credito);

        $desembolso = Desembolso::on($conexion_credito)->find($desembolso_id);
        $credito = Credito::on($conexion_credito)->find($desembolso->credito_id);

        $tipo_comision = Comision::on($conexion_credito)
            ->where('comision', 'DESEMBOLSO A DOMICILIO')
            ->get()->last();

        $modo_desembolso = $frmCambioModo->modo_desembolso;
        if ($modo_desembolso == 'DOMICILIO') {

            $comision_domicilio = filter_var($frmCambioModo->comision_domicilio, FILTER_VALIDATE_BOOLEAN);
            $monto_comision = floatval($frmCambioModo->monto_comision);

            if ($comision_domicilio) {
                // Inserta comisión cobrada
                ComisionPago::on($conexion_credito)->create([
                    'desembolso_id' => $desembolso_id,
                    'comision_id' => $tipo_comision->id,
                    'monto' => $monto_comision,
                    'agencia_caja' => $agencia_caja,
                    'caja_id' => $caja_id,
                    'fecha_pago' => $credito->fecha_desembolso,
                    'datos_creacion' => $datos_registro,
                    'datos_actualizacion' => $datos_registro,
                ]);
            }
            // Actualiza el modo de desembolso
            $desembolso->modo_desembolso = $modo_desembolso;
            $desembolso->datos_actualizacion = $datos_registro;
            $desembolso->save();
        } else if ($modo_desembolso == 'OFICINA') {

            // Se elimina la comision cobrada
            ComisionPago::on($conexion_credito)->where([
                'desembolso_id' => $desembolso_id,
                'comision_id' => $tipo_comision->id
            ])->delete();

            // Actualiza el modo de desembolso
            $desembolso->modo_desembolso = $modo_desembolso;
            $desembolso->datos_actualizacion = $datos_registro;
            $desembolso->save();
        }

        return redirect()->route('cre.copia_cronograma', [
            'cliente_id' => $credito->cliente_id,
            'credito_id' =>  $credito->id,
            'agencia_id' => $agencia_credito
        ]);
    }
}
