<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;

use App\Models\General\Agencia;

use App\Http\Controllers\Controller;

class GeneralController extends Controller
{

    public function datos_registro()
    {
        $fecha = $this->fecha_larga();
        $usuario = session('usuario_dni');
        $nombre_dispositivo = session('dispositivo')->nombre;
        $tipo_dispositivo = session('dispositivo')->tipo;

        $datos_registro = (object)[
            'fecha' => $fecha,
            'usuario' => $usuario,
            'nombre_dispositivo' => $nombre_dispositivo,
            'tipo_dispositivo' => $tipo_dispositivo
        ];

        return json_encode($datos_registro);
    }
    public function compressImage($archivo, $ruta, $calidad)
    {

        // Obtenemos la información de la imagen
        $imgInfo = getimagesize($archivo);

        $mime = $imgInfo['mime'];

        // Creamos una imagen
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($archivo);
                break;
            case 'image/png':
                $image = imagecreatefrompng($archivo);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($archivo);
                break;
            default:
                $image = imagecreatefromjpeg($archivo);
        }

        // Guardamos la imagen
        imagejpeg($image, $ruta, $calidad);

        // Devolvemos la imagen comprimida
        return true;
    }

    public function verificar_servicio($servicio, $agencia_id)
    {

        $verificar_agencia = Agencia::select($servicio)->find($agencia_id);

        if ($verificar_agencia->$servicio == 1) {
            return ['resultado' => true];
        } else {
            return ['resultado' => false];
        }
    }

    public function fecha_larga()
    {
        date_default_timezone_set("America/Lima");
        $data = date('Y-m-d h:i:s');

        return $data;
    }

    public function fecha_corta()
    {
        date_default_timezone_set("America/Lima");
        $data = date('Y-m-d');

        return $data;
    }

    public function verificar_nulo($value)
    {
        if (is_null($value) || empty($value) || $value == 'null') {
            return null;
        }
        return $value;
    }
}
