<?php

namespace Modules\Creditos\Presentation\Controllers\Credito;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Gth\Presentation\Controllers\Usuarios\UsuarioController;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Evaluacion_financiera;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_activo_corriente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_activo_no_corriente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_pasivo_corriente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_flujo_caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_comentarios;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_convenio;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, Color};
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class EvaluacionFinancieraController extends Controller
{
    public function evaluacion_financiera($cliente_id, $agencia_id)
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'EVALUACION_FINANCIERA', 'CREDITOS_CREDITO');
            if ($band == 1) {
                $conexion = 'master_' .  $agencia_id;

                $datos_registro = (new CreditosController)->datos_registro($agencia_id);
                $datos_personales = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                    ->select(
                        'cli_reg.id',
                        'cli_reg.nombres',
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.dni',
                        'cli_reg.central_riesgo',
                        'ag.nombre as nombre_agencia',
                        'dis.distrito',
                        'prov.provincia',
                        'dep.departamento',
                        'cli_reg.direccion',

                        'cli_neg.id as negocio_id',
                        'cli_neg.vinculado as negocio_vinculado',
                        'ciiu.codigo as codigo_ciiu'
                    )
                    ->join('solucion_master.agencias as ag', 'cli_reg.agencia_id', 'ag.id_agencia')
                    ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
                    ->join('solucion_master.provincias as prov', 'cli_reg.provincia_id', 'prov.id')
                    ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
                    ->leftjoin('cliente_negocios as cli_neg', 'cli_reg.id', 'cli_neg.cliente_id')
                    ->leftjoin('solucion_master.ciiu', 'cli_neg.ciiu_id', 'ciiu.id')
                    ->where([['cli_reg.id', $cliente_id]])
                    ->get();

                if (count($datos_personales) > 1) {
                    $datos_personales = $datos_personales->where('negocio_vinculado', 1);
                    $datos_personales = $datos_personales->values()[0];
                } else {
                    $datos_personales = $datos_personales[0];
                }

                $evaluacion_id = Evaluacion_financiera::on($conexion)->select('id')->where('cliente_id', $cliente_id)->get()->last();
                // Verificar si el CIIU es del convenio de planillas
                if ($datos_personales->codigo_ciiu == 9980) {

                    if ($evaluacion_id == null) {
                        $evaluacion_id = Evaluacion_financiera::on($conexion)->create([
                            'cliente_id' => $cliente_id,
                            'datos_creacion' => $datos_registro
                        ]);
                        $convenio = Ef_convenio::on($conexion)->create([
                            'evaluacion_id' => $evaluacion_id,
                            'datos_creacion' => $datos_registro
                        ]);
                    } else {
                        $evaluacion_id = $evaluacion_id->id;
                        $convenio = Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->get()->last();
                        if ($convenio == null) {
                            $convenio = Ef_convenio::on($conexion)->create([
                                'evaluacion_id' => $evaluacion_id,
                                'datos_creacion' => $datos_registro
                            ]);
                        }
                    }
                    $convenio = Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->get()->last();
                    $datos_evaluacion = Evaluacion_financiera::on($conexion)->where('id', $evaluacion_id)->get()->last();

                    return Inertia::render('Creditos/Creditos/evaluacion_financiera_convenio', [
                        'agencia_id' =>   intval($agencia_id),
                        'cliente_id' => intval($cliente_id),
                        'datos_personales' => $datos_personales,
                        'datos_evaluacion' => $datos_evaluacion,
                        'evaluacion_id' => $evaluacion_id,
                        'convenio' => $convenio
                    ]);
                } else {
                    if ($evaluacion_id == null) {

                        // Funcional para COSTOS OPERATIVOS
                        $unidades_vacias_1[] = (object) array('id' => 1, 'descripcion' => 'AGUA', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 2, 'descripcion' => 'ALQUILER', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 3, 'descripcion' => 'CABLE', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 4, 'descripcion' => 'CELULAR', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 5, 'descripcion' => 'CUOTAS ENTIDADES NO SUPERVIZADAS', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 6, 'descripcion' => 'IMPUESTO VEHICULAR', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 7, 'descripcion' => 'IMPUESTOS', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 8, 'descripcion' => 'INTERNET', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 9, 'descripcion' => 'LUZ', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 10, 'descripcion' => 'OTROS', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 11, 'descripcion' => 'PERSONAL', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 12, 'descripcion' => 'SOAT', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 13, 'descripcion' => 'TELEFONO', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 14, 'descripcion' => 'TRANSPORTE', 'monto' => 0);
                        $unidades_vacias_1[] = (object) array('id' => 15, 'descripcion' => 'TRIBUTOS', 'monto' => 0);

                        $unidades_vacias_1 = json_encode($unidades_vacias_1);

                        // Funcional para GASTOS FAMILIARES
                        $unidades_vacias_2[] = (object) array('id' => 1, 'descripcion' => 'AGUA', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 2, 'descripcion' => 'ALQUILER (CASA)', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 3, 'descripcion' => 'CANASTA BÁSICA', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 4, 'descripcion' => 'CUOTAS_CONSUMO', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 5, 'descripcion' => 'EDUCACIÓN', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 6, 'descripcion' => 'GAS', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 7, 'descripcion' => 'GASTOS SOCIALES', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 8, 'descripcion' => 'IMPREVISTOS', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 9, 'descripcion' => 'INTERNET', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 10, 'descripcion' => 'LUZ', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 11, 'descripcion' => 'OTROS', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 12, 'descripcion' => 'SALUD', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 13, 'descripcion' => 'TELÉFONO', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 14, 'descripcion' => 'TRANSPORTE', 'monto' => 0);
                        $unidades_vacias_2[] = (object) array('id' => 15, 'descripcion' => 'VESTIDO', 'monto' => 0);

                        $unidades_vacias_2 = json_encode($unidades_vacias_2);


                        //-----------------------------------------------------

                        $evaluacion_id = Evaluacion_financiera::on($conexion)->create([
                            'cliente_id' => $cliente_id,
                            'datos_creacion' => $datos_registro
                        ]);
                        $evaluacion_id = $evaluacion_id->id;

                        $activo_corriente = Ef_activo_corriente::on($conexion)->create([
                            'evaluacion_id' => $evaluacion_id,
                            'datos_creacion' => $datos_registro
                        ]);

                        $activo_no_corriente = Ef_activo_no_corriente::on($conexion)->create([
                            'evaluacion_id' => $evaluacion_id,
                            'datos_creacion' => $datos_registro
                        ]);
                        $pasivo_corriente = Ef_pasivo_corriente::on($conexion)->create([
                            'evaluacion_id' => $evaluacion_id,
                            'datos_creacion' => $datos_registro
                        ]);
                        $flujo_caja = Ef_flujo_caja::on($conexion)->create([
                            'evaluacion_id' => $evaluacion_id,
                            'costos_operativos' => $unidades_vacias_1,
                            'gastos_familiares' => $unidades_vacias_2,
                            'datos_creacion' => $datos_registro

                        ]);
                        $comentarios = Ef_comentarios::on($conexion)->create([
                            'evaluacion_id' => $evaluacion_id,
                            'datos_creacion' => $datos_registro
                        ]);
                    } else {
                        $evaluacion_id = $evaluacion_id->id;
                    }

                    $datos_evaluacion = Evaluacion_financiera::on($conexion)->where('id', $evaluacion_id)->get()->last();
                    $activo_corriente = Ef_activo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->get()->last();
                    $activo_no_corriente = Ef_activo_no_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->get()->last();
                    $pasivo_corriente = Ef_pasivo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->get()->last();
                    $flujo_caja = Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->get()->last();
                    $comentarios = Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->get()->last();

                    return Inertia::render('Creditos/Creditos/evaluacion_financiera', [
                        'agencia_id' =>   intval($agencia_id),
                        'cliente_id' => intval($cliente_id),
                        'datos_personales' => $datos_personales,
                        'datos_evaluacion' => $datos_evaluacion,
                        'evaluacion_id' => $evaluacion_id,
                        'activo_corriente' => $activo_corriente,
                        'activo_no_corriente' => $activo_no_corriente,
                        'pasivo_corriente' => $pasivo_corriente,
                        'flujo_caja' => $flujo_caja,
                        'comentarios' => $comentarios,
                    ]);
                }
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }

    public function guardar_activo_corriente(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $sub_grupo = $request->sub_grupo;
        $evaluacion_id = $request->evaluacion_id;
        $cliente_id = $request->cliente_id;

        if ($sub_grupo == 'CAJA') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_activo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['caja' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'ADELANTO_PROVEEDORES') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_activo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['adelantos' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'VARIOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_activo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['varios' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'BANCOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_activo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['bancos' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'CUENTAS_COBRAR') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_activo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['cuentas_cobrar' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'INVENTARIO_MERCADERIA') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_activo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['inventario_mercaderia' => $valor, 'datos_actualizacion' => $datos_registro]);
        }

        Evaluacion_financiera::on($conexion)->where('id', $evaluacion_id)->update(['datos_actualizacion' => $datos_registro]);

        return redirect()->route('cre.evaluacion_financiera', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }

    public function guardar_activo_no_corriente(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
        $año = substr($fecha_corta, 0, 4);

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $sub_grupo = $request->sub_grupo;
        $evaluacion_id = $request->evaluacion_id;
        $cliente_id = $request->cliente_id;

        if ($sub_grupo == 'MUEBLES_ENSERES') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_activo_no_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['muebles_enseres' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'INMUEBLE_MAQUINARIA_EQUIPO') {

            $valor = (new CreditosController)->verificar_nulo($request->valor);
            $nueva_foto = $request->nueva_foto;
            if ($nueva_foto != 'false') {
                $nueva_foto = json_decode($nueva_foto);
                if ($nueva_foto->nuevo_1 == 'true' && $request->nombre_1 != 'null') {
                    $nombre_foto = $request->nombre_1;
                    $archivo = $_FILES['foto_1']['tmp_name'];
                    $ruta = '/imagenes_server/creditos/evaluaciones/bienes/' . $agencia_id . '/' . $año;
                    $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_foto;
                    $calidad = 10;
                    (new CreditosController)->compressImage($archivo, $ruta, $calidad);
                }
                if ($nueva_foto->nuevo_2 == 'true' && $request->nombre_2 != 'null') {
                    $nombre_foto = $request->nombre_2;
                    $archivo = $_FILES['foto_2']['tmp_name'];
                    $ruta = '/imagenes_server/creditos/evaluaciones/bienes/' . $agencia_id . '/' . $año;
                    $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_foto;
                    $calidad = 10;
                    (new CreditosController)->compressImage($archivo, $ruta, $calidad);
                }
                if ($nueva_foto->nuevo_3 == 'true' && $request->nombre_3 != 'null') {
                    $nombre_foto = $request->nombre_3;
                    $archivo = $_FILES['foto_3']['tmp_name'];
                    $ruta = '/imagenes_server/creditos/evaluaciones/bienes/' . $agencia_id . '/' . $año;
                    $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_foto;
                    $calidad = 10;
                    (new CreditosController)->compressImage($archivo, $ruta, $calidad);
                }
                if ($nueva_foto->nuevo_4 == 'true' && $request->nombre_4 != 'null') {
                    $nombre_foto = $request->nombre_4;
                    $archivo = $_FILES['foto_4']['tmp_name'];
                    $ruta = '/imagenes_server/creditos/evaluaciones/bienes/' . $agencia_id . '/' . $año;
                    $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_foto;
                    $calidad = 10;
                    (new CreditosController)->compressImage($archivo, $ruta, $calidad);
                }
            }

            Ef_activo_no_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update([
                'inmueble_maquinaria_equipo' => $valor,
                'datos_actualizacion' => $datos_registro
            ]);
        }

        Evaluacion_financiera::on($conexion)->where('id', $evaluacion_id)->update(['datos_actualizacion' => $datos_registro]);
        return redirect()->route('cre.evaluacion_financiera', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }

    public function guardar_pasivo_corriente(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $sub_grupo = $request->sub_grupo;
        $evaluacion_id = $request->evaluacion_id;
        $cliente_id = $request->cliente_id;

        if ($sub_grupo == 'OTROS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_pasivo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['otros' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'ADELANTO_PROVEEDORES') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_pasivo_corriente::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['adelanto_proveedores' => $valor, 'datos_actualizacion' => $datos_registro]);
        }

        Evaluacion_financiera::on($conexion)->where('id', $evaluacion_id)->update(['datos_actualizacion' => $datos_registro]);
        return redirect()->route('cre.evaluacion_financiera', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }
    public function guardar_flujo_caja(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $sub_grupo = $request->sub_grupo;
        $evaluacion_id = $request->evaluacion_id;
        $cliente_id = $request->cliente_id;

        if ($sub_grupo == 'VENTAS_MONTO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['ventas_monto' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'OTROS_INGRESOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['otros_ingresos' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'VENTAS_DETALLADO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['ventas_detallado' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'COSTO_VENTAS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['costo_ventas' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'COSTOS_OPERATIVOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['costos_operativos' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'GASTOS_FAMILIARES') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['gastos_familiares' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'PRESTAMOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['prestamos' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'VEHICULOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_flujo_caja::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['vehiculos' => $valor, 'datos_actualizacion' => $datos_registro]);
        }

        Evaluacion_financiera::on($conexion)->where('id', $evaluacion_id)->update(['datos_actualizacion' => $datos_registro]);
        return redirect()->route('cre.evaluacion_financiera', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }
    public function guardar_comentarios(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $sub_grupo = $request->sub_grupo;
        $evaluacion_id = $request->evaluacion_id;
        $cliente_id = $request->cliente_id;

        if ($sub_grupo == 'ANTECEDENTES_CLIENTE') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['antecedentes_cliente' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_NEGOCIO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_negocio' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_DOMICILIO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_domicilio' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_FAMILIAR_VECINO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_familiar_vecino' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_PARIENTE') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_pariente' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_AVAL') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_aval' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'DESTINO_PRESTAMO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['destino_prestamo' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'OTROS_COMENTARIOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_comentarios::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['otros_comentarios' => $valor, 'datos_actualizacion' => $datos_registro]);
        }

        Evaluacion_financiera::on($conexion)->where('id', $evaluacion_id)->update(['datos_actualizacion' => $datos_registro]);
        return redirect()->route('cre.evaluacion_financiera', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }
    public function guardar_convenio(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $sub_grupo = $request->sub_grupo;
        $evaluacion_id = $request->evaluacion_id;
        $cliente_id = $request->cliente_id;

        if ($sub_grupo == 'SUELDO_NETO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['sueldo_neto' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'PORCENTAJE_DESCUENTO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['porcentaje_descuento' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'OTROS_INGRESOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['otros_ingresos' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'PRESTAMOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['prestamos' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else  if ($sub_grupo == 'ANTECEDENTES_CLIENTE') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['antecedentes_cliente' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_LABORALES') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_laborales' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_DOMICILIO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_domicilio' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_PARIENTE_VECINO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_pariente_vecino' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'REFERENCIAS_AVAL') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['referencias_aval' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'DESTINO_PRESTAMO') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['destino_prestamo' => $valor, 'datos_actualizacion' => $datos_registro]);
        } else if ($sub_grupo == 'OTROS_COMENTARIOS') {
            $valor = (new CreditosController)->verificar_nulo($request->valor);
            Ef_convenio::on($conexion)->where('evaluacion_id', $evaluacion_id)->update(['otros_comentarios' => $valor, 'datos_actualizacion' => $datos_registro]);
        }

        Evaluacion_financiera::on($conexion)->where('id', $evaluacion_id)->update(['datos_actualizacion' => $datos_registro]);

        return redirect()->route('cre.evaluacion_financiera', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }

    public function exportar(Request $request)
    {
        $modo = $request->modo;

        if ($modo == 'microempresarial') {
            return $this->exportar_microempresarial($request);
        } else if ($modo == 'comentarios') {
            return $this->exportar_comentarios($request);
        } else if ($modo == 'convenio') {
            return $this->exportar_convenio($request);
        }
    }

    public function exportar_microempresarial($request)
    {
        $agencia_id = $request->agencia_id;
        $datos_personales = json_decode($request->datos_personales);
        $datos_pariente = json_decode($request->datos_pariente);
        $datos_aval = json_decode($request->datos_aval);
        $datos_evaluacion = json_decode($request->datos_evaluacion);
        $totales = json_decode($request->totales);
        $evaluacion_financiera = json_decode($request->evaluacion_financiera);

        // Leer Plantilla-------------------------
        $inputFileName = './report_templates/creditos/evaluaciones/rptEvaluacionFinanciera.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $reader->setLoadSheetsOnly('rptEFMicroempresarial');
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos -----------------------------

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));

        $titular = $datos_personales->apellido_paterno . ' ' .
            $datos_personales->apellido_materno . ' ' .
            $datos_personales->nombres . ' - ' .
            $datos_personales->dni;
        $sheet->setCellValue('B5', $titular);

        if (count($datos_pariente) > 0) {
            $pariente = $datos_pariente[0]->apellido_paterno . ' ' .
                $datos_pariente[0]->apellido_materno . ' ' .
                $datos_pariente[0]->nombres . ' - ' .
                $datos_pariente[0]->dni;
            $sheet->setCellValue('B7', $pariente);
        }

        if (count($datos_aval) > 0) {
            $pariente = $datos_aval[0]->apellido_paterno . ' ' .
                $datos_aval[0]->apellido_materno . ' ' .
                $datos_aval[0]->nombres . ' - ' .
                $datos_aval[0]->dni;
            $sheet->setCellValue('B9', $pariente);
        }

        $sheet->setCellValue('M5', json_decode($datos_evaluacion->datos_creacion)->fecha);

        $datos_actualizacion = json_decode($datos_evaluacion->datos_actualizacion);
        $sheet->setCellValue('M7',   $datos_actualizacion == null ? '-' :  $datos_actualizacion->fecha);
        $sheet->setCellValue('M9', $datos_personales->nombre_agencia);

        // OBTENER FORMATOS

        $formato_titulo_texto = $sheet->getStyle("B15")->exportArray();
        $formato_titulo_monto = $sheet->getStyle("C15")->exportArray();

        $formato_subtitulo_texto = $sheet->getStyle("B16")->exportArray();
        $formato_subtitulo_monto = $sheet->getStyle("C16")->exportArray();

        $formato_tabla_encabezado = $sheet->getStyle("B17")->exportArray();
        $formato_tabla_texto = $sheet->getStyle("C17")->exportArray();
        $formato_tabla_cantidad = $sheet->getStyle("D17")->exportArray();
        $formato_tabla_monto = $sheet->getStyle("E17")->exportArray();

        $formato_datos_texto = $sheet->getStyle("B18")->exportArray();
        $formato_datos_monto = $sheet->getStyle("C18")->exportArray();

        $formato_subdatos_texto = $sheet->getStyle("B19")->exportArray();
        $formato_subdatos_monto = $sheet->getStyle("C19")->exportArray();


        $sheet->removeRow(19);
        $sheet->removeRow(18);
        $sheet->removeRow(17);
        $sheet->removeRow(16);
        $sheet->removeRow(15);

        $fila_1 = 14;
        $fila_2 = 14;

        // ------------------------- ACTIVO CORRIENTE -------------------------

        $categorias = [
            'ACTIVO',
            'ACTIVO CORRIENTE',
            'CAJA (EFECTIVO)',
            'BANCOS',
            'CUENTAS POR COBRAR A CLIENTES',
            'ADELANTOS REALIZADOS A PROVEEDORES',
            'VARIOS'
        ];

        foreach ($categorias as  $key => $value) {

            $sheet->insertNewRowBefore($fila_1 + 1);

            $sheet->setCellValue('B' . $fila_1, $value);
            $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
            $sheet->mergeCells($rango);

            if ($key == 0) {
                $sheet->getStyle($rango)->applyFromArray($formato_titulo_texto);
            } else if ($key == 1) {
                $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);
            } else {
                $sheet->getStyle($rango)->applyFromArray($formato_datos_texto);
            }

            $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
            $sheet->mergeCells($rango);

            if ($key == 0) {
                $sheet->getStyle($rango)->applyFromArray($formato_titulo_monto);
            } else if ($key == 1) {
                $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);
            } else {
                $sheet->getStyle($rango)->applyFromArray($formato_datos_monto);
            }

            $fila_1++;
        }

        $fila_1 = 14;

        $activo_corriente = $evaluacion_financiera->activo_corriente;
        $total_activo = $totales->activo;

        $sheet->setCellValue('E' . $fila_1, $total_activo->total);
        $sheet->setCellValue('E' . $fila_1 + 1, $total_activo->total_activo_corriente);
        $sheet->setCellValue('E' . $fila_1 + 2, $activo_corriente->caja == null ? 0 : $activo_corriente->caja);
        $sheet->setCellValue('E' . $fila_1 + 3, $total_activo->total_bancos == null ? 0 : $total_activo->total_bancos);
        $sheet->setCellValue('E' . $fila_1 + 4, $total_activo->total_cuentas_cobrar == null ? 0 : $total_activo->total_cuentas_cobrar);
        $sheet->setCellValue('E' . $fila_1 + 5, $activo_corriente->adelantos == null ? 0 : $activo_corriente->adelantos);
        $sheet->setCellValue('E' . $fila_1 + 6, $activo_corriente->varios == null ? 0 : $activo_corriente->varios);

        $fila_1 += 8;
        $sheet->insertNewRowBefore($fila_1 + 1);

        // ------------------------- INVENTARIO -------------------------

        $sheet->setCellValue('B' . $fila_1, 'INVENTARIO');
        $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);

        $sheet->setCellValue('E' . $fila_1, $total_activo->total_inventario);
        $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);

        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);

        $sheet->setCellValue('B' . $fila_1, 'PRODUCTO/MATERIA PRIMA');
        $rango =  'B' . $fila_1 . ':' . 'C' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->setCellValue('D' . $fila_1, 'CANT.');
        $sheet->setCellValue('E' . $fila_1, 'PREC. COMPRA');
        $sheet->setCellValue('F' . $fila_1, 'SUBTOTAL');

        $rango =  'B' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->getStyle($rango)->applyFromArray($formato_tabla_encabezado);

        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);

        $inventario_mercaderia = json_decode($activo_corriente->inventario_mercaderia);

        if ($inventario_mercaderia != null) {

            foreach ($inventario_mercaderia as $item) {

                $sheet->insertNewRowBefore($fila_1 + 1);

                $sheet->setCellValue('B' . $fila_1, $item->descripcion);
                $rango =  'B' . $fila_1 . ':' . 'C' . $fila_1;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_tabla_texto);

                $sheet->setCellValue('D' . $fila_1, $item->cantidad);
                $sheet->getStyle('D' . $fila_1)->applyFromArray($formato_tabla_cantidad);

                $sheet->setCellValue('E' . $fila_1, $item->precio_unitario);
                $sheet->getStyle('E' . $fila_1)->applyFromArray($formato_tabla_monto);

                $sheet->setCellValue('F' . $fila_1, floatval($item->cantidad) *  floatval($item->precio_unitario));
                $sheet->getStyle('F' . $fila_1)->applyFromArray($formato_tabla_monto);

                $fila_1++;
            }
        }

        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);

        // ------------------------- ACTIVO NO CORRIENTE -------------------------

        $sheet->setCellValue('B' . $fila_1, 'ACTIVO NO CORRIENTE');
        $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);

        $sheet->setCellValue('E' . $fila_1, $total_activo->total_activo_no_corriente);
        $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);

        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);

        $activo_no_corriente = $evaluacion_financiera->activo_no_corriente;

        // MUEBLES Y ENSERES -------------------------
        $sheet->setCellValue('B' . $fila_1, 'MUEBLES Y ENSERES');
        $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_datos_texto);

        $sheet->setCellValue('E' . $fila_1, $total_activo->total_muebles_enseres);
        $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_datos_monto);

        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);

        $muebles_enseres = json_decode($activo_no_corriente->muebles_enseres);

        if ($muebles_enseres != null) {
            foreach ($muebles_enseres as $key => $item) {
                $sheet->insertNewRowBefore($fila_1 + 1);

                $sheet->setCellValue('B' . $fila_1, $item->descripcion);
                $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_subdatos_texto);

                $sheet->setCellValue('E' . $fila_1, $item->monto);
                $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_subdatos_monto);

                $fila_1++;
            }
        }
        $sheet->insertNewRowBefore($fila_1 + 1);

        // INMUEBLE, MAQUINARIA Y EQUIPO -------------------------

        $sheet->setCellValue('B' . $fila_1, 'INMUEBLE, MAQUINARIA Y EQUIPO');
        $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_datos_texto);

        $sheet->setCellValue('E' . $fila_1, $total_activo->total_inmueble_maquinaria);
        $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_datos_monto);

        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);

        $inmueble_maquinaria_equipo = json_decode($activo_no_corriente->inmueble_maquinaria_equipo);

        if ($inmueble_maquinaria_equipo != null) {

            foreach ($inmueble_maquinaria_equipo as $key => $item) {
                $sheet->insertNewRowBefore($fila_1 + 1);

                $sheet->setCellValue('B' . $fila_1, $item->cantidad . ' ' . $item->descripcion);
                $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_subdatos_texto);

                $sheet->setCellValue('E' . $fila_1, floatval($item->cantidad)  * floatval($item->precio_actual));
                $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_subdatos_monto);

                $fila_1++;
            }
        }
        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);
        // ------------------------- PASIVO  -------------------------

        // PASIVO CORRIENTE -------------------------

        $categorias = [
            'PASIVO',
            'PASIVO CORRIENTE',
            'DEUDA A CORTO PLAZO MENOR A 1 AÑO',
            'ADELANTO RECIBIDO DE PROVEEDORES'
        ];

        $fila_1_1 = $fila_1;
        foreach ($categorias as  $key => $value) {

            $sheet->insertNewRowBefore($fila_1_1 + 1);

            $sheet->setCellValue('B' . $fila_1_1, $value);
            $rango =  'B' . $fila_1_1 . ':' . 'D' . $fila_1_1;
            $sheet->mergeCells($rango);

            if ($key == 0) {
                $sheet->getStyle($rango)->applyFromArray($formato_titulo_texto);
            } else if ($key == 1) {
                $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);
            } else {
                $sheet->getStyle($rango)->applyFromArray($formato_datos_texto);
            }

            $rango =  'E' . $fila_1_1 . ':' . 'F' . $fila_1_1;
            $sheet->mergeCells($rango);

            if ($key == 0) {
                $sheet->getStyle($rango)->applyFromArray($formato_titulo_monto);
            } else if ($key == 1) {
                $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);
            } else {
                $sheet->getStyle($rango)->applyFromArray($formato_datos_monto);
            }

            $fila_1_1++;
        }

        $total_pasivo = $totales->pasivo;
        $pasivo_corriente = $evaluacion_financiera->pasivo_corriente;

        $sheet->setCellValue('E' . $fila_1, $total_pasivo->total);
        $sheet->setCellValue('E' . $fila_1 + 1, $total_pasivo->total_pasivo_corriente);
        $sheet->setCellValue('E' . $fila_1 + 2, $total_pasivo->total_deuda_corto_plazo);
        $sheet->setCellValue('E' . $fila_1 + 3, $total_pasivo->total_adelanto_proveedores);

        $fila_1 += 4;
        $sheet->insertNewRowBefore($fila_1 + 1);

        $adelanto_proveedores = json_decode($pasivo_corriente->adelanto_proveedores);

        if ($adelanto_proveedores != null) {
            foreach ($adelanto_proveedores as $key => $item) {
                $sheet->insertNewRowBefore($fila_1 + 1);

                $sheet->setCellValue('B' . $fila_1, $item->descripcion);
                $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_subdatos_texto);

                $sheet->setCellValue('E' . $fila_1, $item->monto);
                $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_subdatos_monto);

                $fila_1++;
            }
        }

        $sheet->insertNewRowBefore($fila_1 + 1);

        $sheet->setCellValue('B' . $fila_1, 'OTROS');
        $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_datos_texto);

        $sheet->setCellValue('E' . $fila_1, $pasivo_corriente->otros == null ? 0 : $pasivo_corriente->otros);
        $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_datos_monto);

        $fila_1 += 2;
        $sheet->insertNewRowBefore($fila_1 + 1);

        // PASIVO NO CORRIENTE -------------------------

        $sheet->setCellValue('B' . $fila_1, 'PASIVO NO CORRIENTE');
        $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);

        $sheet->setCellValue('E' . $fila_1, $total_pasivo->total_pasivo_no_corriente);
        $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);

        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);

        $sheet->setCellValue('B' . $fila_1, 'DEUDA A LARGO PLAZO MAYOR A 1 AÑO');
        $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_datos_texto);

        $sheet->setCellValue('E' . $fila_1, $total_pasivo->total_deuda_largo_plazo);
        $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_datos_monto);

        $fila_1 += 2;
        $sheet->insertNewRowBefore($fila_1 + 1);
        // ------------------------- PATRIMONIO  -------------------------

        $total_patrimonio = $totales->patrimonio;

        $sheet->setCellValue('B' . $fila_1, 'PATRIMONIO');
        $rango =  'B' . $fila_1 . ':' . 'D' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_texto);

        $sheet->setCellValue('E' . $fila_1, $total_patrimonio);
        $rango =  'E' . $fila_1 . ':' . 'F' . $fila_1;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_monto);

        $fila_1 += 1;
        $sheet->insertNewRowBefore($fila_1 + 1);

        // ------------------------- VENTAS  -------------------------

        $ingresos_egresos = $totales->ingresos_egresos;

        $sheet->setCellValue('H' . $fila_2, 'VENTAS');
        $rango =  'H' . $fila_2 . ':' . 'L' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);

        $sheet->setCellValue('M' . $fila_2, $ingresos_egresos->total_ventas);
        $rango =  'M' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $sheet->setCellValue('H' . $fila_2, 'DESCRIPCIÓN');
        $rango =  'H' . $fila_2 . ':' . 'K' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->setCellValue('L' . $fila_2, 'CANT.');
        $sheet->setCellValue('M' . $fila_2, 'PREC. UNIT.');
        $sheet->setCellValue('N' . $fila_2, 'SUBTOTAL');

        $rango =  'H' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->getStyle($rango)->applyFromArray($formato_tabla_encabezado);

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $ventas = json_decode($evaluacion_financiera->flujo_caja->ventas_detallado);

        if ($ventas != null) {
            foreach ($ventas as $key => $item) {
                if ($fila_2 >= $fila_1) {
                    $sheet->insertNewRowBefore($fila_2 + 1);
                }
                $sheet->setCellValue('H' . $fila_2, $item->descripcion);
                $rango =  'H' . $fila_2 . ':' . 'K' . $fila_2;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_tabla_texto);

                $sheet->setCellValue('L' . $fila_2, $item->cantidad);
                $sheet->getStyle('L' . $fila_2)->applyFromArray($formato_tabla_cantidad);

                $sheet->setCellValue('M' . $fila_2, $item->precio_unitario);
                $sheet->getStyle('M' . $fila_2)->applyFromArray($formato_tabla_monto);

                $sheet->setCellValue('N' . $fila_2, floatval($item->cantidad) *  floatval($item->precio_unitario));
                $sheet->getStyle('N' . $fila_2)->applyFromArray($formato_tabla_monto);
                $fila_2++;
            }
        }

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        // ------------------------- COSTO DE VENTAS  -------------------------
        $sheet->setCellValue('H' . $fila_2, 'COSTO DE VENTAS');
        $rango =  'H' . $fila_2 . ':' . 'L' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);

        $sheet->setCellValue('M' . $fila_2, $ingresos_egresos->total_costo_ventas);
        $rango =  'M' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $sheet->setCellValue('H' . $fila_2, 'DESCRIPCIÓN');
        $rango =  'H' . $fila_2 . ':' . 'K' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->setCellValue('L' . $fila_2, 'CANT.');
        $sheet->setCellValue('M' . $fila_2, 'PREC. UNIT.');
        $sheet->setCellValue('N' . $fila_2, 'SUBTOTAL');

        $rango =  'H' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->getStyle($rango)->applyFromArray($formato_tabla_encabezado);

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $costo_ventas = json_decode($evaluacion_financiera->flujo_caja->costo_ventas);

        if ($costo_ventas != null) {
            foreach ($costo_ventas as $key => $item) {
                if ($fila_2 >= $fila_1) {
                    $sheet->insertNewRowBefore($fila_2 + 1);
                }
                $sheet->setCellValue('H' . $fila_2, $item->descripcion);
                $rango =  'H' . $fila_2 . ':' . 'K' . $fila_2;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_tabla_texto);

                $sheet->setCellValue('L' . $fila_2, $item->cantidad);
                $sheet->getStyle('L' . $fila_2)->applyFromArray($formato_tabla_cantidad);

                $sheet->setCellValue('M' . $fila_2, $item->precio_unitario);
                $sheet->getStyle('M' . $fila_2)->applyFromArray($formato_tabla_monto);

                $sheet->setCellValue('N' . $fila_2, floatval($item->cantidad) *  floatval($item->precio_unitario));
                $sheet->getStyle('N' . $fila_2)->applyFromArray($formato_tabla_monto);


                $fila_2++;
            }
        }

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        // ------------------------- COSTOS OPERATIVOS -------------------------

        $sheet->setCellValue('H' . $fila_2, 'UTILIDAD BRUTA');
        $rango =  'H' . $fila_2 . ':' . 'L' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_texto);

        $sheet->setCellValue('M' . $fila_2, $ingresos_egresos->total_utilidad_bruta);
        $rango =  'M' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_monto);

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $sheet->setCellValue('H' . $fila_2, 'COSTOS OPERATIVOS');
        $rango =  'H' . $fila_2 . ':' . 'L' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);

        $sheet->setCellValue('M' . $fila_2, $ingresos_egresos->total_costos_operativos);
        $rango =  'M' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $flujo_caja = $evaluacion_financiera->flujo_caja;
        $costos_operativos = json_decode($flujo_caja->costos_operativos);

        if ($costos_operativos != null) {
            $limite_mitad = round(count($costos_operativos) / 2, 0);
            $fila_3 = $fila_2;

            foreach ($costos_operativos as $key => $item) {

                if ($key + 1 <= $limite_mitad) {
                    if ($fila_2 >= $fila_1) {
                        $sheet->insertNewRowBefore($fila_2 + 1);
                    }
                    $sheet->setCellValue('H' . $fila_2, $item->descripcion);
                    $rango =  'H' . $fila_2 . ':' . 'I' . $fila_2;
                    $sheet->mergeCells($rango);
                    $sheet->getStyle($rango)->applyFromArray($formato_subdatos_texto);

                    $sheet->setCellValue('J' . $fila_2, $item->monto);
                    $sheet->getStyle('J' . $fila_2)->applyFromArray($formato_subdatos_monto);
                    $fila_2++;
                } else {
                    $sheet->setCellValue('L' . $fila_3, $item->descripcion);
                    $rango =  'L' . $fila_3 . ':' . 'M' . $fila_3;
                    $sheet->mergeCells($rango);
                    $sheet->getStyle($rango)->applyFromArray($formato_subdatos_texto);

                    $sheet->setCellValue('N' . $fila_3, $item->monto);
                    $sheet->getStyle('N' . $fila_3)->applyFromArray($formato_subdatos_monto);

                    $fila_3++;
                }
            }
        }

        $fila_2 += 1;

        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        // ------------------------- OTROS INGRESOS -------------------------

        $sheet->setCellValue('H' . $fila_2, 'UTILIDAD NETA');
        $rango =  'H' . $fila_2 . ':' . 'L' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_texto);

        $sheet->setCellValue('M' . $fila_2, $ingresos_egresos->total_utilidad_neta);
        $rango =  'M' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_monto);

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $sheet->setCellValue('H' . $fila_2, 'OTROS INGRESOS');
        $rango =  'H' . $fila_2 . ':' . 'L' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);

        $sheet->setCellValue('M' . $fila_2, $ingresos_egresos->total_otros_ingresos);
        $rango =  'M' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);

        $fila_2 += 1;

        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $otros_ingresos = json_decode($flujo_caja->otros_ingresos);

        if ($otros_ingresos != null) {

            foreach ($otros_ingresos as $key => $item) {
                if ($fila_2 >= $fila_1) {
                    $sheet->insertNewRowBefore($fila_2 + 1);
                }
                $sheet->setCellValue('H' . $fila_2, $item->descripcion);
                $rango =  'H' . $fila_2 . ':' . 'K' . $fila_2;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_subdatos_texto);

                $sheet->setCellValue('L' . $fila_2, $item->monto);
                $rango =  'L' . $fila_2 . ':' . 'N' . $fila_2;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_subdatos_monto);

                $fila_2++;
            }
        }

        $fila_2 += 1;

        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        // ------------------------- GASTOS FAMILIARES -------------------------

        $sheet->setCellValue('H' . $fila_2, 'GASTOS FAMILIARES');
        $rango =  'H' . $fila_2 . ':' . 'L' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_texto);

        $sheet->setCellValue('M' . $fila_2, $ingresos_egresos->total_gastos_familiares);
        $rango =  'M' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_subtitulo_monto);

        $fila_2 += 1;

        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        $gastos_familiares = json_decode($flujo_caja->gastos_familiares);

        if ($gastos_familiares != null) {

            $limite_mitad = round(count($gastos_familiares) / 2, 0);
            $fila_3 = $fila_2;

            foreach ($gastos_familiares as $key => $item) {

                if ($key + 1 <= $limite_mitad) {
                    if ($fila_2 >= $fila_1) {
                        $sheet->insertNewRowBefore($fila_2 + 1);
                    }
                    $sheet->setCellValue('H' . $fila_2, $item->descripcion);
                    $rango =  'H' . $fila_2 . ':' . 'I' . $fila_2;
                    $sheet->mergeCells($rango);
                    $sheet->getStyle($rango)->applyFromArray($formato_subdatos_texto);

                    $sheet->setCellValue('J' . $fila_2, $item->monto);
                    $sheet->getStyle('J' . $fila_2)->applyFromArray($formato_subdatos_monto);
                    $fila_2++;
                } else {
                    $sheet->setCellValue('L' . $fila_3, $item->descripcion);
                    $rango =  'L' . $fila_3 . ':' . 'M' . $fila_3;
                    $sheet->mergeCells($rango);
                    $sheet->getStyle($rango)->applyFromArray($formato_subdatos_texto);

                    $sheet->setCellValue('N' . $fila_3, $item->monto);
                    $sheet->getStyle('N' . $fila_3)->applyFromArray($formato_subdatos_monto);

                    $fila_3++;
                }
            }
        }

        $fila_2 += 1;
        if ($fila_2 >= $fila_1) {
            $sheet->insertNewRowBefore($fila_2 + 1);
        }

        // ------------------------- EXCEDENTE -------------------------

        $sheet->insertNewRowBefore($fila_2 + 1);

        $sheet->setCellValue('H' . $fila_2, 'EXCEDENTE');
        $rango =  'H' . $fila_2 . ':' . 'L' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_texto);

        $sheet->setCellValue('M' . $fila_2, $ingresos_egresos->total_excedente);
        $rango =  'M' . $fila_2 . ':' . 'N' . $fila_2;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_monto);


        if ($fila_2 >= $fila_1) {
            $fila = $fila_2;
        } else {
            $fila = $fila_1;
        }
        $fila += 2;
        $sheet->insertNewRowBefore($fila + 1);

        // ------------------------- PRÉSTAMOS -------------------------

        $sheet->setCellValue('B' . $fila, 'PRÉSTAMOS CON ENTIDADES FINANCIERAS');
        $rango =  'B' . $fila . ':' . 'N' . $fila;
        $sheet->mergeCells($rango);
        $sheet->getStyle($rango)->applyFromArray($formato_titulo_texto);

        $fila += 1;
        $sheet->insertNewRowBefore($fila + 1);

        $sheet->setCellValue('B' . $fila, 'ENTIDAD');
        $rango =  'B' . $fila . ':' . 'D' . $fila;
        $sheet->mergeCells($rango);
        $sheet->setCellValue('E' . $fila, 'MONTO');
        $sheet->setCellValue('F' . $fila, 'TOTAL A PAG.');
        $rango =  'F' . $fila . ':' . 'G' . $fila;
        $sheet->mergeCells($rango);
        $sheet->setCellValue('H' . $fila, 'FRECUENCIA');
        $sheet->setCellValue('I' . $fila, 'CUOTA');
        $sheet->setCellValue('J' . $fila, 'PLAZO');
        $sheet->setCellValue('K' . $fila, 'CU. PAGADA');
        $sheet->setCellValue('L' . $fila, 'CU. RES');
        $sheet->setCellValue('M' . $fila, 'SALDO');
        $sheet->setCellValue('N' . $fila, 'DÍA PAGO');

        $rango =  'B' . $fila . ':' . 'N' . $fila;
        $sheet->getStyle($rango)->applyFromArray($formato_tabla_encabezado);

        $fila += 1;
        $sheet->insertNewRowBefore($fila + 1);

        $prestamos = json_decode($flujo_caja->prestamos);

        if ($prestamos != null) {
            $total_monto_prestamo = 0;
            $total_saldo_pagar = 0;

            foreach ($prestamos as $key => $item) {

                $sheet->insertNewRowBefore($fila + 1);

                $sheet->setCellValue('B' . $fila, $item->entidad);
                $rango =  'B' . $fila . ':' . 'D' . $fila;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_tabla_texto);

                $sheet->setCellValue('E' . $fila, $item->monto_prestamo);
                $sheet->getStyle('E' . $fila)->applyFromArray($formato_tabla_monto);

                $sheet->setCellValue('F' . $fila, floatval($item->monto_cuota) *  floatval($item->plazo));
                $rango =  'F' . $fila . ':' . 'G' . $fila;
                $sheet->mergeCells($rango);
                $sheet->getStyle($rango)->applyFromArray($formato_tabla_monto);

                $frecuencia_pago = $item->frecuencia_pago;
                if ($frecuencia_pago == 1) {
                    $frecuencia_pago = 'MENSUAL';
                } else if ($frecuencia_pago == 2) {
                    $frecuencia_pago = 'QUINCENAL';
                } else if ($frecuencia_pago == 4) {
                    $frecuencia_pago = 'SEMANAL';
                } else if ($frecuencia_pago == 30) {
                    $frecuencia_pago = 'DIARIO';
                }
                $sheet->setCellValue('H' . $fila, $frecuencia_pago);
                $sheet->getStyle('H' . $fila)->applyFromArray($formato_tabla_cantidad);

                $sheet->setCellValue('I' . $fila, floatval($item->monto_cuota));
                $sheet->getStyle('I' . $fila)->applyFromArray($formato_tabla_monto);

                $sheet->setCellValue('J' . $fila, floatval($item->plazo));
                $sheet->getStyle('J' . $fila)->applyFromArray($formato_tabla_cantidad);

                $sheet->setCellValue('K' . $fila, floatval($item->cuotas_pagadas));
                $sheet->getStyle('K' . $fila)->applyFromArray($formato_tabla_cantidad);

                $sheet->setCellValue('L' . $fila, floatval($item->plazo - $item->cuotas_pagadas));
                $sheet->getStyle('L' . $fila)->applyFromArray($formato_tabla_cantidad);

                $saldo_pagar = floatval($item->monto_cuota) *  floatval($item->plazo)
                    - floatval($item->monto_cuota) *  floatval($item->cuotas_pagadas);

                $sheet->setCellValue('M' . $fila, $saldo_pagar);
                $sheet->getStyle('M' . $fila)->applyFromArray($formato_tabla_monto);

                $sheet->setCellValue('N' . $fila, floatval($item->dia_pago));
                $sheet->getStyle('N' . $fila)->applyFromArray($formato_tabla_cantidad);


                $total_monto_prestamo += $item->monto_prestamo;
                $total_saldo_pagar += $saldo_pagar;
                $fila++;
            }

            $sheet->setCellValue('E' . $fila,  $total_monto_prestamo);
            $sheet->getStyle('E' . $fila)->applyFromArray($formato_datos_monto);

            $sheet->setCellValue('M' . $fila,  $total_saldo_pagar);
            $sheet->getStyle('M' . $fila)->applyFromArray($formato_datos_monto);
        }

        // Convertir PDF-------------------------

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptEFMicroempresarial', 5);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];

        // $writer = new Xlsx($spreadsheet);
        // $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.xlsx');
        // $path_xlsx = '/temp_files/' . $nombre_archivo . '.xlsx';

        // return ['path_xlsx' => $path_xlsx];
    }

    public function exportar_comentarios($request)
    {
        $agencia_id = $request->agencia_id;
        $datos_personales = json_decode($request->datos_personales);
        $datos_pariente = json_decode($request->datos_pariente);
        $datos_aval = json_decode($request->datos_aval);
        $datos_evaluacion = json_decode($request->datos_evaluacion);
        $comentarios = json_decode($request->comentarios);

        // Leer Plantilla-------------------------
        $inputFileName = './report_templates/creditos/evaluaciones/rptEvaluacionFinanciera.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $reader->setLoadSheetsOnly('rptEFComentarios');
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos -----------------------------

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));

        $titular = $datos_personales->apellido_paterno . ' ' .
            $datos_personales->apellido_materno . ' ' .
            $datos_personales->nombres . ' - ' .
            $datos_personales->dni;
        $sheet->setCellValue('B5', $titular);

        if (count($datos_pariente) > 0) {
            $pariente = $datos_pariente[0]->apellido_paterno . ' ' .
                $datos_pariente[0]->apellido_materno . ' ' .
                $datos_pariente[0]->nombres . ' - ' .
                $datos_pariente[0]->dni;
            $sheet->setCellValue('B7', $pariente);
        }

        if (count($datos_aval) > 0) {
            $pariente = $datos_aval[0]->apellido_paterno . ' ' .
                $datos_aval[0]->apellido_materno . ' ' .
                $datos_aval[0]->nombres . ' - ' .
                $datos_aval[0]->dni;
            $sheet->setCellValue('B9', $pariente);
        }

        $sheet->setCellValue('M5', json_decode($datos_evaluacion->datos_creacion)->fecha);

        $datos_actualizacion = json_decode($datos_evaluacion->datos_actualizacion);
        $sheet->setCellValue('M7',   $datos_actualizacion == null ? '-' :  $datos_actualizacion->fecha);
        $sheet->setCellValue('M9', $datos_personales->nombre_agencia);

        // COMENTARIOS

        $sheet->setCellValue('B13',   $comentarios->antecedentes_cliente == null ? '-' :   $comentarios->antecedentes_cliente);
        $sheet->setCellValue('B16',   $comentarios->referencias_negocio == null ? '-' :   $comentarios->referencias_negocio);
        $sheet->setCellValue('B19',   $comentarios->referencias_domicilio == null ? '-' :   $comentarios->referencias_domicilio);
        $sheet->setCellValue('B22',   $comentarios->referencias_familiar_vecino == null ? '-' :   $comentarios->referencias_familiar_vecino);
        $sheet->setCellValue('B25',   $comentarios->referencias_pariente == null ? '-' :   $comentarios->referencias_pariente);
        $sheet->setCellValue('B28',   $comentarios->referencias_aval == null ? '-' :   $comentarios->referencias_aval);
        $sheet->setCellValue('B31',   $comentarios->destino_prestamo == null ? '-' :   $comentarios->destino_prestamo);
        $sheet->setCellValue('B34',   $comentarios->otros_comentarios == null ? '-' :   $comentarios->otros_comentarios);

        // Convertir PDF-------------------------

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptEFComentarios', 5);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }

    public function exportar_convenio($request)
    {
        $agencia_id = $request->agencia_id;
        $datos_personales = json_decode($request->datos_personales);
        $datos_pariente = json_decode($request->datos_pariente);
        $datos_aval = json_decode($request->datos_aval);
        $convenio = json_decode($request->convenio);


        // Leer Plantilla-------------------------
        $inputFileName = './report_templates/creditos/evaluaciones/rptEvaluacionFinanciera.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $reader->setLoadSheetsOnly('rptEFConvenio');
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();

        // Insertando datos -----------------------------

        // Encabezado

        $sheet->setCellValue('B1', (new CreditosController)->header_footer($agencia_id));

        $titular = $datos_personales->apellido_paterno . ' ' .
            $datos_personales->apellido_materno . ' ' .
            $datos_personales->nombres . ' - ' .
            $datos_personales->dni;
        $sheet->setCellValue('B5', $titular);

        if (count($datos_pariente) > 0) {
            $pariente = $datos_pariente[0]->apellido_paterno . ' ' .
                $datos_pariente[0]->apellido_materno . ' ' .
                $datos_pariente[0]->nombres . ' - ' .
                $datos_pariente[0]->dni;
            $sheet->setCellValue('B7', $pariente);
        }

        if (count($datos_aval) > 0) {
            $pariente = $datos_aval[0]->apellido_paterno . ' ' .
                $datos_aval[0]->apellido_materno . ' ' .
                $datos_aval[0]->nombres . ' - ' .
                $datos_aval[0]->dni;
            $sheet->setCellValue('B9', $pariente);
        }

        $sheet->setCellValue('J5', json_decode($convenio->datos_creacion)->fecha);

        $datos_actualizacion = json_decode($convenio->datos_actualizacion);
        $sheet->setCellValue('J7',   $datos_actualizacion == null ? '-' :  $datos_actualizacion->fecha);
        $sheet->setCellValue('J9', $datos_personales->nombre_agencia);


        $sheet->setCellValue('C13', $convenio->sueldo_neto);
        $sheet->setCellValue('H13', $convenio->porcentaje_descuento / 100);

        // Obteniendo formatos OTROS INGRESOS-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];

        while ($celda <= 3) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celdas = $sheet->getStyle($columna_1 . 16)->exportArray();

            $lista_formatos_celdas[] = $formato_celdas;
            $celda++;
        }

        // Obteniendo formato SUBTOTAL-----------------------------
        $celda = 9;
        $lista_formatos_subtotal = [];

        while ($celda <= 10) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_subtotal = $sheet->getStyle($columna_1 . 16)->exportArray();

            $lista_formatos_subtotal[] = $formato_subtotal;
            $celda++;
        }

        $fila = 16;
        $sheet->removeRow($fila);

        $otros_ingresos = $convenio->otros_ingresos;
        $total_otros_ingresos = 0;

        if ($otros_ingresos != null) {
            $otros_ingresos = json_decode($convenio->otros_ingresos);

            foreach ($otros_ingresos as $item) {
                $total_otros_ingresos += floatval($item->monto);
                $sheet->insertNewRowBefore($fila + 1);
                $sheet->setCellValue('B' . $fila, $item->descripcion);
                $sheet->setCellValue('D' . $fila, $item->monto);
                $sheet->getStyle('B' . $fila)->applyFromArray($lista_formatos_celdas[0]);
                $sheet->getStyle('D' . $fila)->applyFromArray($lista_formatos_celdas[2]);

                $rango =  'B' . $fila . ':' . 'C' . $fila;
                $sheet->mergeCells($rango);

                $fila += 1;
            }
        }
        $sheet->setCellValue('D15', $total_otros_ingresos);

        $subtotal = (floatval($convenio->sueldo_neto) * floatval($convenio->porcentaje_descuento / 100)) + $total_otros_ingresos;

        $sheet->setCellValue('J' . $fila - 1, 'SUBTOTAL');
        $sheet->getStyle('J' . $fila - 1)->applyFromArray($lista_formatos_subtotal[0]);
        $sheet->setCellValue('K' . $fila - 1, $subtotal);
        $sheet->getStyle('K' . $fila - 1)->applyFromArray($lista_formatos_subtotal[1]);

        // Obteniendo formatos PRÉSTAMOS-----------------------------
        $celda = 1;
        $lista_formatos_celdas = [];
        $lista_formatos_totales = [];

        $fila += 3;

        while ($celda <= 10) {
            $columna_1 = (new CreditosController)->num2char($celda);
            $formato_celdas = $sheet->getStyle($columna_1 . $fila)->exportArray();
            $formato_totales = $sheet->getStyle($columna_1 . $fila + 1)->exportArray();

            $lista_formatos_celdas[] = $formato_celdas;
            $lista_formatos_totales[] = $formato_totales;
            $celda++;
        }

        $sheet->removeRow($fila + 1);
        $sheet->removeRow($fila);

        $total_cuota = 0;
        $total_monto = 0;
        $total_saldo = 0;

        if ($convenio->prestamos != null) {
            $prestamos = json_decode($convenio->prestamos);

            $columna = 1;

            $data = [];
            foreach ($prestamos as $item) {
                $total_cuota += ($item->monto_cuota) * intval($item->frecuencia_pago);
                $total_monto += $item->monto_prestamo;
                $total_saldo += ($item->monto_cuota * ($item->plazo - $item->cuotas_pagadas)) * intval($item->frecuencia_pago);

                $object = (object)[
                    'entidad' => $item->entidad,
                    'monto' =>  $item->monto_prestamo,
                    'total_pagar' => $item->monto_cuota * $item->plazo,
                    'frecuencia' => ($item->frecuencia_pago == 1 ? 'MENSUAL' : ($item->frecuencia_pago == 2 ? 'QUINCENAL' : ($item->frecuencia_pago == 4 ? 'SEMANAL' : ($item->frecuencia_pago == 30 ? 'DIARIO' :
                        'OTROS')))),
                    'cuota' => $item->monto_cuota,
                    'plazo' => $item->plazo,
                    'cu_pagada' => $item->cuotas_pagadas,
                    'cu_res' => $item->plazo - $item->cuotas_pagadas,
                    'saldo' => $item->monto_cuota * ($item->plazo - $item->cuotas_pagadas),
                    'dia_pago' => $item->dia_pago,
                ];

                $data[] = $object;
            }

            foreach ($data as $item) {
                $columna = 1;
                $sheet->insertNewRowBefore($fila + 1);
                foreach ($item as $valor) {

                    $sheet->setCellValue((new CreditosController)->num2char($columna) . $fila, $valor);
                    $sheet->getStyle((new CreditosController)->num2char($columna) . $fila)->applyFromArray($lista_formatos_celdas[$columna - 1]);
                    $columna += 1;
                }
                $fila += 1;
            }
            $sheet->insertNewRowBefore($fila + 1);
            $sheet->setCellValue('C' . $fila, $total_monto);
            $sheet->getStyle('C' . $fila)->applyFromArray($lista_formatos_totales[1]);

            $sheet->setCellValue('J' . $fila, $total_saldo);
            $sheet->getStyle('J' . $fila)->applyFromArray($lista_formatos_totales[8]);
        } else {
            $fila -= 1;
        }

        $excedente = $convenio->sueldo_neto * ($convenio->porcentaje_descuento / 100) -
            $total_cuota + $total_otros_ingresos;

        $sheet->setCellValue('J' . $fila + 2, $excedente);

        $sheet->setCellValue('B' . $fila + 5, $convenio->antecedentes_cliente);
        $sheet->setCellValue('B' . $fila + 8, $convenio->referencias_laborales);
        $sheet->setCellValue('B' . $fila + 11, $convenio->referencias_domicilio);
        $sheet->setCellValue('B' . $fila + 14, $convenio->referencias_pariente_vecino);
        $sheet->setCellValue('B' . $fila + 17, $convenio->referencias_aval);
        $sheet->setCellValue('B' . $fila + 20, $convenio->destino_prestamo);
        $sheet->setCellValue('B' . $fila + 23, $convenio->otros_comentarios);

        // Convertir PDF-------------------------

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->SetFont('verdana');

        $nombre_archivo = (new CreditosController)->concatenar_aleatorio('rptEFConvenio', 5);
        $writer->save($_SERVER['DOCUMENT_ROOT'] . '/temp_files/' . $nombre_archivo . '.pdf');
        $path_pdf =  '/temp_files/' . $nombre_archivo . '.pdf';

        return ['path_pdf' => $path_pdf];
    }
}
