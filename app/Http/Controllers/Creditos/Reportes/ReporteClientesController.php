<?php

namespace App\Http\Controllers\Creditos\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;
use App\Models\General\Agencia;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\General\Departamento;
use App\Models\General\Provincia;
use App\Models\General\Distrito;
use App\Models\General\Cargo;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Credito\Cuota;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Mantenimiento\Credito\Estado;
use App\Models\Creditos\Clientes\Visita;
use App\Models\Creditos\Clientes\Comentario;
use App\Models\General\Datos_aplicacion;
use ClienteComentarios;
use DOTNET;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ReporteClientesController extends Controller
{
    public function cumpleanios($modo)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_CUMPLEAÑOS', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_MIS_CUMPLEAÑOS', 'CREDITOS_REPORTES');
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

                return Inertia::render('Creditos/Reportes/Clientes/cumpleanios', [
                    'modo' => $modo,
                    'usuarios' => $usuarios,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }





    public function buscar(Request $request)
    {
        $modo = $request->modo;

        if ($modo == 'con_cumpleanios') {
            return $this->buscar_con_cumpleanios($request);
        }
        if ($modo == 'activos') {
            return $this->buscar_activos($request);
        }
        if ($modo == 'inactivos') {
            return $this->buscar_inactivos($request);
        }
        if ($modo == 'pago_hoy') {
            return $this->buscar_pago_hoy($request);
        }
        if ($modo == 'por_asesor') {
            return $this->buscar_por_asesor($request);
        }
    }
    public function buscar_activos($request)
    {



        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $asesor_id = $request->asesor_id;
        $calificacion = $request->calificacion;

        $por_asesor = $request->por_asesor;
        $por_calificacion = $request->por_calificacion;


        // dd($asesor_id, $calificacion);


        $estado_credito = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado_credito->id;

        if ($por_asesor == 'true'  && $por_calificacion == 'false') {

            $condition_1 =  [
                ['cli_reg.asesor_id', $asesor_id],
                ['cli_neg.vinculado', 1]

            ];
        } else if ($por_asesor == 'false' && $por_calificacion == 'true') {
            $condition_1 =  [
                ['cli_reg.calificacion', $calificacion],
                ['cli_neg.vinculado', 1]
            ];
        } else if ($por_asesor == 'true'   && $por_calificacion == 'true') {

            $condition_1 = [['cli_reg.asesor_id', $asesor_id], ['cli_reg.calificacion', $calificacion], ['cli_neg.vinculado', 1]];
        } else {
            $condition_1 = [['cli_neg.vinculado', 1]];
        }




        $estado_credito = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado_credito->id;


        $rango = Credito::on($conexion)
            ->select(
                'cliente_id'
            )
            ->distinct()
            ->where('estado_id', $estado_id)
            ->where('asesor_id', $asesor_id)
            ->get();



        $lista_clientes_activos = Cliente::on($conexion)->from('cliente_registros as cli_reg')
            ->select(
                'cli_reg.id',
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.central_riesgo',
                'cli_reg.direccion as direccion_domicilio',
                'cli_reg.calificacion',
                'cli_reg.notas',

                'usu.usuario',

                'dep.departamento',
                'pro.provincia',
                'dis.distrito',

                'cli_neg.nombre',
                'cli_neg.direccion as direccion_negocio',

                'dep_n.departamento as departamento_negocio',
                'pro_n.provincia as provincia_negocio',
                'dis_n.distrito as distrito_negocio'
            )

            ->leftjoin('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')

            ->join('solucion_master.departamentos as dep', 'dep.id', 'cli_reg.departamento_id')
            ->join('solucion_master.provincias as pro', 'pro.id', 'cli_reg.provincia_id')
            ->join('solucion_master.distritos as dis', 'dis.id', 'cli_reg.distrito_id')

            ->join('cliente_negocios as cli_neg', 'cli_neg.cliente_id', 'cli_reg.id')
            ->join('solucion_master.departamentos as dep_n', 'dep_n.id', 'cli_neg.departamento_id')
            ->join('solucion_master.provincias as pro_n', 'pro_n.id', 'cli_neg.provincia_id')
            ->join('solucion_master.distritos as dis_n', 'dis_n.id', 'cli_neg.distrito_id')
            ->whereIn('cli_reg.id', $rango)
            ->where($condition_1)
            ->orderBy('apellido_paterno', 'asc')
            ->get();



        // dd($lista_clientes_activos);

        return ['lista_clientes_activos' => $lista_clientes_activos];
    }
    public function buscar_inactivos($request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));
        $por_asesor = $request->por_asesor;
        $por_calificacion = $request->por_calificacion;
        $por_numero_credito = $request->por_numero_credito;

        $por_asesor = filter_var($request->por_asesor, FILTER_VALIDATE_BOOLEAN);
        $por_calificacion = filter_var($request->por_calificacion, FILTER_VALIDATE_BOOLEAN);
        $por_numero_credito = filter_var($request->por_numero_credito, FILTER_VALIDATE_BOOLEAN);

        $estados = Estado::on($conexion)->select('id')->whereIn('estado', [
            'CANCELADO TOTAL',
            'CANCELADO PARCIAL'
        ])->get();

        $estados_cancelados = [];

        foreach ($estados as $value) {
            $estados_cancelados[] = $value->id;
        }

        $estados_cancelados = json_encode($estados_cancelados);
        $estados_cancelados = str_replace("[", "(", $estados_cancelados);
        $estados_cancelados = str_replace("]", ")", $estados_cancelados);

        $rango = Credito::on($conexion)->from('credito_registros as cre_reg_1')
            ->select(
                'cre_reg_1.cliente_id',
                DB::raw("(SELECT COUNT(cre_reg_2.id) from credito_registros cre_reg_2
                where cre_reg_2.cliente_id = cre_reg_1.cliente_id and cre_reg_2.estado_id not in $estados_cancelados) as cantidad_creditos")
            )
            ->join('cliente_registros as cli_reg', 'cre_reg_1.cliente_id',  'cli_reg.id')
            ->whereBetween('cre_reg_1.fecha_ultimo_pago', [$fecha_desde, $fecha_hasta])
            ->distinct('cliente_id')
            ->get();

        $rango = $rango->where('cantidad_creditos', 0)->values()->pluck('cliente_id');
        $rango = $rango->toArray();


        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);


        $lista_clientes_inactivos = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select(
                'cre_reg.id',
                'cre_reg.cliente_id',
                'cli_reg.asesor_id',
                DB::raw('MAX(cre_reg.fecha_ultimo_pago) as fecha_inactivo'),

                DB::raw("TIMESTAMPDIFF(MONTH,MAX(cre_reg.fecha_ultimo_pago),'$fecha_actual') as tiempo_inactivo"),

                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.numero_expediente',
                'cli_reg.dni',
                'cli_reg.central_riesgo',
                'cli_reg.direccion as direccion_domicilio',
                'cli_reg.calificacion',
                'cli_reg.telefonos',
                'cli_reg.notas',

                DB::raw("MAX(cre_apr.numero_credito) as numero_credito"),

                'cli_reg.departamento_id as departamento_domicilio',
                'cli_reg.provincia_id as provincia_domicilio',
                'cli_reg.distrito_id as distrito_domicilio',

                'cli_neg.nombre',
                'cli_neg.direccion as direccion_negocio',

                'cli_neg.departamento_id as departamento_negocio',
                'cli_neg.provincia_id as provincia_negocio',
                'cli_neg.distrito_id as distrito_negocio',
            )
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id', 'cli_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_apr.id', 'cre_reg.aprobacion_id')
            ->join('cliente_negocios as cli_neg', 'cli_neg.cliente_id', 'cli_reg.id')
            ->where('cli_neg.vinculado', 1)
            ->whereIn('cli_reg.id', $rango)
            ->groupBy('cli_reg.id')
            ->orderBy('fecha_inactivo', 'asc')
            ->get();



        if ($por_asesor) {
            $asesor_id = $request->asesor_id;
            $lista_clientes_inactivos = $lista_clientes_inactivos->where('asesor_id', $asesor_id);
        }

        if ($por_calificacion) {
            $calificacion = $request->calificacion;
            $lista_clientes_inactivos = $lista_clientes_inactivos->where('calificacion', $calificacion);
        }

        if ($por_numero_credito) {
            $numero_credito = $request->numero_credito;
            $lista_clientes_inactivos = $lista_clientes_inactivos->where('numero_credito', $numero_credito);
        }

        $lista_clientes_inactivos = $lista_clientes_inactivos->map(function ($row, $index) use ($conexion) {

            $expediente = $row->numero_expediente . '-' . $row->numero_credito;
            $cliente = $row->apellido_paterno . ' ' . $row->apellido_materno . ' ' . $row->nombres;

            $telefonos = json_decode($row->telefonos);

            $numeros = $telefonos->t1;
            $t2 = $telefonos->t2;
            $t3 = $telefonos->t3;
            $t4 = $telefonos->t4;

            if ($t2 != null) {
                $numeros .= ' - ' . $t2;
            }

            if ($t3 != null) {
                $numeros .= ' - ' . $t3;
            }

            if ($t4 != null) {
                $numeros .= ' - ' . $t4;
            }

            $asesor = Usuario::find($row->asesor_id);
            $usuario_asesor = $asesor->usuario;

            $departamentos = Departamento::find([$row->departamento_domicilio, $row->departamento_negocio]);
            $provincias = Provincia::find([$row->provincia_domicilio, $row->provincia_negocio]);
            $distritos = Distrito::find([$row->distrito_domicilio, $row->distrito_negocio]);

            $departamento_domicilio = $departamento_negocio = $departamentos[0]->departamento;
            $provincia_domicilio = $provincia_negocio = $provincias[0]->provincia;
            $distrito_domicilio = $distrito_negocio = $distritos[0]->distrito;

            if (count($departamentos) == 2) {
                $departamento_negocio = $departamentos[1]->departamento;
                $provincia_negocio = $provincias[1]->provincia;
                $distrito_negocio = $distritos[1]->distrito;
            }

            $direccion_domicilio = $row->direccion_domicilio . ' / ';
            $direccion_domicilio .= $distrito_domicilio . ' - ';
            $direccion_domicilio .= $provincia_domicilio . ' - ';
            $direccion_domicilio .= $departamento_domicilio;

            $direccion_negocio = $row->direccion_negocio . ' / ';
            $direccion_negocio .= $distrito_negocio . ' - ';
            $direccion_negocio .= $provincia_negocio . ' - ';
            $direccion_negocio .= $departamento_negocio;

            $ultimo_comentario = Comentario::on($conexion)
                ->where('cliente_id', $row->cliente_id)
                ->where('motivo', 'INACTIVO')
                ->latest('fecha_comentario')
                ->first([
                    'fecha_comentario',
                    'comentario',
                    DB::raw("SUBSTRING(datos_creacion,42,8) as usuario")
                ]);

            $comentario_inactivo = null;

            if ($ultimo_comentario != null) {

                $usuario = Usuario::find($ultimo_comentario->usuario);
                $usuario = $usuario->usuario;

                $comentario_inactivo  = $ultimo_comentario->fecha_comentario . ' (';
                $comentario_inactivo  .= $usuario . '): ';
                $comentario_inactivo  .= $ultimo_comentario->comentario;
            }

            return [
                'index' => $index,
                'id' => $row->id,
                'cliente_id' => $row->cliente_id,
                'fecha_inactivo' => $row->fecha_inactivo,
                'tiempo_inactivo' => $row->tiempo_inactivo,
                'expediente' => $expediente,
                'numero_credito' => $row->numero_expediente,
                'dni' => $row->dni,
                'cliente' => $cliente,
                'telefonos' => $numeros,
                'asesor' => $usuario_asesor,
                'central_riesgo' => $row->central_riesgo,
                'direccion_domicilio' => $direccion_domicilio,
                'direccion_negocio' => $direccion_negocio,
                'calificacion' => $row->calificacion,
                'notas' => $row->notas == null ? '-' : $row->notas,
                'ultimo_comentario' => $comentario_inactivo
            ];
        });

        return ['lista_clientes_inactivos' => $lista_clientes_inactivos];
    }

    public function buscar_por_asesor($request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $por_asesor = $request->por_asesor;


        if ($por_asesor == 'true') {

            $asesor_id = $request->asesor_id;
            $condition_1 =  [
                ['cli_reg.asesor_id', $asesor_id]

            ];
        } else {
            $condition_1 = [];
        }


        $lista_clientes = Cliente::on($conexion)->from('cliente_registros as cli_reg')
            ->select(
                'cli_reg.apellido_paterno',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',
                'cli_reg.numero_expediente',
                'cli_reg.dni',
                'cli_reg.fecha_nacimiento',
                'cli_reg.asesor_id',

                'usu.usuario',
                'cli_reg.telefonos',

            )
            ->leftjoin('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
            ->where($condition_1)
            ->orderBy('cli_reg.apellido_paterno', 'asc')
            ->get();


        return ['lista_clientes' => $lista_clientes];
    }

    public function buscar_con_cumpleanios($request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $por_asesor = $request->por_asesor;

        $filtro = '';
        if ($por_asesor == 'true') {
            $asesor_id = $request->asesor_id;
            $filtro = "cli_reg.asesor_id = $asesor_id and ";
        }

        $clientes = Cliente::on($conexion)->from('cliente_registros as cli_reg')
            ->select(
                'cli_reg.id',
                'cli_reg.fecha_nacimiento',
                'cli_reg.codigo_expediente',
                DB::raw("CONCAT(cli_reg.apellido_paterno,' ',cli_reg.apellido_materno,' ',cli_reg.nombres) as cliente"),

                'cli_reg.calificacion',
                'cli_reg.telefonos',

                'usu.usuario as usuario_asesor'
            )
            ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
            ->whereRaw($filtro . "DATE_FORMAT(cli_reg.fecha_nacimiento, '%c-%d') BETWEEN DATE_FORMAT('" . $fecha_desde . "', '%c-%d') AND DATE_FORMAT('" . $fecha_hasta . "', '%c-%d')")
            ->orderBy('cli_reg.fecha_nacimiento', 'asc')
            ->get();


        $estado_credito = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado_credito->id;

        $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta . "+ 1 days"));

        $lista_clientes = [];

        $lista_clientes = $clientes->map(function ($row, $index) use (
            $conexion,
            $fecha_desde,
            $fecha_hasta,
            $estado_id
        ) {

            $creditos = Credito::on($conexion)->where('cliente_id', $row['id'])->get();
            $creditos_activos = $creditos->where('estado_id', $estado_id)->count();

            if ($creditos_activos == 0) {
                if (count($creditos) > 0) {
                    $row['estado'] = 'INACTIVO';
                } else {
                    $row['estado'] = 'OTROS';
                }
            } else {
                $row['estado'] = 'ACTIVO';
            }

            $row['index'] = $index;

            $visitas = Visita::on($conexion)->where('cliente_id', $row['id'])
                ->whereBetween('fecha_hora_visita', [$fecha_desde, $fecha_hasta])
                ->get()->count();

            if ($visitas > 0) {
                $row['visitado'] = 'SI';
            } else {
                $row['visitado'] = 'NO';
            }

            $telefonos = json_decode($row['telefonos']);

            $row['telefono_1'] =  $telefonos == null ? '-' : $telefonos->t1;
            $row['nota_1'] = $telefonos == null ? '-' : $telefonos->n1;
            $row['telefono_2'] = $telefonos == null ? '-' : $telefonos->t2;
            $row['nota_2'] = $telefonos == null ? '-' : $telefonos->n2;
            $row['telefono_3'] = $telefonos == null ? '-' : $telefonos->t3;
            $row['nota_3'] = $telefonos == null ? '-' : $telefonos->n3;
            $row['telefono_4'] = $telefonos == null ? '-' : $telefonos->t4;
            $row['nota_4'] = $telefonos == null ? '-' : $telefonos->n4;

            return $row;
        });

        return ['lista_clientes' => $lista_clientes];
    }
    public function comentar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $fecha_actual = (new CreditosController)->fecha_larga_aplicacion($agencia_id);

        $frmDatosComentario = json_decode($request->frmDatosComentario);

        $modo = $request->modo;
        $cliente_id = $frmDatosComentario->cliente_id;
        $comentario = mb_strtoupper($frmDatosComentario->comentario);

        Visita::on($conexion)->create([
            'cliente_id' => $cliente_id,
            'motivo' => 'CUMPLEAÑOS',
            'comentario' => $comentario,
            'fecha_hora_visita' => $fecha_actual,
            'datos_creacion' => $datos_registro
        ]);

        return redirect()->route('rep.cli.cumpleanios', $modo);
    }
    public function fecha_larga_aplicacion($agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;
        $data = Datos_aplicacion::on($conexion)->where([
            ['descripcion', 'FECHA_CREDITOS'],
        ])->get()->last();

        date_default_timezone_set("America/Lima");
        $hora = date('H:i:s');

        return $data->valor_fecha . ' ' . $hora;
    }



    public function comentar_inactivos(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $cliente_id = $request->cliente_id;
        $comentario = $request->comentario;
        $modo = $request->modo;

        $fecha_comentario = $this->fecha_larga_aplicacion($agencia_id);

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $comentario = strtoupper($comentario);

        Comentario::on($conexion)->create([
            'cliente_id' => $cliente_id,
            'motivo' => 'INACTIVO',
            'comentario' => $comentario,
            'fecha_comentario' => $fecha_comentario,
            'datos_creacion' => $datos_registro,

        ]);
        return redirect()->route('rep.cli.clientes_inactivos', $modo);
    }
    public function comentar_activos(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $cliente_id = $request->cliente_id;
        $comentario = $request->comentario;
        $modo = $request->modo;

        $fecha_comentario = $this->fecha_larga_aplicacion($agencia_id);

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $comentario = strtoupper($comentario);

        Comentario::on($conexion)->create([
            'cliente_id' => $cliente_id,
            'motivo' => 'GENERAL',
            'comentario' => $comentario,
            'fecha_comentario' => $fecha_comentario,
            'datos_creacion' => $datos_registro,

        ]);
        return redirect()->route('rep.cli.clientes_activos', $modo);
    }
    public function listar_comentarios_inactivos(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $cliente_id = $request->cliente_id;

        $lista_inactivos_comentarios = Comentario::on($conexion)->from('cliente_comentarios as cli_com')
            ->select(
                'cli_com.id',
                'cli_com.cliente_id',
                'cli_com.motivo',
                'cli_com.comentario',
                'cli_com.fecha_comentario',
                'usu.usuario as usuario'
            )
            ->join('solucion_master.usuarios as usu', DB::raw("SUBSTR(cli_com.datos_creacion,42,8)"), 'usu.dni')
            ->where([['cli_com.cliente_id', $cliente_id], ['cli_com.motivo', 'INACTIVO']])
            ->orderby('cli_com.fecha_comentario', 'desc')
            ->get();

        return ['lista_inactivos_comentarios' => $lista_inactivos_comentarios];
    }
    public function listar_comentarios_activos(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $cliente_id = $request->cliente_id;

        $lista_activos_comentarios = Comentario::on($conexion)->from('cliente_comentarios as cli_com')
            ->select(
                'cli_com.id',
                'cli_com.cliente_id',
                'cli_com.motivo',
                'cli_com.comentario',
                'cli_com.fecha_comentario',
                'usu.usuario as usuario'
            )
            ->join('solucion_master.usuarios as usu', DB::raw("SUBSTR(cli_com.datos_creacion,42,8)"), 'usu.dni')
            ->where([['cli_com.cliente_id', $cliente_id], ['cli_com.motivo', 'GENERAL']])
            ->orderby('cli_com.fecha_comentario', 'asc')
            ->get();

        return ['lista_activos_comentarios' => $lista_activos_comentarios];
    }
    public function comentarios(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $cliente_id = $request->cliente_id;

        $año_comentario = $request->año_comentario;

        $lista_comentarios = Visita::on($conexion)->from('cliente_visitas as cli_vis')
            ->select(
                'cli_vis.id',
                'cli_vis.comentario',
                'cli_vis.fecha_hora_visita',
                'cli_vis.datos_creacion',

                'usu.usuario as usuario_registro'
            )
            ->join('solucion_master.usuarios as usu', DB::raw("SUBSTR(cli_vis.datos_creacion,42,8)"), 'usu.dni')
            ->where('cli_vis.cliente_id', $cliente_id)
            ->whereRaw('YEAR(cli_vis.fecha_hora_visita) = ?', [$año_comentario])
            ->orderby('cli_vis.fecha_hora_visita', 'desc')
            ->get();

        $lista_comentarios = $lista_comentarios->map(function ($row, $index) {
            $row['index'] = $index;
            return $row;
        });

        return ['lista_comentarios' => $lista_comentarios];
    }

    public function clientes_activos($modo)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_ACTIVOS', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_MIS_ACTIVOS', 'CREDITOS_REPORTES');
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

                return Inertia::render('Creditos/Reportes/Clientes/clientes_activos', [
                    'modo' => $modo,
                    'usuarios' => $usuarios,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function clientes_inactivos($modo)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_INACTIVOS', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_MIS_INACTIVOS', 'CREDITOS_REPORTES');
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

                return Inertia::render('Creditos/Reportes/Clientes/clientes_inactivos', [
                    'modo' => $modo,
                    'usuarios' => $usuarios,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function exportar(Request $request)
    {
        $modo = $request->modo;
        if ($modo == 'pago_hoy') {
            return $this->exportar_pago_hoy($request);
        }
    }


    public function clientes_por_asesor($modo)
    {

        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_POR_ASESOR', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_POR_MI_ASESOR', 'CREDITOS_REPORTES');
            }

            if ($band == 1) {


                if ($modo == 'completo') {
                    $usuarios = Usuario::select(
                        'dni',
                        'usuario',
                        'agencia_id',
                        'habilitado'
                    )->where('cargo_id', 2)
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

                return Inertia::render('Creditos/Reportes/Clientes/clientes_asesor', [
                    'modo' => $modo,
                    'usuarios' => $usuarios,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function exportar_clientes_activos(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $datos_recibidos =   json_decode($request->datos_tabla);
        $tipo = $request->tipo;
        $data = [];

        $orden = 1;

        foreach ($datos_recibidos as $item) {
            $object = (object)[

                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'asesor' => $item->usuario,
                'riesgo' => $item->central_riesgo,
                'domicilio' => $item->direccion_domicilio,
                'ubicacion' => $item->distrito . ' - ' . $item->provincia . ' - ' . $item->departamento,
                'direccion' => $item->direccion_negocio,
                'ubicacion_negocio' => $item->distrito_negocio . ' - ' . $item->provincia_negocio . ' - ' . $item->departamento_negocio,
                'cal' => $item->calificacion,
                'notas' => $item->notas,

            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName =  './report_templates/creditos/reportes/rptClientesActivos.xlsx';

        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $columna = 1;
        $lista_formatos_columnas = [];
        $numero_celdas = ($tipo == 'clientes_activos') ?  9 : 12;

        while ($columna <= $numero_celdas) {
            $letra_columna = (new CreditosController)->num2char($columna);
            $formato_columna = $sheet->getStyle($letra_columna . 4)->exportArray();

            $lista_formatos_columnas[] = $formato_columna;
            $columna++;
        }


        // Insertando datos-----------------------------
        $indice = 4;

        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor); //insetando datos
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_columnas[$columna_2 - 1]); //insetando formato de la celda
                $columna_2 += 1;
            }

            $indice += 1;
        }

        // Exportar para descarga-------------------------
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $ret['data'] = base64_encode(ob_get_contents());
        ob_end_clean();

        return $ret['data'];
    }

    public function exportar_clientes_inactivos(Request $request)
    {
        // Ordenando array de datos-------------------------------
        $datos_recibidos =   json_decode($request->datos_tabla);

        $data = [];

        foreach ($datos_recibidos as $item) {

            $object = (object)[
                'numero' => $item->index + 1,
                'fecha_inactivo' => $item->fecha_inactivo,
                'tiempo_inactivo' => $item->tiempo_inactivo . ' mes(es)',
                'expediente' => $item->expediente,
                'dni' => $item->dni,
                'calificacion' => $item->calificacion,
                'cliente' =>  $item->cliente,
                'telefonos' => $item->telefonos,
                'asesor' => $item->asesor,
                'riesgo' => $item->central_riesgo,
                'domicilio' => $item->direccion_domicilio,
                'negocio' => $item->direccion_negocio,
                'notas' => $item->notas,
                'ultimo_comentario' => $item->ultimo_comentario,
            ];
            $data[] = $object;
        }


        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptClientesInactivos.xlsx';

        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $columna = 1;
        $lista_formatos_columnas_1 = [];
        // $lista_formatos_columnas_2 = [];
        $lista_formatos_totales = [];
        $numero_celdas =  14;

        while ($columna <= $numero_celdas) {
            $letra_columna = (new CreditosController)->num2char($columna);
            $formato_columna_1 = $sheet->getStyle($letra_columna . 5)->exportArray();
            // $formato_columna_2 = $sheet->getStyle($letra_columna . 6)->exportArray();
            $formato_total = $sheet->getStyle($letra_columna . 8)->exportArray();

            $lista_formatos_columnas_1[] = $formato_columna_1;
            // $lista_formatos_columnas_2[] = $formato_columna_2;
            $lista_formatos_totales[] = $formato_total;
            $columna++;
        }


        $sheet->removeRow(8);
        $sheet->removeRow(7);
        $sheet->removeRow(6);
        $sheet->removeRow(5);

        // Insertando datos-----------------------------
        $indice = 5;

        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                // if ($indice % 2 == 0) {
                // $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_columnas_2[$columna_2 - 1]);
                // } else {
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_columnas_1[$columna_2 - 1]);
                // }

                $columna_2 += 1;
            }

            $indice += 1;
        }

        // Exportar para descarga-------------------------

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptClientesInactivos', 5);


        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }

    public function pago_hoy($modo)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_PAGO_HOY', 'CREDITOS_REPORTES');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CLIENTES_MI_PAGO_HOY', 'CREDITOS_REPORTES');
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

                return Inertia::render('Creditos/Reportes/Clientes/pago_hoy', [
                    'modo' => $modo,
                    'usuarios' => $usuarios,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function buscar_pago_hoy(request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_agencia = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        $por_asesor = $request->por_asesor;

        $condition = [['cre_reg.estado_id', $estado_id]];

        if ($por_asesor == 'true') {
            $asesor_id = $request->asesor_id;
            $condition[] = ['cre_reg.asesor_id', $asesor_id];
        }

        $rango = Credito::on($conexion)->from('credito_registros as cre_reg')
            ->select('cre_reg.id')
            ->where($condition)->get();



        $lista_clientes = Cuota::on($conexion)->from('credito_cuotas as cre_cuo')
            ->select(
                'cre_reg.id',
                'cre_reg.dias_atraso',
                'cre_reg.cliente_id',

                'cli_reg.apellido_paterno',
                'cli_reg.numero_expediente',
                'cli_reg.apellido_materno',
                'cli_reg.nombres',

                'cre_apr.monto',
                'cre_apr.plazo',
                'cre_apr.periodo_pago',
                'cre_apr.numero_credito',

                'cre_cuo.numero_cuota',
                'cre_cuo.cuota',
                'cre_cuo.estado',
                'cre_cuo.fecha_ultimo_pago',


                'usu_1.usuario as usuario_asesor'
            )
            ->join('credito_registros as cre_reg', 'cre_cuo.credito_id', 'cre_reg.id')
            ->join('credito_aprobaciones as cre_apr', 'cre_reg.aprobacion_id',  'cre_apr.id')
            ->join('cliente_registros as cli_reg', 'cre_reg.cliente_id',  'cli_reg.id')
            ->join('solucion_master.usuarios as usu_1', 'cre_reg.asesor_id', 'usu_1.dni')
            ->whereIn('cre_cuo.credito_id', $rango)
            ->where('cre_cuo.fecha_vencimiento', $fecha_agencia)
            ->orderBy('cli_reg.apellido_paterno', 'asc')
            ->get();




        $totales = (object)[
            'total' => $lista_clientes->count(),
            'pagados' => $lista_clientes->where('estado', 'C')->count(),
            'restantes' => $lista_clientes->where('estado', 'P')->count(),
        ];


        return [
            'lista_clientes' => $lista_clientes,
            'totales' => $totales
        ];
    }

    public function exportar_pago_hoy($request)
    {
        // Ordenando array de datos-------------------------------
        $clientes = json_decode($request->lista_clientes);

        $data = [];

        $orden = 1;
        foreach ($clientes as $item) {
            $object = (object)[
                'numero' => $orden,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'asesor' => $item->usuario_asesor,
                'expediente' => $item->numero_expediente . ' - ' . $item->numero_credito,
                'dias_atraso' => $item->dias_atraso,
                'capital' => $item->monto,
                'plazo' => round($item->plazo, 0) . ' ' . (new CreditosController)->periodo_medicion($item->periodo_pago),
                'cancelado' => $item->estado == 'C' ? 'SI' : null,

            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName = './report_templates/creditos/reportes/rptClientesPagoHoy.xlsx';
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];

        while ($celda <= 8) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celda_1 = $sheet->getStyle($columna_1 . 4)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;

            $celda++;
        }

        // Insertando datos-----------------------------
        $indice = 4;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);
                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_2 - 1]);

                $columna_2 += 1;
            }

            $indice += 1;
        }

        // Exportar para descarga-------------------------
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $ret['data'] = base64_encode(ob_get_contents());
        ob_end_clean();

        return $ret['data'];
    }
    public function exportar_clientes_asesor(Request $request)
    {

        // Ordenando array de datos-------------------------------
        $clientes = json_decode($request->lista_clientes);

        $data = [];
        $indice = 1;
        foreach ($clientes as $item) {

            $telefono = json_decode($item->telefonos);

            $object = (object)[

                'numero' => $indice,
                'cliente' =>  $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres,
                'numero_expediente' => $item->numero_expediente,
                'dni' => $item->dni,
                'fecha_nacimiento' => $item->fecha_nacimiento,
                'telefonos' => $telefono->t1 . ' ' . $telefono->o1 . ' ' . $telefono->n1,
                'usuario' => $item->usuario
            ];
            $indice += 1;


            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("./report_templates/creditos/reportes/rptClientesAsesor.xlsx");
        $spreadsheet = $reader->load("./report_templates/creditos/reportes/rptClientesAsesor.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 7) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda = $sheet->getStyle($columna_1 . 4)->exportArray();

            $lista_formatos_celdas[] = $formato_celda;
            $celda++;
        }

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

        // Exportar para descarga-------------------------
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $ret['data'] = base64_encode(ob_get_contents());
        ob_end_clean();


        return $ret['data'];
    }
    public function exportar_clientes_cumpleanios(Request $request)
    {

        $datos_recibidos =   json_decode($request->datos_tabla);
        $agencia_id = $request->agencia_id;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        $tipo = $request->tipo;

        $data = [];

        $orden = 1;

        foreach ($datos_recibidos as $item) {
            $telefonos = json_decode($item->telefonos);
            $object = (object)[
                'numero' => $orden,
                'visitado' => $item->visitado,
                'fecha_nacimiento' => $item->fecha_nacimiento,
                'cliente' =>  $item->cliente,
                'calificacion' => $item->calificacion,
                'estado' => $item->estado,
                'usuario_asesor' => $item->usuario_asesor,

                'telefono_1' => $telefonos->t1,
                'nota_1' => $telefonos->n1,
                'telefono_2' => $telefonos->t2,
                'nota_2' => $telefonos->n2,
                'telefono_3' => $telefonos->t3,
                'nota_3' => $telefonos->n3,
                'telefono_4' => $telefonos->t4,
                'nota_4' => $telefonos->n4,


            ];
            $orden += 1;
            $data[] = $object;
        }

        // Leer Plantilla-------------------------
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $inputFileName =  './report_templates/creditos/reportes/rptClientesCumpleanios.xlsx';

        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Obteniendo formatos-----------------------------
        $celda = 1;
        $lista_formatos_celdas_1 = [];

        while ($celda <= 15) {
            $columna_1 = (new CreditosController)->num2char($celda);

            $formato_celda_1 = $sheet->getStyle($columna_1 . 5)->exportArray();

            $lista_formatos_celdas_1[] = $formato_celda_1;


            $celda++;
        }


        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));
        $sheet->setCellValue('O2', 'Desde ' . $fecha_desde . ' hasta ' . $fecha_hasta);

        // Insertando datos-----------------------------
        $indice = 5;
        $total = 0;
        foreach ($data as $item) {
            $columna_2 = 1;
            foreach ($item as $valor) {

                $sheet->setCellValue((new CreditosController)->num2char($columna_2) . $indice, $valor);

                $sheet->getStyle((new CreditosController)->num2char($columna_2) . $indice)->applyFromArray($lista_formatos_celdas_1[$columna_2 - 1]);


                $columna_2 += 1;
            }

            $indice += 1;
            $total += 1;
        }

        $indice += 1;

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptClientesCumpleanios', 5);

        // dd($nombre_archivo);



        $writer = new Xlsx($spreadsheet);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        return ['path_xlsx' => $path_xlsx];
    }
}
