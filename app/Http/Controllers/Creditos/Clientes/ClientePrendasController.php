<?php

namespace App\Http\Controllers\Creditos\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Mantenimiento\Album\Categoria;
use App\Models\General\Datos_aplicacion;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Prenda;
use App\Models\Creditos\Clientes\AlbumFoto;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;


class ClientePrendasController extends Controller
{

    public function prendas($cliente_id, $agencia_id)
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PRENDAS', 'CREDITOS_CLIENTES');

            if ($band == 1) {

                $conexion = 'master_' .  $agencia_id;

                $datos_cliente = Cliente::on($conexion)->from('cliente_registros as cli_reg')
                    ->select(
                        'cli_reg.dni',
                        'cli_reg.direccion',
                        'cli_reg.apellido_paterno',
                        'cli_reg.apellido_materno',
                        'cli_reg.nombres',
                        'cli_reg.codigo_expediente',
                        'cli_reg.codigo_expediente',

                        'usu.usuario as usuario_asesor',
                        'dis.distrito',
                        'pro.provincia',
                        'dep.departamento'
                    )
                    ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
                    ->join('solucion_master.distritos as dis', 'cli_reg.distrito_id', 'dis.id')
                    ->join('solucion_master.provincias as pro', 'cli_reg.provincia_id', 'pro.id')
                    ->join('solucion_master.departamentos as dep', 'cli_reg.departamento_id', 'dep.id')
                    ->where('cli_reg.id', $cliente_id)
                    ->get()
                    ->last();

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
                            'cli_pre.entregado',
                            'cli_pre.acta_entrega',
                            'cli_pre.comentario',
                            'cli_pre.fotos',
                            'cli_pre.datos_creacion',

                            'us.usuario as usuario_registro'
                        ]
                    )
                    ->where('cli_pre.cliente_id', $cliente_id)
                    ->get();

                return Inertia::render('Creditos/Clientes/prendas')->with([
                    'cliente_id' => intval($cliente_id),
                    'agencia_id' => intval($agencia_id),
                    'datos_cliente' => $datos_cliente,
                    'datos_prendas' => $datos_prendas
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function guardar(Request $request)
    {
        // dd($request);
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
        $año = date("Y", strtotime($fecha_actual));

        $modo = $request->modo;
        $cliente_id =  $request->cliente_id;
        $frmDatosPrenda = json_decode($request->frmDatosPrenda);

        $cantidad = 1;
        $descripcion = mb_strtoupper($frmDatosPrenda->descripcion);

        $marca = (new CreditosController)->verificar_nulo($frmDatosPrenda->marca);
        if ($marca != null) {
            $marca = mb_strtoupper($marca);
        }
        $modelo =  (new CreditosController)->verificar_nulo($frmDatosPrenda->modelo);
        if ($modelo != null) {
            $modelo = mb_strtoupper($modelo);
        }
        $serie =  (new CreditosController)->verificar_nulo($frmDatosPrenda->serie);
        if ($serie != null) {
            $serie = mb_strtoupper($serie);
        }
        $color =  mb_strtoupper($frmDatosPrenda->color);

        $estado = $frmDatosPrenda->estado;
        $fecha_compra = $frmDatosPrenda->fecha_compra;

        $numero_comprobante = (new CreditosController)->verificar_nulo($frmDatosPrenda->numero_comprobante);
        if ($numero_comprobante != null) {
            $numero_comprobante = mb_strtoupper($numero_comprobante);
        }

        $precio_compra = $frmDatosPrenda->precio_compra;
        $precio_actual = $frmDatosPrenda->precio_actual;

        $comentario = (new CreditosController)->verificar_nulo($frmDatosPrenda->comentario);
        if ($comentario != null) {
            $comentario = mb_strtoupper($comentario);
        }

        if ($modo == 'NUEVO') {
            $prenda = Prenda::on($conexion)->create([
                'cliente_id' => $cliente_id,
                'cantidad' => $cantidad,
                'descripcion' => $descripcion,
                'marca' => $marca,
                'modelo' => $modelo,
                'serie' => $serie,
                'color' => $color,
                'estado' => $estado,
                'fecha_compra' => $fecha_compra,
                'numero_comprobante' => $numero_comprobante,
                'precio_compra' => $precio_compra,
                'precio_actual' => $precio_actual,
                'comentario' => $comentario,
                'datos_creacion' => $datos_registro,
            ]);

            $prenda_id = $prenda->id;
        } else if ($modo == 'EDITAR') {

            $prenda_id = $frmDatosPrenda->id;

            Prenda::on($conexion)->where('id', $prenda_id)->update([
                'cantidad' => $cantidad,
                'descripcion' => $descripcion,
                'marca' => $marca,
                'modelo' => $modelo,
                'serie' => $serie,
                'color' => $color,
                'estado' => $estado,
                'fecha_compra' => $fecha_compra,
                'numero_comprobante' => $numero_comprobante,
                'precio_compra' => $precio_compra,
                'precio_actual' => $precio_actual,
                'comentario' => $comentario,
                'datos_actualizacion' => $datos_registro,
            ]);
        }

        $fotos = (object)[
            'foto_1' => null,
            'foto_2' => null,
            'foto_3' => null,
            'foto_4' => null
        ];

        $fotos_actuales = $frmDatosPrenda->fotos;

        $nueva_foto = $frmDatosPrenda->fotos_nuevas;
        if ($nueva_foto->nuevo_1) {
            if ($request->nombre_1 != 'null') {
                $nombre_foto = $año . '_' . $cliente_id . '_' . $prenda_id . $request->nombre_1;
                $fotos->foto_1 = $nombre_foto;
                $archivo = $_FILES['foto_1']['tmp_name'];
                $ruta = '/imagenes_server/creditos/clientes/prendas/fotos/' . $agencia_id . '/' . $año;
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_foto;
                $calidad = 10;
                (new CreditosController)->compressImage($archivo, $ruta, $calidad);
            } else {
                $fotos->foto_1 = null;
            }
        } else {
            $fotos->foto_1 = $fotos_actuales->foto_1;
        }

        if ($nueva_foto->nuevo_2) {
            if ($request->nombre_2 != 'null') {
                $nombre_foto = $año . '_' . $cliente_id . '_' . $prenda_id . $request->nombre_2;
                $fotos->foto_2 = $nombre_foto;
                $archivo = $_FILES['foto_2']['tmp_name'];
                $ruta = '/imagenes_server/creditos/clientes/prendas/fotos/' . $agencia_id . '/' . $año;
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_foto;
                $calidad = 10;
                (new CreditosController)->compressImage($archivo, $ruta, $calidad);
            } else {
                $fotos->foto_2 = null;
            }
        } else {
            $fotos->foto_2 = $fotos_actuales->foto_2;
        }

        if ($nueva_foto->nuevo_3) {
            if ($request->nombre_3 != 'null') {
                $nombre_foto = $año . '_' . $cliente_id . '_' . $prenda_id . $request->nombre_3;
                $fotos->foto_3 = $nombre_foto;
                $archivo = $_FILES['foto_3']['tmp_name'];
                $ruta = '/imagenes_server/creditos/clientes/prendas/fotos/' . $agencia_id . '/' . $año;
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_foto;
                $calidad = 10;
                (new CreditosController)->compressImage($archivo, $ruta, $calidad);
            } else {
                $fotos->foto_3 = null;
            }
        } else {
            $fotos->foto_3 = $fotos_actuales->foto_3;
        }

        if ($nueva_foto->nuevo_4) {
            if ($request->nombre_4 != 'null') {
                $nombre_foto = $año . '_' . $cliente_id . '_' . $prenda_id . $request->nombre_4;
                $fotos->foto_4 = $nombre_foto;
                $archivo = $_FILES['foto_4']['tmp_name'];
                $ruta = '/imagenes_server/creditos/clientes/prendas/fotos/' . $agencia_id . '/' . $año;
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_foto;
                $calidad = 10;
                (new CreditosController)->compressImage($archivo, $ruta, $calidad);
            } else {
                $fotos->foto_4 = null;
            }
        } else {
            $fotos->foto_4 = $fotos_actuales->foto_4;
        }

        Prenda::on($conexion)->where('id', $prenda_id)
            ->update(['fotos' => json_encode($fotos)]);

        return redirect()->route('cli.prendas', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }

    public function eliminar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $cliente_id = $request->cliente_id;
        $prenda_id = $request->prenda_id;

        Prenda::on($conexion)->where('id', $prenda_id)->delete();

        return redirect()->route('cli.prendas', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }

    public function subir_acta(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
        $año = date("Y", strtotime($fecha_actual));

        $cliente_id =  $request->cliente_id;
        $lista_prendas = json_decode($request->lista_prendas);
        $nombre = '';
        foreach ($lista_prendas as $item) {
            $nombre .= '_' . $item;
        }

        $archivo = $_FILES['acta_entrega']['tmp_name'];
        $path_name = pathinfo($_FILES['acta_entrega']['name']);
        $extension = "." . $path_name['extension'];
        $nombre_imagen = $año  . $nombre . $extension;
        $ruta = '/imagenes_server/creditos/clientes/prendas/actas/' . $agencia_id . '/' . $año;
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_imagen;
        $calidad = 10;
        (new CreditosController)->compressImage($archivo, $ruta, $calidad);

        foreach ($lista_prendas as $value) {
            Prenda::on($conexion)->where('id', $value)->update([
                'disponible' => 0,
                'entregado' => 1,
                'acta_entrega' => $nombre_imagen,
                'datos_actualizacion' => $datos_registro
            ]);
        }

        return redirect()->route('cli.prendas', [
            'cliente_id' => $cliente_id,
            'agencia_id' => $agencia_id
        ]);
    }
}
