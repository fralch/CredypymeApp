<?php

namespace App\Http\Controllers\Creditos\Inversion;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\Usuarios\UsuarioController;
use App\Models\General\Agencia;
use App\Models\General\Banco;
use App\Models\Creditos\Caja\Caja;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Inversion\InversionMeta;
use App\Models\Creditos\Inversion\InversionMetaMovimiento;
use App\Models\Creditos\Mantenimiento\Inversion\ProductosMeta;
use App\Models\Creditos\Cuenta\BancoMovimiento;
use App\Models\Gth\Usuarios\Usuario;
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

class InversionMetaController extends Controller
{

    public function crear($cliente_id, $agencia_id)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREAR_INVERSION_META', 'CREDITOS_INVERSION');

            if ($band == 1) {


                $datos_voucher = null;
                if (Session::has('datos_voucher')) {
                    $datos_voucher = Session::get('datos_voucher');
                    Session::forget('datos_voucher');
                }


                $conexion = 'master_' .  $agencia_id;

                $datos_titular = Cliente::on($conexion)
                    ->select(
                        'id',
                        'dni',
                        'apellido_paterno',
                        'apellido_materno',
                        'nombres',
                        'datos_creacion',
                    )
                    ->where('id', $cliente_id)
                    ->get()->last();

                $productos_meta = ProductosMeta::on($conexion)
                    ->select(
                        'id',
                        'producto',
                        'valor_meta',
                    )
                    ->where('habilitado', 1)
                    ->get();

                return Inertia::render(
                    'Creditos/Inversion/crear_inversion_meta',
                    [
                        'datos_titular' => $datos_titular,
                        'productos_meta' => $productos_meta,
                        'agencia_id' => intVal($agencia_id),
                        'datos_voucher' => $datos_voucher,
                    ]
                );
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }
    public function registrar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $frmDatosInversion = json_decode($request->frmDatosInversion);
        $cliente_id = $frmDatosInversion->cliente_id;
        $producto_meta_id = $frmDatosInversion->producto_meta_id;
        $valor_meta = $frmDatosInversion->valor_meta;

        $comentario = (new CreditosController)->verificar_nulo($frmDatosInversion->comentario);

        if ($comentario != null) {
            $comentario = mb_strtoupper($comentario);
        }

        $caja = json_decode($request->caja);
        $caja_apertura = $caja->id;
        $agencia_caja = $caja->agencia_id;

        InversionMeta::on($conexion)->create(
            [
                'producto_meta_id' => $producto_meta_id,
                'cliente_id' => $cliente_id,
                'comentario' => $comentario,
                'valor_meta' => $valor_meta,
                'fecha_apertura' => $fecha,
                'agencia_caja_apertura' => $agencia_caja,
                'caja_apertura' => $caja_apertura,
                'datos_creacion' => $datos_registro
            ]
        );

        $datos_voucher = [
            'fecha_apertura' => $fecha
        ];

        Session::put('datos_voucher', $datos_voucher);

        return redirect()->route('inv.meta.crear', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }


    public function inversion_meta($agencia_id, $inversion_id)
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'VER_INVERSION_META', 'CREDITOS_INVERSION');

            if ($band == 1) {

                return Inertia::render(
                    'Creditos/Inversion/inversion_meta',
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

    public function listar_recursos(Request $request)
    {

        $agencia_id = $request->input('agencia_id');
        $inversion_id = $request->input('inversion_id');
        $conexion = 'master_' .  $agencia_id;

        $datos_inversion = InversionMeta::on($conexion)
            ->from('inversion_meta_registros as inv_met_reg')
            ->select(
                'cli_reg.id as cliente_id',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',

                'inv_met_reg.id as inversion_id',
                'inv_met_reg.valor_meta',
                'inv_met_reg.acumulado',
                'inv_met_reg.datos_creacion',
                'inv_met_reg.fecha_movimiento',
                'inv_met_reg.fecha_cierre',

                'inv_pro_met.producto',

                'usu.usuario as usuario_registro'
            )
            ->join('cliente_registros as cli_reg', 'cli_reg.id', 'inv_met_reg.cliente_id')
            ->join('inversion_productos_meta as inv_pro_met', 'inv_pro_met.id', 'inv_met_reg.producto_meta_id')
            ->join('solucion_master.usuarios as usu', DB::raw('SUBSTR(inv_met_reg.datos_creacion,42,8)'), 'usu.dni')
            ->where('inv_met_reg.id', $inversion_id)
            ->get()->last();


        $cerrado = 0;

        if ($datos_inversion->fecha_cierre != null) {
            $cerrado = 1;
        }

        $lista_movimientos = InversionMetaMovimiento::on($conexion)
            ->from('inversion_meta_movimientos as inv_met_mov')
            ->select(
                'inv_met_mov.id',
                'inv_met_mov.tipo',
                'inv_met_mov.monto',
                'inv_met_mov.comentario',
                'inv_met_mov.agencia_caja',
                'inv_met_mov.caja_id',
                DB::raw("SUBSTRING(inv_met_mov.datos_creacion,11,19) as fecha_registro"),
            )
            ->where('inv_met_mov.inversion_id', $inversion_id)
            ->get();


        $lista_movimientos = $lista_movimientos->map(function ($row) {


            $conexion_caja = 'master_' . $row['agencia_caja'];
            $caja_id = $row['caja_id'];

            $datos_caja = Caja::on($conexion_caja)->find($caja_id);
            $usuario_caja = Usuario::find($datos_caja->dni);

            $row['agencia_caja'] = (new CreditosController)->agencia_abreviacion($row['agencia_caja']);
            $row['usuario_caja'] = $usuario_caja->usuario;

            return $row;
        });

        $bancos = Banco::where([
            ['agencia_id', $agencia_id],
            ['habilitado', 1]
        ])
            ->orderBy('banco', 'asc')
            ->get();

        return response()->json(
            [
                'success' => 200,
                'datos_inversion' => $datos_inversion,
                'agencia_id' => intVal($agencia_id),
                'lista_movimientos' => $lista_movimientos,
                'bancos' => $bancos,
                'cerrado' => $cerrado
            ]
        );
    }

    public function abonar_retirar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_movimiento = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $por_banco = filter_var($request->por_banco, FILTER_VALIDATE_BOOLEAN);
        $inversion_id = $request->inversion_id;

        $frmDatosInversion = json_decode($request->frmDatosInversion);
        $tipo = $frmDatosInversion->tipo;
        $monto = $frmDatosInversion->monto;
        $acumulado = $frmDatosInversion->acumulado;
        $comentario =  (new CreditosController)->verificar_nulo($frmDatosInversion->comentario);

        if ($comentario !== null) {
            $comentario = mb_strtoupper($comentario);
        }

        if ($por_banco) {
            $banco = Banco::find($frmDatosInversion->banco_id);
            $comentario = '(' . $banco->banco . ') ' . $comentario;
        }

        $agencia_caja = $frmDatosInversion->agencia_caja;
        $caja_id = $frmDatosInversion->caja_id;
        $banco_id = $frmDatosInversion->banco_id;

        InversionMetaMovimiento::on($conexion)->create(
            [
                'inversion_id' =>   $inversion_id,
                'tipo' =>   $tipo,
                'monto' =>   $monto,
                'comentario' => $comentario,
                'agencia_caja' =>   $agencia_caja,
                'caja_id' =>   $caja_id,
                'banco_id' =>   $por_banco ? $banco_id : null,
                'datos_creacion' =>   $datos_registro
            ]
        );

        if ($tipo == 'I') {
            $acumulado = $acumulado + $monto;
        } else {
            $acumulado = $acumulado - $monto;
        }

        InversionMeta::on($conexion)
            ->where('id', $inversion_id)
            ->update([
                'fecha_movimiento' => $fecha_movimiento,
                'acumulado' => $acumulado,
                'datos_actualizacion' => $datos_registro
            ]);

        $datos_voucher = [
            'fecha_registro' => $fecha_movimiento,
            'comentario' => $comentario,
        ];

        // Agregar como MOVIMIENTO en BANCO ------------

        if ($por_banco) {
            // Registrar el movimiento del BANCO seleccionado
            $inversion = InversionMeta::on($conexion)->find($inversion_id);
            $producto = ProductosMeta::on($conexion)->find($inversion->producto_meta_id);

            $cliente = Cliente::on($conexion)->find($inversion->cliente_id);
            $cliente = $cliente->apellido_paterno . ' '
                . $cliente->apellido_materno . ' '
                . $cliente->nombres;

            $datos_movimiento = [
                'banco_id' => $banco_id,
                'tipo'  => 'I',
                'agencia_inversion'  => $agencia_id,
                'inversion_id'  => $inversion_id,
                'operacion' => 'ABONO',
                'modo' => 'DE_CAJA',
                'monto' => $monto,
                'fecha_movimiento' => $fecha_movimiento,
                'agencia_operacion' => session('id_agencia'),
                'caja_operacion' => $caja_id,
                'descripcion' => 'ABONO INVERSIÓN ' . '(' . $producto->producto . '): ' . $cliente,
                'datos_creacion' => $datos_registro
            ];

            BancoMovimiento::on($conexion)->create($datos_movimiento);

            // Actualizar el ACUMULADO de la cuenta en BANCO

            $banco = Banco::find($banco_id);
            $banco->acumulado += floatval($monto);
            $banco->datos_actualizacion = $datos_registro;
            $banco->save();
        }

        // -----------------------------------

        return response()->json([
            'status' => 200,
            'message' => 'Operación REGISTRADA',
            'agencia_id' => $agencia_id,
            'inversion_id' => $inversion_id,
            'datos_voucher' => $datos_voucher
        ]);
    }

    public function cerrar(Request $request)
    {
        $agencia_id = $request->agencia_id;

        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_movimiento = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $inversion_id = $request->inversion_id;
        $frmDatosCierre = json_decode($request->frmDatosCierre);
        $acumulado = $frmDatosCierre->acumulado;
        $comentario =  (new CreditosController)->verificar_nulo($frmDatosCierre->comentario);

        if ($comentario != null) {
            $comentario = mb_strtoupper($comentario);
        }
        $agencia_caja = $frmDatosCierre->agencia_caja;
        $caja_id = $frmDatosCierre->caja_id;

        InversionMetaMovimiento::on($conexion)
            ->create(
                [
                    'inversion_id' => $inversion_id,
                    'tipo' => 'E',
                    'monto' => $acumulado,
                    'comentario' => $comentario,
                    'agencia_caja' => $agencia_caja,
                    'caja_id' => $caja_id,
                    'datos_creacion' => $datos_registro,
                ]
            );

        InversionMeta::on($conexion)
            ->where('id', $inversion_id)
            ->update(
                [
                    'fecha_movimiento' => $fecha_movimiento,
                    'fecha_cierre' => $fecha_movimiento,
                    'acumulado' => 0,
                    'agencia_caja_cierre' => $agencia_caja,
                    'caja_cierre' => $caja_id,
                    'comentario_cierre' => $comentario,
                    'datos_actualizacion' => $datos_registro,
                ]
            );

        $datos_voucher = [
            'fecha_registro' => $fecha_movimiento,
            'comentario' => $comentario,
        ];

        return response()->json([
            'status' => 200,
            'message' => 'Inversión CERRADA',
            'agencia_id' => $agencia_id,
            'inversion_id' => $inversion_id,
            'datos_voucher' => $datos_voucher
        ]);
    }
    public function voucher(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = session('id_agencia');
        $celular_agencia = Agencia::select('celular')
            ->where('id_agencia', $agencia_id)
            ->get()->last();
        $fecha_aplicacion = (new CreditosController)->fecha_larga_aplicacion($agencia_id);


        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("./report_templates/caja/reportes/vchMovimientoMeta.xlsx");
        $spreadsheet = $reader->load("./report_templates/caja/reportes/vchMovimientoMeta.xlsx");

        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos-----------------------------

        $frmDatosVoucher = json_decode($request->frmDatosVoucher);

        $cliente = $frmDatosVoucher->cliente;
        $producto = $frmDatosVoucher->producto;
        $monto = $frmDatosVoucher->monto;
        $concepto = $request->concepto;
        $comentario = $frmDatosVoucher->comentario;

        $sheet->setCellValue("A2", $request->titulo);
        $sheet->setCellValue("A4", $producto);
        $sheet->setCellValue("A6", $cliente);
        $sheet->setCellValue("A8", $concepto);
        $sheet->setCellValue("A12", $comentario);
        $sheet->setCellValue("C8", $monto);
        $sheet->setCellValue("C10", $monto);

        $texto = null;

        if ($celular_agencia->celular != null) {
            $texto = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $texto->createText('¿Alguna duda?, llámanos o escríbenos al Whatsapp ');
            $numero = $texto->createTextRun($celular_agencia->celular);
            $numero->getFont()->setBold(true);
            $numero->getFont()->setSize(7);


            $sheet->setCellValue('A15', $texto);
            $sheet->setCellValue('A16', 'AGENCIA ' . $request->agencia);
            $sheet->setCellValue('A17', $fecha_aplicacion);
            $sheet->setCellValue('A18', $request->usuario . ' - ' . $request->dispositivo);
        } else {
            $sheet->setCellValue('A15', 'AGENCIA ' . $request->agencia);
            $sheet->setCellValue('A16', $fecha_aplicacion);
            $sheet->setCellValue('A17', $request->usuario . ' - ' . $request->dispositivo);
            $sheet->setCellValue('A18', null);
        }

        // Exportar para descarga-------------------------

        // Convertir PDF-------------------------

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('vchMovimientoMeta', 5);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }
}
