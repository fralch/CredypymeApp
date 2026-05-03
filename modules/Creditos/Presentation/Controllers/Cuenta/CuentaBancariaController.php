<?php

namespace Modules\Creditos\Presentation\Controllers\Cuenta;

use App\Http\Controllers\Controller;
use App\Auditoria\RegistradorAuditoria;
use App\Finanzas\CoordinadorTransacciones;
use App\MultiAgencia\ResolvedorConexionAgencia;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Modules\Creditos\Presentation\Requests\Cuenta\RegistrarMovimientoBancarioRequest;

class CuentaBancariaController extends Controller
{
    public function __construct(
        private readonly ResolvedorConexionAgencia $resolvedorConexionAgencia,
        private readonly CoordinadorTransacciones $coordinadorTransacciones,
        private readonly RegistradorAuditoria $registradorAuditoria,
    ) {
    }

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
        $agencia_id = (int) $request->input('agencia_id');
        $conexion = $this->resolvedorConexionAgencia->conexionMaster($agencia_id);

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

        $agencia_id = (int) $request->input('agencia_id');
        $conexion = $this->resolvedorConexionAgencia->conexionMaster($agencia_id);

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
            $conexion_movimiento = $this->resolvedorConexionAgencia->conexionMaster((int) $agencia_operacion);

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

    public function registrar_movimiento(RegistrarMovimientoBancarioRequest $request)
    {
        $claveIdempotencia = (string) $request->header('Idempotency-Key', '');
        $usuarioId = session('usuario_dni');

        if ($this->registradorAuditoria->existeIdempotencia('CREDITOS', 'MOVIMIENTO_BANCARIO', $usuarioId, $claveIdempotencia)) {
            return response()->json(['message' => 'Solicitud duplicada'], 409);
        }

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
        $agencia_operacion = (int) $request->agencia_operacion;
        $agencia_banco = (int) $request->agencia_banco;

        $conexion_operacion = $this->resolvedorConexionAgencia->conexionMaster($agencia_operacion);
        $conexion_banco = $this->resolvedorConexionAgencia->conexionMaster($agencia_banco);

        $datos_registro = (new CreditosController)->datos_registro($agencia_banco);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_banco);

        $banco_id = $request->banco_id;
        $frmDatosMovimiento = json_decode($request->frmDatosMovimiento, true);

        $validador = Validator::make($frmDatosMovimiento ?? [], [
            'monto' => ['required', 'numeric', 'gt:0'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'modo' => ['required', 'string'],
        ]);

        if ($validador->fails()) {
            return response()->json(['message' => 'Datos inválidos', 'errors' => $validador->errors()], 422);
        }

        $descripcion = mb_strtoupper(trim((string) ($frmDatosMovimiento['descripcion'] ?? '')));
        $monto = (float) $frmDatosMovimiento['monto'];
        $modo = (string) $frmDatosMovimiento['modo'];

        $banco = Banco::find($banco_id);
        if ($banco === null) {
            return response()->json(['message' => 'Banco no encontrado'], 404);
        }

        $claveIdempotencia = (string) $request->header('Idempotency-Key', '');
        $requestId = (string) $request->header('X-Request-ID', (string) Str::uuid());

        return $this->coordinadorTransacciones->ejecutar([$conexion_operacion, $conexion_banco, 'master'], function () use ($modo, $request, $operacion, $banco_id, $agencia_operacion, $datos_registro, $fecha_larga, $conexion_banco, $conexion_operacion, $descripcion, $banco, $monto, $claveIdempotencia, $requestId) {
            if ($modo == 'A_CAJA') {
                $caja_operacion = $request->caja_operacion;

                BancoMovimiento::on($conexion_banco)
                    ->create([
                        'banco_id' => $banco_id,
                        'operacion' => $operacion,
                        'tipo' => 'E',
                        'monto' => $monto,
                        'modo' => $modo,
                        'fecha_movimiento' => $fecha_larga,
                        'agencia_operacion' => $agencia_operacion,
                        'caja_operacion' => $caja_operacion,
                        'descripcion' => $descripcion,
                        'datos_creacion' => $datos_registro
                    ]);

                CajaTransferencia::on($conexion_operacion)
                    ->create([
                        'tipo' => 'A_CAJA',
                        'remitente_id' => $caja_operacion,
                        'destinatario_id' => $caja_operacion,
                        'agencia_id' => $agencia_operacion,
                        'descripcion' => $descripcion,
                        'monto' => $monto,
                        'estado' => 'CONFIRMADO',
                        'datos_creacion' => $datos_registro,
                        'datos_actualizacion' => $datos_registro
                    ]);
            } else if ($modo == 'A_CUENTA') {
                $cuenta_operacion = $request->cuenta_operacion;

                BancoMovimiento::on($conexion_banco)
                    ->create([
                        'banco_id' => $banco_id,
                        'operacion' => $operacion,
                        'tipo' => 'E',
                        'monto' => $monto,
                        'modo' => $modo,
                        'fecha_movimiento' => $fecha_larga,
                        'agencia_operacion' => $agencia_operacion,
                        'cuenta_operacion' => $cuenta_operacion,
                        'descripcion' => $descripcion,
                        'datos_creacion' => $datos_registro
                    ]);

                CuentaTransferencia::on($conexion_operacion)
                    ->create([
                        'tipo' => 'A_CUENTA',
                        'remitente_id' => $cuenta_operacion,
                        'destinatario_id' => $cuenta_operacion,
                        'agencia_id' => $agencia_operacion,
                        'descripcion' => $descripcion,
                        'monto' => $monto,
                        'estado' => 'CONFIRMADO',
                        'datos_creacion' => $datos_registro,
                        'datos_actualizacion' => $datos_registro
                    ]);

                $tipo_movimiento = TipoMovimiento::on($conexion_operacion)
                    ->where('nombre', 'TRANSFERENCIA A CUENTA')->get()->last();

                CuentaMovimiento::on($conexion_operacion)
                    ->create([
                        'cuenta_id' => $cuenta_operacion,
                        'tipo' => 'I',
                        'monto' => $monto,
                        'descripcion' => 'TRANSFERENCIA DE CUENTA BANCARIA (' . $banco->banco . ')',
                        'tipo_movimiento_id' => $tipo_movimiento->id,
                        'fecha_movimiento' => $fecha_larga,
                        'datos_creacion' => $datos_registro,
                        'datos_actualizacion' => $datos_registro
                    ]);

                $cuenta = CuentaUsuario::on($conexion_operacion)->find($cuenta_operacion);
                $cuenta->monto += $monto;
                $cuenta->save();
            } else {
                return response()->json(['message' => 'Modo inválido'], 422);
            }

            $banco->acumulado -= $monto;
            $banco->datos_actualizacion = $datos_registro;
            $banco->save();

            $this->registradorAuditoria->registrar([
                'modulo' => 'CREDITOS',
                'codigo_operacion' => 'MOVIMIENTO_BANCARIO',
                'agencia_id' => $agencia_operacion,
                'usuario_id' => session('usuario_dni'),
                'idempotencia_clave' => $claveIdempotencia !== '' ? $claveIdempotencia : null,
                'request_id' => $requestId,
                'ip' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
                'entidad' => 'BANCO',
                'entidad_id' => (string) $banco_id,
                'datos_entrada' => ['tipo' => 'RETIRAR', 'detalle' => $frmDatosMovimiento],
                'datos_salida' => ['acumulado' => $banco->acumulado],
                'estado' => 'CONFIRMADO',
            ]);

            return response()->json([
                'message' => 'Movimiento registrado correctamente',
                'acumulado' => $banco->acumulado
            ]);
        });
    }

    public function abonar($request, $operacion)
    {

        $agencia_operacion = (int) $request->agencia_operacion;
        $agencia_banco = (int) $request->agencia_banco;

        $conexion_operacion = $this->resolvedorConexionAgencia->conexionMaster($agencia_operacion);
        $conexion_banco = $this->resolvedorConexionAgencia->conexionMaster($agencia_banco);

        $datos_registro = (new CreditosController)->datos_registro($agencia_banco);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_banco);

        $banco_id = $request->banco_id;
        $frmDatosMovimiento = json_decode($request->frmDatosMovimiento, true);

        $validador = Validator::make($frmDatosMovimiento ?? [], [
            'monto' => ['required', 'numeric', 'gt:0'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'modo' => ['required', 'string'],
        ]);

        if ($validador->fails()) {
            return response()->json(['message' => 'Datos inválidos', 'errors' => $validador->errors()], 422);
        }

        $descripcion = mb_strtoupper(trim((string) ($frmDatosMovimiento['descripcion'] ?? '')));
        $monto = (float) $frmDatosMovimiento['monto'];
        $modo = (string) $frmDatosMovimiento['modo'];

        $banco = Banco::find($banco_id);
        if ($banco === null) {
            return response()->json(['message' => 'Banco no encontrado'], 404);
        }

        $caja_operacion = $request->caja_operacion;
        $claveIdempotencia = (string) $request->header('Idempotency-Key', '');
        $requestId = (string) $request->header('X-Request-ID', (string) Str::uuid());

        return $this->coordinadorTransacciones->ejecutar([$conexion_operacion, $conexion_banco, 'master'], function () use ($request, $operacion, $banco_id, $agencia_operacion, $caja_operacion, $conexion_banco, $conexion_operacion, $datos_registro, $fecha_larga, $descripcion, $banco, $monto, $modo, $claveIdempotencia, $requestId, $frmDatosMovimiento) {
            BancoMovimiento::on($conexion_banco)
                ->create([
                    'banco_id' => $banco_id,
                    'operacion' => $operacion,
                    'tipo' => 'I',
                    'monto' => $monto,
                    'modo' => $modo,
                    'fecha_movimiento' => $fecha_larga,
                    'agencia_operacion' => $agencia_operacion,
                    'caja_operacion' => $caja_operacion,
                    'descripcion' => $descripcion,
                    'datos_creacion' => $datos_registro
                ]);

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

            if ($categoria === null || $subcategoria === null || $area_trabajo === null || $comprobante === null) {
                return response()->json(['message' => 'Catálogos incompletos para registrar la comisión'], 422);
            }

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
                    'monto' => $monto,
                    'concepto' => $descripcion,
                    'documento' => null,
                    'fecha_transaccion' => $fecha_larga,
                    'datos_creacion' => $datos_registro
                ]);

            $banco->acumulado += $monto;
            $banco->datos_actualizacion = $datos_registro;
            $banco->save();

            $this->registradorAuditoria->registrar([
                'modulo' => 'CREDITOS',
                'codigo_operacion' => 'MOVIMIENTO_BANCARIO',
                'agencia_id' => $agencia_operacion,
                'usuario_id' => session('usuario_dni'),
                'idempotencia_clave' => $claveIdempotencia !== '' ? $claveIdempotencia : null,
                'request_id' => $requestId,
                'ip' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
                'entidad' => 'BANCO',
                'entidad_id' => (string) $banco_id,
                'datos_entrada' => ['tipo' => 'ABONAR', 'detalle' => $frmDatosMovimiento],
                'datos_salida' => ['acumulado' => $banco->acumulado],
                'estado' => 'CONFIRMADO',
            ]);

            return response()->json([
                'message' => 'Movimiento registrado correctamente',
                'acumulado' => $banco->acumulado
            ]);
        });
    }
    public function transferir($request, $operacion)
    {
        $agencia_operacion = (int) $request->agencia_operacion;
        $agencia_banco = (int) $request->agencia_banco;

        $conexion_banco = $this->resolvedorConexionAgencia->conexionMaster($agencia_banco);

        $datos_registro = (new CreditosController)->datos_registro($agencia_banco);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_banco);

        $banco_id = $request->banco_id;
        $frmDatosMovimiento = json_decode($request->frmDatosMovimiento, true);

        $validador = Validator::make($frmDatosMovimiento ?? [], [
            'monto' => ['required', 'numeric', 'gt:0'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'banco_transferencia_id' => ['required', 'integer', 'min:1', 'different:banco_id'],
            'modo' => ['nullable', 'string'],
        ], [], [
            'banco_transferencia_id' => 'banco de destino',
        ]);

        if ($validador->fails()) {
            return response()->json(['message' => 'Datos inválidos', 'errors' => $validador->errors()], 422);
        }

        $banco_transferencia_id = (int) $frmDatosMovimiento['banco_transferencia_id'];
        $descripcion = mb_strtoupper(trim((string) ($frmDatosMovimiento['descripcion'] ?? '')));
        $monto = (float) $frmDatosMovimiento['monto'];
        $modo = (string) ($frmDatosMovimiento['modo'] ?? 'A_BANCO');

        $banco_envio = Banco::find($banco_id);
        $banco_recepcion = Banco::find($banco_transferencia_id);
        if ($banco_envio === null || $banco_recepcion === null) {
            return response()->json(['message' => 'Banco no encontrado'], 404);
        }

        $claveIdempotencia = (string) $request->header('Idempotency-Key', '');
        $requestId = (string) $request->header('X-Request-ID', (string) Str::uuid());

        return $this->coordinadorTransacciones->ejecutar([$conexion_banco, 'master'], function () use ($operacion, $banco_id, $banco_transferencia_id, $agencia_operacion, $conexion_banco, $descripcion, $datos_registro, $fecha_larga, $banco_envio, $banco_recepcion, $monto, $modo, $claveIdempotencia, $requestId, $frmDatosMovimiento) {
            BancoMovimiento::on($conexion_banco)
                ->create([
                    'banco_id' => $banco_id,
                    'operacion' => $operacion,
                    'tipo' => 'E',
                    'monto' => $monto,
                    'modo' => 'A_BANCO',
                    'fecha_movimiento' => $fecha_larga,
                    'agencia_operacion' => $agencia_operacion,
                    'banco_operacion' => $banco_transferencia_id,
                    'descripcion' => $descripcion,
                    'datos_creacion' => $datos_registro
                ]);

            BancoMovimiento::on($conexion_banco)
                ->create([
                    'banco_id' => $banco_transferencia_id,
                    'operacion' => $operacion,
                    'tipo' => 'I',
                    'monto' => $monto,
                    'modo' => $modo,
                    'fecha_movimiento' => $fecha_larga,
                    'agencia_operacion' => $agencia_operacion,
                    'banco_operacion' => $banco_id,
                    'descripcion' => $descripcion,
                    'datos_creacion' => $datos_registro
                ]);

            $banco_envio->acumulado -= $monto;
            $banco_envio->datos_actualizacion = $datos_registro;
            $banco_envio->save();

            $banco_recepcion->acumulado += $monto;
            $banco_recepcion->datos_actualizacion = $datos_registro;
            $banco_recepcion->save();

            $this->registradorAuditoria->registrar([
                'modulo' => 'CREDITOS',
                'codigo_operacion' => 'MOVIMIENTO_BANCARIO',
                'agencia_id' => $agencia_operacion,
                'usuario_id' => session('usuario_dni'),
                'idempotencia_clave' => $claveIdempotencia !== '' ? $claveIdempotencia : null,
                'request_id' => $requestId,
                'ip' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
                'entidad' => 'BANCO',
                'entidad_id' => (string) $banco_id,
                'datos_entrada' => ['tipo' => 'TRANSFERIR', 'detalle' => $frmDatosMovimiento],
                'datos_salida' => ['acumulado' => $banco_envio->acumulado],
                'estado' => 'CONFIRMADO',
            ]);

            return response()->json([
                'message' => 'Movimiento registrado correctamente',
                'acumulado' => $banco_envio->acumulado
            ]);
        });
    }
}
