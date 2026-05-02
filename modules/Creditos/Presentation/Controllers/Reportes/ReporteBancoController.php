<?php

namespace Modules\Creditos\Presentation\Controllers\Reportes;

use App\Http\Controllers\Controller;

use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\BancoMovimiento;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Banco;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Records\BancoRecord;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Carbon\Carbon;

class ReporteBancoController extends Controller
{
    public function movimientos_bancarios()
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CAJA_MOVIMIENTOS_BANCARIOS', 'CREDITOS_REPORTES');

            if ($band == 1) {

                return Inertia('Creditos/Reportes/Caja/movimientos_bancarios');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar_recursos(Request $request)
    {

        $agencias = Agencia::all();

        $usuarios_caja = [];
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

        $bancos = Banco::select('id', 'banco', 'agencia_id')
            ->where('habilitado', '1')
            ->orderBy('banco', 'asc')
            ->get();

        return response()->json([
            'usuarios' => $usuarios_caja,
            'bancos' => $bancos
        ]);
    }

    public function buscar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = date("Y-m-d", strtotime($request->input('fecha_hasta') . "+ 1 days"));

        // $filtro_caja = filter_var($request->input('filtro_caja'), FILTER_VALIDATE_BOOLEAN);

        // if ($filtro_caja) {
        //     $usuarios_cajas = json_decode($request->usuarios_cajas);
        // } else {
        //     $usuarios_cajas = [];
        // }

        $conexion = 'master_' . $agencia_id;

        $lista_movimientos = BancoMovimiento::on($conexion)->from('banco_movimientos as ban_mov')
            ->select(
                'ban_mov.id',
                'ban_mov.banco_id',
                'ban_mov.fecha_movimiento',
                'ban_mov.tipo',
                'ban_mov.operacion',
                'ban_mov.modo',
                'ban_mov.monto',
                'ban_mov.banco_operacion as banco_transferencia_id',

                'ban_mov.descripcion',

                'ban_mov.caja_operacion',
                'ban_mov.cuenta_operacion',

                'ban_1.banco',
                'ban_2.banco as banco_transferencia',

                'usu.dni as usuario_id',
                'usu.usuario as usuario_operacion',
            )
            ->join('solucion_master.bancos as ban_1', 'ban_mov.banco_id', 'ban_1.id')
            ->leftjoin('solucion_master.bancos as ban_2', 'ban_mov.banco_operacion', 'ban_2.id')
            ->join(
                'solucion_master.usuarios as usu',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(ban_mov.datos_creacion, '$.usuario'))"),
                'usu.dni'
            )
            ->whereBetween('ban_mov.fecha_movimiento', [$fecha_desde, $fecha_hasta])
            // ->when($filtro_caja, function ($query) use ($usuarios_cajas) {
            //     return $query->whereIn('usu.dni', $usuarios_cajas);
            // })
            ->orderBy('ban_mov.id', 'desc')
            ->get();

        foreach ($lista_movimientos as $key => $value) {
            $value['index'] = $key;
        }

        return response()->json([
            'lista_movimientos' => $lista_movimientos
        ]);
    }
    public function buscar_general(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = date("Y-m-d", strtotime($request->input('fecha_hasta') . "+ 1 days"));

        $conexion_records = 'records_' . $agencia_id;
        $lista_movimientos = BancoRecord::on($conexion_records)
            ->from('banco_records as ban_rec')
            ->select(
                'ban_rec.id',
                'ban_rec.banco_id',
                'ban_rec.fecha',
                'ban_rec.monto_inicial',
                'ban_rec.monto_final',

                'ban.banco',
                'ban.agencia_id',
                DB::raw('ban_rec.monto_final - ban_rec.monto_inicial as diferencia')

            )
            ->join('solucion_master.bancos as ban', 'ban_rec.banco_id', 'ban.id')
            ->where('cierre_id', '<>', null)
            ->whereBetween('fecha', [$fecha_desde, $fecha_hasta])
            ->orderBy('fecha', 'desc')
            ->orderBy('banco', 'asc')
            ->get();

        return response()->json([
            'lista_movimientos' => $lista_movimientos
        ]);
    }

    public function exportar(Request $request)
    {
        $modo = $request->modo;

        if ($modo == 'general') {
            return  $this->exportar_general($request);
        } elseif ($modo == 'detallado') {
            return  $this->exportar_detallado($request);
        }
    }
    public function exportar_general($request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $lista_movimientos = json_decode($request->lista_movimientos);

        $orden = 1;
        foreach ($lista_movimientos as $item) {

            $object = (object)[
                'numero' => $orden,
                'fecha' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha)),
                'banco' =>  $item->banco,
                'monto_inicial' =>  $item->monto_inicial,
                'monto_final' =>  $item->monto_final,
                'diferencia' =>  $item->diferencia
            ];

            $data[] = $object;

            $orden++;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptMBGeneral');
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptMovimientosBancarios.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Rellenando TÍTULO y ENCABEZADOS ---------------
        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('G2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

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
        $nombre_archivo = $controller->concatenar_aleatorio('rptMBGeneral', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
    public function exportar_detallado($request)
    {
        // Ordenando array de datos-------------------------------
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $lista_movimientos = json_decode($request->lista_movimientos);

        foreach ($lista_movimientos as $item) {

            $object = (object)[
                'numero' => $item->index + 1,
                'fecha' =>  Date::dateTimeToExcel(Carbon::parse($item->fecha_movimiento)),
                'tipo' => $item->tipo,
                'banco' =>  $item->banco,
                'operacion' =>  $item->operacion,
                'modo' =>  $item->modo,
                'descripcion' =>  $item->descripcion,
                'monto' =>  $item->monto,
                'usuario_operacion' =>  $item->usuario_operacion,
                'banco_transferencia' =>  $item->banco_transferencia == null ? '-' : $item->banco_transferencia,
            ];

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly('rptMBDetallado');
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptMovimientosBancarios.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Rellenando TÍTULO y ENCABEZADOS ---------------
        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('K2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

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
        $nombre_archivo = $controller->concatenar_aleatorio('rptMBDetallado', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
