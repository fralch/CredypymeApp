<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\General\Cargo;
use App\Models\Gth\Usuarios\Usuario;

use App\Models\General\Agencia;

use App\Models\Creditos\Clientes\Pariente;
use App\Models\Creditos\Clientes\Aval;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Caja\Transaccion;
use App\Models\Creditos\Mantenimiento\Transacciones\Categoria;
use App\Models\Creditos\Mantenimiento\Transacciones\Subcategoria;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Monolog\Handler\IFTTTHandler;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Support\Facades\Http;

define('API_EQU_URL',  getenv('VITE_S_API_EXTERNA'));

class ReporteEquifaxController extends Controller
{
    public function informe_equifax($modo)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_INFORME_EQUIFAX', 'CREDITOS_REPORTES');
            }
            if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MI_INFORME_EQUIFAX', 'CREDITOS_REPORTES');
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
                    )->whereIn('cargo_id', $cargos)
                        ->where('habilitado', 1)
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

                return Inertia::render('Creditos/Reportes/Creditos/informe_equifax', [
                    'modo' => $modo,
                    'usuarios' => $usuarios

                ]);
            } else {
                return redirect('/');
            }
        }
    }

    public function buscar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $modo = $request->modo;
        $vigentes = $request->vigentes;
        $cancelado_parcial = $request->cancelado_parcial;
        $cancelado_total = $request->cancelado_total;
        $por_asesor = $request->por_asesor;
        $por_dias_atraso = $request->por_dias_atraso;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));


        $estados = [];

        if ($vigentes == 'true') {
            $estados[] = 'DESEMBOLSADO';
        }
        if ($cancelado_parcial == 'true') {
            $estados[] = 'CANCELADO PARCIAL';
        }
        if ($cancelado_total == 'true') {
            $estados[] = 'CANCELADO TOTAL';
        }

        $estados_id = Estado::on($conexion)->select('id')->whereIn('estado', $estados)->get();

        $rango = Credito::on($conexion)
            ->select('id')
            ->whereIn('estado_id', $estados_id)
            ->whereBetween('fecha_desembolso', [$fecha_desde, $fecha_hasta]);

        if ($por_asesor == 'true') {
            $asesor_id = $request->asesor_seleccionado;

            $rango = $rango->where('asesor_id', $asesor_id);
        }

        if ($por_dias_atraso == 'true') {
            $desde_dias = $request->desde_dias;
            $hasta_dias = $request->hasta_dias;

            $rango = $rango->whereBetween('dias_atraso', [$desde_dias, $hasta_dias]);
        }

        $rango = $rango->get();

        $creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.cliente_id',
                'cre_reg.fecha_desembolso',
                'cre_reg.dias_atraso',
                'cre_reg.saldo_total',
                DB::raw('(SELECT cre_cen_rie.nombre_breve FROM credito_central_riesgo cre_cen_rie WHERE cre_reg.dias_atraso between cre_cen_rie.dias_desde AND cre_cen_rie.dias_hasta ) AS tipo_riesgo'),

                'cre_pro.agencia_pariente',
                'cre_pro.pariente_id',
                'cre_pro.agencia_aval',
                'cre_pro.aval_id',
                'cre_pro.agencia_pariente_aval',
                'cre_pro.pariente_aval_id',

                'cre_tip.tipo',
                'cre_est.estado',

                'usu_1.usuario as usuario_asesor'
            )

            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('credito_estados as cre_est', 'cre_reg.estado_id', 'cre_est.id')
            ->join('solucion_master.usuarios as usu_1', 'cre_reg.asesor_id', 'usu_1.dni')
            ->whereIn('cre_reg.id', $rango)
            ->orderBy('cre_reg.fecha_desembolso', 'asc')
            ->get();



        foreach ($creditos as $item) {


            $datos_cliente = $this->informacion_cliente($agencia_id, $item->cliente_id);

            $item->dni_titular = $datos_cliente->dni;
            $item->apellido_paterno_titular = $datos_cliente->apellido_paterno;
            $item->apellido_materno_titular = $datos_cliente->apellido_materno;
            $item->nombres_titular = $datos_cliente->nombres;
            $item->direccion_titular = $datos_cliente->direccion;
            $item->distrito_titular = $datos_cliente->distrito;
            $item->provincia_titular = $datos_cliente->provincia;
            $item->departamento_titular = $datos_cliente->departamento;

            if ($item->pariente_id != null &&  $item->agencia_pariente != null) {

                $cliente_id = $item->pariente_id;
                $agencia_pariente = $item->agencia_pariente;

                if (in_array($agencia_pariente, [2, 3, 5])) {
                    $datos_cliente = $this->informacion_cliente($agencia_pariente, $cliente_id);
                } else if (in_array($agencia_pariente, [1, 4, 6])) {
                    $datos_pariente =
                        [
                            'agencia_id' => $agencia_pariente,
                            'cliente_id' => $cliente_id
                        ];

                    $response = Http::get(API_EQU_URL . "/api/cli/listado_externa/datos_cliente", $datos_pariente);

                    if ($response->successful()) {

                        $response = $response->json();

                        if ($response['datos_cliente']) {
                            $datos_cliente = (object) $response['datos_cliente'];
                        } else {
                            $datos_cliente = null;
                        }
                    } else {
                        $datos_cliente = null;
                    }
                }

                if ($datos_cliente != null) {
                    $item->cliente_pariente_id = $cliente_id;
                    $item->dni_pariente = $datos_cliente->dni;
                    $item->apellido_paterno_pariente = $datos_cliente->apellido_paterno;
                    $item->apellido_materno_pariente = $datos_cliente->apellido_materno;
                    $item->nombres_pariente = $datos_cliente->nombres;
                    $item->direccion_pariente = $datos_cliente->direccion;
                    $item->distrito_pariente = $datos_cliente->distrito;
                    $item->provincia_pariente = $datos_cliente->provincia;
                    $item->departamento_pariente = $datos_cliente->departamento;
                } else {
                    $item->pariente_id = null;
                }
            } else {
                $item->pariente_id = null;
            }


            if ($item->aval_id != null  &&  $item->agencia_aval != null) {

                $cliente_id = $item->aval_id;
                $agencia_aval = $item->agencia_aval;

                if (in_array($agencia_aval, [2, 3, 5])) {
                    $datos_cliente = $this->informacion_cliente($agencia_aval, $cliente_id);
                } else if (in_array($agencia_aval, [1, 4, 6])) {
                    $datos_aval =
                        [
                            'agencia_id' => $agencia_aval,
                            'cliente_id' => $cliente_id
                        ];

                    $response = Http::get(API_EQU_URL . "/api/cli/listado_externa/datos_cliente", $datos_aval);



                    if ($response->successful()) {

                        $response = $response->json();

                        if ($response['datos_cliente']) {
                            $datos_cliente = (object) $response['datos_cliente'];
                        } else {
                            $datos_cliente = null;
                        }
                    } else {
                        $datos_cliente = null;
                    }
                }

                if ($datos_cliente != null) {

                    $item->cliente_aval_id = $cliente_id;
                    $item->dni_aval = $datos_cliente->dni;
                    $item->apellido_paterno_aval = $datos_cliente->apellido_paterno;
                    $item->apellido_materno_aval = $datos_cliente->apellido_materno;
                    $item->nombres_aval = $datos_cliente->nombres;
                    $item->direccion_aval = $datos_cliente->direccion;
                    $item->distrito_aval = $datos_cliente->distrito;
                    $item->provincia_aval = $datos_cliente->provincia;
                    $item->departamento_aval = $datos_cliente->departamento;
                } else {
                    $item->aval_id = null;
                }
            } else {
                $item->aval_id = null;
            }

            if ($item->pariente_aval_id != null &&  $item->agencia_pariente_aval != null) {

                $cliente_id = $item->pariente_aval_id;
                $agencia_pariente_aval = $item->agencia_pariente_aval;

                if (in_array($agencia_pariente_aval, [2, 3, 5])) {
                    $datos_cliente = $this->informacion_cliente($agencia_pariente_aval, $cliente_id);
                } else if (in_array($agencia_pariente_aval, [1, 4, 6])) {


                    $datos_pariente_aval =
                        [
                            'agencia_id' => $agencia_pariente_aval,
                            'cliente_id' => $cliente_id
                        ];

                    $response = Http::get(API_EQU_URL . "/api/cli/listado_externa/datos_cliente", $datos_pariente_aval);

                    if ($response->successful()) {

                        $response = $response->json();

                        if ($response['datos_cliente']) {
                            $datos_cliente = (object) $response['datos_cliente'];
                        } else {
                            $datos_cliente = null;
                        }
                    } else {
                        $datos_cliente = null;
                    }
                }

                if ($datos_cliente != null) {
                    $item->cliente_pariente_aval_id = $cliente_id;
                    $item->dni_pariente_aval = $datos_cliente->dni;
                    $item->apellido_paterno_pariente_aval = $datos_cliente->apellido_paterno;
                    $item->apellido_materno_pariente_aval = $datos_cliente->apellido_materno;
                    $item->nombres_pariente_aval = $datos_cliente->nombres;
                    $item->direccion_pariente_aval = $datos_cliente->direccion;
                    $item->distrito_pariente_aval = $datos_cliente->distrito;
                    $item->provincia_pariente_aval = $datos_cliente->provincia;
                    $item->departamento_pariente_aval = $datos_cliente->departamento;
                } else {
                    $item->pariente_aval_id = null;
                }
            } else {
                $item->pariente_aval_id = null;
            }
        }

        $lista_creditos = $creditos;

        return ['lista_creditos' => $lista_creditos];
    }

    public function informacion_cliente($agencia_id, $cliente_id)
    {
        $conexion = 'master_' .  $agencia_id;

        return Cliente::on($conexion)->from('cliente_registros as cli_reg')
            ->select(
                'cli_reg.id',
                'cli_reg.dni',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.direccion',

                'dep.departamento',
                'pro.provincia',
                'dis.distrito'
            )
            ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
            ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id', 'pro.id')
            ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
            ->where('cli_reg.id', $cliente_id)
            ->get()
            ->last();
    }

    public function exportar(Request $request)
    {
        $tipo = $request->tipo;

        if ($tipo  == 'EQUIFAX') {
            return $this->exportar_equifax($request);
        } else if ($tipo == 'SENTINEL') {
            return  $this->exportar_sentinel($request);
        }
    }

    public function exportar_equifax($request)
    {
        $agencia_id = $request->agencia_id;
        $fecha_corta_aplicacion = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        // Ordenando array de datos-------------------------------
        $lista_reporte = json_decode($request->lista_reporte);
        $data = [];

        $periodo = substr($fecha_corta_aplicacion, 5, 2) . '/' . substr($fecha_corta_aplicacion, 0, 4);

        foreach ($lista_reporte as $item) {

            $object = (object)[
                'eliminar' => null,
                'periodo' => $periodo,
                'codigo_entidad' => 121681,
                'codigo_tarjeta' =>  null,
                'codigo_prestamo' =>  null,
                'codigo_agencia' =>  null,
                'tipo_documento' =>  1,
                'dni' =>  $item->dni,
                'razon_social' =>  null,
                'apellido_paterno' =>  $item->apellido_paterno,
                'apellido_materno' =>  $item->apellido_materno,
                'nombres' =>  $item->nombres,
                'tipo_persona' =>  1,
                'modalidad' =>  7,
                'mn_deuda_vigente' =>  null,
                'mn_deuda_refinanciada' => null,
                'mn_deuda_vencida_menor_30' => null,
                'mn_deuda_vencida_mayor_30' => null,
                'mn_deuda_judicial' => null,
                'mn_deuda_indirecta' => null,
                'mn_deuda_avalada' => null,
                'mn_linea_credito' => null,
                'mn_credito_castigado' => null,

                'me_deuda_vigente' =>  null,
                'me_deuda_refinanciada' => null,
                'me_deuda_vencida_menor_30' => null,
                'me_deuda_vencida_mayor_30' => null,
                'me_deuda_judicial' => null,
                'me_deuda_indirecta' => null,
                'me_deuda_avalada' => null,
                'me_linea_credito' => null,
                'me_credito_castigado' => null,
                'calificacion' => null,
                'dias_vencidos' => $item->dias_atraso,
                'direccion' => $item->direccion,
                'distrito' => $item->distrito,
                'provincia' => $item->provincia,
                'departamento' => $item->departamento,
                'telefono' => null
            ];

            switch ($item->modo) {

                case 'titular':

                    if ($item->dias_atraso <= 7) {
                        if ($item->tipo == 'REFINANCIADO' || $item->tipo == 'REPROGRAMADO') {
                            $object->mn_deuda_refinanciada = $item->saldo_total;
                        } else {
                            $object->mn_deuda_vigente = $item->saldo_total;
                        }
                    } else if ($item->dias_atraso > 7) {
                        if ($item->dias_atraso <= 30) {
                            $object->mn_deuda_vencida_menor_30 = $item->saldo_total;
                        } else {
                            $object->mn_deuda_vencida_mayor_30 = $item->saldo_total;
                        }
                    }

                    break;
                case 'aval':
                    $object->mn_deuda_indirecta = $item->saldo_total;

                    break;
                case 'pariente':

                    $object->mn_deuda_indirecta = $item->saldo_total;
                    break;
                case 'pariente_aval':

                    $object->mn_deuda_indirecta = $item->saldo_total;
                    break;
            }

            switch ($item->calificacion) {
                case 'NORMAL':
                    $object->calificacion = 0;
                    break;
                case 'CPP':
                    $object->calificacion = 1;
                    break;
                case 'DEFICIENTE':
                    $object->calificacion = 2;
                    break;
                case 'DUDOSO':
                    $object->calificacion = 3;
                    break;
                case 'PÉRDIDA':
                    $object->calificacion = 4;
                    break;
                case 'TOTAL':
                    $object->calificacion = 4;
                    break;
            }

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptInformeEquifax.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();


        // Obteniendo formatos-----------------------------
        $celda = 1;
        while ($celda <= 39) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda = $sheet->getStyle($columna_1 . 10)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;

            $celda++;
        }

        // Insertando datos-----------------------------
        $indice = 10;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                $columna_2 += 1;
            }

            $indice += 1;
        }


        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptInformeEquifax', 5);

        // Exportar para descarga-------------------------

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function exportar_sentinel($request)
    {
        $agencia_id = $request->agencia_id;
        $fecha_corta_aplicacion = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        // Ordenando array de datos-------------------------------
        $lista_reporte = json_decode($request->lista_reporte);
        $data = [];

        $mes_reporte = substr($fecha_corta_aplicacion, 0, 4) . '/' . substr($fecha_corta_aplicacion, 5, 2);

        foreach ($lista_reporte as $item) {

            $object = (object)[
                'mes_reporte' => $mes_reporte,
                'codigo_entidad' => null,
                'numero_credito' =>  null,
                'tipo_documento' =>  1,
                'dni' =>  $item->dni,
                'razon_social' =>  null,
                'apellido_paterno' =>  $item->apellido_paterno,
                'apellido_materno' =>  $item->apellido_materno,
                'nombres' =>  $item->nombres,
                'tipo_persona' =>  1,
                'tipo_credito' =>  5,

                'mn_deuda_vigente' =>  null,
                'mn_deuda_refinanciada' => null,
                'mn_deuda_vencida_menor_30' => null,
                'mn_deuda_vencida_mayor_30' => null,
                'mn_deuda_judicial' => null,
                'mn_deuda_indirecta' => null,
                'mn_deuda_avalada' => null,
                'mn_linea_credito' => null,
                'mn_credito_castigado' => null,

                'me_deuda_vigente' =>  null,
                'me_deuda_refinanciada' => null,
                'me_deuda_vencida_menor_30' => null,
                'me_deuda_vencida_mayor_30' => null,
                'me_deuda_judicial' => null,
                'me_deuda_indirecta' => null,
                'me_deuda_avalada' => null,
                'me_linea_credito' => null,
                'me_credito_castigado' => null,
                'calificacion' => null,
                'dias_vencidos' => $item->dias_atraso,
                'direccion' => $item->direccion,
                'distrito' => $item->distrito,
                'provincia' => $item->provincia,
                'departamento' => $item->departamento,
                'telefono' => null,
                'estado' => null
            ];

            switch ($item->modo) {

                case 'titular':
                    if ($item->dias_atraso <= 8) {
                        if ($item->tipo == 'REFINANCIADO' || $item->tipo == 'REPROGRAMADO') {
                            $object->mn_deuda_refinanciada = $item->saldo_total;
                        } else {
                            $object->mn_deuda_vigente = $item->saldo_total;
                        }
                    } else if ($item->dias_atraso > 8) {
                        if ($item->dias_atraso <= 30) {
                            $object->mn_deuda_vencida_menor_30 = $item->saldo_total;
                        } else {
                            $object->mn_deuda_vencida_mayor_30 = $item->saldo_total;
                        }
                    }

                    break;
                case 'aval':
                    $object->mn_deuda_indirecta = $item->saldo_total;
                    break;
                case 'pariente':
                    $object->mn_deuda_indirecta = $item->saldo_total;
                    break;
                case 'pariente_aval':
                    $object->mn_deuda_indirecta = $item->saldo_total;
                    break;
            }

            $dias_atraso = $item->dias_atraso;

            if ($dias_atraso <= 8) {
                $object->calificacion = 0;
            } elseif ($dias_atraso > 8 && $dias_atraso <= 30) {
                $object->calificacion = 1;
            } elseif ($dias_atraso > 30 && $dias_atraso <= 60) {
                $object->calificacion = 2;
            } elseif ($dias_atraso > 60 && $dias_atraso <= 120) {
                $object->calificacion = 3;
            } elseif ($dias_atraso > 120) {
                $object->calificacion = 4;
            }

            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptInformeSentinel.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 0;
        while ($celda <= 37) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda = $sheet->getStyle($columna_1 . 2)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;

            $celda++;
        }

        // Insertando datos-----------------------------
        $indice = 2;
        foreach ($data as $item) {
            $columna_2 = 0;
            foreach ($item as $valor) {
                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2]);
                $columna_2 += 1;
            }

            $indice += 1;
        }

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptInformeSentinel', 5);

        // Exportar para descarga-------------------------

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
