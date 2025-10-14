<?php

namespace App\Http\Controllers\Creditos\Credito;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\Usuarios\UsuarioController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\General\Datos_aplicacion;

use App\Models\Creditos\Credito\Propuesta;
use App\Models\Creditos\Credito\Aprobacion;
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
use App\Models\Creditos\Mantenimiento\Credito\Comision;
use App\Models\Creditos\Mantenimiento\Credito\ComisionRango;

use App\Models\General\Agencia;
use App\Models\Gth\Usuarios\Usuario;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Prophecy\Prophet;

use Illuminate\Support\Facades\Http;

define('API_APR_URL',  getenv('VITE_S_API_EXTERNA'));

class AprobacionController extends Controller
{
    public function aprobacion($propuesta_id, $aprobacion_id = 0, $agencia_id)
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'APROBACION', 'CREDITOS_CREDITO');
            if ($band == 1) {

                $conexion = 'master_' .  $agencia_id;

                $datos_propuesta = Propuesta::on($conexion)->from('credito_propuestas as cre_pro')
                    ->select(
                        'cre_pro.id',
                        'cre_pro.agencia_id',
                        'cre_pro.cliente_id',
                        'cre_pro.agencia_pariente',
                        'cre_pro.pariente_id',
                        'cre_pro.agencia_aval',
                        'cre_pro.aval_id',
                        'cre_pro.agencia_pariente_aval',
                        'cre_pro.pariente_aval_id',
                        'cre_pro.promotor_id',
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
                        'cre_pro.prendario',
                        'cre_pro.prendas',
                        'cre_pro.comentario_garantia',
                        'cre_pro.comentario_propuesta',
                        'cre_pro.fecha_propuesta',
                        'cre_pro.datos_creacion',

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

                $cliente_id = $datos_propuesta->cliente_id;
                $datos_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                    ->select(
                        'cli_reg.id',
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

                        $response = Http::get(API_APR_URL . "/api/cli/listado_externa/datos_cliente", $params);


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

                        $response = Http::get(API_APR_URL . "/api/cli/listado_externa/datos_cliente", $params);


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
                        'agencia_pariente_aval' => $datos_propuesta->agencia_pariente_aval,
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
                                'agencia_id' => $datos_propuesta->agencia_pariente_aval,
                                'cliente_id' => $pariente_aval_id
                            ];

                        $response = Http::get(API_APR_URL . "/api/cli/listado_externa/datos_cliente", $params);


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


                $negocio_id = $datos_cliente->negocio_id;
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

                        'solucion_master.dep.departamento',
                        'solucion_master.pro.provincia',
                        'solucion_master.dis.distrito'
                    )
                    ->join('solucion_master.ciiu', 'cli_neg.ciiu_id', 'ciiu.id')
                    ->join('solucion_master.departamentos as dep', 'cli_neg.departamento_id',  'dep.id')
                    ->join('solucion_master.provincias as pro', 'cli_neg.provincia_id',  'pro.id')
                    ->join('solucion_master.distritos as dis', 'cli_neg.distrito_id',  'dis.id')
                    ->where('cli_neg.id', $negocio_id)
                    ->get()->last();

                if ($aprobacion_id != 0) {

                    $datos_aprobacion = Aprobacion::on($conexion)->from('credito_aprobaciones as cre_apr')
                        ->select(
                            'cre_apr.id',
                            'cre_apr.propuesta_id',
                            'cre_apr.monto',
                            'cre_apr.tasa_interes',
                            'cre_apr.plazo',
                            'cre_apr.periodo_pago',
                            'cre_apr.es_especial',
                            'cre_apr.mora_adicional',
                            'cre_apr.con_dias_gracia',
                            'cre_apr.dias_gracia_ci',
                            'cre_apr.dias_gracia_si',
                            'cre_apr.cuota',
                            'cre_apr.sector_id',
                            'cre_apr.producto_id',
                            'cre_apr.subproducto_id',
                            'cre_apr.tipo_id',
                            'cre_apr.pago_oficina',
                            'cre_apr.considerado_uno',
                            'cre_apr.promotor_id',
                            'cre_apr.comentario_aprobacion',
                            'cre_apr.fecha_aprobacion',

                            'cre_apr.datos_creacion',

                            'cre_sec.sector',
                            'cre_prod.producto',
                            'cre_sub.subproducto',
                            'cre_tip.tipo',

                            'us_1.usuario as usuario_promotor',
                            'us_2.usuario as usuario_registro'
                        )
                        ->join('credito_sectores as cre_sec', 'cre_apr.sector_id', 'cre_sec.id')
                        ->join('credito_productos as cre_prod', 'cre_apr.producto_id', 'cre_prod.id')
                        ->leftjoin('credito_subproductos as cre_sub', 'cre_apr.subproducto_id', 'cre_sub.id')
                        ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
                        ->join('solucion_master.usuarios as us_1', 'cre_apr.promotor_id',  'us_1.dni')
                        ->join(
                            'solucion_master.usuarios as us_2',
                            DB::raw("SUBSTRING(cre_apr.datos_creacion,42,8)"),
                            'us_2.dni'
                        )

                        ->where('cre_apr.id', $aprobacion_id)
                        ->get()->last();
                } else {
                    $datos_aprobacion = null;
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
                        'agencia_id' => $datos_pariente_aval->agencia_pariente_aval,
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

                $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
                $estado_id = $estado->id;

                $creditos = Credito::on($conexion)->select(
                    'id',
                    'estado_id',
                    'fecha_ultimo_pago'
                )->where(
                    'cliente_id',
                    $cliente_id
                )->get();

                $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
                $estado_id = $estado->id;

                $creditos_activos =  $creditos->where('estado_id', $estado_id);

                if ($creditos_activos->count() == 0) {

                    if ($creditos->count() == 0) {
                        $datos_inactividad = (object)[
                            'inactivo' => false,
                            'fecha_inactividad' => null
                        ];
                    } else {
                        $datos_inactividad = (object)[
                            'inactivo' => true,
                            'fecha_inactividad' => $creditos->last()->fecha_ultimo_pago
                        ];
                    }
                } else {
                    $datos_inactividad = (object)[
                        'inactivo' => false,
                        'fecha_inactividad' => null
                    ];
                }

                $minimo_inactividad = Datos_aplicacion::on($conexion)->select('valor_entero')
                    ->where('descripcion', 'MESES_INACTIVIDAD')->get()->last();

                $minimo_inactividad = $minimo_inactividad->valor_entero;

                return Inertia::render('Creditos/Creditos/aprobacion', [
                    'agencia_id' => intval($agencia_id),
                    'propuesta_id' => intval($propuesta_id),
                    'aprobacion_id' => intval($aprobacion_id),
                    'datos_propuesta' => $datos_propuesta,
                    'datos_cliente' => $datos_cliente,
                    'datos_prendas' => $datos_prendas,
                    'datos_pariente' => $datos_pariente,
                    'datos_aval' => $datos_aval,
                    'datos_pariente_aval' => $datos_pariente_aval,
                    'datos_negocio' => $datos_negocio,
                    'datos_aprobacion' => $datos_aprobacion,
                    'creditos_vinculados_de' => $creditos_vinculados_de,
                    'creditos_vinculados_a' => $creditos_vinculados_a,
                    'sectores' => $sectores,
                    'productos' => $productos,
                    'subproductos' => $subproductos,
                    'tipos' => $tipos,
                    'garantias' => $garantias,
                    'usuarios' => $usuarios,
                    'datos_inactividad' => $datos_inactividad,
                    'minimo_inactividad' => intVal($minimo_inactividad)
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

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $modo = $request->modo;

        $estado = Estado::on($conexion)->select('id')->where('estado', $modo)->get()->last();
        $estado_id = $estado->id;


        $prendas = json_decode($request->datos_prendas);

        if ($modo == 'DESAPROBADO') {

            $propuesta_id = $request->propuesta_id;
            $comentario_desaprobacion = $request->comentario_desaprobacion;

            Propuesta::on($conexion)->where('id', $propuesta_id)->update([
                'estado_id' => $estado_id,
                'comentario_desaprobacion' => mb_strtoupper($comentario_desaprobacion),
                'prendario' => 0,
                'prendas' => null,
                'datos_actualizacion' => $datos_registro
            ]);

            foreach ($prendas as $item) {
                Prenda::on($conexion)->where('id', $item->id)->update([
                    'disponible' => 1,
                    'datos_actualizacion' => $datos_registro
                ]);
            }

            return redirect()->route('cre.index');
        } else if ($modo == 'APROBADO') {
            $frmAprobacion = json_decode($request->frmAprobacion);

            $propuesta_id = $frmAprobacion->propuesta_id;
            $cliente_id = $frmAprobacion->cliente_id;

            $agencia = Agencia::select('nueva_empresa')->where('id_agencia', $agencia_id)->get()->last();
            $nueva_empresa = $agencia->nueva_empresa;

            $codigo_expediente = $frmAprobacion->codigo_expediente;

            $monto = floatval($frmAprobacion->monto);
            $cuota = floatval($frmAprobacion->cuota);
            $sector_id = $frmAprobacion->sector_id;
            $producto_id = $frmAprobacion->producto_id;
            $subproducto_id = $frmAprobacion->subproducto_id;
            $tipo_id = $frmAprobacion->tipo_id;
            $periodo_pago = $frmAprobacion->periodo_pago;
            $plazo = floatval($frmAprobacion->plazo);
            $es_especial = filter_var($frmAprobacion->es_especial, FILTER_VALIDATE_BOOLEAN);
            if ($es_especial) {
                $mora_adicional = floatval($frmAprobacion->mora_adicional);
            } else {
                $mora_adicional = 0;
            }

            $con_dias_gracia = filter_var($frmAprobacion->con_dias_gracia, FILTER_VALIDATE_BOOLEAN);
            $dias_gracia_ci = $frmAprobacion->dias_gracia_ci;
            $dias_gracia_si = $frmAprobacion->dias_gracia_si;
            $tasa_interes = floatval($frmAprobacion->tasa_interes);
            $ciiu_codigo = intval($frmAprobacion->ciiu_codigo);

            if ($frmAprobacion->forma_pago == 'OFICINA') {
                $forma_pago = 1;
            } else {
                $forma_pago = 0;
            }

            $considerado_uno = $frmAprobacion->considerado_uno;
            $promotor_id = $frmAprobacion->promotor_id;
            $comentario_aprobacion = mb_strtoupper($frmAprobacion->comentario_aprobacion);
            $fecha_aprobacion = mb_strtoupper($frmAprobacion->fecha_aprobacion);

            // Verificar si es CIIU de convenio
            if ($ciiu_codigo == 9980) {
                $convenio = true;
            } else {
                $convenio = false;
            }

            if ($codigo_expediente == null) {
                $nuevo_expediente = (object)$this->generar_expediente($agencia_id);
                $codigo_expediente = $nuevo_expediente->codigo_expediente;
                Cliente::on($conexion)->where('id', $cliente_id)->update([
                    'numero_expediente' => $nuevo_expediente->numero_expediente,
                    'codigo_expediente' => $nuevo_expediente->codigo_expediente,
                    'datos_actualizacion' => $datos_registro
                ]);
            }

            $numero_credito = $this->generar_numero_credito($cliente_id, $agencia_id);
            $codigo_seguimiento = $this->generar_codigo_seguimiento(
                $agencia_id,
                $codigo_expediente,
                $numero_credito,
                $convenio
            );

            if ($nueva_empresa) {
                $expediente = Cliente::on($conexion)->select('numero_expediente_2', 'codigo_expediente_2')
                    ->where('id', $cliente_id)->get();

                if ($expediente) {
                    $nuevo_expediente = (object)$this->generar_expediente_2($agencia_id);
                    $codigo_expediente_2 = $nuevo_expediente->codigo_expediente_2;
                    Cliente::on($conexion)->where('id', $cliente_id)->update([
                        'numero_expediente_2' => $nuevo_expediente->numero_expediente_2,
                        'codigo_expediente_2' => $nuevo_expediente->codigo_expediente_2,
                        'datos_actualizacion' => $datos_registro
                    ]);
                }

                $numero_credito_2 = $this->generar_numero_credito_2($cliente_id, $agencia_id);

                $codigo_seguimiento_2 = $this->generar_codigo_seguimiento(
                    $agencia_id,
                    $codigo_expediente_2,
                    $numero_credito_2,
                    $convenio
                );
            } else {
                $numero_credito_2 = null;
                $codigo_seguimiento_2 = null;
            }

            $lista_comisiones = Comision::on($conexion)
                ->where([
                    ['comision', '!=', 'DESEMBOLSO A DOMICILIO'],
                    ['habilitado', 1]
                ])->get();

            $comisiones = [];

            foreach ($lista_comisiones as $comision) {
                $comision_id = $comision->id;
                $comision_rango = ComisionRango::on($conexion)->where('comision_id', $comision_id)->get()->toArray();

                $array_filtrado = array_filter($comision_rango, function ($var) use ($monto) {

                    $monto_desde = intval($var['monto_desde']);
                    $monto_hasta = intval($var['monto_hasta']);

                    return   intval($monto) >= $monto_desde && intval($monto) <= $monto_hasta;
                });

                foreach ($array_filtrado as $item) {
                    $monto_cobrar = $item['monto_cobrar'];
                }

                $cobrar_comision = 1;

                if ($comision->comision == 'RIESGO CREDITICIO' && $numero_credito != 1) {
                    $cobrar_comision = 0;
                }

                if ($comision->comision == 'RIESGO CREDITICIO') {
                    $monto_cobrar = round(($item['monto_cobrar'] / 100) * $monto, 1);
                }


                $obj = [
                    'comision_id' => $comision_id,
                    'cobrar_comision' =>  $cobrar_comision,
                    'monto_cobrar' => floatval($monto_cobrar)
                ];

                $comisiones[] = $obj;
            }

            $prendario = $frmAprobacion->prendario;

            // Si el CRÉDITO ya no es PRENDARIO
            if ($prendario == false) {

                Propuesta::on($conexion)->where('id', $propuesta_id)->update([
                    'prendario' => 0,
                    'prendas' => null,
                    'datos_actualizacion' => $datos_registro
                ]);

                foreach ($prendas as $item) {
                    Prenda::on($conexion)->where('id', $item->id)->update([
                        'disponible' => 1,
                        'datos_actualizacion' => $datos_registro
                    ]);
                }
            }

            $aprobacion = Aprobacion::on($conexion)->create([
                'propuesta_id' => $propuesta_id,
                'numero_credito' => $numero_credito,
                'numero_credito_2' => $numero_credito_2,
                'monto' => $monto,
                'tasa_interes' => $tasa_interes,
                'plazo' => $plazo,
                'periodo_pago' => $periodo_pago,
                'es_especial' => $es_especial,
                'mora_adicional' => $mora_adicional,
                'con_dias_gracia' => $con_dias_gracia,
                'dias_gracia_ci' => $dias_gracia_ci,
                'dias_gracia_si' => $dias_gracia_si,
                'cuota' => $cuota,
                'sector_id' => $sector_id,
                'producto_id' => $producto_id,
                'subproducto_id' => $subproducto_id,
                'tipo_id' => $tipo_id,
                'pago_oficina' => $forma_pago,
                'considerado_uno' => $considerado_uno,
                'promotor_id' => $promotor_id,
                'comentario_aprobacion' => $comentario_aprobacion,
                'estado_id' => $estado_id,
                'fecha_aprobacion' => $fecha_aprobacion,
                'cobrar_' => $fecha_aprobacion,
                'comisiones' => json_encode($comisiones),
                'codigo_seguimiento' => $codigo_seguimiento,
                'codigo_seguimiento_2' => $codigo_seguimiento_2,
                'datos_creacion' => $datos_registro
            ]);

            $aprobacion_id = $aprobacion->id;

            Propuesta::on($conexion)->where('id', $propuesta_id)->update([
                'estado_id' => $estado_id,
                'datos_actualizacion' => $datos_registro
            ]);

            return redirect()->route('cre.aprobacion', [
                'propuesta_id' => $propuesta_id,
                'aprobacion_id' => $aprobacion_id,
                'agencia_id' => $agencia_id
            ]);
        }
    }

    public function exportar(Request $request)
    {
        $datos_cliente = json_decode($request->datos_cliente);
        $datos_pariente = json_decode($request->datos_pariente);
        $datos_aval = json_decode($request->datos_aval);
        $datos_pariente_aval = json_decode($request->datos_pariente_aval);
        $datos_negocio = json_decode($request->datos_negocio);
        $datos_propuesta = json_decode($request->datos_propuesta);
        $datos_aprobacion = json_decode($request->datos_aprobacion);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("./report_templates/creditos/reportes/rptFichaCredito.xlsx");
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptFichaCredito.xlsx");

        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos-----------------------------

        // DATOS HEADER

        if (intval($datos_negocio->ciiu_codigo) == 9980) {
            $sheet->setCellValue("B2", 'FICHA DE CRÉDITO(CONVENIO) - APROBACIÓN');
        } else {
            $sheet->setCellValue("B2", 'FICHA DE CRÉDITO - APROBACIÓN');
        }

        $sheet->setCellValue("H2", $datos_aprobacion->monto);
        $sheet->setCellValue("J2", $datos_aprobacion->fecha_aprobacion);

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

        // DATOS APROBACIÓN

        if ($datos_aprobacion->pago_oficina == 1) {
            $sheet->setCellValue("J39", 'OFICINA');
        } else {
            $sheet->setCellValue("J39", 'NEGOCIO');
        }
        $sheet->setCellValue("C40", $datos_aprobacion->comentario_aprobacion);
        $sheet->setCellValue("C41", $datos_aprobacion->usuario_promotor);
        $sheet->setCellValue("E41", $datos_aprobacion->sector);
        $sheet->setCellValue("H41", $datos_cliente->usuario_asesor);
        $sheet->setCellValue("C42", $datos_aprobacion->producto);

        if ($datos_aprobacion->subproducto != null) {
            $sheet->setCellValue("H42", $datos_propuesta->subproducto);
        }

        if ($datos_aprobacion->es_especial) {
            $sheet->setCellValue("J41", 'ESPECIAL');
        }

        $sheet->setCellValue("C43", $datos_aprobacion->tipo);
        $sheet->setCellValue("E43", $datos_aprobacion->monto);
        $sheet->setCellValue("H43", round($datos_aprobacion->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($datos_aprobacion->periodo_pago));
        $sheet->setCellValue("J43", $datos_aprobacion->cuota);
        $sheet->setCellValue("C44", $datos_aprobacion->usuario_registro);
        $sheet->setCellValue("E44", $datos_aprobacion->tasa_interes / 100);

        if ($datos_aprobacion->considerado_uno == 1) {
            $sheet->setCellValue("H44", 'SI');
        } else {
            $sheet->setCellValue("H44", 'NO');
        }

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

        $estado = Estado::on($conexion)->select('id')->where('estado', 'APROBADO')->get()->last();
        $estado_id = $estado->id;

        return Aprobacion::on($conexion)->from('credito_aprobaciones as cre_apr')
            ->select(
                'cre_apr.id',
                'cre_apr.id as aprobacion_id',
                'cre_apr.propuesta_id',
                'cre_apr.monto',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.comisiones',
                'cre_apr.datos_creacion',

                'cre_pro.agencia_id',
                'cre_pro.cliente_id',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.codigo_expediente',

                'age.nombre as agencia',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_registro'

            )->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('cliente_registros as cli_reg', 'cre_pro.cliente_id', 'cli_reg.id')
            ->join('solucion_master.agencias as age', 'cre_pro.agencia_id', 'age.id_agencia')
            ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->join('solucion_master.usuarios as us_2', DB::raw("SUBSTRING(cre_apr.datos_creacion,42,8)"), 'us_2.dni')
            ->join('credito_estados as cre_est', 'cre_apr.estado_id', 'cre_est.id')
            ->where([
                ['cre_apr.estado_id', $estado_id],
                [DB::raw("SUBSTR(cre_apr.datos_creacion,11,10)"), $fecha_aplicacion]
            ])
            ->get();
    }
    public function listar_detallado(Request $request)
    {

        $agencia_id = (new CreditosController)->verificar_nulo($request->agencia_id);
        $fecha_aplicacion = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        if ($agencia_id == null) {
            return [];
        }

        $conexion = 'master_' .  $agencia_id;

        $estado = Estado::on($conexion)->select('id')->where('estado', 'APROBADO')->get()->last();
        $estado_id = $estado->id;

        $aprobaciones = Aprobacion::on($conexion)->from('credito_aprobaciones as cre_apr')
            ->select(
                'cre_apr.id',
                'cre_apr.id as aprobacion_id',
                'cre_apr.propuesta_id',
                'cre_apr.monto',
                'cre_apr.cuota',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.tasa_interes',
                'cre_apr.con_dias_gracia',
                'cre_apr.dias_gracia_ci',
                'cre_apr.dias_gracia_si',
                'cre_apr.codigo_seguimiento',
                'cre_apr.codigo_seguimiento_2',
                'cre_apr.datos_creacion',

                'cre_pro.agencia_id',
                'cre_pro.cliente_id',
                'cre_pro.agencia_pariente',
                'cre_pro.pariente_id',
                'cre_pro.agencia_aval',
                'cre_pro.aval_id',
                'cre_pro.agencia_pariente_aval',
                'cre_pro.pariente_aval_id',

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.codigo_expediente',
                'cli_reg.codigo_expediente_2',
                'cli_reg.direccion',

                'dis.distrito',
                'pro.provincia',
                'dep.departamento',


                DB::raw("null as apellido_paterno_pariente"),
                DB::raw("null as apellido_materno_pariente"),
                DB::raw("null as nombres_pariente"),
                DB::raw("null as dni_pariente"),
                DB::raw("null as direccion_pariente"),

                DB::raw("null as apellido_paterno_aval"),
                DB::raw("null as apellido_materno_aval"),
                DB::raw("null as nombres_aval"),
                DB::raw("null as dni_aval"),
                DB::raw("null as direccion_aval"),

                DB::raw("null as apellido_paterno_pariente_aval"),
                DB::raw("null as apellido_materno_pariente_aval"),
                DB::raw("null as nombres_pariente_aval"),
                DB::raw("null as dni_pariente_aval"),
                DB::raw("null as direccion_pariente_aval"),

                'age.nombre as agencia',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_registro',

                'cre_tip.tipo',
                'cre_prod.producto'
            )->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('cliente_registros as cli_reg', 'cre_pro.cliente_id', 'cli_reg.id')
            ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
            ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id', 'pro.id')
            ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
            ->join('solucion_master.agencias as age', 'cre_pro.agencia_id', 'age.id_agencia')
            ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->join('solucion_master.usuarios as us_2', DB::raw("SUBSTR(cre_apr.datos_creacion,42,8)"), 'us_2.dni')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('credito_productos as cre_prod', 'cre_apr.producto_id', 'cre_prod.id')
            ->join('credito_estados as cre_est', 'cre_apr.estado_id', 'cre_est.id')
            ->where([
                ['cre_apr.estado_id', $estado_id],
                [DB::raw("SUBSTR(cre_apr.datos_creacion,11,10)"), $fecha_aplicacion]
            ])
            ->get();

        $fechas_vencimiento = [];

        foreach ($aprobaciones as $item) {

            if ($item->pariente_id != null && $item->agencia_pariente != null) {
                if (in_array($item->agencia_pariente, [2, 3, 5])) {
                    $conexion_pariente = 'master_' . $item->agencia_pariente;

                    $datos_cliente = Cliente::on($conexion_pariente)
                        ->select(
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres',
                            'dni',
                            'direccion'
                        )
                        ->where('id', $item->pariente_id)
                        ->get()->last();
                } else if (in_array($item->agencia_pariente, [1, 4, 6])) {

                    $params =
                        [
                            'agencia_id' => $item->agencia_pariente,
                            'cliente_id' => $item->pariente_id
                        ];

                    $response = Http::get(API_APR_URL . "/api/cli/listado_externa/datos_cliente", $params);


                    if ($response->successful()) {

                        $response = $response->json();
                        if ($response['datos_cliente']) {
                            $datos_cliente = (new Cliente)->newInstance($response['datos_cliente'], true);
                        } else {
                            $datos_cliente = null;
                        }
                    } else {
                        $datos_cliente = null;
                    }
                }

                if ($datos_cliente != null) {
                    $item->apellido_paterno_pariente = $datos_cliente->apellido_paterno;
                    $item->apellido_materno_pariente = $datos_cliente->apellido_materno;
                    $item->nombres_pariente = $datos_cliente->nombres;
                    $item->dni_pariente = $datos_cliente->dni;
                    $item->direccion_pariente = $datos_cliente->direccion;
                }
            }

            if ($item->aval_id != null && $item->agencia_aval != null) {

                if (in_array($item->agencia_aval, [2, 3, 5])) {
                    $conexion_aval = 'master_' .  $item->agencia_aval;

                    $datos_cliente = Cliente::on($conexion_aval)
                        ->select(
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres',
                            'dni',
                            'direccion'
                        )
                        ->where('id',  $item->aval_id)->get()->last();
                } else if (in_array($item->agencia_aval, [1, 4, 6])) {
                    $params =
                        [
                            'agencia_id' => $item->agencia_aval,
                            'cliente_id' => $item->aval_id
                        ];

                    $response = Http::get(API_APR_URL . "/api/cli/listado_externa/datos_cliente", $params);

                    if ($response->successful()) {

                        $response = $response->json();
                        if ($response['datos_cliente']) {
                            $datos_cliente = (new Cliente)->newInstance($response['datos_cliente'], true);
                        } else {
                            $datos_cliente = null;
                        }
                    } else {
                        $datos_cliente = null;
                    }
                }

                if ($datos_cliente != null) {
                    $item->apellido_paterno_aval = $datos_cliente->apellido_paterno;
                    $item->apellido_materno_aval = $datos_cliente->apellido_materno;
                    $item->nombres_aval = $datos_cliente->nombres;
                    $item->dni_aval = $datos_cliente->dni;
                    $item->direccion_aval = $datos_cliente->direccion;
                }
            }

            if ($item->pariente_aval_id != null && $item->agencia_pariente_aval != null) {

                if (in_array($item->agencia_pariente_aval, [2, 3, 5])) {
                    $conexion_pariente_aval = 'master_' .  $item->agencia_pariente_aval;
                    $datos_cliente = Cliente::on($conexion_pariente_aval)
                        ->select(
                            'apellido_paterno',
                            'apellido_materno',
                            'nombres',
                            'dni',
                            'direccion'
                        )
                        ->where('id',  $item->pariente_aval_id)->get()->last();
                } else if (in_array($item->agencia_pariente_aval, [1, 4, 6])) {
                    $params =
                        [
                            'agencia_id' => $item->agencia_pariente_aval,
                            'cliente_id' => $item->pariente_aval_id
                        ];

                    $response = Http::get(API_APR_URL . "/api/cli/listado_externa/datos_cliente", $params);

                    if ($response->successful()) {

                        $response = $response->json();
                        if ($response['datos_cliente']) {
                            $datos_cliente = (new Cliente)->newInstance($response['datos_cliente'], true);
                        } else {
                            $datos_cliente = null;
                        }
                    } else {
                        $datos_cliente = null;
                    }
                }


                if ($datos_cliente != null) {
                    $item->apellido_paterno_pariente_aval = $datos_cliente->apellido_paterno;
                    $item->apellido_materno_pariente_aval = $datos_cliente->apellido_materno;
                    $item->nombres_pariente_aval = $datos_cliente->nombres;
                    $item->dni_pariente_aval = $datos_cliente->dni;
                    $item->direccion_pariente_aval = $datos_cliente->direccion;
                }
            }


            $objetoRequest = new \Illuminate\Http\Request();
            $objetoRequest->setMethod('POST');
            $objetoRequest->request->add([
                'agencia_id' => $agencia_id,
                'plazo' => $item->plazo,
                'monto' => $item->monto,
                'tasa_interes' => $item->tasa_interes,
                'periodo_pago' => $item->periodo_pago,
                'dias_gracia' => $item->dias_gracia,
                'gracia_con_interes' => $item->con_interes_gracia,
                'fecha_desembolso' => $item->fecha_aprobacion,
            ]);

            $cronograma = (new CreditosController)->cronograma_sin_redondeo($objetoRequest);

            $object = (object)[
                'aprobacion_id' => $item->aprobacion_id,
                'fecha_vencimiento' => end($cronograma['datos_calendario'])->fecha_pago,
            ];

            $fechas_vencimiento[] = $object;
        }

        return [
            'aprobaciones' => $aprobaciones,
            'fechas_vencimiento' => $fechas_vencimiento
        ];
    }

    public function generar_expediente($agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $ultimo_expediente = Cliente::on($conexion)->max('numero_expediente');

        if ($ultimo_expediente == null) {
            $ultimo_expediente = 0;
        }

        $numero_expediente = intval($ultimo_expediente) + 1;

        $agencia = Agencia::select('nombre')->where('id_agencia', $agencia_id)->get()->last();

        switch ($agencia->nombre) {

            case 'ADMINISTRATIVA':
                $abreviacion = 'CADM';
                break;
            case 'EL TAMBO':
                $abreviacion = 'CTMB';
                break;
            case 'HUANCAYO':
                $abreviacion = 'CHYO';
                break;
            case 'CHILCA':
                $abreviacion = 'CCHI';
                break;
            case 'HUANCAVELICA':
                $abreviacion = 'CHVC';
                break;
            case 'PAMPAS':
                $abreviacion = 'CPAM';
                break;
        }

        $codigo_expediente = $abreviacion . (str_pad(strval(intval($numero_expediente)), 5, "0", STR_PAD_LEFT));

        return [
            'numero_expediente' => $numero_expediente,
            'codigo_expediente' => $codigo_expediente
        ];
    }

    public function generar_expediente_2($agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $ultimo_expediente_2 = Cliente::on($conexion)->max('numero_expediente_2');

        if ($ultimo_expediente_2 == null) {
            $ultimo_expediente_2 = 0;
        }

        $numero_expediente_2 = intval($ultimo_expediente_2) + 1;

        $agencia = Agencia::select('nombre')->where('id_agencia', $agencia_id)->get()->last();

        switch ($agencia->nombre) {

            case 'ADMINISTRATIVA':
                $abreviacion = 'CADM';
                break;
            case 'EL TAMBO':
                $abreviacion = 'CTMB';
                break;
            case 'HUANCAYO':
                $abreviacion = 'CHYO';
                break;
            case 'CHILCA':
                $abreviacion = 'CCHI';
                break;
            case 'HUANCAVELICA':
                $abreviacion = 'CHVC';
                break;
            case 'PAMPAS':
                $abreviacion = 'CPAM';
                break;
        }

        $codigo_expediente_2 = $abreviacion . (str_pad(strval(intval($numero_expediente_2)), 5, "0", STR_PAD_LEFT));

        return [
            'numero_expediente_2' => $numero_expediente_2,
            'codigo_expediente_2' => $codigo_expediente_2
        ];
    }

    public function generar_numero_credito($cliente_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $ultimo_credito = Credito::on($conexion)->where('cliente_id', $cliente_id)->count();

        $numero_credito = intval($ultimo_credito) + 1;

        return  $numero_credito;
    }

    public function generar_numero_credito_2($cliente_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $ultimo_credito_2 = Credito::on($conexion)->where(
            [
                ['cliente_id', $cliente_id],
                ['nueva_empresa', 1]
            ]
        )->count();

        $numero_credito_2 = intval($ultimo_credito_2) + 1;

        return  $numero_credito_2;
    }

    public function generar_codigo_seguimiento($agencia_id, $expediente, $numero_credito, $convenio)
    {
        $codigo_agencia = 'A' . $agencia_id;
        $numero_credito_formato = (str_pad(strval(intval($numero_credito)), 4, "0", STR_PAD_LEFT));

        if ($convenio) {
            $codigo_seguimiento = $codigo_agencia . '-CONV-' . $expediente . '-' . $numero_credito_formato;
        } else {
            $codigo_seguimiento = $codigo_agencia . '-' . $expediente . '-' . $numero_credito_formato;
        }

        return $codigo_seguimiento;
    }

    public function riesgo_crediticio(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        if ($agencia_id == 0 || $agencia_id == null) {
            return [];
        }

        $estado = Estado::on($conexion)->select('id')->where('estado', 'APROBADO')->get()->last();
        $estado_id = $estado->id;

        return  Aprobacion::on($conexion)->from('credito_aprobaciones as cre_apr')
            ->select(
                'cre_apr.id',
                'cre_apr.propuesta_id',
                'cre_apr.monto',
                'cre_apr.tasa_interes',
                'cre_apr.cuota',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.comentario_aprobacion',
                'cre_apr.fecha_aprobacion',
                'cre_apr.comisiones',
                'cre_apr.datos_creacion',

                'cre_pro.agencia_id',
                'cre_pro.cliente_id',
                'cre_pro.garantia_id',
                'cre_pro.comentario_garantia',
                'cre_pro.valor_garantia',
                'cre_pro.comentario_propuesta',

                'cli_reg.dni',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.codigo_expediente',
                'cli_reg.imagen_dni',
                'cli_reg.codigo_expediente',

                'us_1.usuario as usuario_asesor',
                'us_2.usuario as usuario_registro',

                'cre_gar.garantia'

            )
            ->join('credito_propuestas as cre_pro', 'cre_apr.propuesta_id', 'cre_pro.id')
            ->join('cliente_registros as cli_reg', 'cre_pro.cliente_id', 'cli_reg.id')
            ->join('solucion_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->join('solucion_master.usuarios as us_2', DB::raw("SUBSTR(cre_apr.datos_creacion,42,8)"), 'us_2.dni')
            ->join('credito_garantias as cre_gar', 'cre_pro.garantia_id', 'cre_gar.id')
            ->where('cre_apr.estado_id', $estado_id)
            ->get();
    }

    public function actualizar_comision(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $aprobacion_id = $request->aprobacion_id;
        $comisiones = $request->comisiones;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        return Aprobacion::on($conexion)->where('id', $aprobacion_id)
            ->update([
                'comisiones' => $comisiones,
                'datos_actualizacion' => $datos_registro
            ]);
    }

    public function anular_aprobacion(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $aprobacion_id = $request->aprobacion_id;
        $comentario_anulacion = $request->comentario_anulacion;

        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $estado_anulado = Estado::on($conexion)->where('estado', 'ANULADO')
            ->get()->last();
        $estado_anulado_id = $estado_anulado->id;

        $prendario = Producto::on($conexion)->where('producto', 'CRÉDITO PRENDARIO')
            ->get()->last();
        $prendario_id = $prendario->id;

        $aprobacion = Aprobacion::on($conexion)->find($aprobacion_id);

        // Para liberar prendas en caso sea un CRÉDITO PRENDARIO
        if ($aprobacion->producto_id  == $prendario_id) {

            $propuesta_id = $aprobacion->propuesta_id;
            $propuesta = Propuesta::on($conexion)->find($propuesta_id);

            $prendas = json_decode($propuesta->prendas);

            foreach ($prendas as $item) {
                $prenda = Prenda::on($conexion)->find($item);
                $prenda->disponible = 1;
                $prenda->save();
            }
        }
        // ----------------------------------------------------------------

        $aprobacion->estado_id = $estado_anulado_id;
        $aprobacion->comentario_anulacion = $comentario_anulacion;
        $aprobacion->datos_actualizacion = $datos_registro;
        $aprobacion->save();

        return redirect()->back();
    }

    public function anular_aprobacion_todos(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $comentario_anulacion = $request->comentario_anulacion;

        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $estado_aprobado = Estado::on($conexion)->where('estado', 'APROBADO')
            ->get()->last();
        $estado_aprobado_id = $estado_aprobado->id;

        $estado_anulado = Estado::on($conexion)->where('estado', 'ANULADO')
            ->get()->last();
        $estado_anulado_id = $estado_anulado->id;

        $prendario = Producto::on($conexion)->where('producto', 'CRÉDITO PRENDARIO')
            ->get()->last();
        $prendario_id = $prendario->id;

        $aprobaciones = Aprobacion::on($conexion)->where(['estado_id' => $estado_aprobado_id])->get();

        foreach ($aprobaciones as $item) {

            // Para liberar prendas en caso sea un CRÉDITO PRENDARIO
            if ($item->producto_id  == $prendario_id) {

                $propuesta_id = $item->propuesta_id;
                $propuesta = Propuesta::on($conexion)->find($propuesta_id);

                $prendas = json_decode($propuesta->prendas);

                foreach ($prendas as $item_1) {
                    $prenda = Prenda::on($conexion)->find($item_1);
                    $prenda->disponible = 1;
                    $prenda->save();
                }
            }
            // ----------------------------------------------------------------

            // $aprobacion = Aprobacion::on($conexion)->find($item->id);

            $item->estado_id = $estado_anulado_id;
            $item->comentario_anulacion = $comentario_anulacion;
            $item->datos_actualizacion = $datos_registro;
            $item->save();
        }

        return redirect()->back();
    }
}
