<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Caja\Transaccion;
use App\Models\Creditos\Caja\EnvioPagos;
use App\Models\Creditos\Caja\RecepcionPagos;

use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Mantenimiento\Transacciones\Categoria;
use App\Models\Creditos\Mantenimiento\Transacciones\Subcategoria;
use App\Models\General\AreaTrabajo;
use App\Models\Creditos\Mantenimiento\Transacciones\Comprobante;
use App\Models\Creditos\Caja\Caja;
use App\Models\General\Cargo;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;


class CajaTransferenciaPagoController extends Controller
{

    public function envioPagos()
    {
        $x = session()->all();


        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ENVIO_PAGOS', 'CREDITOS_CAJA');

            if ($band == 1) {


                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;

                $lista_cargos = ['AUXILIAR DE OPERACIONES', 'JEFE DE OPERACIONES'];

                $cargos = Cargo::whereIn('cargo', $lista_cargos)->get();

                $cargos_id = [];

                foreach ($cargos as $item) {
                    $cargos_id[] = $item->id;
                };

                $usuarios = Usuario::select()->where([
                    ['habilitado', 1]
                ])->whereIn('cargo_id', $cargos_id)->get();


                // $categorias = Categoria::on($conexion)->where('habilitado', 1)->get();

                return Inertia::render('Creditos/Caja/envio_pagos', [
                    'usuarios' => $usuarios,



                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function registrar_envio(Request $request)
    {
        // return $request;
        $agencia_remitente_id = $request->agencia_remitente_id;
        $conexion = 'master_' .  $agencia_remitente_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_remitente_id);
        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_remitente_id);
        $año  = intval(date("Y", strtotime($fecha_corta)));

        $caja_remitente = $request->remitente_id;
        $frmDatosEnvio = json_decode($request->frmDatosEnvio);

        $agencia_destinatario_id = $frmDatosEnvio->agencia_destinatario_id;
        $usuario_destinatario = $frmDatosEnvio->destinatario_id;
        $monto = $frmDatosEnvio->monto;
        $concepto = mb_strtoupper($frmDatosEnvio->concepto);
        $motivo = $frmDatosEnvio->motivo;


        $envio = EnvioPagos::on($conexion)->create([
            'agencia_remitente_id' => $agencia_remitente_id,
            'caja_remitente' => $caja_remitente,
            'agencia_destinatario_id' => $agencia_destinatario_id,
            'usuario_destinatario' => $usuario_destinatario,
            'monto' => $monto,
            'concepto' => $concepto,
            'motivo' => $motivo,
            'datos_creacion' => $datos_registro
        ]);
        return redirect()->route('cre.index');
    }

    public function recepcionPagos()
    {
        $x = session()->all();


        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'RECEPCION_PAGOS', 'CREDITOS_CAJA');

            if ($band == 1) {
                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;

                $lista_cargos = ['AUXILIAR DE OPERACIONES', 'JEFE DE OPERACIONES'];

                $cargos = Cargo::whereIn('cargo', $lista_cargos)->get();

                $cargos_id = [];

                foreach ($cargos as $item) {
                    $cargos_id[] = $item->id;
                };

                $usuarios = Usuario::select()->where([
                    ['habilitado', 1]
                ])->whereIn('cargo_id', $cargos_id)->get();


                // $categorias = Categoria::on($conexion)->where('habilitado', 1)->get();

                return Inertia::render('Creditos/Caja/recepcion_pagos', [
                    'usuarios' => $usuarios,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function registrar_recepcion(Request $request)
    {
        // return $request;
        $agencia_destinatario = $request->agencia_destinatario;
        $conexion = 'master_' .  $agencia_destinatario;

        $datos_registro = (new CreditosController)->datos_registro($agencia_destinatario);
        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_destinatario);
        $año  = intval(date("Y", strtotime($fecha_corta)));

        $caja_destinatario = $request->caja_destinatario;
        $frmDatosEnvio = json_decode($request->frmDatosEnvio);

        $agencia_remitente_id = $frmDatosEnvio->agencia_remitente_id;
        $usuario_remitente = $frmDatosEnvio->usuario_remitente;
        $monto = $frmDatosEnvio->monto;
        $concepto = mb_strtoupper($frmDatosEnvio->concepto);
        $motivo = $frmDatosEnvio->motivo;


        $envio = RecepcionPagos::on($conexion)->create([
            'agencia_destinatario_id' => $agencia_destinatario,
            'caja_destinatario' => $caja_destinatario,
            'agencia_remitente_id' => $agencia_remitente_id,
            'usuario_remitente' => $usuario_remitente,
            'monto' => $monto,
            'concepto' => $concepto,
            'motivo' => $motivo,
            'datos_creacion' => $datos_registro
        ]);
        return redirect()->route('cre.index');
    }
    public function listarTransferenciaPagos()
    {
        $x = session()->all();
        $fecha_larga = substr((new CreditosController)->fecha_larga_aplicacion($x['id_agencia']), 0, 10);
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSFERENCIA_PAGOS', 'CREDITOS_CAJA');
            if ($band == 1) {
                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;
                return Inertia::render('Creditos/Caja/listar_transferencia_pagos', [
                    'fecha_larga' => $fecha_larga,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listarTransferenciasPagosGet(Request $request)
    {
        // return $request;
        $my_agencia = session('id_agencia');
        $conexion = 'master_' .  $my_agencia;

        $agencia = $request->agencia;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $motivo = $request->motivo;
        $tipo = $request->tipo;

        if ($tipo == 'E') {
            if ($agencia != 0 && $motivo != 0) {
                $lista_transferencias = EnvioPagos::on($conexion)->from('caja_envios_pagos as ep')
                    ->select(
                        'usu.usuario as caja',
                        'ep.monto',
                        'ep.concepto',
                        'ep.motivo',
                        'ep.datos_creacion',
                        'usu2.usuario as usuario',
                        'ag.nombre as agencia',
                    )
                    ->join('caja_registros as cj', 'cj.id', '=', 'ep.caja_remitente')
                    ->join('solucion_master.usuarios as usu', 'cj.dni', 'usu.dni')
                    ->join('solucion_master.usuarios as usu2', 'ep.usuario_destinatario', 'usu2.dni')
                    ->join('solucion_master.agencias as ag', 'ep.agencia_destinatario_id', 'ag.id_agencia')
                    ->where('ep.motivo', $motivo)
                    ->where('ep.agencia_destinatario_id', $agencia)
                    ->whereBetween(DB::raw("SUBSTR(ep.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();
                return $lista_transferencias;
            }
            if ($agencia == 0 && $motivo != 0) {
                $lista_transferencias = EnvioPagos::on($conexion)->from('caja_envios_pagos as ep')
                    ->select(
                        'usu.usuario as caja',
                        'ep.monto',
                        'ep.concepto',
                        'ep.motivo',
                        'ep.datos_creacion',
                        'usu2.usuario as usuario',
                        'ag.nombre as agencia',
                    )
                    ->join('caja_registros as cj', 'cj.id', '=', 'ep.caja_remitente')
                    ->join('solucion_master.usuarios as usu', 'cj.dni', 'usu.dni')
                    ->join('solucion_master.usuarios as usu2', 'ep.usuario_destinatario', 'usu2.dni')
                    ->join('solucion_master.agencias as ag', 'ep.agencia_destinatario_id', 'ag.id_agencia')
                    ->where('ep.motivo', $motivo)
                    ->whereBetween(DB::raw("SUBSTR(ep.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();
                return $lista_transferencias;
            }
            if ($agencia != 0 && $motivo == 0) {
                $lista_transferencias = EnvioPagos::on($conexion)->from('caja_envios_pagos as ep')
                    ->select(
                        'usu.usuario as caja',
                        'ep.monto',
                        'ep.concepto',
                        'ep.motivo',
                        'ep.datos_creacion',
                        'usu2.usuario as usuario',
                        'ag.nombre as agencia',
                    )
                    ->join('caja_registros as cj', 'cj.id', '=', 'ep.caja_remitente')
                    ->join('solucion_master.usuarios as usu', 'cj.dni', 'usu.dni')
                    ->join('solucion_master.usuarios as usu2', 'ep.usuario_destinatario', 'usu2.dni')
                    ->join('solucion_master.agencias as ag', 'ep.agencia_destinatario_id', 'ag.id_agencia')
                    ->where('ep.agencia_destinatario_id', $agencia)
                    ->whereBetween(DB::raw("(ep.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();
                return $lista_transferencias;
            }
            $lista_transferencias = EnvioPagos::on($conexion)->from('caja_envios_pagos as ep')
                ->select(
                    'usu.usuario as caja',
                    'ep.monto',
                    'ep.concepto',
                    'ep.motivo',
                    'ep.datos_creacion',
                    'usu2.usuario as usuario',
                    'ag.nombre as agencia',
                )
                ->join('caja_registros as cj', 'cj.id', '=', 'ep.caja_remitente')
                ->join('solucion_master.usuarios as usu', 'cj.dni', 'usu.dni')
                ->join('solucion_master.usuarios as usu2', 'ep.usuario_destinatario', 'usu2.dni')
                ->join('solucion_master.agencias as ag', 'ep.agencia_destinatario_id', 'ag.id_agencia')
                ->whereBetween(DB::raw("SUBSTR(ep.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $lista_transferencias;
        }
        if ($tipo == 'R') {
            if ($agencia != 0 && $motivo != 0) {
                $lista_transferencias = RecepcionPagos::on($conexion)->from('caja_recepcion_pagos as rp')
                    ->select(
                        'usu_r.usuario as usuario',
                        'rp.monto',
                        'rp.concepto',
                        'rp.motivo',
                        'rp.datos_creacion',
                        'usu_d.usuario as caja',
                        'ag_r.nombre as agencia',
                    )
                    ->join('caja_registros as cj', 'cj.id', '=', 'rp.caja_destinatario')
                    ->join('solucion_master.usuarios as usu_d', 'cj.dni', 'usu_d.dni')
                    ->join('solucion_master.usuarios as usu_r', 'rp.usuario_remitente', 'usu_r.dni')
                    ->join('solucion_master.agencias as ag_r', 'rp.agencia_remitente_id', 'ag_r.id_agencia')
                    ->where('rp.motivo', $motivo)
                    ->where('rp.agencia_remitente_id', $agencia)
                    ->whereBetween(DB::raw("SUBSTR(rp.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();
                return $lista_transferencias;
            }
            if ($agencia == 0 && $motivo != 0) {
                $lista_transferencias = RecepcionPagos::on($conexion)->from('caja_recepcion_pagos as rp')
                    ->select(
                        'usu_r.usuario as usuario',
                        'rp.monto',
                        'rp.concepto',
                        'rp.motivo',
                        'rp.datos_creacion',
                        'usu_d.usuario as caja',
                        'ag_r.nombre as agencia',
                    )
                    ->join('caja_registros as cj', 'cj.id', '=', 'rp.caja_destinatario')
                    ->join('solucion_master.usuarios as usu_d', 'cj.dni', 'usu_d.dni')
                    ->join('solucion_master.usuarios as usu_r', 'rp.usuario_remitente', 'usu_r.dni')
                    ->join('solucion_master.agencias as ag_r', 'rp.agencia_remitente_id', 'ag_r.id_agencia')
                    ->where('rp.motivo', $motivo)
                    ->whereBetween(DB::raw("SUBSTR(rp.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();
                return $lista_transferencias;
            }
            if ($agencia != 0 && $motivo == 0) {
                $lista_transferencias = RecepcionPagos::on($conexion)->from('caja_recepcion_pagos as rp')
                    ->select(
                        'usu_r.usuario as usuario',
                        'rp.monto',
                        'rp.concepto',
                        'rp.motivo',
                        'rp.datos_creacion',
                        'usu_d.usuario as caja',
                        'ag_r.nombre as agencia',
                    )
                    ->join('caja_registros as cj', 'cj.id', '=', 'rp.caja_destinatario')
                    ->join('solucion_master.usuarios as usu_d', 'cj.dni', 'usu_d.dni')
                    ->join('solucion_master.usuarios as usu_r', 'rp.usuario_remitente', 'usu_r.dni')
                    ->join('solucion_master.agencias as ag_r', 'rp.agencia_remitente_id', 'ag_r.id_agencia')
                    ->where('rp.agencia_remitente_id', $agencia)
                    ->whereBetween(DB::raw("SUBSTR(rp.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                    ->get();
                return $lista_transferencias;
            }
            $lista_transferencias = RecepcionPagos::on($conexion)->from('caja_recepcion_pagos as rp')
                ->select(
                    'usu_r.usuario as usuario',
                    'rp.monto',
                    'rp.concepto',
                    'rp.motivo',
                    'rp.datos_creacion',
                    'usu_d.usuario as caja',
                    'ag_r.nombre as agencia',
                )
                ->join('caja_registros as cj', 'cj.id', '=', 'rp.caja_destinatario')
                ->join('solucion_master.usuarios as usu_d', 'cj.dni', 'usu_d.dni')
                ->join('solucion_master.usuarios as usu_r', 'rp.usuario_remitente', 'usu_r.dni')
                ->join('solucion_master.agencias as ag_r', 'rp.agencia_remitente_id', 'ag_r.id_agencia')
                ->whereBetween(DB::raw("SUBSTR(rp.datos_creacion,11,10)"), [$fecha_desde, $fecha_hasta])
                ->get();
            return $lista_transferencias;
        }
    }
}
