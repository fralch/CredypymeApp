<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Caja\Caja;
use App\Models\Creditos\Caja\PagoCuota;
use App\Models\Creditos\Caja\PagoMora;
use App\Models\Creditos\Caja\PagoNotificacion;
use App\Models\Creditos\Caja\PagoVoucher;

use App\Models\Creditos\Clientes\Pariente;
use App\Models\Creditos\Clientes\Aval;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Prenda;
use App\Models\Creditos\Credito\Carrito;
use App\Models\Creditos\Credito\CarritoDetalle;
use App\Models\General\Feriado;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Credito\Notificacion;
use App\Models\Creditos\Credito\Propuesta;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Mantenimiento\Credito\Producto;
use App\Models\General\Banco;
use App\Models\Creditos\Cuenta\BancoMovimiento;
use App\Models\Creditos\Inversion\InversionMeta;
use App\Models\Creditos\Inversion\InversionMetaMovimiento;
use App\Models\Creditos\Mantenimiento\Inversion\ProductosMeta;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\General\Agencia;
use App\Models\General\Cargo;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

define('API_COB_URL',  getenv('VITE_S_API_EXTERNA'));

class CajaCobranzaExternaController extends Controller
{
    public function cobranza($credito_id, $agencia_id)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'COBRANZA', 'CREDITOS_CAJA');
            if ($band == 1) {

                return Inertia::render('Creditos/Caja/cobranza_externa', [
                    'agencia_id' => intval($agencia_id),
                    'credito_id' => intval($credito_id),
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function pagar(Request $request)
    {

        $agencia_origen = $request->agencia_origen;

        $agencia_id = $request->agencia_id;
        $caja_id = $request->caja_id;
        $total_cobro = json_decode($request->datos_cobranza)->total_cobro;
        $cliente = $request->cliente;

        $datos_pago = array_merge(
            $request->all(),
            [
                'datos_sesion' => (new CreditosController)->datos_registro($agencia_origen),
            ]
        );

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post(API_COB_URL . "/api/caj/cobranza_externa/pagar", $datos_pago);

        if ($response->successful()) {

            $response = $response->json();


            // Registrar en INVERSION META la COBRANZA EXTERNA ----------------
            $movimiento_id = $this->registrar_inversion(
                'COBRANZA',
                $agencia_id,
                $caja_id,
                $cliente,
                $total_cobro
            );

            return response()->json([
                'status' => $response['status'],
                'message'  => $response['message'],
                'cancelado'  => $response['cancelado'],
                'datos_voucher'  => $response['datos_voucher'],
                'movimiento_id' => $movimiento_id
            ]);
        } else {
            $response = $response->json();
            return response()->json([
                'status' => $response['status'],
                'error'  => $response['error']
            ]);
        }
    }

    public function cancelar(Request $request)
    {
        $agencia_origen = $request->agencia_origen;

        $agencia_id = $request->agencia_id;
        $caja_id = $request->caja_id;
        $total_cobro = json_decode($request->datos_cancelacion)->total_cancelar;
        $cliente = $request->cliente;

        $datos_pago = array_merge(
            $request->all(),
            [
                'datos_sesion' => (new CreditosController)->datos_registro($agencia_origen),
            ]
        );

        $http = Http::asMultipart();

        foreach ($datos_pago as $key => $value) {

            // Si es un archivo -------------------------------------------------
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $http = $http->attach(
                    $key,
                    file_get_contents($value->getRealPath()),
                    $value->getClientOriginalName()
                );
            }
            // Si es array u objeto lo convertimos a JSON ------------------------
            elseif (is_array($value) || is_object($value)) {
                $http = $http->attach($key, json_encode($value));
            }
            // Si es string/numérico → lo enviamos directo -----------------------
            else {
                if ($key == 'prendario') {
                    $http->attach($key, (string) $value ? 'true' : 'false');
                } else {
                    $http->attach($key, (string) $value);
                }
            }
        }

        $response = $http->post(API_COB_URL . "/api/caj/cobranza_externa/cancelar");


        if ($response->successful()) {

            $response = $response->json();

            // Registrar en INVERSION META la CANCELACION EXTERNA ----------------
            $movimiento_id = $this->registrar_inversion(
                'CANCELACION',
                $agencia_id,
                $caja_id,
                $cliente,
                $total_cobro
            );

            return response()->json([
                'status' => $response['status'],
                'message'  => $response['message'],
                'cancelado'  => $response['cancelado'],
                'datos_voucher'  => $response['datos_voucher'],
                'movimiento_id' => $movimiento_id
            ]);
        } else {
            $response = $response->json();
            return $response;
        }
    }


    public function registrar_inversion($modo, $agencia_id, $caja_id, $cliente, $monto)
    {
        // Registrar en INVERSION META la COBRANZA EXTERNA ----------------

        $conexion_local = 'master_5';

        $agencia_caja = session('id_agencia');
        $caja_id = $caja_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_caja);

        $producto = null;

        if ($agencia_id == 1) {
            $producto = 'COBRANZAS_TAMBO';
        } else if ($agencia_id == 4) {
            $producto = 'COBRANZAS_HVCA';
        } else if ($agencia_id == 6) {
            $producto = 'COBRANZAS_CHILCA';
        }

        $producto_id = ProductosMeta::on($conexion_local)->where('producto', $producto)->value('id');

        $inversion_id = InversionMeta::on($conexion_local)->where('producto_meta_id', $producto_id)->value('id');

        $movimiento = InversionMetaMovimiento::on($conexion_local)->create(
            [
                'inversion_id' =>   $inversion_id,
                'tipo' =>   'I',
                'monto' =>   $monto,
                'comentario' => '(' . $modo . ') - ' . $cliente,
                'agencia_caja' => $agencia_caja,
                'caja_id' =>   $caja_id,
                'banco_id' =>  null,
                'datos_creacion' =>  $datos_registro
            ]
        );

        $fecha_movimiento = (new CreditosController)->fecha_larga_aplicacion($agencia_caja);
        $datos_registro = (new CreditosController)->datos_registro($agencia_caja);

        $inversion =  InversionMeta::on($conexion_local)->find($inversion_id);

        $inversion->fecha_movimiento =  $fecha_movimiento;
        $inversion->acumulado +=  floatval($monto);
        $inversion->datos_actualizacion =  $datos_registro;
        $inversion->save();

        return $movimiento->id;
    }
}
