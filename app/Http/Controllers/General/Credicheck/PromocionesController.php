<?php

namespace App\Http\Controllers\General\Credicheck;

use App\Models\General\Credicheck\Promociones;
use App\Models\General\Usuarios_permiso;
use App\Models\General\Permiso;
use App\Http\Controllers\General\GeneralController;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PromocionesController extends Controller
{
    public function verificarPermiso($dni, $modulo, $area)
    {

        $band = 0;

        $id_permiso = Permiso::select('id')->where('modulo', $modulo)->where('area', $area)->get();

        $result = Usuarios_permiso::select('id')->where('usuarios_permisos.permiso_id', $id_permiso[0]['id'])->where('usuarios_permisos.usuario_id', $dni)->get();

        if (empty($result[0]['id'])) {
            $band = 0;
        } else {
            $band = 1;
        }

        return $band;
    }

    public function promociones()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = $this->verificarPermiso($x['usuario_dni'], 'CREDICHECK_PROMOCIONES', 'GENERAL_MANTENIMIENTO');
            if ($band == 1) {

                return Inertia::render(
                    'General/Mantenimiento/Credicheck/promociones'
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar_recursos()
    {

        $promociones = Promociones::from('credicheck_promociones')->select(

            'id',
            'titulo_vista',
            'titulo_detalle',
            'subtitulo_vista',
            'subtitulo_detalle',
            'oferta_vista',
            'oferta_detalle',
            'descripcion_vista',
            'descripcion_detalle',
            'empresa',
            'lugar',
            'stock',
            'vigencia',
            'habilitado',
            'imagen_vista',
            'imagen_detalle',
            DB::raw("SUBSTRING(created_at,1,10) as fecha_registro"),
          
        )
    
        ->orderBy('fecha_registro', 'desc')
        ->get();

        // dd($promociones);

        return [
            'promociones' => $promociones,
         
        ];
    }

    public function listar_recursos_api()
    {

        $promociones = Promociones::from('credicheck_promociones')->select(

            'id',
            'titulo_vista',
            'titulo_detalle',
            'subtitulo_vista',
            'subtitulo_detalle',
            'oferta_vista',
            'oferta_detalle',
            'descripcion_vista',
            'descripcion_detalle',
            'empresa',
            'lugar',
            'stock',
            'vigencia',
            'habilitado',
            'imagen_vista',
            'imagen_detalle',
            DB::raw("SUBSTRING(created_at,1,10) as fecha_registro"),
          
        )
    
        ->orderBy('fecha_registro', 'desc')
        ->where('habilitado',1)
        ->get();

        // dd($promociones);

        return [
            'promociones' => $promociones,
         
        ];
    }

 


    public function guardar(Request $request){


        $datos_registro = (new GeneralController)->datos_registro();

        $modo = $request->modo;

        $id = $request->id;
        $titulo_vista = $request->titulo_vista;
        $titulo_detalle = $request->titulo_detalle;
        $subtitulo_vista = $request->subtitulo_vista;
        $subtitulo_detalle = $request->subtitulo_detalle;
        $oferta_vista = $request->oferta_vista;
        $oferta_detalle = $request->oferta_detalle;
        $descripcion_vista = $request->descripcion_vista;
        $descripcion_detalle = $request->descripcion_detalle;
        $empresa = $request->empresa;
        $lugar = $request->lugar;
        $stock = $request->stock;
        $vigencia = $request->vigencia;
        $habilitado = $request->habilitado;


        $resultado = 'ERROR';

        if ($modo == 'NUEVA') {

            $id_promocion=  Promociones::on('master')->create(array(
                'titulo_vista' => $titulo_vista,
                'titulo_detalle' => $titulo_detalle,
                'subtitulo_vista' => $subtitulo_vista,
                'subtitulo_detalle' => $subtitulo_detalle,
                'oferta_vista' => $oferta_vista,
                'oferta_detalle' => $oferta_detalle,
                'descripcion_vista' => $descripcion_vista,
                'descripcion_detalle' => $descripcion_detalle,
                'empresa' => $empresa,
                'lugar' => $lugar,
                'stock' => $stock,
                'vigencia' => $vigencia,
                'habilitado' => 1,
        


            ));

            $id_promocion = $id_promocion->id;

            if ($request->hasFile('imagen_vista')) {

                $path_name = pathinfo($_FILES['imagen_vista']['name']);
                $extension = "." . $path_name['extension'];
                $nombreDocumento =  $id_promocion . "_vista" . $extension;
                $archivo = $_FILES['imagen_vista']['tmp_name'];
                $ruta = '/imagenes_server/general/credicheck/promociones';
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombreDocumento;
                move_uploaded_file($archivo, $ruta);
                Promociones::where('id', $id_promocion)
                    ->update(['imagen_vista' => $nombreDocumento]);

            }


            if ($request->hasFile('imagen_detalle')) {

                $path_name = pathinfo($_FILES['imagen_detalle']['name']);
                $extension = "." . $path_name['extension'];
                $nombreDocumento =  $id_promocion . "_detalle" . $extension;
                $archivo = $_FILES['imagen_detalle']['tmp_name'];
                $ruta = '/imagenes_server/general/credicheck/promociones';
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombreDocumento;
                // $calidad = 20;
                move_uploaded_file($archivo, $ruta);

                // (new GeneralController)->compressImage($archivo, $ruta, $calidad);

                Promociones::where('id', $id_promocion)
                    ->update(['imagen_detalle' => $nombreDocumento]);
            }




    }    else if ($modo == 'EDITAR') {

        if ($habilitado == true) {

            $habilitado = 1;
        }

        if ($habilitado == false) {

            $habilitado = 0;
        }

     
        Promociones::where('id', $id)->update([
            'titulo_vista' => $titulo_vista,
            'titulo_detalle' => $titulo_detalle,
            'subtitulo_vista' => $subtitulo_vista,
            'subtitulo_detalle' => $subtitulo_detalle,
            'oferta_vista' => $oferta_vista,
            'oferta_detalle' => $oferta_detalle,
            'descripcion_vista' => $descripcion_vista,
            'descripcion_detalle' => $descripcion_detalle,
            'empresa' => $empresa,
            'lugar' => $lugar,
            'stock' => $stock,
            'vigencia' => $vigencia,
            'habilitado' => $habilitado,
       
        ]);

        if ($request->hasFile('imagen_vista')) {


       
            $path_name = pathinfo($_FILES['imagen_vista']['name']);
            $extension = "." . $path_name['extension'];
            $nombreDocumento =  $id . "_vista" . $extension;
            $archivo = $_FILES['imagen_vista']['tmp_name'];
            $ruta = '/imagenes_server/general/credicheck/promociones';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombreDocumento;
            move_uploaded_file($archivo, $ruta);
            Promociones::where('id', $id)
            ->update(['imagen_vista' => $nombreDocumento]);
        
        }


        if ($request->hasFile('imagen_detalle')) {

            $path_name = pathinfo($_FILES['imagen_detalle']['name']);
            $extension = "." . $path_name['extension'];
            $nombreDocumento =  $id . "_detalle" . $extension;
            $archivo = $_FILES['imagen_detalle']['tmp_name'];
            $ruta = '/imagenes_server/general/credicheck/promociones';
            $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombreDocumento;
            move_uploaded_file($archivo, $ruta);
            Promociones::where('id', $id)
            ->update(['imagen_detalle' => $nombreDocumento]);

        }

}

// return redirect()->route('gen.man.cre.promociones');



    }
    }