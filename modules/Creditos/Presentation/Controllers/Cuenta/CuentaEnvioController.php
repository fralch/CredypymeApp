<?php

namespace Modules\Creditos\Presentation\Controllers\Cuenta;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Transferencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Movimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\TipoMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\Envio;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\EntidadBancaria;
use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class CuentaEnvioController extends Controller
{

    public function envio()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ENVIO', 'CREDITOS_CUENTA');

            if ($band == 1) {

                $usuarios = Usuario::select(
                    'dni',
                    'usuario',
                    'apellido_paterno',
                    'apellido_materno',
                    'agencia_id'
                )->where('habilitado', 1)->orderBy('usuario', 'asc')->get();

                $bancos = EntidadBancaria::all();

                return Inertia::render('Creditos/Cuenta/envio', [

                    'usuarios' => $usuarios,
                    'bancos' => $bancos
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar_cuentas($agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;

        return  CuentaUsuario::on($conexion)->from('cuenta_usuarios as cue_usu')
            ->select(
                'cue_usu.id',
                'cue_usu.monto',
                'usu.usuario',
                'usu.dni',

                'usu.agencia_id',
            )
            ->join('solucion_master.usuarios as usu', 'usu.dni', 'cue_usu.dni')
            ->where('cue_usu.dni', '<>', session('usuario_dni'))
            ->where([['cue_usu.con_cuenta',  1], ['usu.habilitado', 1]])
            ->orderBy('usu.usuario', 'asc')
            ->get();
    }

    public function mis_envios()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MIS_ENVIOS', 'CREDITOS_CUENTA');

            if ($band == 1) {

                $mi_agencia_id = session('id_agencia');
                $conexion = 'master_' .  $mi_agencia_id;

                $mi_cuenta = CuentaUsuario::on($conexion)->where('dni', session('usuario_dni'))->get()->last();
                $cuenta_id = $mi_cuenta->id;

                $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($mi_agencia_id);
                $fecha_menos_7_dias = date("Y-m-d", strtotime($fecha_actual . "- 7 days"));
                $fecha_mas_7_dias = date("Y-m-d", strtotime($fecha_actual . "+ 7 days"));

                $envios = Envio::on($conexion)->where('remitente_id', $cuenta_id)
                    ->whereBetween(DB::raw("SUBSTR(datos_creacion,11,10)"), [$fecha_menos_7_dias, $fecha_mas_7_dias])
                    ->orderBy('id', 'desc')
                    ->get();

                $lista_envios = [];

                foreach ($envios as $item) {
                    $tabla = 'solucion_master_' .  $item->agencia_destinatario_id;

                    $envio = Envio::on($conexion)->from('cuenta_envios as cue_env')
                        ->select(
                            'cue_env.id',
                            'cue_env.concepto',
                            'cue_env.estado',
                            'cue_env.monto',
                            'cue_env.agencia_remitente_id',
                            'cue_env.agencia_destinatario_id',
                            'cue_env.comentario_rechazo',
                            'cue_env.comprobante_envio',
                            'cue_env.datos_creacion',
                            'cue_env.datos_actualizacion',
                            'cue_env.comprobante_envio',
                            'cue_env.comprobante_recepcion',

                            'usu_1.usuario as usuario_remitente',
                            'usu_2.usuario as usuario_destinatario',

                            'age_rec.nombre as agencia_remitente',
                            'age_des.nombre as agencia_destino'
                        )
                        ->join('cuenta_usuarios as cue_usu_1', 'cue_env.remitente_id', 'cue_usu_1.id')
                        ->join("$tabla.cuenta_usuarios as cue_usu_2", 'cue_env.destinatario_id', "cue_usu_2.id")
                        ->join('solucion_master.usuarios as usu_1', 'usu_1.dni', 'cue_usu_1.dni')
                        ->join('solucion_master.usuarios as usu_2', 'usu_2.dni', 'cue_usu_2.dni')
                        ->join('solucion_master.agencias as age_rec', 'cue_env.agencia_remitente_id', 'age_rec.id_agencia')
                        ->join('solucion_master.agencias as age_des', 'cue_env.agencia_destinatario_id', 'age_des.id_agencia')
                        ->where('cue_env.id', $item->id)
                        ->get()->last();

                    $lista_envios[] = $envio;
                }


                $agencias = Agencia::all();

                $lista_recepciones = [];
                foreach ($agencias as $item) {
                    $conexion = 'master_' .  $item->id_agencia;
                    $tabla = "solucion_master_" . $mi_agencia_id;

                    $recepcion = Envio::on($conexion)->from('cuenta_envios as cue_env') //
                        ->select(
                            'cue_env.id',
                            'cue_env.remitente_id',
                            'cue_env.destinatario_id',
                            'cue_env.concepto',
                            'cue_env.estado',
                            'cue_env.monto',
                            'cue_env.agencia_remitente_id',
                            'cue_env.agencia_destinatario_id',
                            'cue_env.comentario_rechazo',
                            'cue_env.datos_creacion',
                            'cue_env.datos_actualizacion',
                            'cue_env.comprobante_envio',
                            'cue_env.comprobante_recepcion',

                            'usu_1.usuario as usuario_remitente',
                            'usu_2.usuario as usuario_destinatario',

                            'age_rec.nombre as agencia_remitente',
                            'age_des.nombre as agencia_destino'
                        )
                        ->join('cuenta_usuarios as cue_usu_1', 'cue_usu_1.id', 'cue_env.remitente_id')
                        ->join("$tabla.cuenta_usuarios as cue_usu_2", 'cue_usu_2.id', 'cue_env.destinatario_id')
                        ->join('solucion_master.usuarios as usu_1', 'usu_1.dni', 'cue_usu_1.dni')
                        ->join('solucion_master.usuarios as usu_2', 'usu_2.dni', 'cue_usu_2.dni')
                        ->join('solucion_master.agencias as age_rec', 'cue_env.agencia_remitente_id', 'age_rec.id_agencia')
                        ->join('solucion_master.agencias as age_des', 'cue_env.agencia_destinatario_id', 'age_des.id_agencia')
                        ->where([['cue_env.agencia_destinatario_id', $mi_agencia_id], ['cue_env.destinatario_id', $cuenta_id]])
                        ->whereBetween(DB::raw("SUBSTR(cue_env.datos_creacion,11,10)"),  [$fecha_menos_7_dias, $fecha_mas_7_dias])
                        ->orderBy('cue_env.id', 'desc')
                        ->get();

                    foreach ($recepcion as $item) {
                        $lista_recepciones[] = $item;
                    }
                }

                return Inertia::render('Creditos/Cuenta/mis_envios', [
                    'envios' => $lista_envios,
                    'recepciones' => $lista_recepciones

                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function registrar(Request $request)
    {
        $agencia_remitente_id = $request->agencia_remitente_id;
        $conexion = 'master_' .  $agencia_remitente_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_remitente_id);
        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_remitente_id);
        $año  = intval(date("Y", strtotime($fecha_corta)));

        $remitente_id = $request->remitente_id;
        $frmDatosEnvio = json_decode($request->frmDatosEnvio);

        $agencia_destinatario_id = $frmDatosEnvio->agencia_destinatario_id;
        $destinatario_id = $frmDatosEnvio->destinatario_id;
        $monto = $frmDatosEnvio->monto;
        $concepto = mb_strtoupper($frmDatosEnvio->concepto);
        $tipo = $frmDatosEnvio->tipo;
        $usuario_gestion_id = $frmDatosEnvio->usuario_gestion_id;

        if ($frmDatosEnvio->entidad_id == 0) {
            $entidad_id = null;
        } else {
            $entidad_id = $frmDatosEnvio->entidad_id;
        }

        $envio = Envio::on($conexion)->create([
            'agencia_remitente_id' => $agencia_remitente_id,
            'remitente_id' => $remitente_id,
            'agencia_destinatario_id' => $agencia_destinatario_id,
            'destinatario_id' => $destinatario_id,
            'monto' => $monto,
            'concepto' => $concepto,
            'estado' => 'PENDIENTE',
            'tipo' => $tipo,
            'usuario_gestion_id' => $usuario_gestion_id,
            'entidad_id' => $entidad_id,
            'datos_creacion' => $datos_registro
        ]);

        $envio_id = $envio->id;

        $path_name = pathinfo($_FILES['documento']['name']);
        $extension = "." . $path_name['extension'];
        $nombre_archivo = $año . '_env_' . $envio_id . $extension;
        $archivo = $_FILES['documento']['tmp_name'];
        $ruta = '/imagenes_server/creditos/cuenta/envios/' . $agencia_remitente_id . '/' . $año . '/';
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_archivo;
        $calidad = 10;
        // move_uploaded_file($archivo, $ruta);

        (new CreditosController)->compressImage($archivo, $ruta, $calidad);


        Envio::on($conexion)->where('id', $envio_id)
            ->update(['comprobante_envio' => $nombre_archivo]);


        return redirect()->route('cre.index');
    }


    public function confirmar(Request $request)
    {
        $agencia_destinatario = session('id_agencia');
        $conexion_destinatario = 'master_' .  $agencia_destinatario;

        $agencia_remitente = $request->agencia_remitente;
        $conexion_remitente = 'master_' .  $agencia_remitente;

        $datos_registro = (new CreditosController)->datos_registro($agencia_destinatario);
        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_destinatario);

        $fecha_remitente = (new CreditosController)->fecha_larga_aplicacion($agencia_remitente);
        $fecha_destinatario = (new CreditosController)->fecha_larga_aplicacion($agencia_destinatario);

        $año  = intval(date("Y", strtotime($fecha_corta)));

        $modo = $request->modo;

        if ($modo == 'ACEPTAR') {

            $envio = json_decode($request->envio);
            $envio_id = $envio->id;
            $remitente_id = $envio->remitente_id;
            $destinatario_id = $envio->destinatario_id;
            $monto = $envio->monto;
            // $json_datos_creacion = json_decode($envio->datos_creacion);
            // $fecha_remitente = $json_datos_creacion->fecha;

            // dd($fecha_remitente,$fecha_larga_aplicacion);

            CuentaUsuario::on($conexion_remitente)->where('id', $remitente_id)
                ->update([
                    'monto' => DB::raw("monto-$monto"),
                    'datos_actualizacion' => $datos_registro
                ]);

            CuentaUsuario::on($conexion_destinatario)->where('id', $destinatario_id)
                ->update([
                    'monto' => DB::raw("monto+$monto"),
                    'datos_actualizacion' => $datos_registro
                ]);

            $tipo_movimiento = TipoMovimiento::on($conexion_remitente)->where('nombre', 'ENVÍO A AGENCIA')->get()->last();
            $tipo_movimiento_id = $tipo_movimiento->id;

            Movimiento::on($conexion_remitente)->create([
                'cuenta_id' => $remitente_id,
                'tipo' => 'E',
                'monto' => $monto,
                'descripcion' => $tipo_movimiento->nombre,
                'tipo_movimiento_id' => $tipo_movimiento_id,
                'fecha_movimiento' => $fecha_remitente,
                'datos_creacion' => $datos_registro
            ]);

            $tipo_movimiento = TipoMovimiento::on($conexion_destinatario)->where('nombre', 'RECEPCIÓN DE AGENCIA')->get()->last();
            $tipo_movimiento_id = $tipo_movimiento->id;

            Movimiento::on($conexion_destinatario)->create([
                'cuenta_id' => $destinatario_id,
                'tipo' => 'I',
                'monto' => $monto,
                'descripcion' => $tipo_movimiento->nombre,
                'tipo_movimiento_id' => $tipo_movimiento_id,
                'fecha_movimiento' => $fecha_destinatario,
                'datos_creacion' => $datos_registro
            ]);

            $path_name = pathinfo($_FILES['documento']['name']);
            $extension = "." . $path_name['extension'];
            $nombre_archivo = $año . '_rec_' . $envio_id . $extension;
            $archivo = $_FILES['documento']['tmp_name'];
            $ruta = '/imagenes_server/creditos/cuenta/envios/' . $agencia_remitente . '/' . $año . '/';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_archivo;
            $calidad = 10;
            // move_uploaded_file($archivo, $ruta);

            (new CreditosController)->compressImage($archivo, $ruta, $calidad);

            Envio::on($conexion_remitente)->where('id', $envio_id)
                ->update([
                    'estado' => 'CONFIRMADO',
                    'comprobante_recepcion' => $nombre_archivo,
                    'datos_actualizacion' => $datos_registro
                ]);
        } else if ($modo == 'RECHAZAR') {

            $envio_id = $request->envio_id;
            $comentario = $request->comentario;

            Envio::on($conexion_remitente)->where('id', $envio_id)
                ->update([
                    'estado' => 'RECHAZADO',
                    'comentario_rechazo' => $comentario,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return redirect()->route('cue.mis_envios');
    }

    public function rechazar_envio(Request $request)
    {
        // return $request;
        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));
        $id = $request->id;
        $comentario = mb_strtoupper($request->comentario);

        Envio::where('id', $id)
            ->update([
                'estado' => 'RECHAZADO',
                'comentario_rechazo' => $comentario,
                'datos_actualizacion' => $datos_registro
            ]);

        return redirect()->route('cue.mis_envios');
    }

    // public function verificar_pendientes()
    // {
    //     $fecha_actual = (new CreditosController)->fecha_corta_aplicacion(session('id_agencia'));

    //     return Transferencia::select('id')->where([
    //         ['agencia_id', session('id_agencia')],
    //         [DB::raw("STR_TO_DATE(SUBSTRING(datos_creacion,11,19), '%Y-%m-%d')"), $fecha_actual],
    //         ['estado', 'PENDIENTE']
    //     ])->get();
    // }

    public function imprimir(Request $request)
    {

        $datos_envio = json_decode($request->datos_envio);
        $tipo = $request->tipo;

        // Ordenando array de datos-------------------------------

        $inputFileName = './report_templates/caja/reportes/vchEnvio.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos-----------------------------

        if ($tipo == 'envio') {
            $sheet->setCellValue("A2", 'ENVÍO DE EFECTIVO');
        } else if ($tipo == 'recepcion') {
            $sheet->setCellValue("A2", 'RECEPCIÓN DE EFECTIVO');
        }

        $sheet->setCellValue("B4", $datos_envio->agencia_remitente);
        $sheet->setCellValue("B5", $datos_envio->usuario_remitente);
        $sheet->setCellValue("B6", $datos_envio->agencia_destino);
        $sheet->setCellValue("B7", $datos_envio->usuario_destinatario);

        $sheet->setCellValue("A9", $datos_envio->concepto);
        $sheet->setCellValue("B11", $datos_envio->monto);

        $datos_creacion = json_decode($datos_envio->datos_creacion);
        $datos_actualizacion = json_decode($datos_envio->datos_actualizacion);

        $sheet->setCellValue("B13", $datos_creacion->fecha);
        $sheet->setCellValue("B14", $datos_actualizacion->fecha);

        // Exportar para descarga-------------------------

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('vchEnvio', 5);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }
}
