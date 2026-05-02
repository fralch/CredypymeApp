<?php

namespace Modules\Logistica\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\CierreController;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Gth\Presentation\Controllers\Usuarios\UsuarioController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroRecordController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoRecordController;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;
use Illuminate\Http\Request;

use Inertia\Inertia;

class LogisticaController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function index()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'PRINCIPAL', 'LOGISTICA');
            if ($band == 1) {

                return Inertia::render('Logistica/principal');
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }
    public function cierre_dia()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'CIERRE_DIA', 'LOGISTICA_APLICACION');
            if ($band == 1) {
                return Inertia::render('Logistica/Aplicacion/cierre_dia');
            } else {
                $mensaje = 'RECHAZADO';
                return (new UsuarioController)->home($mensaje);
                die();
            }
        }
    }
    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------
    public function cerrar_dia()
    {
        $modulo = Datos_aplicacion::where('descripcion', 'FECHA_LOGISTICA')->get()->last();
        $modulo_id = $modulo->id;

        $cierre_id = (new CierreController)->registrar($modulo_id);

        $guardar_activos = (new ActivoRecordController)->cierre_dia($cierre_id);
        $guardar_suministros = (new SuministroRecordController)->cierre_dia($cierre_id);
        $fecha_actual = date($this->fecha_corta_aplicacion());
        $fecha_nueva = date("Y-m-d", strtotime($fecha_actual . "+ 1 day"));

        Datos_aplicacion::where('descripcion', 'FECHA_LOGISTICA')
            ->update(['valorFecha' => $fecha_nueva]);

        return redirect()->route('log.index');
    }
    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------
    public function fecha_larga_aplicacion()
    {
        $data = Datos_aplicacion::where('descripcion', 'FECHA_LOGISTICA')->get()->last();

        date_default_timezone_set("America/Lima");
        $hora = date('H:i:s');

        return $data->valorFecha . ' ' . $hora;
    }

    public function fecha_corta_aplicacion()
    {
        $data = Datos_aplicacion::where('descripcion', 'FECHA_LOGISTICA')->get()->last();

        return $data->valorFecha;
    }
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
    public function verificar_nulo($value)
    {
        if (is_null($value) || empty($value) || $value == 'null' || trim($value) == '') {
            return null;
        }
        return trim($value);
    }
    public function num2char($num)
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
    public function header_footer($agencia_id)
    {
        $fecha_actual = $this->fecha_larga_aplicacion($agencia_id);
        $usuario = session('usuario');
        $nombre_dispositivo = session('dispositivo')->nombre;
        $tipo_dispositivo = session('dispositivo')->tipo;

        return $fecha_actual . ' - ' . $usuario . ' - ' . $nombre_dispositivo . ' - ' . $tipo_dispositivo;
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


    // --------------------------------------------------------------------------


}
