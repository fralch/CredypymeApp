<?php

namespace App\Http\Controllers\Creditos\Caja;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Caja\Transaccion;

use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Mantenimiento\Transacciones\Categoria;
use App\Models\Creditos\Mantenimiento\Transacciones\Subcategoria;
use App\Models\General\AreaTrabajo;
use App\Models\Creditos\Mantenimiento\Transacciones\Comprobante;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;


class CajaTransaccionController extends Controller
{

    public function transaccion($transaccion_id = 0)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSACCION', 'CREDITOS_CAJA');

            if ($band == 1) {

                $agencia_id = session('id_agencia');
                $conexion = 'master_' .  $agencia_id;

                $transaccion_registrada = null;
                if ($transaccion_id != 0) {
                    $transaccion_registrada = Transaccion::on($conexion)->where('id', $transaccion_id)->get()->last();
                }

                $categorias = Categoria::on($conexion)->where('habilitado', 1)
                    ->orderBy('categoria', 'asc')
                    ->get();
                $subcategorias = Subcategoria::on($conexion)->where('habilitado', 1)
                    ->orderBy('subcategoria', 'asc')
                    ->get();
                $comprobantes = Comprobante::on($conexion)->where('habilitado', 1)
                    ->orderBy('comprobante', 'asc')
                    ->get();

                $areas_trabajo = AreaTrabajo::where('habilitado', 1)->orderBy('area', 'asc')->get();
                $usuarios = Usuario::from('usuarios as usu')->select(
                    'usu.dni',
                    'usu.usuario',
                    'usu.nombres',
                    'usu.apellido_paterno',
                    'usu.apellido_materno',
                    'usu.agencia_id',

                    'age.nombre as agencia'
                )

                    ->join('agencias as age', 'age.id_agencia', 'usu.agencia_id')
                    ->where([
                        ['usu.habilitado', 1]
                    ])
                    ->orderBy('usuario', 'asc')
                    ->get();



                return Inertia::render('Creditos/Caja/transaccion', [
                    'agencia_id' => intval($agencia_id),
                    'transaccion_registrada' => $transaccion_registrada,
                    'categorias' => $categorias,
                    'subcategorias' => $subcategorias,
                    'areas_trabajo' => $areas_trabajo,
                    'usuarios' => $usuarios,
                    'comprobantes' => $comprobantes,

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

        $agencia_id_sesion = session('id_agencia');
        $conexion = 'master_' .  $agencia_id_sesion;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id_sesion);
        $fecha_larga_aplicacion = (new CreditosController)->fecha_larga_aplicacion($agencia_id_sesion);
        $año = substr($fecha_larga_aplicacion, 0, 4);

        $frmDatosTransaccion = json_decode($request->frmDatosTransaccion);
        $agencia_id = $request->agencia_id;


        $caja_id = $request->caja_id;

        $tipo = $frmDatosTransaccion->tipo_transaccion;
        $categoria_id = $frmDatosTransaccion->categoria_id;
        $subcategoria_id = $frmDatosTransaccion->subcategoria_id;

        $usuario_id = $frmDatosTransaccion->usuario_id;
        $area_trabajo_id = $frmDatosTransaccion->area_trabajo_id;
        $comprobante_id = $frmDatosTransaccion->comprobante_id;

        $monto = $frmDatosTransaccion->monto;
        $concepto = mb_strtoupper($frmDatosTransaccion->concepto);

        $transaccion = Transaccion::on($conexion)->create([
            'tipo' => $tipo,
            'categoria_id' => $categoria_id,
            'subcategoria_id' => $subcategoria_id,
            'agencia_id' => $agencia_id,
            'usuario_id' => $usuario_id,
            'area_trabajo_id' => $area_trabajo_id,
            'comprobante_id' => $comprobante_id,
            'caja_id' => $caja_id,
            'monto' => $monto,
            'concepto' => $concepto,
            'fecha_transaccion' => $fecha_larga_aplicacion,
            'datos_creacion' => $datos_registro
        ]);

        $transaccion_id = $transaccion->id;


        $path_name = pathinfo($_FILES['documento']['name']);
        $extension = "." . $path_name['extension'];
        $nombre_archivo = $año . '_trans_' . $transaccion_id . $extension;
        $archivo = $_FILES['documento']['tmp_name'];
        $ruta = '/imagenes_server/creditos/caja/transacciones/' . $agencia_id_sesion . '/' . $año;
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_archivo;
        $calidad = 10;
        // move_uploaded_file($archivo, $ruta);


        // dd($archivo, $ruta, $calidad);

        (new CreditosController)->compressImage($archivo, $ruta, $calidad);

        Transaccion::on($conexion)->where('id', $transaccion_id)
            ->update(['documento' => $nombre_archivo]);



        return redirect()->route('caj.transaccion', ['transaccion_id' => $transaccion_id]);
    }
}
