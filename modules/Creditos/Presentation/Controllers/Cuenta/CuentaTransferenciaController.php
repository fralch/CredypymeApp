<?php

namespace Modules\Creditos\Presentation\Controllers\Cuenta;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transferencia as CajaTransferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Transferencia as CuentaTransferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Movimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\TipoMovimiento;
use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CuentaTransferenciaController extends Controller
{

    public function transferencia($tipo)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;

            if ($tipo == 'A_CAJA') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSFERENCIA_CAJA', 'CREDITOS_CUENTA');
            } else if ($tipo == 'A_CUENTA') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSFERENCIA_CUENTA', 'CREDITOS_CUENTA');
            } else if ($tipo == 'A_MI_CAJA') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSFERENCIA_MI_CAJA', 'CREDITOS_CUENTA');
            }

            if ($band == 1) {

                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;

                if ($tipo == 'A_CAJA') {
                    $destinatarios = Caja::on($conexion)->from('caja_registros as caj_reg')
                        ->select(
                            'caj_reg.id',
                            'caj_reg.monto_apertura',
                            'caj_reg.dni',
                            'caj_reg.agencia_id',

                            'usu.usuario'

                        )
                        ->join('solucion_master.usuarios as usu', 'usu.dni', 'caj_reg.dni')
                        ->where([
                            ['caj_reg.dni', '<>', session('usuario_dni')],
                            ['caj_reg.agencia_id', session('id_agencia')],
                            ['caj_reg.datos_cierre', null]
                        ])
                        ->get();
                } else if ($tipo == 'A_CUENTA') {
                    $destinatarios = CuentaUsuario::on($conexion)->from('cuenta_usuarios as cue_usu')
                        ->select(
                            'cue_usu.id',
                            'cue_usu.dni',

                            'usu.usuario',
                            'usu.agencia_id'
                        )
                        ->join('solucion_master.usuarios as usu', 'usu.dni', 'cue_usu.dni')
                        ->where([
                            ['cue_usu.dni', '<>', session('usuario_dni')],
                            ['usu.agencia_id', session('id_agencia')],
                            ['cue_usu.con_cuenta', 1],
                            ['usu.habilitado', 1]
                        ])
                        ->get();
                } else if ($tipo == 'A_MI_CAJA') {
                    $destinatarios = Caja::on($conexion)->from('caja_registros as caj_reg')
                        ->select(
                            'caj_reg.id',
                            'caj_reg.monto_apertura',
                            'caj_reg.dni',
                            'caj_reg.agencia_id',

                            'usu.usuario'

                        )
                        ->join('solucion_master.usuarios as usu', 'usu.dni', 'caj_reg.dni')
                        ->where([
                            ['caj_reg.dni', session('usuario_dni')],
                            ['caj_reg.agencia_id', session('id_agencia')],
                            ['caj_reg.datos_cierre', null]
                        ])
                        ->get();
                }

                return Inertia::render('Creditos/Cuenta/transferencia', [
                    'agencia_id' => intval($agencia_id),
                    'tipo' => $tipo,
                    'destinatarios' => $destinatarios,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function mis_transferencias()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_TRANSFERENCIAS', 'CREDITOS_CUENTA');

            if ($band == 1) {

                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;

                $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

                $tu_cuenta = CuentaUsuario::on($conexion)->where([
                    ['dni', session('usuario_dni')],
                    ['con_cuenta', 1]
                ])->get()->last();

                $tu_cuenta_id = 0;
                $transferencias_recibidas = [];
                $transferencias_enviadas = [];

                if ($tu_cuenta != null) {

                    $tu_cuenta_id = $tu_cuenta->id;

                    // Para transferencias recibidas

                    $transferencias_de_caja = CajaTransferencia::on($conexion)->from('caja_transferencias as caj_tra')
                        ->select(
                            'caj_tra.id',
                            'caj_tra.tipo',
                            DB::raw("'DE_CAJA' as tipo_envio"),
                            'caj_tra.remitente_id',
                            'caj_tra.destinatario_id',
                            'caj_tra.descripcion',
                            'caj_tra.monto',
                            'caj_tra.estado',
                            'caj_tra.comentario_rechazo',
                            'caj_tra.datos_creacion',
                            'caj_tra.datos_actualizacion',

                            'us_1.usuario as usuario_remitente',
                            'us_2.usuario as usuario_destinatario'
                        )
                        ->join('caja_registros as caj_reg_1', 'caj_tra.remitente_id', 'caj_reg_1.id')
                        ->join('cuenta_usuarios as cue_usu', 'caj_tra.destinatario_id', 'cue_usu.id')
                        ->join('solucion_master.usuarios as us_1', 'caj_reg_1.dni', 'us_1.dni')
                        ->join('solucion_master.usuarios as us_2', 'cue_usu.dni', 'us_2.dni')
                        ->where([
                            ['caj_tra.tipo', 'A_CUENTA'],
                            ['caj_tra.destinatario_id', $tu_cuenta_id],
                            [DB::raw("SUBSTR(caj_tra.datos_creacion,11,10)"),  $fecha_actual]
                        ]);


                    $transferencias_recibidas = CuentaTransferencia::on($conexion)->from('cuenta_transferencias as cue_tra')
                        ->select(
                            'cue_tra.id',
                            'cue_tra.tipo',
                            DB::raw("'DE_CUENTA' as tipo_envio"),
                            'cue_tra.remitente_id',
                            'cue_tra.destinatario_id',
                            'cue_tra.descripcion',
                            'cue_tra.monto',
                            'cue_tra.estado',
                            'cue_tra.comentario_rechazo',
                            'cue_tra.datos_creacion',
                            'cue_tra.datos_actualizacion',

                            'us_1.usuario as usuario_remitente',
                            'us_2.usuario as usuario_destinatario'
                        )
                        ->join('cuenta_usuarios as cue_usu_1', 'cue_tra.remitente_id', 'cue_usu_1.id')
                        ->join('cuenta_usuarios as cue_usu_2', 'cue_tra.destinatario_id', 'cue_usu_2.id')
                        ->join('solucion_master.usuarios as us_1', 'cue_usu_1.dni', 'us_1.dni')
                        ->join('solucion_master.usuarios as us_2', 'cue_usu_2.dni', 'us_2.dni')
                        ->where([
                            ['cue_tra.tipo', 'A_CUENTA'],
                            ['cue_tra.destinatario_id', $tu_cuenta_id],
                            [DB::raw("SUBSTR(cue_tra.datos_creacion,11,10)"),  $fecha_actual]
                        ])
                        ->union($transferencias_de_caja)
                        ->orderBy('datos_creacion', 'desc')
                        ->get();


                    // Para transferencias enviadas
                    $transferencias_a_caja = CuentaTransferencia::on($conexion)->from('cuenta_transferencias as cue_tra')
                        ->select(
                            'cue_tra.id',
                            'cue_tra.tipo',
                            'cue_tra.tipo as tipo_envio',
                            'cue_tra.remitente_id',
                            'cue_tra.destinatario_id',
                            'cue_tra.descripcion',
                            'cue_tra.monto',
                            'cue_tra.estado',
                            'cue_tra.comentario_rechazo',
                            'cue_tra.datos_creacion',
                            'cue_tra.datos_actualizacion',

                            'us_1.usuario as usuario_remitente',
                            'us_2.usuario as usuario_destinatario'
                        )
                        ->join('cuenta_usuarios as cue_usu', 'cue_tra.remitente_id', 'cue_usu.id')
                        ->join('caja_registros as caj_reg', 'cue_tra.destinatario_id', 'caj_reg.id')
                        ->join('solucion_master.usuarios as us_1', 'cue_usu.dni', 'us_1.dni')
                        ->join('solucion_master.usuarios as us_2', 'caj_reg.dni', 'us_2.dni')
                        ->where([
                            ['cue_tra.tipo', 'A_CAJA'],
                            ['cue_tra.remitente_id', $tu_cuenta_id],
                            [DB::raw("SUBSTR(cue_tra.datos_creacion,11,10)"),  $fecha_actual]
                        ]);

                    $transferencias_enviadas = CuentaTransferencia::on($conexion)->from('cuenta_transferencias as cue_tra')
                        ->select(
                            'cue_tra.id',
                            'cue_tra.tipo',
                            'cue_tra.tipo as tipo_envio',
                            'cue_tra.remitente_id',
                            'cue_tra.destinatario_id',
                            'cue_tra.descripcion',
                            'cue_tra.monto',
                            'cue_tra.estado',
                            'cue_tra.comentario_rechazo',
                            'cue_tra.datos_creacion',
                            'cue_tra.datos_actualizacion',

                            'us_1.usuario as usuario_remitente',
                            'us_2.usuario as usuario_destinatario'
                        )
                        ->join('cuenta_usuarios as cue_usu_1', 'cue_tra.remitente_id', 'cue_usu_1.id')
                        ->join('cuenta_usuarios as cue_usu_2', 'cue_tra.destinatario_id', 'cue_usu_2.id')
                        ->join('solucion_master.usuarios as us_1', 'cue_usu_1.dni', 'us_1.dni')
                        ->join('solucion_master.usuarios as us_2', 'cue_usu_2.dni', 'us_2.dni')
                        ->where([
                            ['cue_tra.tipo', 'A_CUENTA'],
                            ['cue_tra.remitente_id', $tu_cuenta_id],
                            [DB::raw("SUBSTR(cue_tra.datos_creacion,11,10)"),  $fecha_actual]
                        ])
                        ->union($transferencias_a_caja)
                        ->orderBy('datos_creacion', 'desc')
                        ->get();
                }

                return Inertia::render('Creditos/Cuenta/mis_transferencias', [
                    'agencia_id' => intval($agencia_id),
                    'transferencias_recibidas' => $transferencias_recibidas,
                    'transferencias_enviadas' => $transferencias_enviadas
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function registrar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $tipo = $request->tipo;

        $frmDatosTransferencia = json_decode($request->frmDatosTransferencia);
        $monto = $frmDatosTransferencia->monto;
        $remitente_id = $frmDatosTransferencia->remitente_id;
        $destinatario_id = $frmDatosTransferencia->destinatario_id;
        $descripcion = mb_strtoupper($frmDatosTransferencia->descripcion);

        if ($tipo == 'A_MI_CAJA') {
            $tipo = 'A_CAJA';
        }

        CuentaTransferencia::on($conexion)->create([
            'tipo' => $tipo,
            'remitente_id' => $remitente_id,
            'destinatario_id' => $destinatario_id,
            'agencia_id' => session('id_agencia'),
            'descripcion' => $descripcion,
            'monto' => $monto,
            'estado' => 'PENDIENTE',
            'datos_creacion' => $datos_registro
        ]);

        return redirect()->route('cre.index');
    }

    public function confirmar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_movimiento = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $modo = $request->modo;

        if ($modo == 'ACEPTAR') {
            $transferencia = json_decode($request->transferencia);
            $transferencia_id = $transferencia->id;
            $monto = $transferencia->monto;
            $tipo_envio = $transferencia->tipo_envio;

            if ($tipo_envio == 'DE_CAJA') {

                $transferencia = CajaTransferencia::on($conexion)->where('id', $transferencia_id)->get()->last();

                CuentaUsuario::on($conexion)->where('id', $transferencia->destinatario_id)
                    ->update([
                        'monto' => DB::raw("monto+$monto"),
                        'datos_actualizacion' => $datos_registro
                    ]);

                // Registrando el movimiento

                $tipo_movimiento = TipoMovimiento::on($conexion)->where('nombre', 'TRANSFERENCIA A CUENTA')->get()->last();
                $tipo_movimiento_id = $tipo_movimiento->id;


                Movimiento::on($conexion)->create([
                    'cuenta_id' => $transferencia->destinatario_id,
                    'tipo' => 'I',
                    'monto' => $monto,
                    'descripcion' =>  $tipo_movimiento->nombre,
                    'tipo_movimiento_id' => $tipo_movimiento_id,
                    'fecha_movimiento' => $fecha_movimiento,
                    'datos_creacion' => $datos_registro
                ]);

                CajaTransferencia::on($conexion)->where('id', $transferencia_id)
                    ->update([
                        'estado' => 'CONFIRMADO',
                        'datos_actualizacion' => $datos_registro
                    ]);
            } else if ($tipo_envio == 'DE_CUENTA') {

                $transferencia = CuentaTransferencia::on($conexion)->where('id', $transferencia_id)->get()->last();

                CuentaUsuario::on($conexion)->where('id', $transferencia->remitente_id)
                    ->update([
                        'monto' => DB::raw("monto-$monto"),
                        'datos_actualizacion' => $datos_registro
                    ]);

                CuentaUsuario::on($conexion)->where('id', $transferencia->destinatario_id)
                    ->update([
                        'monto' => DB::raw("monto+$monto"),
                        'datos_actualizacion' => $datos_registro
                    ]);

                // Registrando el movimiento

                $tipo_movimiento = TipoMovimiento::on($conexion)->where('nombre', 'TRANSFERENCIA A CUENTA')->get()->last();
                $tipo_movimiento_id = $tipo_movimiento->id;

                Movimiento::on($conexion)->create([
                    'cuenta_id' => $transferencia->remitente_id,
                    'tipo' => 'E',
                    'monto' => $monto,
                    'descripcion' =>  $tipo_movimiento->nombre,
                    'tipo_movimiento_id' => $tipo_movimiento_id,
                    'fecha_movimiento' => $fecha_movimiento,
                    'datos_creacion' => $datos_registro
                ]);

                Movimiento::on($conexion)->create([
                    'cuenta_id' => $transferencia->destinatario_id,
                    'tipo' => 'I',
                    'monto' => $monto,
                    'descripcion' =>  $tipo_movimiento->nombre,
                    'tipo_movimiento_id' => $tipo_movimiento_id,
                    'fecha_movimiento' => $fecha_movimiento,
                    'datos_creacion' => $datos_registro
                ]);

                CuentaTransferencia::on($conexion)->where('id', $transferencia_id)
                    ->update([
                        'estado' => 'CONFIRMADO',
                        'datos_actualizacion' => $datos_registro
                    ]);
            }
        } else if ($modo == 'RECHAZAR') {
            $transferencia_id = $request->transferencia_id;
            $comentario = mb_strtoupper($request->comentario);
            $tipo_envio = $request->tipo_envio;

            if ($tipo_envio == 'DE_CAJA') {
                CajaTransferencia::on($conexion)->where('id', $transferencia_id)
                    ->update([
                        'estado' => 'RECHAZADO',
                        'comentario_rechazo' => $comentario,
                        'datos_actualizacion' => $datos_registro
                    ]);
            } else if ($tipo_envio == 'DE_CUENTA') {
                CuentaTransferencia::on($conexion)->where('id', $transferencia_id)
                    ->update([
                        'estado' => 'RECHAZADO',
                        'comentario_rechazo' => $comentario,
                        'datos_actualizacion' => $datos_registro
                    ]);
            }
        }

        return redirect()->route('cue.mis_transferencias');
    }
}
