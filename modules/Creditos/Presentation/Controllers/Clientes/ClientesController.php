<?php

namespace Modules\Creditos\Presentation\Controllers\Clientes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;
use Modules\General\Infrastructure\Persistence\Eloquent\Departamento;
use Modules\General\Infrastructure\Persistence\Eloquent\Provincia;
use Modules\General\Infrastructure\Persistence\Eloquent\Distrito;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Movimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\General\Infrastructure\Persistence\Eloquent\Sesion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ClientesController extends Controller
{

    public function listado_registro()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = 0;

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'LISTADO_REGISTRO', 'CREDITOS_CLIENTES');


            if ($band == 1) {

                $distritos = Distrito::orderBy('distrito', 'asc')->get();
                $provincias = Provincia::orderBy('provincia', 'asc')->get();
                $departamentos = Departamento::orderBy('departamento', 'asc')->get();

                return Inertia::render('Creditos/Clientes/listado_registro', [
                    'distritos' => $distritos,
                    'provincias' => $provincias,
                    'departamentos' => $departamentos

                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }


    public function verificar_cliente(Request $request)
    {

        $id = $request->id;
        $dni = $request->dni;
        $modo = $request->modo;

        $resultado = '';
        $agencia = null;

        // Temporal-----------------------

        $agencia_id = session('id_agencia');
        $conexion = 'master_' .  $agencia_id;

        $existe_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
            ->select(
                'cli_reg.id',
                'cli_reg.dni',
                'ag.nombre as agencia'
            )
            ->join('solucion_master.agencias as ag', 'ag.id_agencia', 'cli_reg.agencia_id')
            ->where('cli_reg.dni', $dni)
            ->get();
        // ------------------------

        $agencias = Agencia::all();

        foreach ($agencias as $item) {
            $conexion = 'master_' .  $item->id_agencia;

            $existe_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                ->select(
                    'cli_reg.id',
                    'cli_reg.dni',
                    'ag.nombre as agencia'
                )
                ->join('solucion_master.agencias as ag', 'ag.id_agencia', 'cli_reg.agencia_id')
                ->where('cli_reg.dni', $dni)
                ->get();

            if (count($existe_cliente) > 0) {
                break;
            }
        }


        if ($modo == 'NUEVO') {
            if (count($existe_cliente) > 0) {
                $resultado = 'EXISTE';
                $agencia = $existe_cliente[0]->agencia;
            } else {
                $resultado = 'NO_EXISTE';
            }
        } else if ($modo == 'EDITAR') {
            if (count($existe_cliente) > 0) {
                if ($existe_cliente[0]['id'] == $id) {
                    $resultado = 'NO_EXISTE';
                } else {
                    $resultado = 'EXISTE';
                    $agencia = $existe_cliente[0]->agencia;
                }
            } else {
                $resultado = 'NO_EXISTE';
            }
        }

        return ['resultado' => $resultado, 'agencia' => $agencia];
    }

    public function buscar_clientes(Request $request)
    {
        $texto_buscar = $request->texto_buscar;
        $tipo_filtro = $request->tipo_filtro;

        $agencia = $request->agencia;

        $columna = "";
        $operator = 'like';
        $search = "%$texto_buscar%";
        if ($tipo_filtro == 'apellidos_nombres') {
            $columna = 'cli.apellido_paterno, " ", cli.apellido_materno, " ", cli.nombres';
        } else if ($tipo_filtro == 'dni') {
            $columna = 'cli.dni';
        } else if ($tipo_filtro == 'expediente') {
            $columna = 'cli.codigo_expediente';
        } else if ($tipo_filtro == 'direccion') {
            $columna = 'cli.direccion';
        } else if ($tipo_filtro == 'celular') {
            $columna = 'cli.telefonos';
        } else if ($tipo_filtro == 'id') {
            $columna = 'cli.id';
            $operator = '=';
            $search = $texto_buscar;
        }

        if ($agencia == 'TODAS') {
            $agencias = Agencia::all();
            foreach ($agencias as $item) {
                $conexion = 'master_' .  $item->id_agencia;
                $lista_clientes = Cliente::on($conexion)->from('cliente_registros as cli')
                    ->select(
                        'cli.id',
                        'cli.dni',

                        'cli.apellido_paterno',
                        'cli.apellido_materno',
                        'cli.nombres',
                        'cli.fecha_nacimiento',
                        'cli.estado_civil',
                        'cli.sexo',
                        'cli.hijos',
                        'cli.agencia_id',
                        'ag.nombre as agencia',
                        'cli.correo_electronico',

                        'cli.codigo_expediente',
                        DB::raw("IFNULL(cli.asesor_id,0) as asesor_id"),
                        'us.usuario as usuario_asesor',
                        DB::raw("IFNULL(cli.promotor_id,0) as promotor_id"),
                        'cli.central_riesgo',
                        'cli.canal_referencia',

                        'cli.monto_maximo',
                        'cli.notas',
                        'cli.reportar_equifax',

                        'cli.direccion',
                        'cli.departamento_id',
                        'dep.departamento',
                        'cli.provincia_id',
                        'pro.provincia',
                        'cli.distrito_id',
                        'dis.distrito',
                        'cli.referencia_direccion',
                        'cli.telefonos',

                        'cli.imagen_dni',
                        'cli.observaciones',
                    )
                    ->join('solucion_master.agencias as ag', 'ag.id_agencia', 'cli.agencia_id')
                    ->join('solucion_master.departamentos as dep', 'dep.id', 'cli.departamento_id')
                    ->join('solucion_master.provincias as pro', 'pro.id', 'cli.provincia_id')
                    ->join('solucion_master.distritos as dis', 'dis.id', 'cli.distrito_id')
                    ->leftjoin('solucion_master.usuarios as us', 'cli.asesor_id', 'us.dni')
                    ->where([
                        [DB::raw("CONCAT($columna)"), $operator, $search]
                    ])
                    ->get();

                if (count($lista_clientes) > 0) {
                    break;
                }
            }
        } else {
            $conexion = 'master_' .  $agencia;
            $lista_clientes = Cliente::on($conexion)->from('cliente_registros as cli')
                ->select(
                    'cli.id',
                    'cli.dni',

                    'cli.apellido_paterno',
                    'cli.apellido_materno',
                    'cli.nombres',
                    'cli.fecha_nacimiento',
                    'cli.estado_civil',
                    'cli.sexo',
                    'cli.hijos',
                    'cli.agencia_id',
                    'ag.nombre as agencia',
                    'cli.correo_electronico',

                    'cli.codigo_expediente',
                    DB::raw("IFNULL(cli.asesor_id,0) as asesor_id"),
                    'us.usuario as usuario_asesor',
                    DB::raw("IFNULL(cli.promotor_id,0) as promotor_id"),
                    'cli.central_riesgo',
                    'cli.canal_referencia',

                    'cli.monto_maximo',
                    'cli.notas',
                    'cli.reportar_equifax',

                    'cli.direccion',
                    'cli.departamento_id',
                    'dep.departamento',
                    'cli.provincia_id',
                    'pro.provincia',
                    'cli.distrito_id',
                    'dis.distrito',
                    'cli.referencia_direccion',
                    'cli.telefonos',

                    'cli.imagen_dni',
                    'cli.observaciones',
                )
                ->join('solucion_master.agencias as ag', 'ag.id_agencia', 'cli.agencia_id')
                ->join('solucion_master.departamentos as dep', 'dep.id', 'cli.departamento_id')
                ->join('solucion_master.provincias as pro', 'pro.id', 'cli.provincia_id')
                ->join('solucion_master.distritos as dis', 'dis.id', 'cli.distrito_id')
                ->leftjoin('solucion_master.usuarios as us', 'cli.asesor_id', 'us.dni')
                ->where([
                    [DB::raw("CONCAT($columna)"), $operator, $search]
                ])
                ->get();
        }
        return $lista_clientes;
    }

    public function guardar_cliente(Request $request)
    {


        // dd($request);
        $tipo_modulo = $request->tipo_modulo;
        $frmDatosCliente = json_decode($request->frmDatosCliente);

        $agencia_id = $frmDatosCliente->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
        $año = substr($fecha_corta, 0, 4);

        $id = $frmDatosCliente->id;

        $modo = $frmDatosCliente->modo;
        $dni = $frmDatosCliente->dni;
        $apellido_paterno = trim(mb_strtoupper($frmDatosCliente->apellido_paterno));
        $apellido_materno = trim(mb_strtoupper($frmDatosCliente->apellido_materno));
        $nombres = trim(mb_strtoupper($frmDatosCliente->nombres));
        $fecha_nacimiento = $frmDatosCliente->fecha_nacimiento;
        $estado_civil = $frmDatosCliente->estado_civil;
        $sexo = $frmDatosCliente->sexo;
        $hijos = $frmDatosCliente->hijos;

        $correo_electronico = (new CreditosController)->verificar_nulo($frmDatosCliente->correo_electronico);

        $asesor_id = $frmDatosCliente->asesor_id;
        $promotor_id = $frmDatosCliente->promotor_id;
        $central_riesgo = $frmDatosCliente->central_riesgo;
        $canal_referencia = $frmDatosCliente->canal_referencia;

        $monto_maximo = $frmDatosCliente->monto_maximo;
        $notas = (new CreditosController)->verificar_nulo($frmDatosCliente->notas);
        $reportar_equifax = $frmDatosCliente->reportar_equifax;

        $direccion = mb_strtoupper($frmDatosCliente->direccion);
        $departamento_id = $frmDatosCliente->departamento_id;
        $provincia_id = $frmDatosCliente->provincia_id;
        $distrito_id = $frmDatosCliente->distrito_id;
        $referencia_direccion = mb_strtoupper($frmDatosCliente->referencia_direccion);
        $telefonos = json_encode($frmDatosCliente->telefonos);

        $observaciones = (new CreditosController)->verificar_nulo($frmDatosCliente->observaciones);

        if (!$observaciones == null) {
            $observaciones = mb_strtoupper($observaciones);
        }

        if ($modo == 'NUEVO') {

            if (!empty($_FILES['imagen_dni'])) {

                $path_name = pathinfo($_FILES['imagen_dni']['name']);
                $extension = "." . $path_name['extension'];
                $nombre_archivo =  $año . '_' . $dni . $extension;
                $archivo = $_FILES['imagen_dni']['tmp_name'];
                $ruta = '/imagenes_server/creditos/clientes/dni/' . $agencia_id . '/' . $año;
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_archivo;
                $calidad = 20;
                // move_uploaded_file($archivo, $ruta);
                (new CreditosController)->compressImage($archivo, $ruta, $calidad);
            } else {
                $nombre_archivo = null;
            }

            Cliente::on($conexion)->create(array(
                'dni' => $dni,

                'apellido_paterno' => $apellido_paterno,
                'apellido_materno' => $apellido_materno,
                'nombres' => $nombres,
                'fecha_nacimiento' => $fecha_nacimiento,
                'sexo' => $sexo,
                'estado_civil' => $estado_civil,
                'hijos' => $hijos,
                'agencia_id' => $agencia_id,
                'correo_electronico' => $correo_electronico,

                'asesor_id' => $asesor_id,
                'promotor_id' => $promotor_id,
                'central_riesgo' => $central_riesgo,
                'canal_referencia' => $canal_referencia,

                'monto_maximo' => $monto_maximo,
                'notas' => $notas,
                'reportar_equifax' => $reportar_equifax,

                'direccion' => $direccion,
                'departamento_id' => $departamento_id,
                'provincia_id' => $provincia_id,
                'distrito_id' => $distrito_id,
                'referencia_direccion' => $referencia_direccion,
                'telefonos' => $telefonos,

                'imagen_dni' => $nombre_archivo,
                'observaciones' => $observaciones,

                'datos_creacion' => $datos_registro,
            ));
        } else if ($modo == 'EDITAR') {

            if (empty($_FILES['imagen_dni'])) {

                Cliente::on($conexion)->where('id', $id)
                    ->update([
                        'dni' => $dni,

                        'apellido_paterno' => $apellido_paterno,
                        'apellido_materno' => $apellido_materno,
                        'nombres' => $nombres,
                        'fecha_nacimiento' => $fecha_nacimiento,
                        'sexo' => $sexo,
                        'estado_civil' => $estado_civil,
                        'hijos' => $hijos,
                        'agencia_id' => $agencia_id,
                        'correo_electronico' => $correo_electronico,

                        'asesor_id' => $asesor_id,
                        'promotor_id' => $promotor_id,
                        'central_riesgo' => $central_riesgo,
                        'canal_referencia' => $canal_referencia,

                        'monto_maximo' => $monto_maximo,
                        'notas' => $notas,
                        'reportar_equifax' => $reportar_equifax,

                        'direccion' => $direccion,
                        'departamento_id' => $departamento_id,
                        'provincia_id' => $provincia_id,
                        'distrito_id' => $distrito_id,
                        'referencia_direccion' => $referencia_direccion,
                        'telefonos' => $telefonos,

                        'observaciones' => $observaciones,

                        'datos_actualizacion' => $datos_registro,
                    ]);
            } else {

                $path_name = pathinfo($_FILES['imagen_dni']['name']);
                $extension = "." . $path_name['extension'];
                $nombre_archivo = $año . '_' . $dni . $extension;
                $archivo = $_FILES['imagen_dni']['tmp_name'];
                $ruta = '/imagenes_server/creditos/clientes/dni/' . $agencia_id . '/' . $año;
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_archivo;
                $calidad = 20;
                // move_uploaded_file($archivo, $ruta);
                (new CreditosController)->compressImage($archivo, $ruta, $calidad);


                Cliente::on($conexion)->where('id', $id)
                    ->update([
                        'dni' => $dni,
                        'apellido_paterno' => $apellido_paterno,
                        'apellido_materno' => $apellido_materno,
                        'nombres' => $nombres,
                        'fecha_nacimiento' => $fecha_nacimiento,
                        'sexo' => $sexo,
                        'estado_civil' => $estado_civil,
                        'hijos' => $hijos,
                        'agencia_id' => $agencia_id,
                        'correo_electronico' => $correo_electronico,

                        'asesor_id' => $asesor_id,
                        'promotor_id' => $promotor_id,
                        'central_riesgo' => $central_riesgo,
                        'canal_referencia' => $canal_referencia,

                        'monto_maximo' => $monto_maximo,
                        'notas' => $notas,
                        'reportar_equifax' => $reportar_equifax,

                        'direccion' => $direccion,
                        'departamento_id' => $departamento_id,
                        'provincia_id' => $provincia_id,
                        'distrito_id' => $distrito_id,
                        'referencia_direccion' => $referencia_direccion,
                        'telefonos' => $telefonos,

                        'imagen_dni' => $nombre_archivo,
                        'datos_actualizacion' => $datos_registro,
                    ]);
            }
        }
        return redirect()->route('cli.listado_registro', $tipo_modulo);
    }

    public function parientes_avales_negocios($cliente_id, $agencia_id)
    {
        $parientes = (new ParienteController)->listar_parientes($cliente_id, $agencia_id);
        $avales = (new AvalController)->listar_avales($cliente_id, $agencia_id);
        $negocios = (new NegocioController)->listar_negocios($cliente_id, $agencia_id);
        $parientes_dependientes = (new ParienteController)->listar_parientes_dependientes($cliente_id, $agencia_id);
        $avales_dependientes = (new AvalController)->listar_avales_dependientes($cliente_id, $agencia_id);

        $resultado = array(
            'parientes' => $parientes,
            'avales' => $avales,
            'parientes_dependientes' => $parientes_dependientes,
            'avales_dependientes' => $avales_dependientes,
            'negocios' => $negocios
        );

        return $resultado;
    }

    public function eliminar(Request $request)
    {
        $resultado = new \stdClass;

        $cliente_id = $request->cliente_id;
        $agencia_id = $request->agencia_id;

        $datos_cliente = (object)[
            'cliente_id' => $cliente_id,
            'agencia_origen' => $agencia_id
        ];

        (new ClienteTransferenciaController)->eliminar_datos_origen($datos_cliente);

        $resultado->success = true;

        return $resultado;
    }

    public function cantidad_creditos($agencia_id, $cliente_id)
    {
        $response = new \stdClass;

        $conexion = 'master_' . $agencia_id;

        $cantidad_creditos = Credito::on($conexion)->where('cliente_id', $cliente_id)->count();

        $response->success = true;
        $response->cantidad_creditos = $cantidad_creditos;

        return $response;
    }
}
