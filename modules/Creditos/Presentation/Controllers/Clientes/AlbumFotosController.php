<?php

namespace Modules\Creditos\Presentation\Controllers\Clientes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Album\Categoria;
use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\AlbumFoto;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AlbumFotosController extends Controller
{

    public function album_fotos($cliente_id, $agencia_id)
    {

        $conexion = 'master_' .  $agencia_id;

        $datos_cliente = Cliente::on($conexion)->select(
            'id',
            'dni',
            'apellido_paterno',
            'apellido_materno',
            'nombres',
            'agencia_id'
        )
            ->where('id', $cliente_id)
            ->get()->last();

        $fotos_cliente = AlbumFoto::on($conexion)->from('cliente_album_fotos as cli_alb_fot')
            ->select(
                'cli_alb_fot.id',
                'cli_alb_fot.cliente_id',
                'cli_alb_fot.categoria_id',
                'cli_alb_fot.descripcion',
                'cli_alb_fot.comentario',
                'cli_alb_fot.imagen',
                'cli_alb_fot.datos_creacion',
                'cli_alb_fot.created_at',
                'alb_cat.categoria'
            )
            ->join('cliente_album_categorias as alb_cat', 'cli_alb_fot.categoria_id',  'alb_cat.id',)
            ->where('cli_alb_fot.cliente_id', $cliente_id)
            ->get();

        $categorias = Categoria::on($conexion)->orderBy('categoria', 'asc')->get();

        return  [
            'categorias' => $categorias,
            'datos_cliente' => $datos_cliente,
            'fotos_cliente' => $fotos_cliente,
        ];
    }
    public function album_categorias()
    {
        $x = session()->all();
        $conexion = 'master_' .  $x['id_agencia'];

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'ALBUM_CATEGORIAS', 'CREDITOS_MANTENIMIENTO');

            if ($band == 1) {

                $data = Datos_aplicacion::where([
                    ['descripcion', 'FECHA_CREDITOS'],
                    ['id_agencia', $x['id_agencia']]
                ])->get()->last();


                $album_categorias = Categoria::on($conexion)->select()->get();

                $fecha_creditos = json_decode($data);


                return Inertia::render('Creditos/Mantenimiento/album_categorias', [
                    'album_categorias' => $album_categorias
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listarCategorias(Request $request)
    {
        // return $request;
        $id_agencia = $request->id_agencia;
        $conexion = 'master_' .  $id_agencia;

        $estados = Categoria::on($conexion)->select()->get();
        return $estados;
    }

    public function verificar(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;


        $resultado = "NO EXISTE";
        $modo = $request->modo;
        $id = $request->id;
        $categoria = $request->categoria;

        $categoria_existente = Categoria::on($conexion)->select('id')->where('categoria', $categoria)->get();

        if ($modo == "EDITAR") {
            if (count($categoria_existente) > 0) {
                if ($categoria_existente[0]["id"] == $id) {
                    $resultado = "NO EXISTE";
                } else {
                    $resultado = "EXISTE";
                }
            } else {
                $resultado = "NO EXISTE";
            }
        } else {
            if (count($categoria_existente) > 0) {
                $resultado = "EXISTE";
            }
        }
        return $resultado;
    }

    public function guardar(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));

        $modo = $request->modo;
        $categoria = mb_strtoupper($request->categoria);

        $descripcion = (new CreditosController)->verificar_nulo($request->descripcion);
        if ($descripcion != null) {
            $descripcion = mb_strtoupper($request->descripcion);
        };

        $nuevo = $request->nuevo;

        if ($modo == "NUEVO") {
            Categoria::on($conexion)->create(
                [
                    'categoria' => $categoria,
                    'descripcion' => $descripcion,
                    'nuevo' => 1,
                    'datos_creacion' => $datos_registro,
                ]

            );
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            Categoria::on($conexion)->where('id', $id)
                ->update([
                    'categoria' => $categoria,
                    'descripcion' => $descripcion,
                    'nuevo' => $nuevo,
                    'datos_actualizacion' => $datos_registro,
                ]);
        }
        return redirect()->route('man.cli_album_categorias');
    }



    public function guardar_foto(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $fecha_corta = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
        $año = substr($fecha_corta, 0, 4);

        $cliente_id = $request->cliente_id;
        $dni = $request->dni;
        $categoria_id = $request->categoria_id;
        $descripcion = mb_strtoupper($request->descripcion);
        $comentario = mb_strtoupper($request->comentario);


        $foto = AlbumFoto::on($conexion)->create([
            'cliente_id' => $cliente_id,
            'categoria_id' => $categoria_id,
            'descripcion' => $descripcion,
            'comentario' => $comentario,
            'datos_creacion' => $datos_registro
        ]);

        $foto_id = $foto->id;

        $path_name = pathinfo($_FILES['foto_nueva']['name']);
        $extension = "." . $path_name['extension'];
        $nombre_archivo = $año . '_' . $dni . '_' . $categoria_id . '_' . $foto_id . $extension;
        $archivo = $_FILES['foto_nueva']['tmp_name'];
        $ruta = '/imagenes_server/creditos/clientes/album/' . $agencia_id . '/' . $año;
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_archivo;
        $calidad = 20;
        // move_uploaded_file($archivo, $ruta);
        (new CreditosController)->compressImage($archivo, $ruta, $calidad);

        AlbumFoto::on($conexion)->where('id', $foto_id)
            ->update(['imagen' => $nombre_archivo]);

        return true;
    }

    public function eliminar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $foto_id = $request->foto_id;
        $cliente_id = $request->cliente_id;

        AlbumFoto::on($conexion)->where('id', $foto_id)->delete();

        return true;
    }
}
