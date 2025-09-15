<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\General\Cargo;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Clientes\Pariente;
use App\Models\Creditos\Clientes\Aval;
use App\Models\Creditos\Clientes\Negocio;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Credito\Compromiso;
use App\Models\Creditos\Credito\Notificacion;
use App\Models\Creditos\Credito\NotificacionTipo;
use App\Models\Creditos\Caja\Desembolso;

use App\Models\Creditos\Clientes\Cliente;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Monolog\Handler\IFTTTHandler;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

use App\Models\General\Agencia;

use Illuminate\Support\Facades\Http;

define('API_RDM_URL',  getenv('VITE_S_API_EXTERNA'));


class ReporteDiasMoraController extends Controller
{

    public function dias_mora($modo)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = 0;
            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_DIAS_MORA', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CREDITOS_MIS_DIAS_MORA', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'GERENTE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ])->get();

                if ($modo == 'completo') {

                    $asesores = Usuario::select('usuario', 'dni', 'agencia_id')
                        ->where('habilitado', 1)
                        ->whereIn('cargo_id', $cargos)
                        ->orderby('usuario', 'asc')
                        ->get();
                } else if ($modo == 'personal') {

                    $asesores = Usuario::select('usuario', 'dni', 'agencia_id')
                        ->where([['habilitado', 1], ['usuario_real', 0]])
                        ->whereIn('cargo_id', $cargos)
                        ->orWhere('dni', session('usuario_dni'))
                        ->orderby('usuario', 'asc')
                        ->get();
                }

                $usuarios = Usuario::select('usuario', 'dni', 'agencia_id')
                    ->where('habilitado', 1)
                    ->get();

                return Inertia::render('Creditos/Reportes/Creditos/dias_mora', [
                    'modo' => $modo,
                    'asesores' => $asesores,
                    'usuarios' => $usuarios
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar(Request $request)
    {
        $desde_dias = $request->desde_dias;
        $hasta_dias = $request->hasta_dias;
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $asesor_id = $request->asesor_id;
        $operador = '>';
        if ($asesor_id != 0) {
            $operador = '=';
        }

        $tipo_orden = $request->tipo_orden;

        $forma = '';
        $columna = '';
        if ($tipo_orden == 'dias_atraso') {
            $forma = 'desc';
            $columna = 'cre_reg.dias_atraso';
        } elseif ($tipo_orden == 'fecha_desembolso') {
            $forma = 'asc';
            $columna = 'cre_reg.id';
        }

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $lista_creditos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.cliente_id',
                'cre_reg.capital_total',
                'cre_reg.cuotas_pendientes',
                'cre_reg.cuotas_vencidas',
                'cre_reg.monto_vencido',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.saldo_total',
                'cre_reg.fecha_vencimiento',
                'cre_reg.capital_pagado',
                'cre_reg.dias_atraso',
                'cre_reg.fecha_ultimo_pago',
                'cre_reg.fecha_desembolso',

                DB::raw("CONCAT(cli_reg.numero_expediente,' - ',cre_apr.numero_credito) as expediente"),
                DB::raw("CONCAT(cli_reg.apellido_paterno,' ',cli_reg.apellido_materno,' ',cli_reg.nombres) as cliente"),
                'cli_reg.direccion',
                'cli_reg.telefonos',

                'cre_apr.numero_credito',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.cuota',
                'cre_apr.considerado_uno',

                'us_1.usuario as usuario_asesor',

                'cre_tip.tipo',

                'caj_des.modo_desembolso'
            )
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credipyme_master.usuarios as us_1', 'cli_reg.asesor_id', 'us_1.dni')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
            ->whereBetween('cre_reg.dias_atraso', [$desde_dias, $hasta_dias])
            ->where([
                ['cli_reg.asesor_id', $operador, $asesor_id],
                ['cre_reg.estado_id', $estado_id],
            ])
            ->orderBy($columna, $forma)
            ->get();

        $lista_creditos = $lista_creditos->map(function ($row, $index) use ($conexion) {

            $expediente = $row->expediente . ($row->considerado_uno ? " - 1" : "");
            $plazo = round($row->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($row->periodo_pago);

            if ($row->periodo_pago == 'PAGO_UNICO') {
                $plazo .= ' - PU';
            }

            $dias_atraso = $row->dias_atraso . ' d';
            $saldo_capital = round($row->capital_total - $row->capital_pagado, 2);

            return [
                'index' => $index,
                'id' => $row->id,
                'cliente_id' => $row->cliente_id,
                'capital_total' => $row->capital_total,
                'cuotas_pendientes' => $row->cuotas_pendientes,
                'cuotas_vencidas' => $row->cuotas_vencidas,
                'monto_vencido' => $row->monto_vencido,
                'mora_total' => $row->mora_total,
                'mora_pagado' => $row->mora_pagado,
                'saldo_total' => $row->saldo_total,
                'fecha_vencimiento' => $row->fecha_vencimiento,
                'capital_pagado' => $row->capital_pagado,
                'fecha_ultimo_pago' => $row->fecha_ultimo_pago,
                'fecha_desembolso' => $row->fecha_desembolso,
                'modo_desembolso' => $row->modo_desembolso,
                'cliente' => $row->cliente,
                'direccion' => $row->direccion,
                'telefonos' => $row->telefonos,
                'numero_credito' => $row->numero_credito,
                'periodo_pago' => $row->periodo_pago,
                'cuota' => $row->cuota,
                'usuario_asesor' => $row->usuario_asesor,
                'tipo' => $row->tipo,
                'expediente' => $expediente,
                'plazo' => $plazo,
                'dias_atraso' => $dias_atraso,
                'saldo_capital' => $saldo_capital
            ];
        });

        return ['lista_creditos' => $lista_creditos];
    }

    public function detalle($credito_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        $datos_credito = Credito::on($conexion)
            ->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.agencia_id',
                'cre_reg.datos_creacion',
                'cre_reg.dias_atraso',
                'cre_reg.cuota_actual',
                'cre_reg.cuotas_pendientes',
                'cre_reg.capital_total',
                'cre_reg.capital_pagado',
                'cre_reg.interes_total',
                'cre_reg.interes_pagado',
                'cre_reg.redondeo_total',
                'cre_reg.redondeo_pagado',
                'cre_reg.mora_total',
                'cre_reg.mora_pagado',
                'cre_reg.notificaciones_total',
                'cre_reg.notificaciones_pagado',
                'cre_reg.saldo_total',
                'cre_reg.cuotas_vencidas',
                'cre_reg.monto_vencido',
                'cre_reg.acumulado',
                'cre_reg.estado_id',
                'cre_reg.fecha_ultimo_pago',
                'cre_reg.fecha_vencimiento',
                'cre_reg.fecha_hora_cancelado',

                'cre_apr.monto',
                'cre_apr.cuota',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.pago_oficina',
                'cre_apr.tasa_interes',
                'cre_apr.numero_credito',
                'cre_apr.codigo_seguimiento',

                'cre_prop.agencia_pariente',
                'cre_prop.pariente_id',
                'cre_prop.agencia_aval',
                'cre_prop.aval_id',
                'cre_prop.negocio_id',


                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.dni',
                'cli_reg.fecha_nacimiento',
                'cli_reg.estado_civil',
                'cli_reg.sexo',
                'cli_reg.correo_electronico',
                'cli_reg.codigo_expediente',
                'cli_reg.central_riesgo',
                'cli_reg.calificacion',
                'cli_reg.monto_maximo',
                'cli_reg.notas',
                'cli_reg.direccion',
                'cli_reg.referencia_direccion',
                'cli_reg.asesor_id',
                'cli_reg.telefonos',

                'caj_des.modo_desembolso',

                'dep.departamento',
                'pro.provincia',
                'dis.distrito',

                'cre_sec.sector',
                'cre_pro.producto',
                'cre_tip.tipo',
                'cre_est.estado',

                'usu.usuario as usuario_asesor'
            )
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id', 'cre_apr.id')
            ->join('credito_propuestas as cre_prop', 'cre_apr.propuesta_id', 'cre_prop.id')
            ->join('credito_sectores as cre_sec', 'cre_apr.sector_id', 'cre_sec.id')
            ->join('credito_productos as cre_pro', 'cre_apr.producto_id', 'cre_pro.id')
            ->join('credito_tipos as cre_tip', 'cre_apr.tipo_id', 'cre_tip.id')
            ->join('credito_estados as cre_est', 'cre_reg.estado_id', 'cre_est.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('caja_desembolsos as caj_des', 'cre_reg.id', 'caj_des.credito_id')
            ->join('credipyme_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
            ->join('credipyme_master.provincias as pro', 'cli_reg.provincia_id', 'pro.id')
            ->join('credipyme_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
            ->join('credipyme_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
            ->where('cre_reg.id', $credito_id)
            ->get()->last();

        $pariente = null;
        $aval = null;

        if ($datos_credito->pariente_id != null) {

            if (in_array($datos_credito->agencia_pariente, [2, 3, 5])) {
                $conexion_pariente = 'master_' .  $datos_credito->agencia_pariente;

                $pariente = Cliente::on($conexion_pariente)->from('cliente_registros as cli_reg')
                    ->select(
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cli_reg.dni',
                        'cli_reg.fecha_nacimiento',
                        'cli_reg.estado_civil',
                        'cli_reg.sexo',
                        'cli_reg.correo_electronico',
                        'cli_reg.codigo_expediente',
                        'cli_reg.central_riesgo',
                        'cli_reg.calificacion',
                        'cli_reg.monto_maximo',
                        'cli_reg.notas',
                        'cli_reg.direccion',
                        'cli_reg.referencia_direccion',
                        'cli_reg.asesor_id',
                        'cli_reg.telefonos',

                        'dep.departamento',
                        'pro.provincia',
                        'dis.distrito',

                        'usu.usuario as usuario_asesor'
                    )

                    ->join('credipyme_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
                    ->join('credipyme_master.provincias as pro', 'cli_reg.provincia_id', 'pro.id')
                    ->join('credipyme_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
                    ->join('credipyme_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
                    ->where('cli_reg.id', $datos_credito->pariente_id)
                    ->get()->last();
            } else if (in_array($datos_credito->agencia_pariente, [1, 4, 6])) {

                $params =
                    [
                        'agencia_id' => $datos_credito->agencia_pariente,
                        'cliente_id' => $datos_credito->pariente_id
                    ];

                $response = Http::get(API_RDM_URL . "/api/cli/listado_externa/datos_cliente", $params);


                if ($response->successful()) {

                    $response = $response->json();

                    $pariente = (new Cliente)->newInstance($response['datos_cliente'], true);
                } else {
                    $pariente = null;
                }
            }
        }

        if ($datos_credito->aval_id != null) {

            if (in_array($datos_credito->agencia_pariente, [2, 3, 5])) {
                $conexion_aval = 'master_' .  $datos_credito->agencia_aval;

                $aval = Cliente::on($conexion_aval)->from('cliente_registros as cli_reg')
                    ->select(
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cli_reg.dni',
                        'cli_reg.fecha_nacimiento',
                        'cli_reg.estado_civil',
                        'cli_reg.sexo',
                        'cli_reg.correo_electronico',
                        'cli_reg.codigo_expediente',
                        'cli_reg.central_riesgo',
                        'cli_reg.calificacion',
                        'cli_reg.monto_maximo',
                        'cli_reg.notas',
                        'cli_reg.direccion',
                        'cli_reg.referencia_direccion',
                        'cli_reg.asesor_id',
                        'cli_reg.telefonos',

                        'dep.departamento',
                        'pro.provincia',
                        'dis.distrito',

                        'usu.usuario as usuario_asesor'
                    )

                    ->join('credipyme_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
                    ->join('credipyme_master.provincias as pro', 'cli_reg.provincia_id', 'pro.id')
                    ->join('credipyme_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
                    ->join('credipyme_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
                    ->where('cli_reg.id', $datos_credito->aval_id)
                    ->get()->last();
            } else if (in_array($datos_credito->agencia_aval, [1, 4, 6])) {

                $params =
                    [
                        'agencia_id' => $datos_credito->agencia_aval,
                        'cliente_id' => $datos_credito->aval_id
                    ];

                $response = Http::get(API_RDM_URL . "/api/cli/listado_externa/datos_cliente", $params);


                if ($response->successful()) {

                    $response = $response->json();

                    $aval = (new Cliente)->newInstance($response['datos_cliente'], true);
                } else {
                    $aval = null;
                }
            }
        }


        $informacion = [$datos_credito, $pariente, $aval];
        $informacion_tabla = [];

        $i = 0;
        foreach ($informacion as $item) {

            if ($item != null) {
                $informacion_tabla[$i] = (array)[
                    ['propiedad' => 'Nombres', 'valor' => $item->nombres],
                    ['propiedad' => 'Ap. Paterno', 'valor' => $item->apellido_paterno],
                    ['propiedad' => 'Ap. Materno', 'valor' => $item->apellido_materno],
                    ['propiedad' => 'DNI', 'valor' => $item->dni],
                    ['propiedad' => 'Fecha nac.', 'valor' => $item->fecha_nacimiento],
                    ['propiedad' => 'Estado civil', 'valor' => $item->estado_civil],
                    ['propiedad' => 'Sexo', 'valor' => $item->sexo],
                    ['propiedad' => 'Correo', 'valor' => $item->correo_electronico],
                    ['propiedad' => 'Expediente', 'valor' => $item->codigo_expediente],
                    ['propiedad' => 'Asesor', 'valor' => $item->usuario_asesor],
                    ['propiedad' => 'C. riesgo', 'valor' => $item->central_riesgo],
                    ['propiedad' => 'Calificación', 'valor' => $item->calificacion],
                    ['propiedad' => 'Monto máximo', 'valor' => $item->monto_maximo],
                    ['propiedad' => 'Notas', 'valor' => $item->notas],
                    ['propiedad' => 'Dirección', 'valor' => $item->direccion],
                    ['propiedad' => 'Ubicación', 'valor' => $item->distrito . ' ' . $item->provincia . ' ' . $item->departamento],
                    ['propiedad' => 'Referencia', 'valor' => $item->referencia_direccion],
                    ['propiedad' => 'Teléfono 1', 'valor' => json_decode($item->telefonos)->t1 . ' - ' . json_decode($item->telefonos)->o1],
                    ['propiedad' => 'Notas de Teléfono 1', 'valor' => json_decode($item->telefonos)->n1],
                    ['propiedad' => 'Teléfono 2', 'valor' => json_decode($item->telefonos)->t2 . ' - ' . json_decode($item->telefonos)->o2],
                    ['propiedad' => 'Notas de Teléfono 2', 'valor' => json_decode($item->telefonos)->n2],
                    ['propiedad' => 'Teléfono 3', 'valor' => json_decode($item->telefonos)->t3 . ' - ' . json_decode($item->telefonos)->o3],
                    ['propiedad' => 'Notas de Teléfono 3', 'valor' => json_decode($item->telefonos)->n3],
                    ['propiedad' => 'Teléfono 4', 'valor' => json_decode($item->telefonos)->t4 . ' - ' . json_decode($item->telefonos)->o4],
                    ['propiedad' => 'Notas de Teléfono 4', 'valor' => json_decode($item->telefonos)->n4],
                ];
            } else {
                $informacion_tabla[$i] = [];
            }
            $i += 1;
        }

        $datos_titular = $informacion_tabla[0];
        $datos_pariente = $informacion_tabla[1];
        $datos_aval = $informacion_tabla[2];

        $negocio = Negocio::on($conexion)->from('cliente_negocios as cli_neg')
            ->select(
                'cli_neg.nombre',
                'cli_neg.actividad',
                'cli_neg.direccion',
                'cli_neg.referencia_direccion',
                'cli_neg.telefonos',

                'dep.departamento',
                'pro.provincia',
                'dis.distrito'
            )
            ->join('credipyme_master.departamentos as dep', 'cli_neg.departamento_id', 'dep.id')
            ->join('credipyme_master.provincias as pro', 'cli_neg.provincia_id', 'pro.id')
            ->join('credipyme_master.distritos as dis', 'cli_neg.distrito_id', 'dis.id')
            ->where('cli_neg.id', $datos_credito->negocio_id)
            ->get()->last();

        $datos_negocio = (array)[
            ['propiedad' => 'Nombre de negocio', 'valor' => $negocio->nombre],
            ['propiedad' => 'Actividad', 'valor' => $negocio->actividad],
            ['propiedad' => 'Dirección', 'valor' => $negocio->direccion],
            ['propiedad' => 'Ubicación', 'valor' => $negocio->distrito . ' ' . $negocio->provincia . ' ' . $negocio->departamento],
            ['propiedad' => 'Referencia', 'valor' => $negocio->referencia_direccion],
            ['propiedad' => 'Teléfono 1', 'valor' => json_decode($negocio->telefonos)->t1 . ' - ' . json_decode($negocio->telefonos)->o1],
            ['propiedad' => 'Notas de Teléfono 1', 'valor' => json_decode($negocio->telefonos)->n1],
            ['propiedad' => 'Teléfono 2', 'valor' => json_decode($negocio->telefonos)->t2 . ' - ' . json_decode($negocio->telefonos)->o2],
            ['propiedad' => 'Notas de Teléfono 2', 'valor' => json_decode($negocio->telefonos)->n2],
        ];

        $datos_cuotas = Cuota::on($conexion)->where('credito_id', $credito_id)->get();
        $compromisos = Compromiso::on($conexion)->from('credito_compromisos as cre_com')
            ->select(
                'cre_com.id',
                'cre_com.credito_id',
                'cre_com.compromiso',
                'cre_com.fecha_hora_visita',
                'cre_com.fecha_vencimiento',
                'cre_com.datos_creacion',

                'us_1.usuario as usuario_registro',
                'car.cargo'
            )
            ->where('cre_com.credito_id', $credito_id)
            ->join('credipyme_master.usuarios as us_1', DB::raw("SUBSTRING(cre_com.datos_creacion,42,8)"), 'us_1.dni')
            ->join('credipyme_master.cargos as car', 'us_1.cargo_id', 'car.id')
            ->orderby('cre_com.id', 'desc')
            ->get();

        $notificaciones = Notificacion::on($conexion)->from('credito_notificaciones as cre_not')
            ->select(
                'cre_not.id',
                'cre_not.credito_id',
                'cre_not.tipo_id',
                'cre_not.monto',
                'cre_not.acumulado',
                'cre_not.numero_cuota',
                'cre_not.usuario_envio',
                'cre_not.descripcion_envio',
                'cre_not.estado',
                'cre_not.datos_creacion',

                'cre_not_tip.tipo',
                'cre_not_tip.nombre_archivo',

                'us_1.usuario as usuario_envio_usuario',
                'us_2.usuario as usuario_registro'
            )
            ->join('credito_notificaciones_tipos as cre_not_tip', 'cre_not.tipo_id', 'cre_not_tip.id')
            ->leftjoin('credipyme_master.usuarios as us_1', 'cre_not.usuario_envio', 'us_1.dni')
            ->join('credipyme_master.usuarios as us_2', DB::raw("SUBSTRING(cre_not.datos_creacion,42,8)"), 'us_2.dni')
            ->where('cre_not.credito_id', $credito_id)
            ->orderby('cre_not.id', 'asc')
            ->get();

        $notificaciones_tipos = NotificacionTipo::on($conexion)
            ->where('habilitado', 1)->orderby('orden', 'asc')->get();

        return [
            'datos_credito' => $datos_credito,
            'datos_titular' => $datos_titular,
            'pariente' => $pariente,
            'datos_pariente' => $datos_pariente,
            'aval' => $aval,
            'datos_aval' => $datos_aval,
            'datos_negocio' => $datos_negocio,
            'datos_cuotas' => $datos_cuotas,
            'compromisos' => $compromisos,
            'notificaciones' => $notificaciones,
            'notificaciones_tipos' => $notificaciones_tipos
        ];
    }

    public function asignar_compromiso(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $frmDatosCompromiso = json_decode($request->frmDatosCompromiso);
        $credito_id = $frmDatosCompromiso->credito_id;
        $compromiso = mb_strtoupper($frmDatosCompromiso->compromiso);
        $fecha_hora_visita = $frmDatosCompromiso->fecha_visita . ' ' . $frmDatosCompromiso->hora_visita;
        $fecha_vencimiento = $frmDatosCompromiso->fecha_vencimiento;

        Compromiso::on($conexion)->create([
            'credito_id' => $credito_id,
            'compromiso' => $compromiso,
            'fecha_hora_visita' => $fecha_hora_visita,
            'fecha_vencimiento' => $fecha_vencimiento,
            'datos_creacion' => $datos_registro
        ]);

        return $this->detalle($credito_id, $agencia_id);
    }
    public function aplicar_notificacion(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $frmDatosNotificacion = json_decode($request->frmDatosNotificacion);
        $credito_id = $frmDatosNotificacion->credito_id;
        $numero_cuota = $frmDatosNotificacion->numero_cuota;
        $tipo_id = $frmDatosNotificacion->tipo_id;
        $monto = $frmDatosNotificacion->monto;

        Notificacion::on($conexion)->create([
            'credito_id' => $credito_id,
            'tipo_id' => $tipo_id,
            'monto' => $monto,
            'numero_cuota' => $numero_cuota,
            'estado' => 'P',
            'datos_creacion' => $datos_registro
        ]);

        Credito::on($conexion)->where('id', $credito_id)
            ->update([
                'saldo_total' => DB::raw("saldo_total+$monto"),
                'notificaciones_total' => DB::raw("notificaciones_total+$monto"),
                'datos_actualizacion' => $datos_registro
            ]);


        return $this->detalle($credito_id, $agencia_id);
    }
    public function envio_notificacion(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $frmEnvioNotificacion = json_decode($request->frmEnvioNotificacion);
        $credito_id = $frmEnvioNotificacion->credito_id;
        $notificacion_id = $frmEnvioNotificacion->notificacion_id;
        $usuario_envio = $frmEnvioNotificacion->usuario_envio;
        $descripcion_envio = (new CreditosController)->verificar_nulo($frmEnvioNotificacion->descripcion_envio);

        if ($descripcion_envio != null) {
            $descripcion_envio = mb_strtoupper($descripcion_envio);
        }

        Notificacion::on($conexion)->where('id', $notificacion_id)->update([
            'usuario_envio' => $usuario_envio,
            'descripcion_envio' => $descripcion_envio,
            'datos_actualizacion' => $datos_registro
        ]);

        return $this->detalle($credito_id, $agencia_id);
    }

    public function exportar(Request $request)
    {
        $creditos_filtrados = json_decode($request->creditos_filtrados);
        $modo = $request->modo;
        $tipo = $request->tipo;
        // $totales = json_decode($request->totales);

        // Ordenando array de datos-------------------------------
        $data = [];
        $totales = (object)[
            'cantidad_registros' => '0 registros',
            'capital_total' => 0,
            'total_saldo_total' => 0,
            'total_saldo_capital' => 0
        ];

        $orden = 1;
        foreach ($creditos_filtrados as $item) {

            $telefonos = null;
            if ($modo == 'con_telefonos') {
                $telefonos = json_decode($item->telefonos);

                $telefonos = $telefonos->t1 . ' - ' .
                    $telefonos->t2 . ' - ' .
                    $telefonos->t3;
            }

            $object = (object)[
                'orden' => $orden,
                'expediente' => $item->expediente,
                'credito_uno' => '-',
                'cliente' => $item->cliente,
                'dias_atraso' => $item->dias_atraso,
                'capital' => $item->capital_total,
                'plazo' => $item->plazo,
                'fecha_desembolso' => $item->fecha_desembolso,
                'cuota' => $item->cuota,
                'cuotas_pendientes' => $item->cuotas_pendientes,
                'cuotas_vencidas' => $item->cuotas_vencidas,
                'monto_vencido' => $item->monto_vencido,
                'saldo_total' => $item->saldo_total,
                'fecha_ultimo_pago' => $item->fecha_ultimo_pago,
                'saldo_capital' => $item->capital_total - $item->capital_pagado,
                'asesor' => $item->usuario_asesor,
                'direccion' => $item->direccion,
                'telefonos' => $telefonos,
            ];

            if ($orden == 1) {
                $totales->cantidad_registros = $orden . 'registro';
            } else {
                $totales->cantidad_registros = $orden . 'registros';
            }

            $totales->capital_total += $item->capital_total;
            $totales->total_saldo_total += $item->saldo_total;
            $totales->total_saldo_capital += $item->capital_total - $item->capital_pagado;

            $data[] = $object;
            $orden += 1;
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("./report_templates/creditos/reportes/rptDiasMora.xlsx");
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptDiasMora.xlsx");

        $sheet = $spreadsheet->getActiveSheet();

        if ($modo == 'sin_telefonos') {
            $sheet->removeColumnByIndex(19);
        }

        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 18) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda = $sheet->getStyle($columna_1 . 4)->exportArray();

            $formato_totales = $sheet->getStyle($columna_1 . 6)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        $sheet->removeRow(6);


        // Insertando datos-----------------------------
        $indice = 4;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas[$columna_2 - 1]);
                $columna_2 += 1;
            }

            $indice += 1;
        }

        $sheet->insertNewRowBefore($indice + 1);

        $indice += 1;

        $sheet->setCellValue('E' . $indice, $totales->cantidad_registros);
        $sheet->setCellValue('G' . $indice, $totales->capital_total);
        $sheet->setCellValue('N' . $indice, $totales->total_saldo_total);
        $sheet->setCellValue('P' . $indice, $totales->total_saldo_capital);

        foreach ($lista_formatos_totales as $key => $value) {
            $sheet->getStyle((new CreditosController)->num2char($key + 1) . $indice)->applyFromArray($value);
        }

        // Exportar para descarga-------------------------

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptDiasMora', 5);

        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');

        if ($tipo == 'XLSX') {

            $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';
            return ['path_xlsx' => $path_xlsx];
        } else if ($tipo == 'PDF') {

            $xlsxFile = public_path('temp_files/' . $nombre_archivo . '.xlsx');
            $pdfFile = public_path('temp_files/' . $nombre_archivo . '.pdf');

            // Use OpenOffice to convert XLSX to PDF
            $command = env('LIBREOFFICE') . " --headless --convert-to pdf $xlsxFile --outdir " . dirname($pdfFile);
            shell_exec($command);

            $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

            return ['path_pdf' => $path_pdf];
        }
    }
}
