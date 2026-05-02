<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Tipo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Producto;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Departamento;
use Modules\General\Infrastructure\Persistence\Eloquent\Provincia;
use Modules\General\Infrastructure\Persistence\Eloquent\Distrito;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Desembolso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\ComisionPago;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Comision;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Carbon\Carbon;

class ReporteCobranzasController extends Controller
{
    public function cobranzas_auxiliar($modo)
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_COBRANZAS_AUXILIAR', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_MIS_COBRANZAS_AUXILIAR', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {
                $agencias = Agencia::all();

                $usuarios_caja = (array)[];

                if ($modo == 'completo') {
                    foreach ($agencias as $item) {
                        $conexion = 'master_' .  $item->id_agencia;
                        $cuentas = CuentaUsuario::on($conexion)
                            ->from('cuenta_usuarios as cue_usu')
                            ->select(
                                'cue_usu.dni',

                                'usu.usuario',
                                DB::raw("$item->id_agencia as agencia_id"),
                                'usu.habilitado'
                            )
                            ->join('solucion_master.usuarios as usu', 'cue_usu.dni', 'usu.dni')
                            ->where('cue_usu.con_cuenta', 1)
                            ->orderBy('usu.usuario', 'asc')
                            ->get();
                        foreach ($cuentas as $item_1) {
                            $usuarios_caja[] = $item_1;
                        }
                    }
                } elseif ($modo == 'personal') {
                    $usuarios_caja[] = Usuario::find(session('usuario_dni'));
                }

                return Inertia::render('Creditos/Reportes/Caja/cobranzas_auxiliar', [
                    'modo' => $modo,
                    'usuarios_caja' => $usuarios_caja
                ]);
            } else {
                return redirect('/');
            }
        }
    }

    public function lugar_cobranza($modo)
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_LUGAR_COBRANZA', 'CREDITOS_REPORTES');
            } else {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MI_LUGAR_COBRANZA', 'CREDITOS_REPORTES');
            }
            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ])->get();

                if ($modo == 'completo') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->where('habilitado', 1)
                        ->whereIn('cargo_id', $cargos)
                        ->orderBy('usuario', 'asc')
                        ->get();
                } elseif ($modo == 'personal') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->where('dni', session('usuario_dni'))
                        ->orderBy('usuario', 'asc')
                        ->get();
                }

                return Inertia::render('Creditos/Reportes/Creditos/lugar_cobranza', [
                    'modo' => $modo,
                    'usuarios' => $usuarios
                ]);
            } else {
                return redirect('/');
            }
        }
    }

    public function buscar($modo, Request $request)
    {
        if ($modo == 'por_auxiliar') {
            return  $this->buscar_por_auxiliar($request);
        } else if ($modo == 'por_lugar_cobranza') {
            return $this->buscar_por_lugar_cobranza($request);
        } else if ($modo == 'en_negocio') {
            return $this->buscar_en_negocio($request);
        } else if ($modo == 'por_recibo') {
            return $this->buscar_por_recibo($request);
        }
    }

    public function buscar_por_auxiliar($request)
    {


        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = date("Y-m-d", strtotime($request->input('fecha_hasta') . "+ 1 days"));

        $esquema = Cliente::on($conexion)->select(
            DB::raw("0 as pago_id"),
            DB::raw("0 as credito_id"),
            DB::raw("0 as cliente_id"),
            DB::raw("0 as aprobacion_id"),
            DB::raw("0 as numero_cuota"),
            DB::raw("null as fecha_pago_credito"),
            DB::raw("null as fecha_pago"),
            DB::raw("null as fecha_pago_corta"),
            DB::raw("null as agencia_caja"),
            DB::raw("null as caja_id"),
            DB::raw("null as asesor_id"),
            DB::raw("0 as pago_banco"),

            DB::raw("0 as capital"),
            DB::raw("0 as interes"),
            DB::raw("0 as redondeo"),
            DB::raw("0 as moras"),
            DB::raw("0 as notificaciones"),
            DB::raw("0 as dscto_mora"),
            DB::raw("0 as dscto_notificaciones"),
            DB::raw("0 as dscto_interes"),
            DB::raw("0 as comision_desembolso"),
            DB::raw("0 as comision_riesgo"),
            DB::raw("0 as comision_domicilio"),
            DB::raw("null as comentario"),
            DB::raw("null as documento")

        )->take(1);

        $rango = PagoCuota::on($conexion)->select('id')->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_cuotas = PagoCuota::on($conexion)->from('caja_pago_cuotas as caj_pag_cuo')
                ->select(
                    'caj_pag_cuo.id as pago_id',
                    'cre_reg.id as credito_id',
                    'cre_reg.cliente_id',
                    'cre_reg.aprobacion_id',
                    'caj_pag_cuo.numero_cuota',
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_cuo.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_cuo.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_cuo.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_cuo.agencia_caja',
                    'caj_pag_cuo.caja_id',
                    'cre_reg.asesor_id',
                    'caj_pag_cuo.pago_banco',

                    'caj_pag_cuo.capital_pagado as capital',
                    'caj_pag_cuo.interes_pagado as interes',
                    'caj_pag_cuo.redondeo_pagado as redondeo',
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    DB::raw("0 as comision_desembolso"),
                    DB::raw("0 as comision_riesgo"),
                    DB::raw("0 as comision_domicilio"),
                    'caj_pag_cuo.comentario',
                    DB::raw("null as documento")

                )
                ->join('credito_registros as cre_reg', 'caj_pag_cuo.credito_id', 'cre_reg.id')
                ->whereIn('caj_pag_cuo.id', $rango);
        } else {
            $pago_cuotas = [];
        }

        $rango = PagoMora::on($conexion)->select('id')->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_moras = PagoMora::on($conexion)->from('caja_pago_moras as caj_pag_mor')
                ->select(
                    'caj_pag_mor.id as pago_id',
                    'cre_reg.id as credito_id',
                    'cre_reg.cliente_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_mor.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_mor.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_mor.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_mor.agencia_caja',
                    'caj_pag_mor.caja_id',
                    'cre_reg.asesor_id',
                    'caj_pag_mor.pago_banco',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    'caj_pag_mor.monto as moras',
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    DB::raw("0 as comision_desembolso"),
                    DB::raw("0 as comision_riesgo"),
                    DB::raw("0 as comision_domicilio"),
                    'caj_pag_mor.comentario',
                    DB::raw("null as documento")
                )
                ->join('credito_registros as cre_reg', 'caj_pag_mor.credito_id', 'cre_reg.id')
                ->whereIn('caj_pag_mor.id', $rango);
        } else {
            $pago_moras = [];
        }

        $rango = PagoNotificacion::on($conexion)->select('id')->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])->get();

        if (count($rango) > 0) {
            $pago_notificaciones = PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
                ->select(
                    'caj_pag_not.id as pago_id',
                    'cre_reg.id as credito_id',
                    'cre_reg.cliente_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    DB::raw("CONCAT(cre_reg.id,'-',caj_pag_not.fecha_pago)as fecha_pago_credito"),
                    'caj_pag_not.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_not.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_not.agencia_caja',
                    'caj_pag_not.caja_id',
                    'cre_reg.asesor_id',
                    'caj_pag_not.pago_banco',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    'caj_pag_not.monto as notificaciones',
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    DB::raw("0 as comision_desembolso"),
                    DB::raw("0 as comision_riesgo"),
                    DB::raw("0 as comision_domicilio"),
                    'caj_pag_not.comentario',
                    DB::raw("null as documento")
                )
                ->join('credito_notificaciones as cre_not', 'caj_pag_not.notificacion_id', 'cre_not.id')
                ->join('credito_registros as cre_reg', 'cre_not.credito_id', 'cre_reg.id')
                ->whereIn('caj_pag_not.id', $rango);
        } else {
            $pago_notificaciones = [];
        }


        $rango = ComisionPago::on($conexion)
            ->select('id')
            ->whereBetween('fecha_pago', [$fecha_desde, $fecha_hasta])
            ->get();

        if (count($rango) > 0) {

            $comision_desembolso = Comision::on($conexion)->select('id')->where('comision', 'DESEMBOLSO')->get()->last();
            $comision_desembolso_id = $comision_desembolso->id;

            $comision_riesgo = Comision::on($conexion)->select('id')->where('comision', 'RIESGO CREDITICIO')->get()->last();
            $comision_riesgo_id = $comision_riesgo->id;

            $comision_domicilio = Comision::on($conexion)->select('id')->where('comision', 'DESEMBOLSO A DOMICILIO')->get()->last();
            $comision_domicilio_id = $comision_domicilio->id;

            $pago_comisiones_desembolsos = ComisionPago::on($conexion)->from('caja_comisiones_pagos as caj_pag_com')
                ->select(
                    'caj_pag_com.id as pago_id',
                    DB::raw("0 as credito_id"),
                    'cre_reg.cliente_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    'caj_pag_com.fecha_pago as fecha_pago_credito',
                    'caj_pag_com.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_com.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_com.agencia_caja',
                    'caj_pag_com.caja_id',
                    'cre_reg.asesor_id',
                    DB::raw("0 as pago_banco"),

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    'caj_pag_com.monto as comision_desembolso',
                    DB::raw("0 as comision_riesgo"),
                    DB::raw("0 as comision_domicilio"),
                    DB::raw("null as comentario"),
                    DB::raw("null as documento")
                )
                ->join('caja_desembolsos as cre_des', 'caj_pag_com.desembolso_id', 'cre_des.id')
                ->join('credito_registros as cre_reg', 'cre_des.credito_id', 'cre_reg.id')
                ->where('caj_pag_com.comision_id', $comision_desembolso_id)
                ->whereIn('caj_pag_com.id', $rango);

            $pago_comisiones_riesgos = ComisionPago::on($conexion)->from('caja_comisiones_pagos as caj_pag_com')
                ->select(
                    'caj_pag_com.id as pago_id',
                    DB::raw("0 as credito_id"),
                    'cre_reg.cliente_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    'caj_pag_com.fecha_pago as fecha_pago_credito',
                    'caj_pag_com.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_com.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_com.agencia_caja',
                    'caj_pag_com.caja_id',
                    'cre_reg.asesor_id',
                    DB::raw("0 as pago_banco"),

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    DB::raw("0 as comision_desembolso"),
                    'caj_pag_com.monto as comision_riesgo',
                    DB::raw("0 as comision_domicilio"),
                    DB::raw("null as comentario"),
                    DB::raw("null as documento")
                )
                ->join('caja_desembolsos as cre_des', 'caj_pag_com.desembolso_id', 'cre_des.id')
                ->join('credito_registros as cre_reg', 'cre_des.credito_id', 'cre_reg.id')
                ->where('caj_pag_com.comision_id', $comision_riesgo_id)
                ->whereIn('caj_pag_com.id', $rango);

            $pago_comisiones_domicilio = ComisionPago::on($conexion)
                ->from('caja_comisiones_pagos as caj_pag_com')
                ->select(
                    'caj_pag_com.id as pago_id',
                    DB::raw("0 as credito_id"),
                    'cre_reg.cliente_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    'caj_pag_com.fecha_pago as fecha_pago_credito',
                    'caj_pag_com.fecha_pago',
                    DB::raw("SUBSTR(caj_pag_com.fecha_pago,1,10) as fecha_pago_corta"),
                    'caj_pag_com.agencia_caja',
                    'caj_pag_com.caja_id',
                    'cre_reg.asesor_id',
                    DB::raw("0 as pago_banco"),

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    DB::raw("0 as dscto_mora"),
                    DB::raw("0 as dscto_notificaciones"),
                    DB::raw("0 as dscto_interes"),
                    DB::raw("0 as comision_desembolso"),
                    DB::raw("0 as comision_riesgo"),
                    'caj_pag_com.monto as comision_domicilio',
                    DB::raw("null as comentario"),
                    DB::raw("null as documento")
                )
                ->join('caja_desembolsos as cre_des', 'caj_pag_com.desembolso_id', 'cre_des.id')
                ->join('credito_registros as cre_reg', 'cre_des.credito_id', 'cre_reg.id')
                ->where('caj_pag_com.comision_id', $comision_domicilio_id)
                ->whereIn('caj_pag_com.id', $rango);
        } else {
            $pago_comisiones_desembolsos = [];
            $pago_comisiones_riesgos = [];
            $pago_comisiones_domicilio = [];
        }

        $rango = Credito::on($conexion)
            ->select('id')
            ->where('fecha_hora_cancelado', '<>', null)
            ->whereBetween('fecha_hora_cancelado', [$fecha_desde, $fecha_hasta])
            ->get();

        if (count($rango) > 0) {
            $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
            $estado_id = $estado->id;

            $descuentos = Credito::on($conexion)->from('credito_registros as cre_reg')
                ->select(
                    'cre_reg.id as pago_id',
                    'cre_reg.id as credito_id',
                    'cre_reg.cliente_id',
                    'cre_reg.aprobacion_id',
                    DB::raw("0 as numero_cuota"),
                    DB::raw("CONCAT(cre_reg.id,'-',cre_reg.fecha_hora_cancelado)as fecha_pago_credito"),
                    'cre_reg.fecha_hora_cancelado as fecha_pago',
                    DB::raw("SUBSTR(cre_reg.fecha_hora_cancelado,1,10) as fecha_pago_corta"),
                    'cre_reg.agencia_caja_cancelado as agencia_caja',
                    'cre_reg.caja_cancelado_id as caja_id',
                    'cre_reg.asesor_id',
                    'cre_reg.pago_banco_cancelado as pago_banco',

                    DB::raw("0 as capital"),
                    DB::raw("0 as interes"),
                    DB::raw("0 as redondeo"),
                    DB::raw("0 as moras"),
                    DB::raw("0 as notificaciones"),
                    'cre_reg.dscto_mora_cancelado as dscto_mora',
                    'cre_reg.dscto_notificaciones_cancelado as dscto_notificaciones',
                    'cre_reg.dscto_interes_cancelado as dscto_interes',
                    DB::raw("0 as comision_desembolso"),
                    DB::raw("0 as comision_riesgo"),
                    DB::raw("0 as comision_domicilio"),
                    'cre_reg.comentario_cancelado as comentario',
                    'cre_reg.documento_cancelado as documento'
                )
                ->where('cre_reg.estado_id', $estado_id)
                ->whereIn('cre_reg.id', $rango);
        } else {
            $descuentos = [];
        }

        $listas = [
            $pago_cuotas,
            $pago_moras,
            $pago_notificaciones,
            $pago_comisiones_desembolsos,
            $pago_comisiones_riesgos,
            $pago_comisiones_domicilio,
            $descuentos
        ];



        foreach ($listas as $value) {
            if ($value != []) {
                $esquema->union($value);
            }
        }


        $lista = DB::connection($conexion)->table(DB::raw("({$esquema->toSql()}) as pagos"))
            ->mergeBindings($esquema->getQuery())
            ->join('cliente_registros as cli_reg', 'pagos.cliente_id', 'cli_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'pagos.aprobacion_id', 'cre_apr.id')
            ->leftjoin('caja_desembolsos as caj_des', 'pagos.credito_id', 'caj_des.credito_id')
            ->join('solucion_master.usuarios as usu', 'pagos.asesor_id', 'usu.dni')
            ->select(
                'pagos.*',

                'cli_reg.numero_expediente',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',

                'cre_apr.numero_credito',

                DB::raw("IFNULL(caj_des.facturado, 0) as boleta"),

                'usu.usuario as usuario_asesor'
            )
            ->orderBy('pagos.fecha_pago', 'desc')
            ->orderBy('pagos.numero_cuota', 'asc')
            ->get();

        $lista = $lista->groupBy('fecha_pago_credito')->map(function ($row)  use ($conexion, $agencia_id) {

            $cliente = $row[0]->apellido_paterno . ' ' . $row[0]->apellido_materno . ' ' . $row[0]->nombres;

            $total_pago = floatval($row->sum('capital'))  +
                floatval($row->sum('interes'))  +
                floatval($row->sum('redondeo'))  +
                floatval($row->sum('moras'))  +
                floatval($row->sum('notificaciones'))  -
                floatval($row->sum('dscto_mora'))  -
                floatval($row->sum('dscto_notificaciones'))  -
                floatval($row->sum('dscto_interes')) +
                floatval($row->sum('comision_desembolso')) +
                floatval($row->sum('comision_riesgo')) +
                floatval($row->sum('comision_domicilio'));

            $numero_cuota = $row->max('numero_cuota');

            if ($numero_cuota == 0) {
                $numero_cuota = '-';
            }
            $documento = $row->max('documento');

            if ($documento != null) {
                $anio_extraido = explode('_', $documento)[0];
                $ruta = '/imagenes_server/creditos/caja/cancelaciones/' . $agencia_id . '/' . $anio_extraido . '/' . $documento;
            } else {
                $ruta = null;
            }


            return (object)[
                'credito_id' => $row[0]->credito_id,
                'numero_expediente' =>  $row[0]->numero_expediente,
                'numero_credito' =>  $row[0]->numero_credito,
                'numero_cuota' =>  $numero_cuota,
                'cliente' =>  $cliente,
                'fecha_pago' =>  $row[0]->fecha_pago,
                'fecha_pago_corta' =>  $row[0]->fecha_pago_corta,
                'agencia_caja' =>  $row[0]->agencia_caja,
                'caja_id' =>  $row[0]->caja_id,
                'asesor_id' =>  $row[0]->asesor_id,
                'pago_banco' =>  $row[0]->pago_banco,
                'usuario_asesor' => $row[0]->usuario_asesor,
                'capital' => $row->sum('capital'),
                'interes' => $row->sum('interes'),
                'redondeo' => $row->sum('redondeo'),
                'moras' => $row->sum('moras'),
                'notificaciones' => $row->sum('notificaciones'),
                'dscto_mora' => $row->sum('dscto_mora'),
                'dscto_notificaciones' => $row->sum('dscto_notificaciones'),
                'dscto_interes' => $row->sum('dscto_interes'),
                'comision_desembolso' => $row->sum('comision_desembolso'),
                'comision_riesgo' => $row->sum('comision_riesgo'),
                'comision_domicilio' => $row->sum('comision_domicilio'),
                'total_pago' => $total_pago,
                'comentario' => $row->max('comentario'),
                'documento' => $documento,
                'ruta' =>   $ruta,
                'es_desembolso' => $row[0]->comision_desembolso > 0 ? 1 : 0,
                'boleta' => $row[0]->boleta
            ];
        });

        $lista_cobranzas = [];

        $filtro_caja_check = filter_var($request->input('filtro_caja_check'), FILTER_VALIDATE_BOOLEAN);

        if ($filtro_caja_check) {
            $agencia_filtro = $request->input('agencia_filtro');
            $usuarios_cajas = json_decode($request->input('usuarios_cajas'));
            $lista = $lista
                ->where('agencia_caja', $agencia_filtro)
                ->groupBy('agencia_caja');
        } else {
            $lista = $lista->groupBy('agencia_caja');
        }

        foreach ($lista as  $agencia_caja =>  $value) {

            if ($agencia_caja != 0 && $agencia_caja != null) {

                // Construir conexión dinámica
                $conexion_caja = 'master_' . $agencia_caja;

                // Obtener los caja_id únicos
                $caja_ids = $value->pluck('caja_id')->unique();

                // Obtener los datos de caja_registros
                $query = Caja::on($conexion_caja)
                    ->from('caja_registros as caj_reg')
                    ->select('usu.usuario', 'caj_reg.dni', 'caj_reg.id')
                    ->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                    ->whereIn('caj_reg.id', $caja_ids);
                // Indexar por id para acceso rápido


                // Aplicar filtro de usuario si `$filtro_usuario` es verdadero
                if ($filtro_caja_check && !empty($usuarios_cajas)) {
                    $query->whereIn('caj_reg.dni', $usuarios_cajas);
                }

                // Ejecutar la consulta e indexar por ID
                $cajas = $query->get()->keyBy('id');

                // Obtener datos de agencia (una sola consulta por agencia)
                $datos_agencia = Agencia::find($agencia_caja);

                // Asignar datos a los registros
                foreach ($value as $item) {
                    $datos_caja = $cajas[$item->caja_id] ?? null;

                    if ($datos_caja) {
                        // Convertir a array para poder fusionarlo
                        $datos_iniciales = (array) $item;

                        $datos_iniciales['usuario_caja'] = $datos_caja->usuario ?? null;
                        $datos_iniciales['usuario_agencia'] = $datos_agencia->nombre ?? null;
                        $datos_iniciales['dni_caja'] = $datos_caja->dni ?? null;

                        $lista_cobranzas[] = $datos_iniciales;
                    }
                }
            }
        }

        $lista_cobranzas = collect($lista_cobranzas)->sortByDesc('fecha_pago')->values();

        // $total_capital = $lista_cobranzas->sum('capital');
        // $total_interes = $lista_cobranzas->sum('interes');
        // $total_redondeo = $lista_cobranzas->sum('redondeo');
        // $total_mora = $lista_cobranzas->sum('moras');
        // $total_notificaciones = $lista_cobranzas->sum('notificaciones');
        // $total_comision_desembolso = $lista_cobranzas->sum('comision_desembolso');
        // $total_comision_riesgo = $lista_cobranzas->sum('comision_riesgo');
        // $total_comision_domicilio = $lista_cobranzas->sum('comision_domicilio');
        // $total_dscto_mora = $lista_cobranzas->sum('dscto_mora') * -1;
        // $total_dscto_notificaciones = $lista_cobranzas->sum('dscto_notificaciones') * -1;
        // $total_dscto_interes = $lista_cobranzas->sum('dscto_interes') * -1;
        // $total_pago = $lista_cobranzas->sum('total_pago');

        // $totales = (object)[
        //     'total_capital' => $total_capital,
        //     'total_interes' => $total_interes,
        //     'total_redondeo' => $total_redondeo,
        //     'total_mora' => $total_mora,
        //     'total_notificaciones' => $total_notificaciones,
        //     'total_comision_desembolso' => $total_comision_desembolso,
        //     'total_comision_riesgo' => $total_comision_riesgo,
        //     'total_comision_domicilio' => $total_comision_domicilio,
        //     'total_dscto_mora' => $total_dscto_mora,
        //     'total_dscto_notificaciones' => $total_dscto_notificaciones,
        //     'total_dscto_interes' => $total_dscto_interes,
        //     'total_pago' => $total_pago
        // ];


        // dd($lista_cobranzas);

        return response()->json([
            'lista_cobranzas' => $lista_cobranzas,
            'agencia_seleccionada' =>  $agencia_id
        ]);
    }

    public function buscar_por_lugar_cobranza($request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $tipo = $request->tipo;

        $fecha_agencia = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $por_asesor = $request->por_asesor;
        $por_cobrador = $request->por_cobrador;


        $condition = [['cre_reg.estado_id', $estado_id], ['cre_apr.pago_oficina', 0]];

        if ($por_asesor == 'true') {
            $asesor_id = $request->asesor_id;
            $condition[] = ['cre_reg.asesor_id', $asesor_id];
        }

        if ($por_cobrador == 'true') {
            $cobrador_id = $request->cobrador_id;
            $condition[] = ['cre_reg.cobrador_id', $cobrador_id];
        }

        $rango = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select('cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->where($condition)->get();

        if ($tipo == 'pago_hoy') {
            $condition_1 =  [
                ['cre_cuo.fecha_vencimiento', $fecha_agencia],
                ['cre_cuo.estado', 'P']
            ];
            $condition_2 = [
                ['cre_reg.dias_atraso', '>', 0]
            ];
        } elseif ($tipo == 'pago_todos') {
            $condition_1 =  [
                ['cre_cuo.estado', 'P']
            ];
            $condition_2 = [];
        } elseif ($tipo == 'pago_no_hoy') {
            $condition_1 =  [
                ['cre_cuo.fecha_vencimiento', '<>', $fecha_agencia],
                ['cre_cuo.estado', 'P']
            ];
            $condition_2 = [];
        }

        $creditos_vigentes = Cuota::on($conexion)->from('credito_cuotas as cre_cuo')
            ->select(
                'cre_reg.id',
                'cre_reg.dias_atraso',
                'cre_reg.cliente_id',
                'cre_reg.asesor_id',
                'cre_reg.cobrador_id',
                'cre_reg.saldo_total',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.notificaciones_total',
                'cre_reg.notificaciones_pagado',
                'cre_reg.fecha_ultimo_pago',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.direccion',
                'cli_reg.referencia_direccion',

                'dep.departamento',
                'pro.provincia',
                'dis.distrito',

                'cre_apr.monto',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',

                'cre_tip.tipo',

                'cre_cuo.numero_cuota',
                DB::raw("IF(cre_cuo.estado='P',null,cre_cuo.fecha_vencimiento) as fecha_vencimiento"),

                'cre_cuo.cuota',
                'cre_cuo.acumulado',
            )
            ->join('credito_registros as cre_reg', 'cre_cuo.credito_id', 'cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id',  'cre_apr.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id',  'cre_tip.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id',  'dep.id')
            ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id',  'pro.id')
            ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id',  'dis.id')
            ->whereIn('cre_cuo.credito_id', $rango)
            ->where($condition_1)
            ->groupBy('cre_cuo.credito_id');

        $creditos_vencidos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.dias_atraso',
                'cre_reg.cliente_id',
                'cre_reg.asesor_id',
                'cre_reg.cobrador_id',
                'cre_reg.saldo_total',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.notificaciones_total',
                'cre_reg.notificaciones_pagado',
                'cre_reg.fecha_ultimo_pago',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.direccion',
                'cli_reg.referencia_direccion',

                'dep.departamento',
                'pro.provincia',
                'dis.distrito',

                'cre_apr.monto',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',

                'cre_tip.tipo',

                'cre_reg.cuota_actual as numero_cuota',
                DB::raw("(SELECT cre_cuo.fecha_vencimiento from credito_cuotas as cre_cuo
                where cre_cuo.credito_id = cre_reg.id and cre_cuo.numero_cuota = cre_reg.cuota_actual) as fecha_vencimiento"),

                'cre_apr.cuota',
                DB::raw("(SELECT cre_cuo.acumulado from credito_cuotas as cre_cuo
                where cre_cuo.credito_id = cre_reg.id and cre_cuo.numero_cuota = cre_reg.cuota_actual) as acumulado"),

            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id',  'cre_apr.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id',  'cre_tip.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id',  'dep.id')
            ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id',  'pro.id')
            ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id',  'dis.id')
            ->whereIn('cre_reg.id', $rango)
            ->where($condition_2)
            ->groupBy('cre_reg.id');


        if ($tipo == 'pago_hoy') {

            $lista_vigentes = $creditos_vigentes->get();
            $lista_vencidos = $creditos_vencidos->get();

            $vigentes_id = array_column($lista_vigentes->toArray(), 'id');
            $vencidos_id = array_column($lista_vencidos->toArray(), 'id');

            $iguales = array_intersect($vigentes_id, $vencidos_id);

            $restante_vigentes = collect($lista_vigentes)->whereNotIn('id', $iguales);
            $restante_vencidos = collect($lista_vencidos)->whereNotIn('id', $iguales);

            $iguales_vigentes = collect($lista_vigentes)->whereIn('id', $iguales);
            $iguales_vencidos = collect($lista_vencidos)->whereIn('id', $iguales);

            $lista_combinada = [];

            foreach ($iguales_vigentes as $item) {
                foreach ($iguales_vencidos as $item_1) {
                    if ($item->id == $item_1->id) {

                        $cuota_vencida = intVal($item_1->numero_cuota);
                        $cuota_vigente = intVal($item->numero_cuota);

                        if ($cuota_vencida > $cuota_vigente) {
                            $item->numero_cuota = $cuota_vigente . ' - ' . $cuota_vencida;
                        } else {
                            $item->numero_cuota = $cuota_vencida . ' - ' . $cuota_vigente;
                        }

                        $lista_combinada[] = $item;
                    }
                }
            }

            $lista_creditos = array_merge($restante_vigentes->toArray(), $restante_vencidos->toArray(), $lista_combinada);
        } elseif ($tipo == 'pago_todos') {
            $lista_creditos = $creditos_vencidos;
            $lista_creditos = $lista_creditos->orderBy('dias_atraso', 'asc')->get();
        } elseif ($tipo == 'pago_no_hoy') {
            $lista_creditos = $creditos_vigentes;
            $lista_creditos = $lista_creditos->orderBy('dias_atraso', 'asc')->get();
        }
        $lista_creditos = collect($lista_creditos)->sortByDesc('dias_atraso');

        $lista_creditos = $lista_creditos->values();

        $lista_creditos = $lista_creditos->map(function ($row, $index) {

            $cliente = $row['apellido_paterno'] . ' ' .
                $row['apellido_materno'] . ' ' .
                $row['nombres'];

            $ubicacion =  $row['departamento'] . ' - ' .
                $row['provincia'] . ' - ' .
                $row['distrito'];

            $plazo = round($row['plazo'], 0) . ' ' .  (new CreditosController)->periodo_medicion($row['periodo_pago']);

            $asesor = Usuario::find($row['asesor_id']);
            $cobrador = Usuario::find($row['cobrador_id']);

            return [
                'index' => $index,
                'cliente' => $cliente,
                'direccion' => $row['direccion'],
                'referencia_direccion' => $row['referencia_direccion'],
                'dias_atraso' => $row['dias_atraso'] . ' d',
                'capital' => $row['monto'],
                'plazo' => $plazo,
                'tipo' => $row['tipo'],
                'numero_cuota' => $row['numero_cuota'],
                'cuota' => $row['cuota'],
                'ubicacion' => $ubicacion,
                'usuario_asesor' => $asesor->usuario,
                'usuario_cobrador' => $cobrador->usuario,
                // Datos para exportar
                'acumulado' => $row['acumulado'],
                'fecha_ultimo_pago' => $row['fecha_ultimo_pago'],
                'fecha_vencimiento' => $row['fecha_vencimiento'],
                'mora_total' => $row['mora_total'],
                'mora_pagado' => $row['mora_pagado'],
                'notificaciones_total' => $row['notificaciones_total'],
                'notificaciones_pagado' => $row['notificaciones_pagado'],
                'saldo_total' => $row['saldo_total'],
            ];
        });

        return response()->json(['lista_creditos' => $lista_creditos]);
    }

    public function buscar_en_negocio(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $por_asesor = $request->por_asesor;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $condicion_1 = [['numero_recibo', '<>', null]];
        $condicion_2 = [['numero_recibo', '<>', null]];
        $condicion_3 = [['numero_recibo', '<>', null]];
        if ($por_asesor == 'true') {
            $asesor = $request->asesor;
            $condicion_1[] = ['caj_pag_cuo.asesor_id', $asesor];
            $condicion_2[] = ['caj_pag_mor.asesor_id', $asesor];
            $condicion_3[] = ['cre_reg.asesor_id', $asesor];
        }

        $pago_cuotas = PagoCuota::on($conexion)->from('caja_pago_cuotas as caj_pag_cuo')
            ->select(
                'caj_pag_cuo.fecha_pago',

                'cre_reg.cliente_id',

                'caj_pag_cuo.numero_recibo',
                DB::raw("MAX(caj_pag_cuo.numero_cuota) as numero_cuota"),

                DB::raw("SUM(caj_pag_cuo.monto) as pago_cuotas"),
                DB::raw("0 as pago_moras"),
                DB::raw("0 as pago_notificaciones"),

                'caj_pag_cuo.usuario_cobrador',
                'caj_pag_cuo.asesor_id',

                'caj_pag_cuo.caja_id',
                'caj_pag_cuo.agencia_caja',

                // 'cre_reg.dscto_mora_cancelado as dscto_mora',
                // 'cre_reg.dscto_notificaciones_cancelado as dscto_notificaciones',
                // 'cre_reg.dscto_interes_cancelado as dscto_interes',
                DB::raw("0 as dscto_mora"),
                DB::raw("0 as dscto_notificaciones"),
                DB::raw("0 as dscto_interes"),
            )
            ->join('credito_registros as cre_reg', 'caj_pag_cuo.credito_id', 'cre_reg.id')
            ->where($condicion_1)
            ->whereBetween("fecha_pago", [$fecha_desde, $fecha_hasta])
            ->groupBy('caj_pag_cuo.numero_recibo');

        $pago_moras = PagoMora::on($conexion)->from('caja_pago_moras as caj_pag_mor')
            ->select(

                'caj_pag_mor.fecha_pago',

                'cre_reg.cliente_id',

                'caj_pag_mor.numero_recibo',
                DB::raw("0 as numero_cuota"),

                DB::raw("0 as pago_cuotas"),
                DB::raw("SUM(caj_pag_mor.monto) as pago_moras"),
                DB::raw("0 as pago_notificaciones"),

                'caj_pag_mor.usuario_cobrador',
                'caj_pag_mor.asesor_id',

                'caj_pag_mor.caja_id',
                'caj_pag_mor.agencia_caja',

                DB::raw("0 as dscto_mora"),
                DB::raw("0 as dscto_notificaciones"),
                DB::raw("0 as dscto_interes"),
            )
            ->join('credito_registros as cre_reg', 'caj_pag_mor.credito_id', 'cre_reg.id')
            ->where($condicion_2)
            ->whereBetween("fecha_pago", [$fecha_desde, $fecha_hasta])
            ->groupBy('caj_pag_mor.numero_recibo');

        $pago_notificaciones = PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
            ->select(

                'caj_pag_not.fecha_pago',

                'cre_reg.cliente_id',

                'caj_pag_not.numero_recibo',
                DB::raw("0 as numero_cuota"),

                DB::raw("0 as pago_cuotas"),
                DB::raw("0 as pago_moras"),
                DB::raw("SUM(caj_pag_not.monto) as pago_notificaciones"),

                'caj_pag_not.usuario_cobrador',
                'cre_reg.asesor_id',

                'caj_pag_not.caja_id',
                'caj_pag_not.agencia_caja',

                DB::raw("0 as dscto_mora"),
                DB::raw("0 as dscto_notificaciones"),
                DB::raw("0 as dscto_interes"),
            )
            ->join('credito_notificaciones as cre_not', 'caj_pag_not.notificacion_id', 'cre_not.id')
            ->join('credito_registros as cre_reg', 'cre_not.credito_id', 'cre_reg.id')
            ->where($condicion_3)
            ->whereBetween("fecha_pago", [$fecha_desde, $fecha_hasta])
            ->groupBy('caj_pag_not.numero_recibo');

        $lista = [];

        if ($pago_cuotas->get()->count() > 0) {
            $lista = $pago_cuotas;
        }

        if ($pago_moras->get()->count() > 0) {
            $lista =  $lista->union($pago_moras);
        }

        if ($pago_notificaciones->get()->count() > 0) {
            $lista = $lista->union($pago_notificaciones);
        }

        $totales = (object)[
            'total_pago' => 0,
            'total_cuotas' => 0,
            'total_moras' => 0,
            'total_notificaciones' => 0,
            'total_registros' => 0
        ];

        if ($lista == []) {
            $lista_cobranzas = [];
        } else {

            $lista = $lista->orderBy('fecha_pago', 'asc')->get();

            $lista_cobranzas = $lista->groupBy('numero_recibo')->map(function ($row) {

                return [
                    'fecha_pago' => $row[0]->fecha_pago,
                    'cliente_id' => $row[0]->cliente_id,
                    'numero_cuota' => $row->max('numero_cuota'),
                    'numero_recibo' => $row[0]->numero_recibo,
                    'total_pago' => $row->sum('pago_cuotas') + $row->sum('pago_moras') + $row->sum('pago_notificaciones') -
                        $row->sum('dscto_mora') - $row->sum('dscto_notificaciones') - $row->sum('dscto_interes'),
                    'pago_cuotas' => $row->sum('pago_cuotas'),
                    'pago_moras' => $row->sum('pago_moras'),
                    'pago_notificaciones' => $row->sum('pago_notificaciones'),
                    'usuario_cobrador' => $row[0]->usuario_cobrador,
                    'asesor_id' => $row[0]->asesor_id,
                    'agencia_caja' => $row[0]->agencia_caja,
                    'caja_id' => $row[0]->caja_id,
                    'dscto_mora' => $row->sum('dscto_mora'),
                    'dscto_notificaciones' => $row->sum('dscto_notificaciones'),
                    'dscto_interes' => $row->sum('dscto_interes'),
                ];
            });



            // Para que los indices empiezen en 0
            $lista_cobranzas = $lista_cobranzas->values();

            $lista_cobranzas = $lista_cobranzas->map(function ($row, $index) use ($conexion) {
                $row['index'] = $index;

                $conexion_caja = 'master_' .  $row['agencia_caja'];
                $caja_registro = Caja::on($conexion_caja)->select('dni')->where('id', $row['caja_id'])->get()->last();
                $datos_caja = Usuario::select('usuario')->where('dni', $caja_registro->dni)->get()->last();
                $row['usuario_caja'] = $datos_caja->usuario;

                $datos_cliente = Cliente::on($conexion)->select(
                    DB::raw("CONCAT(apellido_paterno,' ',apellido_materno,' ',nombres) as cliente")
                )->where('id', $row['cliente_id'])->get()->last();
                $row['cliente'] = $datos_cliente->cliente;

                if ($row['asesor_id'] != null) {
                    $datos_asesor =  Usuario::select('usuario')->where('dni', $row['asesor_id'])->get()->last();
                    $row['usuario_asesor'] =  $datos_asesor->usuario;
                } else {
                    $row['usuario_asesor'] = null;
                }

                if ($row['usuario_cobrador'] != null) {
                    $datos_cobrador =  Usuario::select('usuario')->where('dni', $row['usuario_cobrador'])->get()->last();
                    $row['usuario_cobrador'] = $datos_cobrador->usuario;
                } else {
                    $row['usuario_cobrador'] = null;
                }

                return $row;
            });

            $totales->total_cuotas = $lista_cobranzas->sum('pago_cuotas');
            $totales->total_moras = $lista_cobranzas->sum('pago_moras');
            $totales->total_notificaciones = $lista_cobranzas->sum('pago_notificaciones');
            $totales->total_dscto_moras = $lista_cobranzas->sum('dscto_mora');
            $totales->total_dscto_notificaciones = $lista_cobranzas->sum('dscto_notificaciones');
            $totales->total_dscto_interes = $lista_cobranzas->sum('dscto_interes');
            $totales->total_pago = $totales->total_cuotas +
                $totales->total_moras +
                $totales->total_notificaciones -
                $totales->total_dscto_moras -
                $totales->total_dscto_notificaciones -
                $totales->total_dscto_interes;
        }

        return ['lista_cobranzas' => $lista_cobranzas, 'totales' => $totales];
    }



    public function exportar(Request $request)
    {
        $modo = $request->modo;

        if ($modo == 'por_auxiliar_operacion') {
            return  $this->exportar_por_operacion($request);
        } elseif ($modo == 'por_auxiliar_dia') {
            return  $this->exportar_por_dia($request);
        } else if ($modo == 'por_auxiliar_resumen') {
            return  $this->exportar_por_resumen($request);
        } elseif ($modo == 'por_lugar_cobranza') {
            return  $this->exportar_por_lugar_cobranza($request);
        } elseif ($modo == 'por_negocio') {
            return  $this->exportar_por_negocio($request);
        }
    }

    public function exportar_por_negocio(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $lista_cobranzas = json_decode($request->lista_cobranzas);
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $data = [];
        $orden = 0;
        foreach ($lista_cobranzas as $item) {
            $orden += 1;
            $object = (object)[
                'orden' => $orden,
                'fecha_registro' => $item->fecha_pago,
                'cliente' =>  $item->cliente,
                'asesor' => $item->usuario_asesor,
                'numero_recibo' => $item->numero_recibo,
                'numero_cuota' => $item->numero_cuota,
                'total_pago' => $item->total_pago,
                'en_cuotas' => $item->pago_cuotas,
                'en_moras' => $item->pago_moras,
                'en_notificaciones' => $item->pago_notificaciones,
                'dscto_mora' => $item->dscto_mora,
                'dscto_notificaciones' => $item->dscto_notificaciones,
                'dscto_interes' => $item->dscto_interes,
                'cobrador' => $item->usuario_cobrador,
                'caja' => $item->usuario_caja,

            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $inputFileName = './report_templates/creditos/reportes/rptCobranzaNegocio.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 15) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda = $sheet->getStyle($columna_1 . 5)->exportArray();
            $lista_formatos_celdas[] = $formato_celda;

            $celda++;
        }

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('O2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-----------------------------
        $indice = 5;



        foreach ($data as  $item) {
            $columna_2 = 1;
            foreach ($item as $key => $valor) {

                if ($key == 'fecha_registro') {
                    $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($valor)));
                } else {
                    $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                }
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);

                $columna_2 += 1;
            }

            $indice += 1;
        }

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCobranzaNegocio', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function exportar_por_operacion($request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $cobranzas = json_decode($request->lista_cobranzas);
        $tipo = $request->tipo;

        $agencia_sesion = session('id_agencia');

        foreach ($cobranzas as $item) {

            $object = (object)[
                'expediente' => $item->numero_expediente . '-' . $item->numero_credito,
                'numero_cuota' => $item->numero_cuota,
                'cliente' =>  $item->cliente,
                'asesor' =>  $item->usuario_asesor,
                'fecha_pago' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha_pago)),
                'capital' => $item->capital,
                'interes' => $item->interes,
                'redondeo' => $item->redondeo,
                'moras' => $item->moras,
                'notificaciones' => $item->notificaciones,
                'comision_desembolso' => $item->comision_desembolso,
                'comision_riesgo' => $item->comision_riesgo,
                'comision_domicilio' => $item->comision_domicilio,
                'dscto_mora' => $item->dscto_mora,
                'dscto_notificaciones' => $item->dscto_notificaciones,
                'dscto_interes' => $item->dscto_interes,
                'total' => $item->total_pago,
                'comentario' => $item->comentario,
                'caja' => $item->usuario_caja,
                'agencia' => $item->usuario_agencia,
                'boleta' => $agencia_sesion == 5 ? ($item->boleta ? 'SI' : 'NO') : NULL
            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptCobranzasOperacion');
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptCobranzasAuxiliar.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('V2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando valores

        $indice = 5;
        $controller = new CreditosController();

        foreach ($data as $item) {
            $columna = 1;
            foreach ($item as $valor) {
                $columnaLetra = $controller->num2char($columna);
                $cell = $columnaLetra . $indice;

                $sheet->setCellValue($cell, $valor);

                $columna++;
            }
            $indice++;
        }

        // Exportar para descarga-------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCobranzasOperacion', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');

        if ($tipo == 'XLSX') {

            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

            return ['path_xlsx' => $path_xlsx];
        } else if ($tipo == 'PDF') {

            $xlsxFile = public_path('temp_files/' . $nombre_archivo . '.xlsx');
            $pdfFile = public_path('temp_files/' . $nombre_archivo . '.pdf');

            // Use OpenOffice to convert XLSX to PDF
            $command = env('LIBREOFFICE') . " --headless --convert-to pdf $xlsxFile --outdir " . dirname($pdfFile);
            shell_exec($command);

            $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

            return ['path_pdf' => $path_pdf];
        }
    }

    public function exportar_por_dia($request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $cobranzas = json_decode($request->lista_cobranzas);
        $totales = json_decode($request->totales);
        $tipo = $request->tipo;

        $data = [];
        $cobranzas = collect($cobranzas)->sortBy('fecha_pago_corta');
        $totales = collect($totales);
        $totales['usuario_caja'] = null;
        $totales['total_operaciones'] = count($cobranzas);

        $data = $cobranzas->groupBy("fecha_pago_corta")->map(function ($row) {
            return $row->groupBy("usuario_caja")->map(function ($row_1) {
                return (object)[
                    'capital' => $row_1->sum('capital'),
                    'interes' => $row_1->sum('interes'),
                    'redondeo' => $row_1->sum('redondeo'),
                    'moras' => $row_1->sum('moras'),
                    'notificaciones' => $row_1->sum('notificaciones'),
                    'comision_desembolso' => $row_1->sum('comision_desembolso'),
                    'comision_riesgo' => $row_1->sum('comision_riesgo'),
                    'comision_domicilio' => $row_1->sum('comision_domicilio'),
                    'dscto_mora' => $row_1->sum('dscto_mora'),
                    'dscto_notificaciones' => $row_1->sum('dscto_notificaciones'),
                    'dscto_interes' => $row_1->sum('dscto_interes'),
                    'total' => $row_1->sum('total_pago'),
                    'usuario_caja' => $row_1[0]->usuario_caja,
                    'operaciones' => $row_1->count('operaciones')
                ];
            });
        });

        $subtotales = $cobranzas->groupBy("fecha_pago_corta")->map(function ($row) {

            return (object)[
                'total_capital' => $row->sum('capital'),
                'total_interes' => $row->sum('interes'),
                'total_redondeo' => $row->sum('redondeo'),
                'total_moras' => $row->sum('moras'),
                'total_notificaciones' => $row->sum('notificaciones'),
                'total_comision_desembolso' => $row->sum('comision_desembolso'),
                'total_comision_riesgo' => $row->sum('comision_riesgo'),
                'total_comision_domicilio' => $row->sum('comision_domicilio'),
                'total_dscto_mora' => $row->sum('dscto_mora'),
                'total_dscto_notificaciones' => $row->sum('dscto_notificaciones'),
                'total_dscto_interes' => $row->sum('dscto_interes'),
                'total_pago' => $row->sum('total_pago'),
                'usuario_caja' => null,
                'total_operaciones' => $row->count('operaciones')
            ];
        });

        $subtotales = $subtotales->values();

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptCobranzasDia');
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptCobranzasAuxiliar.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_subtotales = [];
        $lista_formatos_totales = [];

        while ($celda <= 16) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_subtotales = $sheet->getStyle($columna_1 . 6)->exportArray();
            $formato_totales = $sheet->getStyle($columna_1 . 8)->exportArray();

            $lista_formatos_subtotales[] = $formato_subtotales;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('P2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-----------------------------
        $indice_1 = 5;

        // $data = $data->toArray();

        $indice_2 = 0;
        foreach ($data as $key => $item) {
            $columna_2 = 1;
            foreach ($item as $subitem) {
                $columna_2 = 1;
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice_1, $key);
                $columna_2 += 1;
                $dia_semana = (new CreditosController)->nombre_dia(strtotime($key));
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice_1, ($dia_semana));
                $columna_2 += 1;
                foreach ($subitem as $valor) {
                    $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice_1, $valor);
                    $columna_2 += 1;
                }

                $indice_1 += 1;
            }

            $columna = 2;
            $sheet->setCellValue((new CreditosController)->num2char($columna) . $indice_1, 'SUBTOTAL');
            $sheet->getStyle((new CreditosController)->num2char($columna) . $indice_1)->applyFromArray($lista_formatos_subtotales[$columna - 1]);

            $columna += 1;
            foreach ($subtotales[$indice_2] as  $item_1) {
                $sheet->setCellValue((new CreditosController)->num2char($columna) . $indice_1, $item_1);
                $sheet->getStyle((new CreditosController)->num2char($columna) . $indice_1)->applyFromArray($lista_formatos_subtotales[$columna - 1]);
                $columna += 1;
            }

            $rango =  'B' . $indice_1 - count($item) . ':' . 'B' . $indice_1;
            $sheet->mergeCells($rango);

            $sheet->getStyle('B' . $indice_1)->applyFromArray($lista_formatos_subtotales[0]);

            $indice_2 += 1;
            $indice_1 += 1;
        }

        $spreadsheet->getActiveSheet()->getRowDimension($indice_1)->setRowHeight(9);

        $indice_1 += 1;

        $columna_total = 2;
        $sheet->setCellValue((new CreditosController)->num2char($columna_total) . $indice_1, 'TOTAL');
        $columna_total += 1;

        foreach ($totales as $valor) {
            $sheet->setCellValue((new CreditosController)->num2char($columna_total) . $indice_1, $valor);
            $columna_total += 1;
        }

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice_1)->applyFromArray($value);
        }

        // Exportar para descarga-------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCobranzaPorDia', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');

        if ($tipo == 'XLSX') {

            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

            return ['path_xlsx' => $path_xlsx];
        } else if ($tipo == 'PDF') {

            $xlsxFile = public_path('temp_files/' . $nombre_archivo . '.xlsx');
            $pdfFile = public_path('temp_files/' . $nombre_archivo . '.pdf');

            // Use OpenOffice to convert XLSX to PDF
            $command = env('LIBREOFFICE') . " --headless --convert-to pdf $xlsxFile --outdir " . dirname($pdfFile);
            shell_exec($command);

            $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

            return ['path_pdf' => $path_pdf];
        }
    }
    public function exportar_por_resumen($request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $cobranzas = json_decode($request->lista_cobranzas);
        $totales = json_decode($request->totales);
        $tipo = $request->tipo;

        $data = [];
        $cobranzas = collect($cobranzas)->sortBy('fecha_pago_corta');
        $totales = collect($totales);
        unset($totales['usuario_caja']);
        $totales['total_operaciones'] = count($cobranzas);
        $totales['total_desembolsos'] = $cobranzas->sum('es_desembolso');


        $data = $cobranzas->groupBy("usuario_caja")->map(function ($row) {
            return (object)[
                'caja' => $row[0]->usuario_caja,
                'capital' => $row->sum('capital'),
                'interes' => $row->sum('interes'),
                'redondeo' => $row->sum('redondeo'),
                'moras' => $row->sum('moras'),
                'notificaciones' => $row->sum('notificaciones'),
                'comision_desembolso' => $row->sum('comision_desembolso'),
                'comision_riesgo' => $row->sum('comision_riesgo'),
                'comision_domicilio' => $row->sum('comision_domicilio'),
                'dscto_mora' => $row->sum('dscto_mora'),
                'dscto_notificaciones' => $row->sum('dscto_notificaciones'),
                'dscto_interes' => $row->sum('dscto_interes'),
                'total' => $row->sum('total_pago'),
                'operaciones' => $row->count('operaciones'),
                'desembolsos' => $row->sum('es_desembolso')
            ];
        });

        $data = $data->values();
        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptCobranzasResumen');
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptCobranzasAuxiliar.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];
        $lista_formatos_totales = [];

        while ($celda <= 15) {
            $columna = (new CreditosController)->num2char($celda);
            $formato_celdas = $sheet->getStyle($columna . 5)->exportArray();
            $formato_totales = $sheet->getStyle($columna . 7)->exportArray();

            $lista_formatos_celdas[] = $formato_celdas;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        $sheet->removeRow(7);
        $sheet->removeRow(6);

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('P2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-----------------------------
        $indice = 5;
        foreach ($data as $item) {
            $columna = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna) . $indice)
                    ->applyFromArray($lista_formatos_celdas[$columna - 1]);
                $columna += 1;
            }

            $indice += 1;
        }

        $spreadsheet->getActiveSheet()->getRowDimension($indice)->setRowHeight(9);

        $rango = "B$indice:P$indice";
        $sheet->getStyle($rango)
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THICK)
            ->setColor(new Color('#BCBCBC'));

        $indice += 1;

        // Insertando totales-----------------------------
        $columna = 2;

        foreach ($totales as $item) {
            $sheet->setCellValue((new CreditosController)->num2char($columna) . $indice, $item);
            $sheet->getStyle((new CreditosController)->num2char($columna) . $indice)->applyFromArray($lista_formatos_totales[$columna - 1]);
            $columna += 1;
        }

        // Exportar para descarga-------------------------
        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptCobranzaPorAuxiliar', 5);
        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');

        if ($tipo == 'XLSX') {

            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

            return ['path_xlsx' => $path_xlsx];
        } else if ($tipo == 'PDF') {

            $xlsxFile = public_path('temp_files/' . $nombre_archivo . '.xlsx');
            $pdfFile = public_path('temp_files/' . $nombre_archivo . '.pdf');

            // Use OpenOffice to convert XLSX to PDF
            $command = env('LIBREOFFICE') . " --headless --convert-to pdf $xlsxFile --outdir " . dirname($pdfFile);
            shell_exec($command);

            $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

            return ['path_pdf' => $path_pdf];
        }
    }

    public function exportar_por_lugar_cobranza($request)
    {
        // Ordenando array de datos-------------------------------
        $creditos = json_decode($request->lista_creditos);
        $tipo = $request->tipo;
        $data = [];

        $orden = 1;
        foreach ($creditos as $item) {
            $object = (object)[
                'numero' => $orden,
                'cliente' =>  $item->cliente,
                'capital' => $item->capital,
                'plazo' => $item->plazo,
                'cuota' => $item->cuota,
                'acumulado' => $item->acumulado,
                'numero_cuota' => $item->numero_cuota,
                'fecha_ultimo_pago' => $item->fecha_ultimo_pago,
                'dias_atraso' => $item->dias_atraso,
                'fecha_vencimiento' => $item->fecha_vencimiento,
                'monto_pendiente' => $item->cuota - $item->acumulado,
                'mora' => $item->mora_total - $item->mora_pagado,
                'notificaciones' => $item->notificaciones_total - $item->notificaciones_pagado,
                'total' => $item->cuota - $item->acumulado +
                    ($item->mora_total - $item->mora_pagado) +
                    ($item->notificaciones_total - $item->notificaciones_pagado),
                'saldo_total' => $item->saldo_total,
                'asesor' => $item->usuario_asesor,
            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("./report_templates/creditos/reportes/rptCreditosLugarCobranza.xlsx");
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptCreditosLugarCobranza.xlsx");

        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];
        $lista_formatos_celdas_2 = [];
        while ($celda <= 16) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda_1 = $sheet->getStyle($columna_1 . 4)->exportArray();
            $formato_celda_2 = $sheet->getStyle($columna_1 . 5)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;
            $lista_formatos_celdas_2[] = $formato_celda_2;

            $celda++;
        }

        $sheet->removeRow(5);

        // Insertando datos-----------------------------
        $indice = 4;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                if ($item->numero % 2 != 0) {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_2 - 1]);
                } else if ($item->numero % 2 == 0) {
                    $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_2[$columna_2 - 1]);
                }
                $columna_2 += 1;
            }

            $indice += 1;
        }

        // Exportar para descarga-------------------------

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptLugarCobranza', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');

        if ($tipo == 'XLSX') {

            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

            return ['path_xlsx' => $path_xlsx];
        } else if ($tipo == 'PDF') {

            $xlsxFile = public_path('temp_files/' . $nombre_archivo . '.xlsx');
            $pdfFile = public_path('temp_files/' . $nombre_archivo . '.pdf');

            // Use OpenOffice to convert XLSX to PDF
            $command = env('LIBREOFFICE') . " --headless --convert-to pdf $xlsxFile --outdir " . dirname($pdfFile);
            shell_exec($command);

            $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

            return ['path_pdf' => $path_pdf];
        }
    }


    public function cobranzas_negocio()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_COBRANZAS_NEGOCIO', 'CREDITOS_REPORTES');

            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS',
                    'RECUPERADOR DE CRÉDITOS'
                ])->get();


                // $usuarios_cuentas = (array)[];

                // foreach ($agencias as $item) {
                //     $conexion = 'master_' .  $item->id_agencia;
                //     $cuentas = CuentaUsuario::on($conexion)
                //         ->select('dni')
                //         ->where('con_cuenta', 1)
                //         ->get();
                //     foreach ($cuentas as $item_1) {
                //         $usuarios_cuentas[] = $item_1->dni;
                //     }
                // }

                $usuarios = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )->whereIn('cargo_id', $cargos)
                    ->orderBy('usuario', 'asc')
                    ->get();


                return Inertia::render('Creditos/Reportes/Caja/cobranzas_negocio', [
                    'usuarios' => $usuarios
                ]);
            } else {
                return redirect('/');
            }
        }
    }

    public function descuentos_creditos()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_DESCUENTOS_CREDITOS', 'CREDITOS_REPORTES');


            if ($band == 1) {
                // en caja es todo los usuarios con cuenta
                $agencias = Agencia::all();

                $usuarios_cuentas = (array)[];

                foreach ($agencias as $item) {
                    $conexion = 'master_' .  $item->id_agencia;
                    $cuentas = CuentaUsuario::on($conexion)
                        ->select('dni')
                        ->where('con_cuenta', 1)
                        ->get();
                    foreach ($cuentas as $item_1) {
                        $usuarios_cuentas[] = $item_1->dni;
                    }
                }

                $usuarios_cuenta = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )->whereIn('dni', $usuarios_cuentas)
                    ->orderBy('usuario', 'asc')
                    ->get();

                $lista_cargos = ['ASESOR DE NEGOCIOS'];

                $cargos = Cargo::whereIn('cargo', $lista_cargos)->get();

                $cargos_id = [];

                foreach ($cargos as $item) {
                    $cargos_id[] = $item->id;
                };

                $asesores = Usuario::select()->where([
                    ['habilitado', 1]
                ])->whereIn('cargo_id', $cargos_id)->get();


                return Inertia::render('Creditos/Reportes/Caja/descuento_creditos', [
                    'usuarios_cuenta' => $usuarios_cuenta,
                    'asesores' => $asesores,
                ]);
            } else {
                return redirect('/');
            }
        }
    }
    public function descuentos_creditos_buscar(Request $request)
    {
        // return $request;
        $agencia_seleccionada = $request->agencia_seleccionada;
        $conexion = 'master_' .  $agencia_seleccionada;

        $caja_seleccionada = $request->caja_seleccionada;
        $asesor_seleccionado = $request->asesor_seleccionado;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $tipo_descuento_seleccionado = $request->tipo_descuento_seleccionado;
        $condiciones = [];
        $rango = [];
        $rango_caja = [];

        $estado = Estado::on($conexion)->select('id')->where('estado', 'CANCELADO TOTAL')->get()->last();
        $estado_id = $estado->id;

        if ($asesor_seleccionado != 0) {
            $condiciones[] = ['c_rg.asesor_id', $asesor_seleccionado];
        }
        if ($caja_seleccionada != 0) {
            $rango_caja = Caja::on($conexion)->from('caja_registros as caja')
                ->select('caja.id as caja_cancelado_id')
                ->where('caja.dni', $caja_seleccionada)
                ->get();
            $condiciones[] = ['c_rg.agencia_caja_cancelado', $agencia_seleccionada];
        }
        if ($caja_seleccionada == 0) {
            $rango_caja = Credito::on($conexion)->from('credito_registros as cr')
                ->select('cr.caja_cancelado_id as caja_cancelado_id')
                ->whereBetween('cr.fecha_hora_cancelado', [$fecha_desde, $fecha_hasta])
                ->get();
        }


        if ($tipo_descuento_seleccionado != 0) {
            if ($tipo_descuento_seleccionado == 'mora') {
                $condiciones[] = ['c_rg.dscto_mora_cancelado', '>', 0];
            }
            if ($tipo_descuento_seleccionado == 'notificacion') {
                $condiciones[] = ['c_rg.dscto_notificaciones_cancelado', '>', 0];
            }
            if ($tipo_descuento_seleccionado == 'interes') {
                $condiciones[] = ['c_rg.dscto_interes_cancelado', '>', 0];
            }
        }
        // return $rango_caja;
        // return $condiciones;
        if ($tipo_descuento_seleccionado != 0) {
            $rango = Credito::on($conexion)->from('credito_registros as cr')
                ->select('cr.id')
                ->where('cr.estado_id', $estado_id)
                ->whereIn('cr.caja_cancelado_id', $rango_caja)
                ->get();
        }
        if ($tipo_descuento_seleccionado == 0) {
            $rango = Credito::on($conexion)->from('credito_registros as cr')
                ->select('cr.id')
                ->where('cr.estado_id', $estado_id)
                ->whereIn('cr.caja_cancelado_id', $rango_caja)
                ->Where('cr.dscto_mora_cancelado', '>', 0)
                ->orWhere('cr.dscto_notificaciones_cancelado', '>', 0)
                ->orWhere('cr.dscto_interes_cancelado', '>', 0)
                ->get();
        }



        // return $estado_id;
        // return $rango;
        // return $condiciones;
        $datoss =  Credito::on($conexion)->from('credito_registros as c_rg')
            ->select(
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.codigo_expediente',
                'c_rg.fecha_hora_cancelado',
                'cre_apr.tasa_interes',
                'cre_apr.plazo',
                'cre_apr.monto',
                'cre_apr.periodo_pago',
                'c_rg.dscto_mora_cancelado',
                'c_rg.dscto_notificaciones_cancelado',
                'c_rg.dscto_interes_cancelado',
                'c_rg.datos_creacion',
                'usu_1.usuario as caja',
                'usu_2.usuario as asesor',
                'c_rg.asesor_id',
                'c_rg.estado_id',
            )
            ->join('cliente_registros as cli_reg', 'cli_reg.id', '=', 'c_rg.cliente_id')
            ->join('credito_aprobaciones as cre_apr', 'cre_apr.id', '=', 'c_rg.aprobacion_id')
            ->join('caja_registros as caja', 'caja.id', '=', 'c_rg.caja_cancelado_id')
            ->join('solucion_master.usuarios as usu_1', 'caja.dni', 'usu_1.dni')
            ->join('solucion_master.usuarios as usu_2', 'c_rg.asesor_id', 'usu_2.dni')
            ->whereIn('c_rg.id', $rango)
            ->whereBetween('c_rg.fecha_hora_cancelado', [$fecha_desde, $fecha_hasta])
            ->where($condiciones)
            ->get();

        return $datoss;
    }

    public function exportar_descuento(Request $request)
    {
        // return $request;
        // Ordenando array de datos-------------------------------

        $datos_tabla_descuentos = json_decode($request->datos_tabla_descuentos);
        $totales = json_decode($request->totales);

        $data = [];

        foreach ($datos_tabla_descuentos as $item) {
            $object = (object)[
                'expediente' => $item->codigo_expediente,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'asesor' => $item->asesor,
                'capital' => $item->monto,
                'tasa' => $item->tasa_interes,
                'plazo' => $item->plazo,
                'caja' => $item->caja,
                'dscto_mora_cancelado' => $item->dscto_mora_cancelado,
                'dscto_interes_cancelado' => $item->dscto_interes_cancelado,
                'dscto_notificaciones_cancelado' => $item->dscto_notificaciones_cancelado,
                'fecha_cancelado' => $item->fecha_hora_cancelado,

            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptDescuentos.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];
        $lista_formatos_totales = [];

        while ($celda <= 11) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda = $sheet->getStyle($columna_1 . 4)->exportArray();

            $formato_totales = $sheet->getStyle($columna_1 . 6)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        $sheet->removeRow(6);

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

        $sheet->insertNewRowBefore($indice + 1);

        $indice += 1;


        $columna_total = 8;

        foreach ($totales as $valor) {
            $sheet->setCellValue((new CreditosController)->num2char($columna_total) . $indice, $valor);
            $columna_total += 1;
        }

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        // Exportar para descarga-------------------------
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $ret['data'] = base64_encode(ob_get_contents());
        ob_end_clean();

        return $ret['data'];
    }

    public function buscar_por_recibo(Request $request)
    {

        $lista = [];
        $agencia_id = $request->agencia_id;
        $numero_recibo = $request->numero_recibo;

        $conexion = 'master_' .  $agencia_id;

        $pago_cuotas = PagoCuota::on($conexion)->from('caja_pago_cuotas as caj_pag_cuo')
            ->select(

                'caj_pag_cuo.numero_recibo',
                DB::raw('MAX(caj_pag_cuo.numero_cuota) as max_cuota'),

                DB::raw('SUM(caj_pag_cuo.monto) as monto_total'),
                'caj_pag_cuo.credito_id',
                'caj_pag_cuo.agencia_caja',
                'caj_pag_cuo.caja_id',
                'caj_pag_cuo.usuario_cobrador',
                DB::raw('MIN(caj_pag_cuo.fecha_pago) as fecha_min'),

                DB::raw("'pago_cuotas' as nombre_tabla"),

            )->where('caj_pag_cuo.numero_recibo', $numero_recibo)
            ->groupBy('caj_pag_cuo.numero_recibo')
            ->get()->last();

        $pago_moras = PagoMora::on($conexion)->from('caja_pago_moras as caj_pag_mor')
            ->select(

                'caj_pag_mor.numero_recibo',
                DB::raw("0 as max_cuota"),
                DB::raw('SUM(caj_pag_mor.monto) as monto_total'),
                'caj_pag_mor.credito_id',
                'caj_pag_mor.agencia_caja',
                'caj_pag_mor.caja_id',
                'caj_pag_mor.usuario_cobrador',

                DB::raw('MIN(caj_pag_mor.fecha_pago) as fecha_min'),


                DB::raw("'pago_moras' as nombre_tabla"),

            )->where('caj_pag_mor.numero_recibo', $numero_recibo)
            ->groupBy('caj_pag_mor.numero_recibo')
            ->get()->last();

        $pago_notificaciones = PagoNotificacion::on($conexion)->from('caja_pago_notificaciones as caj_pag_not')
            ->select(

                'caj_pag_not.numero_recibo',
                DB::raw('MAX(cre_not.numero_cuota) as max_cuota'),
                DB::raw('SUM(caj_pag_not.monto) as monto_total'),
                'cre_not.credito_id',
                'caj_pag_not.agencia_caja',
                'caj_pag_not.caja_id',
                'caj_pag_not.usuario_cobrador',
                DB::raw('MIN(caj_pag_not.fecha_pago) as fecha_min'),

                DB::raw("'pago_notificaciones' as nombre_tabla")


            )->join('credito_notificaciones as cre_not', 'caj_pag_not.notificacion_id', 'cre_not.id')
            ->where('caj_pag_not.numero_recibo', $numero_recibo)
            ->groupBy('caj_pag_not.numero_recibo')
            ->get()->last();

        if ($pago_cuotas != null) {
            $lista[] = $pago_cuotas;

            $datos_cancelacion = Credito::on($conexion)
                ->select(
                    'dscto_mora_cancelado',
                    'dscto_notificaciones_cancelado',
                    'dscto_interes_cancelado'
                )
                ->where([
                    ['id', $pago_cuotas->credito_id],
                    ['fecha_hora_cancelado', $pago_cuotas->fecha_min]
                ])->get()->last();
        }

        if ($pago_moras != null) {
            $lista[] =  $pago_moras;
        }

        if ($pago_notificaciones != null) {
            $lista[] = $pago_notificaciones;
        }

        if (count($lista) == 0) {
            $resultado = null;
        } else {
            $monto_unico = 0;
            $numero_cuota = 0;

            $resultado = (object)[
                'cliente' => null,
                'tipo_credito' => null,
                'usuario_asesor' => null,
                'numero_recibo' => null,
                'numero_cuota' => 0,
                'credito_id' => null,

                'agencia_caja' => null,
                'usuario_caja' => null,
                'usuario_cobranza' => null,

                'total_pagado' => 0,
                'pago_cuotas' => 0,
                'pago_moras' => 0,
                'pago_notificaciones' => 0,
                'dscto_mora' => 0,
                'dscto_notificaciones' => 0,
                'dscto_interes' => 0,
                'fecha_registro' => null

            ];

            // dd($lista);
            foreach ($lista as  $value) {

                $monto_unico += $value->monto_total;

                if ($value->nombre_tabla == 'pago_cuotas') {

                    $numero_cuota = $value->max_cuota;

                    $resultado->pago_cuotas = $value->monto_total;
                }
                if ($value->nombre_tabla == 'pago_moras') {

                    if ($numero_cuota == 0) {
                        $numero_cuota = $value->max_cuota;
                    }

                    $resultado->pago_moras = $value->monto_total;
                }
                if ($value->nombre_tabla == 'pago_notificaciones') {

                    if ($numero_cuota == 0) {
                        $numero_cuota = $value->max_cuota;
                    }

                    $resultado->pago_notificaciones = $value->monto_total;
                }
            }

            if ($datos_cancelacion != null) {
                $resultado->dscto_mora = $datos_cancelacion->dscto_mora_cancelado;
                $resultado->dscto_notificaciones = $datos_cancelacion->dscto_notificaciones_cancelado;
                $resultado->dscto_interes =  $datos_cancelacion->dscto_interes_cancelado;

                $total_dsctos = $resultado->dscto_mora +
                    $resultado->dscto_notificaciones +
                    $resultado->dscto_interes;
            } else {
                $total_dsctos = 0;
            }

            $resultado->numero_recibo = $lista[0]->numero_recibo;
            $resultado->numero_cuota = $numero_cuota;
            $resultado->credito_id = $lista[0]->credito_id;
            $resultado->agencia_caja = $lista[0]->agencia_caja;
            $resultado->caja_id = $lista[0]->caja_id;
            $resultado->fecha_registro = $lista[0]->fecha_min;
            $resultado->total_pagado = $monto_unico - $total_dsctos;

            $usuario_cobrador = $lista[0]->usuario_cobrador;
            $credito_id = $lista[0]->credito_id;
            $agencia_caja = $lista[0]->agencia_caja;
            $caja_id = $lista[0]->caja_id;

            if ($credito_id != null) {

                $datos_cliente_credito = Credito::on($conexion)->from('credito_registros as cre_reg')
                    ->select(
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cre_tip.tipo',
                        'usu.usuario as usuario_asesor',

                    )
                    ->join('cliente_registros as cli_reg', 'cli_reg.id', 'cre_reg.cliente_id')
                    ->join('solucion_master.usuarios as usu', 'cre_reg.asesor_id', 'usu.dni')

                    ->join('credito_aprobaciones as cre_apr', 'cre_apr.id', 'cre_reg.aprobacion_id')
                    ->join('credito_tipos as cre_tip', 'cre_tip.id', 'cre_apr.tipo_id')


                    ->where('cre_reg.id', $credito_id)
                    ->get()->last();

                $nombre_cliente = $datos_cliente_credito->apellido_paterno . " " . $datos_cliente_credito->apellido_materno . " " . $datos_cliente_credito->nombres;

                $resultado->cliente = $nombre_cliente;
                $resultado->tipo_credito = $datos_cliente_credito->tipo;
                $resultado->usuario_asesor = $datos_cliente_credito->usuario_asesor;
            }

            if ($usuario_cobrador != null) {

                $datos_usuario = Usuario::select('usuario')->where('dni', $usuario_cobrador)->get()->last();
                $resultado->usuario_cobranza = $datos_usuario->usuario;
            } else {
                $resultado->usuario_cobranza = "-";
            }

            if ($agencia_caja != null && $caja_id != null) {

                $conexion2 = 'master_' .  $agencia_caja;
                $datos_caja = Caja::on($conexion2)->from('caja_registros as caj_reg')
                    ->select(
                        'usu.usuario as usuario_caja',

                    )->join('solucion_master.usuarios as usu', 'caj_reg.dni', 'usu.dni')
                    ->where('caj_reg.id', $caja_id)->get()->last();

                $resultado->usuario_caja = $datos_caja->usuario_caja;
            }
        }

        return [
            'resultado' => $resultado
        ];
    }
}
