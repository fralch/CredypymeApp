<?php

namespace Modules\Creditos\Presentation\Controllers\Inversion;

use App\Http\Controllers\Controller;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Presentation\Controllers\Usuarios\UsuarioController;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Banco;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMeta;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMetaMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Inversion\ProductosMeta;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\BancoMovimiento;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

use Illuminate\Support\Facades\Session;
use InversionMetaRegistros;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

use Illuminate\Support\Facades\Http;

define('API_INV_URL',  getenv('VITE_S_API_EXTERNA'));

class InversionMetaExternaController extends Controller
{

    public function inversion_meta(Request $request)
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'VER_INVERSION_META', 'CREDITOS_INVERSION');

            if ($band == 1) {

                $agencia_id = $request->agencia_id;
                $inversion_id = $request->inversion_id;

                return Inertia::render(
                    'Creditos/Inversion/inversion_meta_externa',
                    [
                        'agencia_id' => intval($agencia_id),
                        'inversion_id' => intval($inversion_id)
                    ]
                );
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }

    public function abonar(Request $request)
    {

        $agencia_origen = $request->agencia_origen;
        $agencia_id = $request->agencia_id;
        $caja_id =  $request->caja_id;
        $total_monto =  $request->monto;
        $cliente = $request->cliente;

        $datos_abono = array_merge(
            $request->all(),
            [
                'datos_sesion' => (new CreditosController)->datos_registro($agencia_origen),
            ]
        );

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post(API_INV_URL . "/api/inv/meta_externa/abonar", $datos_abono);

        if ($response->successful()) {

            $response = $response->json();

            // Registrar en INVERSION META la COBRANZA EXTERNA ----------------
            $movimiento_id = $this->registrar_inversion(
                'INVERSION META',
                $agencia_id,
                $caja_id,
                $cliente,
                $total_monto
            );

            return response()->json([
                'status' => $response['status'],
                'message'  => $response['message'],
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
