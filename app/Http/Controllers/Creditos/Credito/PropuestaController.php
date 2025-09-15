<?php

namespace App\Http\Controllers\Creditos\Credito;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\Usuarios\UsuarioController;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\Creditos\Credito\AprobacionController;

use App\Models\General\Agencia;
use App\Models\General\Feriado;

use App\Models\Creditos\Credito\Evaluacion_financiera;
use App\Models\Creditos\Credito\Propuesta;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Pariente;
use App\Models\Creditos\Clientes\Aval;
use App\Models\Creditos\Clientes\Negocio;
use App\Models\Creditos\Clientes\Prenda;
use App\Models\Creditos\Mantenimiento\Credito\Sector;
use App\Models\Creditos\Mantenimiento\Credito\Producto;
use App\Models\Creditos\Mantenimiento\Credito\Subproducto;
use App\Models\Creditos\Mantenimiento\Credito\Tipo;
use App\Models\Creditos\Mantenimiento\Credito\Garantia;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

use App\Models\Gth\Usuarios\Usuario;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Monolog\Handler\IFTTTHandler;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Illuminate\Support\Facades\Http;

define('API_PRO_URL',  getenv('VITE_S_API_EXTERNA'));

class PropuestaController extends Controller
{
    public function propuesta($cliente_id, $propuesta_id = 0, $agencia_id)
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PROPUESTA', 'CREDITOS_CREDITO');
            if ($band == 1) {

                $conexion = 'master_' .  $agencia_id;

                $verificar_evaluacion = Evaluacion_financiera::on($conexion)->where('cliente_id', $cliente_id)->get()->last();

                if ($verificar_evaluacion == null) {
                    $evaluacion_id = 0;
                } else {
                    $evaluacion_id = $verificar_evaluacion->id;
                }

                $datos_negocio = Negocio::on($conexion)->from('cliente_negocios as cli_neg')
                    ->select(
                        'cli_neg.id',
                        'cli_neg.nombre',
                        'cli_neg.actividad',
                        'cli_neg.direccion',
                        'cli_neg.referencia_direccion',
                        'cli_neg.telefonos',

                        'ciiu.codigo as ciiu_codigo',
                        'ciiu.nombre as ciiu_nombre',

                        'dep.departamento',
                        'pro.provincia',
                        'dis.distrito'
                    )
                    ->join('solucion_master.ciiu', 'cli_neg.ciiu_id', 'ciiu.id')
                    ->join('solucion_master.departamentos as dep', 'cli_neg.departamento_id',  'dep.id')
                    ->join('solucion_master.provincias as pro', 'cli_neg.provincia_id',  'pro.id')
                    ->join('solucion_master.distritos as dis', 'cli_neg.distrito_id',  'dis.id')
                    ->where([['cliente_id', $cliente_id], ['vinculado', 1]])
                    ->get()->last();

                if ($propuesta_id != 0) {
                    $datos_propuesta = Propuesta::on($conexion)->from('credito_propuestas as cre_pro')
                        ->select(
                            'cre_pro.id',
                            'cre_pro.promotor_id',
                            'cre_pro.pariente_id',
                            'cre_pro.agencia_pariente',
                            'cre_pro.aval_id',
                            'cre_pro.agencia_aval',
                            'cre_pro.pariente_aval_id',
                            'cre_pro.agencia_pariente_aval',
                            'cre_pro.monto',
                            'cre_pro.tasa_interes',
                            'cre_pro.plazo',
                            'cre_pro.periodo_pago',
                            'cre_pro.es_especial',
                            'cre_pro.mora_adicional',
                            'cre_pro.con_dias_gracia',
                            'cre_pro.dias_gracia',
                            'cre_pro.cuota',
                            'cre_pro.sector_id',
                            'cre_pro.producto_id',
                            'cre_pro.subproducto_id',
                            'cre_pro.tipo_id',
                            'cre_pro.pago_oficina',
                            'cre_pro.garantia_id',
                            'cre_pro.valor_garantia',
                            'cre_pro.comentario_garantia',
                            'cre_pro.comentario_propuesta',
                            'cre_pro.fecha_propuesta',
                            'cre_pro.datos_creacion',
                            'cre_pro.prendario',
                            'cre_pro.prendas',

                            'cre_sec.sector',
                            'cre_prod.producto',
                            'cre_sub.subproducto',
                            'cre_tip.tipo',
                            'cre_gar.garantia',

                            'us_1.usuario as usuario_promotor',
                            'us_2.usuario as usuario_registro'
                        )
                        ->join('credito_sectores as cre_sec', 'cre_pro.sector_id', 'cre_sec.id')
                        ->join('credito_productos as cre_prod', 'cre_pro.producto_id', 'cre_prod.id')
                        ->leftjoin('credito_subproductos as cre_sub', 'cre_pro.subproducto_id', 'cre_sub.id')
                        ->join('credito_tipos as cre_tip', 'cre_pro.tipo_id', 'cre_tip.id')
                        ->join('credito_garantias as cre_gar', 'cre_pro.garantia_id', 'cre_gar.id')
                        ->join('solucion_master.usuarios as us_1', 'cre_pro.promotor_id',  'us_1.dni')
                        ->join(
                            'solucion_master.usuarios as us_2',
                            DB::raw("SUBSTRING(cre_pro.datos_creacion,42,8)"),
                            'us_2.dni'
                        )

                        ->where('cre_pro.id', $propuesta_id)
                        ->get()->last();

                    $pariente_id = $datos_propuesta->pariente_id;
                    $datos_pariente = null;

                    if ($pariente_id != null) {

                        $datos_pariente = (object)[
                            'agencia_pariente' => $datos_propuesta->agencia_pariente,
                            'pariente_id' => $pariente_id
                        ];

                        if (in_array($datos_propuesta->agencia_pariente, [2, 3, 5])) {

                            $agencia_pariente = $datos_propuesta->agencia_pariente;
                            $conexion_pariente_1 = 'master_' . $agencia_pariente;

                            $datos = Cliente::on($conexion_pariente_1)
                                ->select(
                                    'dni',
                                    'apellido_paterno',
                                    'apellido_materno',
                                    'nombres',
                                    'central_riesgo',

                                )
                                ->where('id', $pariente_id)
                                ->get()->last()->toArray();
                        } else if (in_array($datos_propuesta->agencia_pariente, [1, 4, 6])) {
                            $params =
                                [
                                    'agencia_id' => $datos_propuesta->agencia_pariente,
                                    'cliente_id' => $pariente_id
                                ];

                            $response = Http::get(API_PRO_URL . "/api/cli/listado_externa/datos_cliente", $params);


                            if ($response->successful()) {

                                $response = $response->json();
                                if ($response['datos_cliente']) {
                                    $datos = collect($response['datos_cliente'])->toArray();
                                } else {
                                    $datos = [];
                                }
                            } else {
                                $datos = [];
                            }
                        }


                        foreach ($datos as $key => $value) {
                            $datos_pariente->$key = $value;
                        }
                    }

                    $aval_id = $datos_propuesta->aval_id;
                    $datos_aval = null;

                    if ($aval_id != null) {


                        $datos_aval = (object)[
                            'agencia_aval' => $datos_propuesta->agencia_aval,
                            'aval_id' => $aval_id
                        ];

                        if (in_array($datos_propuesta->agencia_aval, [2, 3, 5])) {
                            $agencia_aval = $datos_propuesta->agencia_aval;
                            $conexion_aval_1 = 'master_' . $agencia_aval;

                            $datos = Cliente::on($conexion_aval_1)->from('cliente_registros as cli_reg')
                                ->select(
                                    'cli_reg.dni',
                                    'cli_reg.apellido_paterno',
                                    'cli_reg.apellido_materno',
                                    'cli_reg.nombres',
                                    'cli_reg.central_riesgo',
                                    'cli_reg.direccion',
                                    'cli_reg.referencia_direccion',
                                    'cli_reg.telefonos',

                                    'dep.departamento',
                                    'pro.provincia',
                                    'dis.distrito'
                                )
                                ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id',  'dep.id')
                                ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id',  'pro.id')
                                ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id',  'dis.id')
                                ->where('cli_reg.id', $aval_id)
                                ->get()->last()->toArray();
                        } else if (in_array($datos_propuesta->agencia_aval, [1, 4, 6])) {
                            $params =
                                [
                                    'agencia_id' => $datos_propuesta->agencia_aval,
                                    'cliente_id' => $aval_id
                                ];

                            $response = Http::get(API_PRO_URL . "/api/cli/listado_externa/datos_cliente", $params);


                            if ($response->successful()) {

                                $response = $response->json();
                                if ($response['datos_cliente']) {
                                    $datos = collect($response['datos_cliente'])->toArray();
                                } else {
                                    $datos = [];
                                }
                            } else {
                                $datos = [];
                            }
                        }

                        foreach ($datos as $key => $value) {
                            $datos_aval->$key = $value;
                        }
                    }

                    $pariente_aval_id = $datos_propuesta->pariente_aval_id;
                    $datos_pariente_aval = null;

                    if ($pariente_aval_id != null) {

                        $datos_pariente_aval = (object)[
                            'agencia_pariente' => $datos_propuesta->agencia_pariente_aval,
                            'pariente_aval_id' => $pariente_aval_id
                        ];


                        if (in_array($datos_propuesta->agencia_pariente_aval, [2, 3, 5])) {
                            $agencia_pariente_aval = $datos_propuesta->agencia_pariente_aval;
                            $conexion_pariente_aval_1 = 'master_' . $agencia_pariente_aval;

                            $datos = Cliente::on($conexion_pariente_aval_1)
                                ->select(
                                    'dni',
                                    'apellido_paterno',
                                    'apellido_materno',
                                    'nombres',
                                    'central_riesgo',

                                )
                                ->where('id', $pariente_aval_id)
                                ->get()->last()->toArray();
                        } else if (in_array($datos_propuesta->agencia_pariente_aval, [1, 4, 6])) {

                            $params =
                                [
                                    'agencia_id' => $datos_propuesta->agencia_aval,
                                    'cliente_id' => $pariente_aval_id
                                ];

                            $response = Http::get(API_PRO_URL . "/api/cli/listado_externa/datos_cliente", $params);


                            if ($response->successful()) {

                                $response = $response->json();
                                if ($response['datos_cliente']) {
                                    $datos = collect($response['datos_cliente'])->toArray();
                                } else {
                                    $datos = [];
                                }
                            } else {
                                $datos = [];
                            }
                        }

                        foreach ($datos as $key => $value) {
                            $datos_pariente_aval->$key = $value;
                        }
                    }

                    // Listar las PRENDAS seleccionadas

                    $prendario = filter_var($datos_propuesta->prendario, FILTER_VALIDATE_BOOLEAN);
                    $datos_prendas = [];

                    if ($prendario) {

                        $prendas = json_decode($datos_propuesta->prendas);
                        $model = Prenda::on($conexion)->from('cliente_prendas as cli_pre');
                        $model->getModel()->setTable('cli_pre');

                        $datos_prendas = $model->join('solucion_master.usuarios as us', DB::raw("SUBSTRING(cli_pre.datos_creacion,42,8)"), 'us.dni')
                            ->select(
                                [
                                    'cli_pre.id',
                                    'cli_pre.cantidad',
                                    'cli_pre.descripcion',
                                    'cli_pre.marca',
                                    'cli_pre.modelo',
                                    'cli_pre.serie',
                                    'cli_pre.color',
                                    'cli_pre.estado',
                                    'cli_pre.fecha_compra',
                                    'cli_pre.numero_comprobante',
                                    'cli_pre.precio_compra',
                                    'cli_pre.precio_actual',
                                    'cli_pre.disponible',
                                    'cli_pre.comentario',
                                    'cli_pre.fotos',
                                    'cli_pre.datos_creacion',

                                    'us.usuario as usuario_registro'
                                ]

                            )
                            ->whereIn('cli_pre.id', $prendas)
                            ->get();

                        $datos_propuesta->prendas = $prendas;
                    }
                } else {
                    $datos_propuesta = null;


                    $datos_pariente = Pariente::on($conexion)->select(
                        'id',
                        'agencia_pariente',
                        'pariente_id',
                        'parentesco'
                    )->where([
                        ['cliente_id', $cliente_id],
                        ['vinculado', 1]
                    ])->get()->last();

                    if ($datos_pariente != null) {
                        if (in_array($datos_pariente->agencia_pariente, [2, 3, 5])) {
                            $conexion_pariente_2 = 'master_' . $datos_pariente->agencia_pariente;

                            $datos = Cliente::on($conexion_pariente_2)
                                ->select(
                                    'dni',
                                    'apellido_paterno',
                                    'apellido_materno',
                                    'nombres',
                                    'central_riesgo'
                                )
                                ->where('id', $datos_pariente->pariente_id)
                                ->get()->last()->toArray();
                        } else if (in_array($datos_pariente->agencia_pariente, [1, 4, 6])) {

                            $params =
                                [
                                    'agencia_id' => $datos_pariente->agencia_pariente,
                                    'cliente_id' => $datos_pariente->pariente_id
                                ];

                            $response = Http::get(API_PRO_URL . "/api/cli/listado_externa/datos_cliente", $params);


                            if ($response->successful()) {

                                $response = $response->json();
                                if ($response['datos_cliente']) {
                                    $datos = collect($response['datos_cliente'])->toArray();
                                } else {
                                    $datos = [];
                                }
                            } else {
                                $datos = [];
                            }
                        }

                        foreach ($datos as $key => $value) {
                            $datos_pariente->$key = $value;
                        }
                    }

                    $datos_aval = Aval::on($conexion)->select(
                        'id',
                        'agencia_aval',
                        'aval_id'
                    )->where([
                        ['cliente_id', $cliente_id],
                        ['vinculado', 1]
                    ])->get()->last();


                    if ($datos_aval != null) {
                        if (in_array($datos_aval->agencia_aval, [2, 3, 5])) {
                            $conexion_aval_2 = 'master_' . $datos_aval->agencia_aval;

                            $datos = Cliente::on($conexion_aval_2)->from('cliente_registros as cli_reg')
                                ->select(
                                    'cli_reg.dni',
                                    'cli_reg.apellido_paterno',
                                    'cli_reg.apellido_materno',
                                    'cli_reg.nombres',
                                    'cli_reg.central_riesgo',
                                    'cli_reg.direccion',
                                    'cli_reg.referencia_direccion',
                                    'cli_reg.telefonos',

                                    'dep.departamento',
                                    'pro.provincia',
                                    'dis.distrito'
                                )
                                ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id',  'dep.id')
                                ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id',  'pro.id')
                                ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id',  'dis.id')
                                ->where('cli_reg.id', $datos_aval->aval_id)
                                ->get()->last()->toArray();
                        } else if (in_array($datos_aval->agencia_aval, [1, 4, 6])) {
                            $params =
                                [
                                    'agencia_id' => $datos_aval->agencia_aval,
                                    'cliente_id' => $datos_aval->aval_id
                                ];

                            $response = Http::get(API_PRO_URL . "/api/cli/listado_externa/datos_cliente", $params);


                            if ($response->successful()) {

                                $response = $response->json();
                                if ($response['datos_cliente']) {
                                    $datos = collect($response['datos_cliente'])->toArray();
                                } else {
                                    $datos = [];
                                }
                            } else {
                                $datos = [];
                            }
                        }

                        foreach ($datos as $key => $value) {
                            $datos_aval->$key = $value;
                        }
                    }


                    if ($datos_aval != null) {

                        if (in_array($datos_aval->agencia_aval, [2, 3, 5])) {
                            $conexion_aval = 'master_' . $datos_aval->agencia_aval;

                            $datos_pariente_aval = Pariente::on($conexion_aval)->select(
                                'id',
                                'agencia_pariente',
                                'pariente_id as pariente_aval_id',
                                'parentesco'
                            )->where([
                                ['cliente_id', $datos_aval->aval_id],
                                ['vinculado', 1]
                            ])->get()->last();
                        } else if (in_array($datos_aval->agencia_aval, [1, 4, 6])) {

                            $params =
                                [
                                    'agencia_id' => $datos_aval->agencia_aval,
                                    'cliente_id' => $datos_aval->aval_id
                                ];

                            $response = Http::get(API_PRO_URL . "/api/cli/listado_externa/datos_pariente_aval", $params);


                            if ($response->successful()) {

                                $response = $response->json();
                                $datos_pariente_aval = $response['datos_pariente_aval'];
                            } else {
                                $datos_pariente_aval = null;
                            }
                        }

                        if ($datos_pariente_aval != null) {

                            if (in_array($datos_pariente_aval->agencia_pariente, [2, 3, 5])) {
                                $conexion_pariente_aval_2 = 'master_' . $datos_pariente_aval->agencia_pariente;

                                $datos = Cliente::on($conexion_pariente_aval_2)
                                    ->select(
                                        'dni',
                                        'apellido_paterno',
                                        'apellido_materno',
                                        'nombres',
                                        'central_riesgo'
                                    )
                                    ->where('id', $datos_pariente_aval->pariente_aval_id)
                                    ->get()->last()->toArray();
                            } else if (in_array($datos_pariente_aval->agencia_pariente, [1, 4, 6])) {


                                $params =
                                    [
                                        'agencia_id' => $datos_pariente_aval->agencia_pariente,
                                        'cliente_id' => $datos_pariente_aval->pariente_aval_id
                                    ];

                                $response = Http::get(API_PRO_URL . "/api/cli/listado_externa/datos_cliente", $params);


                                if ($response->successful()) {

                                    $response = $response->json();
                                    if ($response['datos_cliente']) {
                                        $datos = collect($response['datos_cliente'])->toArray();
                                    } else {
                                        $datos = [];
                                    }
                                } else {
                                    $datos = [];
                                }
                            }

                            foreach ($datos as $key => $value) {
                                $datos_pariente_aval->$key = $value;
                            }
                        }
                    } else {
                        $datos_pariente_aval = null;
                    }


                    // Listar las PRENDAS disponibles del cliente

                    $model = Prenda::on($conexion)->from('cliente_prendas as cli_pre');
                    $model->getModel()->setTable('cli_pre');
                    $datos_prendas = $model->join('solucion_master.usuarios as us', DB::raw("SUBSTRING(cli_pre.datos_creacion,42,8)"), 'us.dni')
                        ->select(
                            [
                                'cli_pre.id',
                                'cli_pre.cantidad',
                                'cli_pre.descripcion',
                                'cli_pre.marca',
                                'cli_pre.modelo',
                                'cli_pre.serie',
                                'cli_pre.color',
                                'cli_pre.estado',
                                'cli_pre.fecha_compra',
                                'cli_pre.numero_comprobante',
                                'cli_pre.precio_compra',
                                'cli_pre.precio_actual',
                                'cli_pre.disponible',
                                'cli_pre.comentario',
                                'cli_pre.fotos',
                                'cli_pre.datos_creacion',

                                'us.usuario as usuario_registro'
                            ]

                        )
                        ->where([
                            ['cli_pre.cliente_id', $cliente_id],
                            ['cli_pre.disponible', 1]
                        ])
                        ->get();
                }

                $datos_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                    ->select(
                        'cli_reg.dni',
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cli_reg.central_riesgo',
                        'cli_reg.calificacion',
                        'cli_reg.direccion',
                        'cli_reg.referencia_direccion',
                        'cli_reg.monto_maximo',
                        'cli_reg.agencia_id',
                        'cli_reg.correo_electronico',
                        'cli_reg.asesor_id',
                        'cli_reg.codigo_expediente',
                        'cli_reg.telefonos',

                        'cli_neg.id as negocio_id',
                        'cli_neg.nombre as nombre_negocio',
                        'cli_neg.actividad as actividad_negocio',

                        'ag.nombre as agencia',
                        'us.usuario as usuario_asesor',

                        'dep.departamento',
                        'pro.provincia',
                        'dis.distrito'
                    )
                    ->join('solucion_master.agencias as ag', 'cli_reg.agencia_id', 'ag.id_agencia')
                    ->join('cliente_negocios as cli_neg', 'cli_reg.id', 'cli_neg.cliente_id')
                    ->join('solucion_master.usuarios as us', 'cli_reg.asesor_id', 'us.dni')
                    ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id',  'dep.id')
                    ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id',  'pro.id')
                    ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id',  'dis.id')
                    ->where([
                        ['cli_reg.id', $cliente_id],
                        ['cli_neg.vinculado', 1]
                    ])->get()->last();


                // dd($datos_pariente_aval->agencia_pariente);
                // Listar los créditos vinculados al cliente
                $parientes_avales = (array)[
                    'PARIENTE' => ($datos_pariente == null ? null : (object)[
                        'agencia_id' => $datos_pariente->agencia_pariente,
                        'cliente_id' => $datos_pariente->pariente_id
                    ]),
                    'AVAL' => ($datos_aval == null ? null : (object)[
                        'agencia_id' => $datos_aval->agencia_aval,
                        'cliente_id' => $datos_aval->aval_id
                    ]),
                    'PARIENTE_AVAL' => ($datos_pariente_aval == null ? null : (object)[
                        'agencia_id' => $datos_pariente_aval->agencia_pariente,
                        'cliente_id' => $datos_pariente_aval->pariente_aval_id
                    ])
                ];



                $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
                $estado_id = $estado->id;

                $creditos_vinculados_de = [];
                $creditos_vinculados_a = [];

                foreach ($parientes_avales as $key => $value) {

                    // Pariente,aval o pariente_aval del cliente

                    if ($value != null) {

                        if (in_array($value->agencia_id, [2, 3, 5])) {
                            $conexion_1 = 'master_' .  $value->agencia_id;
                            $credito_de = Credito::on($conexion_1)->from('credito_registros as cre_reg')
                                ->select(
                                    'cre_reg.id',
                                    'cli_reg.id as cliente_id',
                                    'cre_reg.capital_total',

                                    'cli_reg.apellido_paterno',
                                    'cli_reg.apellido_materno',
                                    'cli_reg.nombres',
                                    'cli_reg.codigo_expediente',
                                    'cli_reg.agencia_id',

                                    'cre_apr.plazo',
                                    'cre_apr.periodo_pago',

                                    'cre_tip.tipo',

                                    'caj_des.datos_creacion',

                                    'usu.usuario as usuario_asesor',

                                    DB::raw("'$key' as vinculo")
                                )
                                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                                ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
                                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                                ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                                ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
                                ->where([['cre_reg.cliente_id', $value->cliente_id], ['cre_reg.estado_id', $estado_id]])
                                ->get();
                            foreach ($credito_de as $item) {
                                $creditos_vinculados_de[] = $item;
                            }
                        }
                    }

                    // Cliente como pariente,aval o pariente_aval de otros

                    $columna_1 = 'agencia_pariente';
                    $columna_2 = 'pariente_id';

                    if ($key == 'AVAL') {
                        $columna_1 = 'agencia_aval';
                        $columna_2 = 'aval_id';
                    } elseif ($key == 'PARIENTE_AVAL') {
                        $columna_1 = 'agencia_pariente_aval';
                        $columna_2 = 'pariente_aval_id';
                    }

                    $agencias = Agencia::all();
                    foreach ($agencias as $item_1) {

                        if (in_array($item_1->id_agencia, [2, 3, 5])) {

                            $conexion_2 = 'master_' .  $item_1->id_agencia;
                            $credito_a = Credito::on($conexion_2)->from('credito_registros as cre_reg')
                                ->select(
                                    'cre_reg.id',
                                    'cre_reg.capital_total',
                                    'cli_reg.id as cliente_id',
                                    'cli_reg.apellido_paterno',
                                    'cli_reg.apellido_materno',
                                    'cli_reg.nombres',
                                    'cli_reg.codigo_expediente',
                                    'cli_reg.agencia_id',

                                    'cre_apr.plazo',
                                    'cre_apr.periodo_pago',

                                    'cre_tip.tipo',

                                    'caj_des.datos_creacion',

                                    'usu.usuario as usuario_asesor',

                                    DB::raw("'$key' as vinculo")
                                )
                                ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
                                ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
                                ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
                                ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                                ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
                                ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
                                ->where([
                                    ["cre_pro.$columna_1", $agencia_id],
                                    ["cre_pro.$columna_2", $cliente_id],
                                    ['cre_reg.estado_id', $estado_id]
                                ])
                                ->get();

                            foreach ($credito_a as $item) {
                                $creditos_vinculados_a[] = $item;
                            }
                        }
                    }
                }

                $sectores = Sector::on($conexion)->where('habilitado', 1)->orderBy('sector', 'asc')->get();
                $productos = Producto::on($conexion)->where('habilitado', 1)->orderBy('producto', 'asc')->get();
                $subproductos = Subproducto::on($conexion)->where('habilitado', 1)->orderBy('subproducto', 'asc')->get();
                $tipos = Tipo::on($conexion)->where('habilitado', 1)->orderBy('tipo', 'asc')->get();
                $garantias = Garantia::on($conexion)->where('habilitado', 1)->orderBy('garantia', 'asc')->get();

                $usuarios = Usuario::where('habilitado', 1)->orderBy('usuario', 'asc')->get();

                $ultimo_credito = (new AprobacionController)->generar_numero_credito($cliente_id, $agencia_id);

                return Inertia::render('Creditos/Creditos/propuesta', [
                    'agencia_id' =>   intval($agencia_id),
                    'propuesta_id' => intval($propuesta_id),
                    'datos_propuesta' => $datos_propuesta,
                    'datos_negocio' => $datos_negocio,
                    'evaluacion_id' => $evaluacion_id,
                    'cliente_id' => intval($cliente_id),
                    'datos_cliente' => $datos_cliente,
                    'datos_pariente' => $datos_pariente,
                    'datos_aval' => $datos_aval,
                    'datos_pariente_aval' => $datos_pariente_aval,
                    'datos_prendas' => $datos_prendas,
                    'creditos_vinculados_de' => $creditos_vinculados_de,
                    'creditos_vinculados_a' => $creditos_vinculados_a,
                    'sectores' => $sectores,
                    'productos' => $productos,
                    'subproductos' => $subproductos,
                    'tipos' => $tipos,
                    'garantias' => $garantias,
                    'usuarios' => $usuarios,
                    'ultimo_credito' => $ultimo_credito,

                ]);
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }

    public function guardar(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $frmPropuesta = json_decode($request->frmPropuesta);

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $cliente_id = $frmPropuesta->cliente_id;
        $negocio_id = $frmPropuesta->negocio_id;
        $promotor_id = $frmPropuesta->promotor_id;

        $pariente_id = $frmPropuesta->pariente_id;
        $agencia_pariente = $frmPropuesta->agencia_pariente;
        $aval_id = $frmPropuesta->aval_id;
        $agencia_aval = $frmPropuesta->agencia_aval;
        $pariente_aval_id = $frmPropuesta->pariente_aval_id;
        $agencia_pariente_aval = $frmPropuesta->agencia_pariente_aval;

        $monto = floatval($frmPropuesta->monto);
        $sector_id = $frmPropuesta->sector_id;
        $producto_id = $frmPropuesta->producto_id;
        $subproducto_id = $frmPropuesta->subproducto_id;
        $tipo_id = $frmPropuesta->tipo_id;
        $periodo_pago = $frmPropuesta->periodo_pago;
        $es_especial = $frmPropuesta->es_especial;
        $plazo = floatval($frmPropuesta->plazo);
        $con_dias_gracia = filter_var($frmPropuesta->con_dias_gracia, FILTER_VALIDATE_BOOLEAN);
        $dias_gracia = $frmPropuesta->dias_gracia;
        $tasa_interes = floatval($frmPropuesta->tasa_interes);
        if ($frmPropuesta->forma_pago == 'OFICINA') {
            $forma_pago = 1;
        } else {
            $forma_pago = 0;
        }
        $garantia_id = $frmPropuesta->garantia_id;

        $valor_garantia = floatval($frmPropuesta->valor_garantia);
        $cuota = floatval($frmPropuesta->cuota);
        $fecha_propuesta = $frmPropuesta->fecha_propuesta;
        $comentario_garantia = mb_strtoupper($frmPropuesta->comentario_garantia);
        $comentario_propuesta = mb_strtoupper($frmPropuesta->comentario_propuesta);

        $prendario = $frmPropuesta->prendario;
        $prendas = null;

        if ($prendario) {
            $prendas = $frmPropuesta->prendas;

            foreach ($prendas as $item) {
                Prenda::on($conexion)->where('id', $item)->update([
                    'disponible' => 0,
                    'datos_actualizacion' => $datos_registro
                ]);
            }

            $prendas = json_encode($prendas);
        }

        // Verifica si el crédito es ESPECIAL y solicita la MORA ADICIONAL

        if ($es_especial) {
            $mora_adicional = floatval($frmPropuesta->mora_adicional);
        } else {
            $mora_adicional = 0;
        }

        $estado = Estado::on($conexion)->select('id')
            ->where('estado', 'PROPUESTO')
            ->get()->last();
        $estado_id = $estado->id;

        $propuesta = Propuesta::on($conexion)->create([
            'agencia_id' => $agencia_id,
            'cliente_id' => $cliente_id,
            'negocio_id' => $negocio_id,
            'promotor_id' => $promotor_id,
            'pariente_id' => $pariente_id,
            'agencia_pariente' => $agencia_pariente,
            'aval_id' => $aval_id,
            'agencia_aval' => $agencia_aval,
            'pariente_aval_id' => $pariente_aval_id,
            'agencia_pariente_aval' => $agencia_pariente_aval,
            'monto' => $monto,
            'tasa_interes' => $tasa_interes,
            'plazo' => $plazo,
            'periodo_pago' => $periodo_pago,
            'es_especial' => $es_especial,
            'mora_adicional' => $mora_adicional,
            'con_dias_gracia' => $con_dias_gracia,
            'dias_gracia' => $dias_gracia,
            'cuota' => $cuota,
            'sector_id' => $sector_id,
            'producto_id' => $producto_id,
            'subproducto_id' => $subproducto_id,
            'tipo_id' => $tipo_id,
            'pago_oficina' => $forma_pago,
            'garantia_id' => $garantia_id,
            'valor_garantia' => $valor_garantia,
            'prendario' => $prendario,
            'prendas' => $prendas,
            'comentario_garantia' => $comentario_garantia,
            'comentario_propuesta' => $comentario_propuesta,
            'estado_id' => $estado_id,
            'fecha_propuesta' => $fecha_propuesta,
            'datos_creacion' => $datos_registro

        ]);

        $propuesta_id = $propuesta->id;

        return redirect()->route('cre.propuesta', [
            'cliente_id' => $cliente_id,
            'propuesta_id' => $propuesta_id,
            'agencia_id' => $agencia_id
        ]);
    }

    public function guardar_telefonos(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $cliente_id = $request->cliente_id;

        $frmDatosCliente = json_decode($request->frmDatosCliente);
        $correo_electronico = $frmDatosCliente->correo_electronico;

        $telefonos = json_encode($frmDatosCliente->telefonos);
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        // dd($request);


        Cliente::on($conexion)->where('id', $cliente_id)
            ->update([
                'correo_electronico' => $correo_electronico,
                'telefonos' => $telefonos,
                'datos_actualizacion' => $datos_registro,
            ]);
    }

    public function exportar(Request $request)
    {
        $datos_cliente = json_decode($request->datos_cliente);
        $datos_pariente = json_decode($request->datos_pariente);
        $datos_aval = json_decode($request->datos_aval);
        $datos_pariente_aval = json_decode($request->datos_pariente_aval);
        $datos_negocio = json_decode($request->datos_negocio);
        $datos_propuesta = json_decode($request->datos_propuesta);

        $tipo = $request->tipo;

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptFichaCredito.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos-----------------------------

        // DATOS HEADER

        if (intval($datos_negocio->ciiu_codigo) == 9980) {
            $sheet->setCellValue("B2", 'FICHA DE CRÉDITO(CONVENIO) - PROPUESTA');
        } else {
            $sheet->setCellValue("B2", 'FICHA DE CRÉDITO - PROPUESTA');
        }

        $sheet->setCellValue("H2", $datos_propuesta->monto);
        $sheet->setCellValue("J2", $datos_propuesta->fecha_propuesta);

        // DATOS TITULAR

        $localidad = $datos_cliente->departamento . ' - ' . $datos_cliente->provincia . ' - ' . $datos_cliente->distrito;
        $telefonos = json_decode($datos_cliente->telefonos);

        $sheet->setCellValue("C5", $datos_cliente->apellido_paterno);
        $sheet->setCellValue("C6", $datos_cliente->apellido_materno);
        $sheet->setCellValue("C7", $datos_cliente->nombres);
        $sheet->setCellValue("C8", $datos_cliente->dni);
        $sheet->setCellValue("C9", $datos_cliente->central_riesgo);
        $sheet->setCellValue("E9", $datos_cliente->monto_maximo);
        $sheet->setCellValue("C10", $localidad);
        $sheet->setCellValue("C11", $datos_cliente->direccion);
        $sheet->setCellValue("C12", $datos_cliente->referencia_direccion);
        $sheet->setCellValue("C13", $telefonos->t1 . ' / ' . $telefonos->t2 . ' / ' . $telefonos->t3);

        // DATOS PARIENTE

        if ($datos_pariente != null) {
            $sheet->setCellValue("H5", $datos_pariente->apellido_paterno);
            $sheet->setCellValue("H6", $datos_pariente->apellido_materno);
            $sheet->setCellValue("H7", $datos_pariente->nombres);
            $sheet->setCellValue("H8", $datos_pariente->dni);
            $sheet->setCellValue("H9", $datos_pariente->central_riesgo);
        }

        // DATOS AVAL

        if ($datos_aval != null) {
            $localidad = $datos_aval->departamento . ' - ' . $datos_aval->provincia . ' - ' . $datos_aval->distrito;
            $telefonos = json_decode($datos_aval->telefonos);

            $sheet->setCellValue("C16", $datos_aval->apellido_paterno);
            $sheet->setCellValue("C17", $datos_aval->apellido_materno);
            $sheet->setCellValue("C18", $datos_aval->nombres);
            $sheet->setCellValue("C19", $datos_aval->dni);
            $sheet->setCellValue("C20", $datos_aval->central_riesgo);
            $sheet->setCellValue("C21", $localidad);
            $sheet->setCellValue("C22", $datos_aval->direccion);
            $sheet->setCellValue("C23", $datos_aval->referencia_direccion);
            $sheet->setCellValue("C24", $telefonos->t1 . ' / ' . $telefonos->t2 . ' / ' . $telefonos->t3);
        }


        // DATOS PARIENTE AVAL

        if ($datos_pariente_aval != null) {
            $sheet->setCellValue("H16", $datos_pariente_aval->apellido_paterno);
            $sheet->setCellValue("H17", $datos_pariente_aval->apellido_materno);
            $sheet->setCellValue("H18", $datos_pariente_aval->nombres);
            $sheet->setCellValue("H19", $datos_pariente_aval->dni);
            $sheet->setCellValue("H20", $datos_pariente_aval->central_riesgo);
        }

        // DATOS NEGOCIO

        $localidad = $datos_negocio->departamento . ' - ' . $datos_negocio->provincia . ' - ' . $datos_negocio->distrito;
        $telefonos = json_decode($datos_negocio->telefonos);

        $sheet->setCellValue("C27", $datos_negocio->ciiu_codigo . ' - ' . $datos_negocio->ciiu_nombre);
        $sheet->setCellValue("C28", $datos_negocio->nombre);
        $sheet->setCellValue("C29", $telefonos->t1 . ' / ' . $telefonos->t2);
        $sheet->setCellValue("C30", $localidad);
        $sheet->setCellValue("C31", $datos_negocio->direccion);
        $sheet->setCellValue("C32", $datos_negocio->referencia_direccion);



        // DATOS PROPUESTA

        if ($datos_propuesta->pago_oficina == 1) {
            $sheet->setCellValue("J33", 'OFICINA');
        } else {
            $sheet->setCellValue("J33", 'NEGOCIO');
        }

        $sheet->setCellValue("C34", $datos_propuesta->comentario_propuesta);
        $sheet->setCellValue("C35", $datos_propuesta->usuario_promotor);
        $sheet->setCellValue("E35", $datos_propuesta->sector);
        $sheet->setCellValue("H35", $datos_cliente->usuario_asesor);
        $sheet->setCellValue("C36", $datos_propuesta->producto);

        if ($datos_propuesta->subproducto != null) {
            $sheet->setCellValue("H36", $datos_propuesta->subproducto);
        }

        if ($datos_propuesta->es_especial) {
            $sheet->setCellValue("J35", 'ESPECIAL');
        }

        $sheet->setCellValue("C37", $datos_propuesta->tipo);
        $sheet->setCellValue("E37", $datos_propuesta->monto);
        $sheet->setCellValue("H37", round($datos_propuesta->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($datos_propuesta->periodo_pago));
        $sheet->setCellValue("J37", $datos_propuesta->cuota);
        $sheet->setCellValue("C38", $datos_propuesta->usuario_registro);
        $sheet->setCellValue("E38", $datos_propuesta->tasa_interes / 100);

        // DATOS FOOTER

        $sheet->setCellValue("B51", (new CreditosController)->header_footer($datos_cliente->agencia_id));

        // Convertir PDF-------------------------

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptFichaCredito', 5);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }

    public function listar(Request $request)
    {
        $agencia_id = (new CreditosController)->verificar_nulo($request->agencia_id);

        if ($agencia_id == null) {
            return [];
        }

        $conexion = 'master_' .  $agencia_id;

        $fecha_aplicacion = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $estado = Estado::on($conexion)->select('id')->where('estado', 'PROPUESTO')->get()->last();
        $estado_id = $estado->id;

        return Propuesta::on($conexion)->from('credito_propuestas as cre_pro')
            ->select(
                'cre_pro.id',
                'cre_pro.id as propuesta_id',
                DB::raw("0 as aprobacion_id"),
                'cre_pro.agencia_id',
                'cre_pro.cliente_id',
                'cre_pro.monto',
                'cre_pro.plazo',
                'cre_pro.periodo_pago',
                'cre_pro.datos_creacion',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.codigo_expediente',

                'age.nombre as agencia',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_registro',

            )->join('cliente_registros as cli_reg', 'cre_pro.cliente_id', 'cli_reg.id')
            ->join('solucion_master.agencias as age', 'cre_pro.agencia_id', 'age.id_agencia')
            ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->join('solucion_master.usuarios as us_2', DB::raw("SUBSTRING(cre_pro.datos_creacion,42,8)"), 'us_2.dni')
            ->join('credito_estados as cre_est', 'cre_pro.estado_id', 'cre_est.id')
            ->where([
                ['cre_pro.estado_id', $estado_id],
                [DB::raw("SUBSTR(cre_pro.datos_creacion,11,10)"), $fecha_aplicacion]
            ])
            ->get();
    }
}
