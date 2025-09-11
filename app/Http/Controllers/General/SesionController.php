<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gth\GthController;

use App\Models\General\Sesion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class SesionController extends Controller
{

    public function usuarios_online()
    {
        Sesion::where(
            'ultima_accion',
            '<',
            [now()->subMinutes(1)]
        )->update(['conectado' => 0]);

        $sesiones = Sesion::from('sesiones as ses')
            ->select(
                'ses.id',
                'ses.usuario_id',
                'ses.conectado',
                'ses.datos_sesion',
                'ses.ultima_accion',
                
                'usu.usuario',
                'usu.agencia_id',

                'age.nombre as agencia'
            )
            ->join('usuarios as usu', 'ses.usuario_id', 'usu.dni')
            ->join('agencias as age', 'age.id_agencia', 'usu.agencia_id')
            ->where(
                'usu.habilitado',
                1
            )->orderBy('usu.usuario', 'asc')->get();

        return $sesiones;
    }
    public function cerrar_sesion()
    {
        Sesion::where('usuario_id', session('usuario_dni'))->update(['conectado' => 0]);
        session()->forget('usuario_dni');
        session()->forget('usuario');
        session()->forget('nombres');
        session()->forget('id_agencia');
        session()->forget('nombre_agencia');
        session()->forget('dispositivo');

        return redirect()->route('welcome');
    }
}
