<?php

namespace Modules\Creditos\Presentation\Controllers\Caja;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transferencia as CajaTransferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Transferencia as CuentaTransferencia;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Billeteo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Movimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\TipoMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Transaccion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Categoria;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Subcategoria;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Comision;



use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;


use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function apertura_caja()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'APERTURA_CAJA', 'CREDITOS_CAJA');
            if ($band == 1) {

                return Inertia::render('Creditos/Caja/apertura_caja');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function aperturar(Request $request)
    {
        $agencia_id = session('id_agencia');
        $dni = session('usuario_dni');
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $frmDatosApertura = json_decode($request->frmDatosApertura);

        $cuenta_id = $frmDatosApertura->cuenta_id;
        $monto = $frmDatosApertura->monto;
        $notas = (new CreditosController)->verificar_nulo($frmDatosApertura->notas);

        if (!$notas == null) {
            $notas = mb_strtoupper($notas);
        }

        $tipo_movimiento = TipoMovimiento::on($conexion)->where('nombre', 'APERTURA DE CAJA')->get()->last();
        $tipo_movimiento_id = $tipo_movimiento->id;

        $fecha_movimiento = (new CreditosController)->fecha_larga_aplicacion($agencia_id);


        Movimiento::on($conexion)->create([
            'cuenta_id' => $cuenta_id,
            'tipo' => 'E',
            'monto' => $monto,
            'descripcion' => $tipo_movimiento->nombre,
            'tipo_movimiento_id' => $tipo_movimiento_id,
            'fecha_movimiento' => $fecha_movimiento,
            'datos_creacion' => $datos_registro
        ]);

        Caja::on($conexion)->create([
            'dni' => $dni,
            'agencia_id' =>  $agencia_id,
            'monto_apertura' => $monto,
            'comentario_apertura' => $notas,
            'datos_apertura' => $datos_registro,
        ]);

        CuentaUsuario::on($conexion)->where('id', $cuenta_id)
            ->update([
                'monto' => DB::raw("monto - $monto"),
                'datos_actualizacion' => $datos_registro
            ]);

        return redirect()->route('cre.index');
    }

    public function generar_clave()
    {
        $agencia_id = session('id_agencia');
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $cantidad = 7;

        $clave_aleatoria = '';
        for ($i = 0; $i < $cantidad; $i++) {
            $caracter_aleatorio =  $caracteres[mt_rand(0, $cantidad - 1)];
            $clave_aleatoria .= $caracter_aleatorio;
        }

        Datos_aplicacion::on($conexion)->where('descripcion', 'CLAVE_TRANSACCION')
            ->update([
                'valor_cadena' => $clave_aleatoria,
                'datos_actualizacion' => $datos_registro
            ]);

        return $clave_aleatoria;
    }

    // public function verificar_clave($clave)
    // {
    //     $agencia_id = session('id_agencia');
    //     $conexion = 'master_' .  $agencia_id;

    //     $resultado = false;

    //     $encontrado = Datos_aplicacion::on($conexion)->where([
    //         ['descripcion', 'CLAVE_TRANSACCION'],
    //         ['valor_cadena', $clave]
    //     ])->get();

    //     if (count($encontrado) > 0) {
    //         $resultado = true;
    //     }

    //     return $resultado;
    // }

    public function cierre_caja()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CIERRE_CAJA', 'CREDITOS_CAJA');
            $band = 1;
            if ($band == 1) {
                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;

                $editar_billeteo = 0;

                $editar_billeteo = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'EDITAR_BILLETEO', 'CREDITOS_CAJA');

                $cuentas = CuentaUsuario::on($conexion)->from('cuenta_usuarios as us_cu')
                    ->select(
                        'us_cu.id',
                        'us_cu.monto',
                        'us_cu.con_cuenta',
                        'us.usuario',
                        'us.nombres',
                        'us.apellido_paterno',
                        'us.apellido_materno',
                        'us.dni',
                        'us_cu.dni',
                        'us.agencia_id as agencia',
                        'ag.nombre as nombre_agencia',
                    )
                    ->join('solucion_master.usuarios as us', 'us.dni', 'us_cu.dni')
                    ->join('solucion_master.agencias as ag', 'us.agencia_id', 'ag.id_agencia')
                    ->where('us_cu.con_cuenta', 1)
                    ->where('us.habilitado', 1)

                    ->get();


                $datos_caja = Caja::on($conexion)->where([
                    ['dni', session('usuario_dni')],
                    ['datos_cierre', null]
                ])->get()->last();

                $transferencias_pendientes = false;
                if ($datos_caja != null) {

                    $transferencias_pendientes = $this->verificar_transferencias($datos_caja->id, $agencia_id);

                    $billeteo = Billeteo::on($conexion)->select(
                        'id',
                        '1_cent as un_centimo',
                        '10_cent as diez_centimos',
                        '20_cent as veinte_centimos',
                        '50_cent as cincuenta_centimos',
                        '1_sol as un_sol',
                        '2_sol as dos_soles',
                        '5_sol as cinco_soles',
                        '10_sol as diez_soles',
                        '20_sol as veinte_soles',
                        '50_sol as cincuenta_soles',
                        '100_sol as cien_soles',
                        '200_sol as doscientos_soles'
                    )
                        ->where('caja_id', $datos_caja->id)
                        ->get()
                        ->last();
                } else {
                    $billeteo = null;
                }

                return Inertia::render('Creditos/Caja/cierre_caja', [
                    'agencia_id' =>  intval($agencia_id),
                    'datos_caja' => $datos_caja,
                    'transferencias_pendientes' => $transferencias_pendientes,
                    'cuentas' => $cuentas,
                    'billeteo' => $billeteo,
                    'editar_billeteo' => $editar_billeteo,


                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function guardar_billeteo(Request $request)
    {


        $agencia_id = $request->agencia_id;
        $modo_billeteo = $request->modo_billeteo;

        // dd($modo_edicion);

        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));

        $caja_id = $request->caja_id;
        $frmBilleteo = json_decode($request->frmBilleteo);



        if ($modo_billeteo == "NUEVO") {

            Billeteo::on($conexion)->create([
                'caja_id' => $caja_id,
                '1_cent' => $frmBilleteo->un_centimo,
                '10_cent' => $frmBilleteo->diez_centimos,
                '20_cent' => $frmBilleteo->veinte_centimos,
                '50_cent' => $frmBilleteo->cincuenta_centimos,
                '1_sol' => $frmBilleteo->un_sol,
                '2_sol' => $frmBilleteo->dos_soles,
                '5_sol' => $frmBilleteo->cinco_soles,
                '10_sol' => $frmBilleteo->diez_soles,
                '20_sol' => $frmBilleteo->veinte_soles,
                '50_sol' => $frmBilleteo->cincuenta_soles,
                '100_sol' => $frmBilleteo->cien_soles,
                '200_sol' => $frmBilleteo->doscientos_soles,
                'datos_creacion' => $datos_registro,
            ]);
        }

        if ($modo_billeteo == "EDITAR") {

            Billeteo::on($conexion)->where('caja_id', $caja_id)->update([
                'caja_id' => $caja_id,
                '1_cent' => $frmBilleteo->un_centimo,
                '10_cent' => $frmBilleteo->diez_centimos,
                '20_cent' => $frmBilleteo->veinte_centimos,
                '50_cent' => $frmBilleteo->cincuenta_centimos,
                '1_sol' => $frmBilleteo->un_sol,
                '2_sol' => $frmBilleteo->dos_soles,
                '5_sol' => $frmBilleteo->cinco_soles,
                '10_sol' => $frmBilleteo->diez_soles,
                '20_sol' => $frmBilleteo->veinte_soles,
                '50_sol' => $frmBilleteo->cincuenta_soles,
                '100_sol' => $frmBilleteo->cien_soles,
                '200_sol' => $frmBilleteo->doscientos_soles,
                'datos_actualizacion' => $datos_registro,

            ]);
            // dd($editar);

        }



        return redirect()->route('caj.cierre_caja');
    }


    public function cerrar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $caja_id = $request->caja_id;

        $frmCierre = json_decode($request->frmCierre);
        $comentario = $frmCierre->comentario;
        $cuenta_id = $frmCierre->cuenta_id;
        $total_ingresos = $frmCierre->total_ingresos;
        $total_egresos = $frmCierre->total_egresos;

        $comentario_cierre  = (new CreditosController)->verificar_nulo($comentario);

        if ($comentario_cierre != null) {
            $comentario_cierre = mb_strtoupper($comentario_cierre);
        }

        Caja::on($conexion)->where('id', $caja_id)
            ->update([
                'comentario_cierre' => $comentario_cierre,
                'monto_cierre_ingresos' => $total_ingresos,
                'monto_cierre_egresos' => $total_egresos,
                'datos_cierre' => $datos_registro,
            ]);
        $fecha_cierre_caja = Caja::on($conexion)
            ->where('id', $caja_id)
            ->get()
            ->last()
            ->toArray();

        $saldo = $total_ingresos - $total_egresos;

        $tipo_movimiento = TipoMovimiento::on($conexion)->where('nombre', 'TRANSFERENCIA SALDO A CUENTA')->get()->last();
        $tipo_movimiento_id = $tipo_movimiento->id;
        $fecha_movimiento = (new CreditosController)->fecha_larga_aplicacion($agencia_id);


        Movimiento::on($conexion)->create([
            'cuenta_id' => $cuenta_id,
            'tipo' => 'I',
            'monto' => $saldo,
            'descripcion' => $tipo_movimiento->nombre,
            'tipo_movimiento_id' => $tipo_movimiento_id,
            'fecha_movimiento' => $fecha_movimiento,
            'datos_creacion' => $datos_registro
        ]);

        CuentaUsuario::on($conexion)->where('id', $cuenta_id)->update([
            'monto' => DB::raw("monto + $saldo"),
            'datos_actualizacion' => $datos_registro
        ]);
        return $fecha_cierre_caja; //cierre exitoso
        // return redirect()->route('cre.index');
    }
    public function declarar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $caja_id = $request->caja_id;
        $usuario_id = $request->usuario_id;
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_larga = (new CreditosController)->fecha_larga_aplicacion($agencia_id);
        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_id);

        $frmDeclaracion = json_decode($request->frmDeclaracion);
        $tipo = $frmDeclaracion->tipo;
        $monto = $frmDeclaracion->monto;
        if ($monto < 0) {
            $monto *= -1;
        }
        $descripcion = mb_strtoupper($frmDeclaracion->descripcion);

        $categoria = Categoria::on($conexion)->where('categoria', $tipo)->get()->last();
        $categoria_id = $categoria->id;

        $subcategoria = Subcategoria::on($conexion)->where('subcategoria', $tipo)->get()->last();
        $subcategoria_id = $subcategoria->id;


        $ubicacion_usuario = Usuario::from('usuarios as usu')
            ->select(
                'dep.departamento',
                'pro.provincia',
                'dis.distrito',
            )
            ->leftjoin('departamentos as dep', 'usu.departamento_id', 'dep.id')
            ->leftjoin('provincias as pro', 'usu.provincia_id', 'pro.id')
            ->leftjoin('distritos as dis', 'usu.distrito_id', 'dis.id')
            ->where('dni', session('usuario_dni'))
            ->get()
            ->last();


        $fecha = (new CreditosController)->fecha_texto($fecha_corta);

        $operaciones[] = (object)[
            'fecha' => $fecha_corta,
            'descripcion' => $tipo,
            'importe' => (new CreditosController)->formato_moneda($monto, 2, 'PEN')
        ];

        $datos_declaracion = (object)
        [
            'solicitante' => session('nombres'),
            'dni_solicitante' => session('usuario_dni'),
            'departamento' => $ubicacion_usuario->departamento,
            'provincia' => $ubicacion_usuario->provincia,
            'distrito' => $ubicacion_usuario->distrito,
            'operaciones' => $operaciones,
            'importe_total' => (new CreditosController)->formato_moneda($monto, 2, 'PEN'),
            'dni_responsable' => null,
            'dni_autorizacion' => null,
            'lugar_fecha' => $fecha
        ];



        $archivos_declaracion = $this->declaracion_jurada($datos_declaracion, false);

        Transaccion::on($conexion)->create([
            'tipo' => $tipo == 'SOBRANTE' ? 'I' : 'E',
            'categoria_id' => $categoria_id,
            'subcategoria_id' => $subcategoria_id,
            'agencia_id' => $agencia_id,
            'usuario_id' => $usuario_id,
            'area_trabajo_id ' => NULL,
            'comprobante_id' => NULL,
            'monto' => $monto,
            'concepto' => $descripcion,
            'caja_id' => $caja_id,
            'fecha_transaccion' => $fecha_larga,
            'regularizado' => 0,
            'datos_creacion' => $datos_registro,

        ]);



        return $archivos_declaracion;
    }

    public function declaracion_jurada($datos_declaracion, $with_pdf)
    {
        $solicitante = mb_strtoupper($datos_declaracion->solicitante);
        $dni_solicitante = $datos_declaracion->dni_solicitante;
        $departamento = mb_strtoupper($datos_declaracion->departamento);
        $provincia = mb_strtoupper($datos_declaracion->provincia);
        $distrito = mb_strtoupper($datos_declaracion->distrito);

        $operaciones = $datos_declaracion->operaciones;
        $importe_total = $datos_declaracion->importe_total;

        $dni_responsable = $datos_declaracion->dni_responsable;
        $dni_autorizacion = $datos_declaracion->dni_autorizacion;
        $lugar_fecha = $datos_declaracion->lugar_fecha;

        // Generate DOCX-------------------------
        $sheet = new TemplateProcessor('./report_templates/caja/reportes/rptDeclaracionJurada.docx');

        $sheet->setValues([
            'solicitante' => $solicitante,
            'dni_solicitante' => $dni_solicitante,
            'departamento' => $departamento,
            'provincia' => $provincia,
            'distrito' => $distrito,
            'dni_responsable' => $dni_responsable,
            'dni_autorizacion' => $dni_autorizacion,
            'importe_total' => $importe_total,
            'lugar_fecha' => $lugar_fecha
        ]);

        $sheet->cloneRowAndSetValues('fecha', $operaciones);

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptDeclaracionJurada', 5);
        $sheet->saveAs($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.docx');
        $path_docx =  '/temp_files/' . $nombre_archivo . '.docx';

        // Convert PDF-------------------------

        $path_pdf = null;

        return [
            'path_docx' => $path_docx,
            'path_pdf' => $path_pdf
        ];
    }

    public function verificar_transferencias($caja_id, $agencia_id)

    {
        $agencia_id = $agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $caja_id =  $caja_id;

        $transferencias_a_caja = CajaTransferencia::on($conexion)
            ->where([
                ['tipo', 'A_CAJA'],
                ['estado', 'PENDIENTE'],
                ['destinatario_id', $caja_id]
            ])
            ->count('id');

        $transferencias_de_caja = CajaTransferencia::on($conexion)
            ->where([
                ['tipo', 'A_CUENTA'],
                ['estado', 'PENDIENTE'],
                ['remitente_id', $caja_id]
            ])
            ->orWhere([
                ['tipo', 'A_CAJA'],
                ['estado', 'PENDIENTE'],
                ['remitente_id', $caja_id]
            ])
            ->count('id');

        $transferencias_de_cuenta = CuentaTransferencia::on($conexion)
            ->where([
                ['tipo', 'A_CAJA'],
                ['estado', 'PENDIENTE'],
                ['destinatario_id', $caja_id]
            ])
            ->count('id');

        return $transferencias_a_caja > 0 || $transferencias_de_caja > 0 || $transferencias_de_cuenta > 0;
    }
}
