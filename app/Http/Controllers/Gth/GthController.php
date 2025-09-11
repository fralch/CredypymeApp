<?php

namespace App\Http\Controllers\Gth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gth\MiPanelGth\MiPanelGthController;


use App\Models\General\Datos_aplicacion;

use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Gth\Planillas\PlanillaVacacioneController;
use App\Http\Controllers\Gth\Usuarios\UsuarioController;
use App\Models\Gth\Planillas\usuario;
use App\Models\Gth\Planillas\Vacacion;
use App\Models\Gth\Planillas\VacacionPeriodo;

use Illuminate\Http\Request;
use Inertia\Inertia;

class GthController extends Controller
{
    // Funciones necesarias para el módulo de GTH

    public function index()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PRINCIPAL', 'GTH');
            if ($band == 1) {
                return Inertia::render('Gth/principal');
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }

    public function fecha_larga_aplicacion()
    {
        $data = Datos_aplicacion::where('descripcion', 'FECHA_GTH')->get()->last();

        date_default_timezone_set("America/Lima");
        $hora = date('H:i:s');

        return $data->valorFecha . ' ' . $hora;
    }

    public function fecha_corta_aplicacion()
    {
        $data = Datos_aplicacion::where('descripcion', 'FECHA_GTH')->get()->last();

        return $data->valorFecha;
    }
    // public function cierre_dia()
    // {
    //     $x = session()->all();
    //     if (empty($x['usuario_dni'])) {
    //         return redirect('/');
    //     } else {
    //         $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CIERRE_DIA', 'GTH_MANTENIMIENTO');
    //         if ($band == 1) {
    //             return Inertia::render('Gth/Mantenimiento/cierre_dia');
    //         } else {
    //             $mensajeTitulo = '¡Ups!';
    //             $mensajeContenido = 'No tienes permitido ver este contenido.';
    //             return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
    //             die();
    //         }
    //     }
    // }
    // public function cerrar_dia()
    // {
    //     $datos_sesion = session()->all();
    //     $fecha_actual_corta = date($this->fecha_corta_aplicacion());
    //     $fecha_actual_larga = date($this->fecha_larga_aplicacion());
    //     $fecha_nueva_corta = date("Y-m-d", strtotime($fecha_actual_corta . "+ 1 day"));

    //     $calcular_vacaciones = (new PlanillaVacacioneController)
    //         ->calcular_vacaciones(
    //             $fecha_actual_corta,
    //             $fecha_actual_larga,
    //             $fecha_nueva_corta,
    //             $datos_sesion
    //         );

    //     Datos_aplicacion::where('descripcion', 'FECHA_GTH')
    //         ->update(['valorFecha' => $fecha_nueva_corta]);



    //     return redirect()->route('cierre_dia');
    // }


    public function datos_registro()
    {
        $fecha = $this->fecha_larga_aplicacion();
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
    public function datos_registro_real()
    {
        date_default_timezone_set("America/Lima");
        $fecha_actual = date("Y-m-d H:i:s");

        $fecha =  $fecha_actual;
        $usuario = session('usuario_dni');
        // $nombre_dispositivo = session('dispositivo')->nombre;
        // $tipo_dispositivo = session('dispositivo')->tipo;

        $datos_registro = (object)[
            'fecha' => $fecha,
            'usuario' => $usuario,
            // 'nombre_dispositivo' => $nombre_dispositivo,
            // 'tipo_dispositivo' => $tipo_dispositivo
        ];

        return json_encode($datos_registro);
    }
    function num2char($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->num2char($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    public function concatenar_aleatorio($string, $characters)
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $rango = 36;
        $cantidad = $characters;

        $aleatorio = '_';
        for ($i = 0; $i < $cantidad; $i++) {
            $caracter_aleatorio =  $caracteres[mt_rand(0, $rango - 1)];
            $aleatorio .= $caracter_aleatorio;
        }

        return $string . $aleatorio;
    }
    public function header_footer($agencia_id)
    {
        $fecha_actual = $this->fecha_larga_aplicacion($agencia_id);
        $usuario = session('usuario');
        $nombre_dispositivo = session('dispositivo')->nombre;
        $tipo_dispositivo = session('dispositivo')->tipo;

        return $fecha_actual . ' - ' . $usuario . ' - ' . $nombre_dispositivo . ' - ' . $tipo_dispositivo;
    }
}
