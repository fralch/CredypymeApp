<?php

namespace Modules\Creditos\Presentation\Controllers\Cuenta;

use App\Http\Controllers\Controller;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\General\Presentation\Controllers\PermisosController;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

use Modules\General\Infrastructure\Persistence\Eloquent\AreaTrabajo;

use Modules\General\Infrastructure\Persistence\Eloquent\Banco;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Categoria;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Subcategoria;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Comprobante;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\BancoMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transaccion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transferencia as CajaTransferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Transferencia as CuentaTransferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Movimiento as CuentaMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\TipoMovimiento;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class CuentaBancariaController extends Controller
{
    public function index()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)
                ->verificarPermiso($x['usuario_dni'], 'CUENTAS_BANCARIAS', 'CREDITOS_CUENTA');
            if ($band == 1) {

                return Inertia('Creditos/Cuenta/cuentas_bancarias');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar($agencia_id)
    {
        $lista_cuentas = Banco::where([
            ['agencia_id', $agencia_id],
            ['habilitado', 1]
        ])
            ->orderBy('banco', 'asc')
            ->get();

        return response()->json(['lista_cuentas' => $lista_cuentas]);
    }

    public function movimientos_agrupado($banco_id, Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' . $agencia_id;

        $lista_movimientos_agrupado = BancoMovimiento::on($conexion)
            ->select(
                DB::raw("CAST(fecha_movimiento AS DATE) as fecha"),
                'tipo',
                DB::raw("SUM(monto) as total")
            )
            ->where('banco_id', $banco_id)
            ->groupBy(DB::raw("CAST(fecha_movimiento AS DATE)"), 'tipo')
            ->orderBy('fecha', 'desc')
            ->orderBy('tipo', 'asc')
            ->get();


        return response()->json(['lista_movimientos_agrupado' => $lista_movimientos_agrupado]);
    }

    public function movimientos_detalle($banco_id, Request $request)
    {

        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' . $agencia_id;

        $fecha = $request->input('fecha');
        $tipo = $request->input('tipo');

        $lista_movimientos = BancoMovimiento::on($conexion)
            ->where([
                ['banco_id', $banco_id],
                ['tipo', $tipo],
                [DB::raw("DATE(fecha_movimiento)"), $fecha]
            ])
            ->orderBy('fecha_movimiento', 'desc')
            ->get();


        $lista_movimientos_detalle = $lista_movimientos->map(function ($row, $index) {

            $agencia_operacion = $row['agencia_operacion'];
            $conexion_movimiento = 'master_' . $agencia_operacion;

            $modo = $row['modo'];

            $descripcion = $row['descripcion'];

            if ($modo == 'DE_CUENTA' || $modo == 'A_CUENTA') {

                $cuenta = CuentaUsuario::on($conexion_movimiento)->find($row['cuenta_operacion']);
                $usuario = $cuenta->dni;
                $descripcion .= ' (' . $modo . ')';
            } elseif ($modo == 'DE_CAJA' || $modo == 'A_CAJA') {

                $caja = Caja::on($conexion_movimiento)->find($row['caja_operacion']);
                $usuario = $caja->dni;

                $descripcion .= ' (' . $modo . ')';
            } elseif ($modo == 'DE_BANCO' || $modo == 'A_BANCO') {
                $datos_creacion = json_decode($row['datos_creacion']);
                $usuario = $datos_creacion->usuario;

                $banco_operacion = $row['banco_operacion'];
                $banco = Banco::find($banco_operacion);

                $descripcion .= ' (' . $modo . ': ' . $banco->banco . ')';
            }

            $usuario_movimiento = Usuario::find($usuario);

            return [
                'fecha_movimiento' => $row['fecha_movimiento'],
                'tipo' => $row['tipo'],
                'operacion' => $row['operacion'],
                'descripcion' => $descripcion,
                'monto' => $row['monto'],
                'modo' => $row['modo'],
                'usuario' => $usuario_movimiento->usuario
            ];
        });


        return response()->json(['lista_movimientos_detalle' => $lista_movimientos_detalle]);
    }

    public function registrar_movimiento(Request $request)
    {
        $tipo = $request->tipo;
        $operacion = null;

        switch ($tipo) {
            case 'RETIRAR':
                $operacion = 'RETIRO';
                return $this->retirar($request, $operacion);
                break;
            case 'ABONAR':
                $operacion = 'COMISION';
                return $this->abonar($request, $operacion);
                break;
            case 'TRANSFERIR':
                $operacion = 'TRANSFERENCIA';
                return $this->transferir($request, $operacion);
                break;
        }
    }
    public function retirar(Request $request, $operacion)
    {
        $agencia_operacion = $request->agencia_operacion;
        $agencia_banco = $request->agencia_banco;

        $conexion_operacion = 'master_' . $agencia_operacion;
        $conexion_banco = 'master_' . $agencia_banco;

        $datos_registro = (new CreditosController)->datos_registro($agencia_banco);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_banco);

        $banco_id = $request->banco_id;
        $frmDatosMovimiento = json_decode($request->frmDatosMovimiento);
        $descripcion = mb_strtoupper(trim($frmDatosMovimiento->descripcion));

        $banco = Banco::find($banco_id);

        if ($frmDatosMovimiento->modo == 'A_CAJA') {
            $caja_operacion = $request->caja_operacion;

            // Registro del movimiento en la CUENTA BANCARIA 
            BancoMovimiento::on($conexion_banco)
                ->create([
                    'banco_id' => $banco_id,
                    'operacion' => $operacion,
                    'tipo' => 'E',
                    'monto' => $frmDatosMovimiento->monto,
                    'modo' => $frmDatosMovimiento->modo,
                    'fecha_movimiento' => $fecha_larga,
                    'agencia_operacion' => $agencia_operacion,
                    'caja_operacion' => $caja_operacion,
                    'descripcion' => $descripcion,
                    'datos_creacion' => $datos_registro
                ]);

            // Registro de la transferencia hacia la CAJA
            CajaTransferencia::on($conexion_operacion)
                ->create([
                    'tipo' => 'A_CAJA',
                    'remitente_id' => $caja_operacion,
                    'destinatario_id' => $caja_operacion,
                    'agencia_id' => $agencia_operacion,
                    'descripcion' => $descripcion,
                    'monto' => $frmDatosMovimiento->monto,
                    'estado' => 'CONFIRMADO',
                    'datos_creacion' => $datos_registro,
                    'datos_actualizacion' => $datos_registro
                ]);
        } else if ($frmDatosMovimiento->modo == 'A_CUENTA') {
            $cuenta_operacion = $request->cuenta_operacion;

            // Registro del movimiento en la CUENTA BANCARIA 
            BancoMovimiento::on($conexion_banco)
                ->create([
                    'banco_id' => $banco_id,
                    'operaion' => $operacion,
                    'tipo' => 'E',
                    'monto' => $frmDatosMovimiento->monto,
                    'modo' => $frmDatosMovimiento->modo,
                    'fecha_movimiento' => $fecha_larga,
                    'agencia_operacion' => $agencia_operacion,
                    'cuenta_operacion' => $cuenta_operacion,
                    'descripcion' => $descripcion,
                    'datos_creacion' => $datos_registro
                ]);

            // Registro de la transferencia hacia la CUENTA
            CuentaTransferencia::on($conexion_operacion)
                ->create([
                    'tipo' => 'A_CUENTA',
                    'remitente_id' => $cuenta_operacion,
                    'destinatario_id' => $cuenta_operacion,
                    'agencia_id' => $agencia_operacion,
                    'descripcion' => $descripcion,
                    'monto' => $frmDatosMovimiento->monto,
                    'estado' => 'CONFIRMADO',
                    'datos_creacion' => $datos_registro,
                    'datos_actualizacion' => $datos_registro
                ]);

            // Registro del movimiento hacia la CUENTA
            $tipo_movimiento = TipoMovimiento::on($conexion_operacion)
                ->where('nombre', 'TRANSFERENCIA A CUENTA')->get()->last();

            CuentaMovimiento::on($conexion_operacion)
                ->create([
                    'cuenta_id' => $cuenta_operacion,
                    'tipo' => 'I',
                    'monto' => $frmDatosMovimiento->monto,
                    'descripcion' => 'TRANSFERENCIA DE CUENTA BANCARIA (' . $banco->banco . ')',
                    'tipo_movimiento_id' => $tipo_movimiento->id,
                    'fecha_movimiento' => $fecha_larga,
                    'datos_creacion' => $datos_registro,
                    'datos_actualizacion' => $datos_registro
                ]);

            // Actualizando SALDO en CUENTA
            $cuenta = CuentaUsuario::on($conexion_operacion)->find($cuenta_operacion);
            $cuenta->monto += $frmDatosMovimiento->monto;
            $cuenta->save();
        }

        // Actualizar ACUMULADO en el BANCO
        $banco->acumulado -= $frmDatosMovimiento->monto;
        $banco->datos_actualizacion = $datos_registro;
        $banco->save();

        return response()->json([
            'message' => 'Movimiento registrado correctamente',
            'acumulado' => $banco->acumulado
        ]);
    }

    public function abonar($request, $operacion)
    {

        $agencia_operacion = $request->agencia_operacion;
        $agencia_banco = $request->agencia_banco;

        $conexion_operacion = 'master_' . $agencia_operacion;
        $conexion_banco = 'master_' . $agencia_banco;

        $datos_registro = (new CreditosController)->datos_registro($agencia_banco);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_banco);

        $banco_id = $request->banco_id;
        $frmDatosMovimiento = json_decode($request->frmDatosMovimiento);
        $descripcion = mb_strtoupper(trim($frmDatosMovimiento->descripcion));

        $banco = Banco::find($banco_id);

        $caja_operacion = $request->caja_operacion;

        // Registro del movimiento en la CUENTA BANCARIA 
        BancoMovimiento::on($conexion_banco)
            ->create([
                'banco_id' => $banco_id,
                'operacion' => $operacion,
                'tipo' => 'I',
                'monto' => $frmDatosMovimiento->monto,
                'modo' => $frmDatosMovimiento->modo,
                'fecha_movimiento' => $fecha_larga,
                'agencia_operacion' => $agencia_operacion,
                'caja_operacion' => $caja_operacion,
                'descripcion' => $descripcion,
                'datos_creacion' => $datos_registro
            ]);

        // Registro del EGRESO para la caja
        $categoria = Categoria::on($conexion_operacion)
            ->where('categoria', 'GASTOS FINANCIEROS')
            ->get()->last();

        $subcategoria = Subcategoria::on($conexion_operacion)
            ->where('subcategoria', 'COMISIONES')
            ->get()->last();

        $area_trabajo = AreaTrabajo::where('area', 'CONTABILIDAD')
            ->get()->last();

        $comprobante = Comprobante::on($conexion_operacion)
            ->where('comprobante', 'OTROS')
            ->get()->last();

        Transaccion::on($conexion_operacion)
            ->create([
                'tipo' => 'E',
                'categoria_id' => $categoria->id,
                'subcategoria_id' => $subcategoria->id,
                'agencia_id' => $agencia_operacion,
                'usuario_id' => session('usuario_dni'),
                'area_trabajo_id' => $area_trabajo->id,
                'comprobante_id' => $comprobante->id,
                'caja_id' => $caja_operacion,
                'monto' => $frmDatosMovimiento->monto,
                'concepto' => $descripcion,
                'documento' => null,
                'fecha_transaccion' => $fecha_larga,
                'datos_creacion' => $datos_registro
            ]);

        // Actualizar ACUMULADO en el BANCO
        $banco->acumulado += $frmDatosMovimiento->monto;
        $banco->datos_actualizacion = $datos_registro;
        $banco->save();
        return response()->json([
            'message' => 'Movimiento registrado correctamente',
            'acumulado' => $banco->acumulado
        ]);
    }
    public function transferir($request, $operacion)
    {
        $agencia_operacion = $request->agencia_operacion;
        $agencia_banco = $request->agencia_banco;

        $conexion_banco = 'master_' . $agencia_banco;

        $datos_registro = (new CreditosController)->datos_registro($agencia_banco);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_banco);

        $banco_id = $request->banco_id;
        $frmDatosMovimiento = json_decode($request->frmDatosMovimiento);
        $banco_transferencia_id = $frmDatosMovimiento->banco_transferencia_id;
        $descripcion = mb_strtoupper(trim($frmDatosMovimiento->descripcion));

        $banco_envio = Banco::find($banco_id);
        $banco_recepcion = Banco::find($banco_transferencia_id);

        // Registro del movimiento en la CUENTA BANCARIA SALIENTE
        BancoMovimiento::on($conexion_banco)
            ->create([
                'banco_id' => $banco_id,
                'operacion' => $operacion,
                'tipo' => 'E',
                'monto' => $frmDatosMovimiento->monto,
                'modo' => 'A_BANCO',
                'fecha_movimiento' => $fecha_larga,
                'agencia_operacion' => $agencia_operacion,
                'banco_operacion' => $banco_transferencia_id,
                'descripcion' => $descripcion,
                'datos_creacion' => $datos_registro
            ]);

        // Registro del movimiento en la CUENTA BANCARIA ENTRANTE
        BancoMovimiento::on($conexion_banco)
            ->create([
                'banco_id' => $banco_transferencia_id,
                'operacion' => $operacion,
                'tipo' => 'I',
                'monto' => $frmDatosMovimiento->monto,
                'modo' =>  $frmDatosMovimiento->modo,
                'fecha_movimiento' => $fecha_larga,
                'agencia_operacion' => $agencia_operacion,
                'banco_operacion' => $banco_id,
                'descripcion' => $descripcion,
                'datos_creacion' => $datos_registro
            ]);

        // Actualizar ACUMULADO en ambos BANCOS

        $banco_envio->acumulado -= $frmDatosMovimiento->monto;
        $banco_envio->datos_actualizacion = $datos_registro;
        $banco_envio->save();

        $banco_recepcion->acumulado += $frmDatosMovimiento->monto;
        $banco_recepcion->datos_actualizacion = $datos_registro;
        $banco_recepcion->save();

        return response()->json([
            'message' => 'Movimiento registrado correctamente',
            'acumulado' => $banco_envio->acumulado
        ]);
    }
}
