<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\General\Infrastructure\Persistence\Eloquent\Sesion;

use Modules\Gth\Presentation\Controllers\GthController;

class Online
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $datos_sesion = Sesion::from('sesiones as ses')
            ->select(
                'ses.id',
                'ses.usuario_id',
                'ses.conectado',
                'ses.datos_sesion',
                'ses.ultima_accion'
            )->join('usuarios as us', 'ses.usuario_id', 'us.dni')
            ->where([
                ['ses.usuario_id', session('usuario_dni')],
                ['us.habilitado', 1]
            ])->get()->last();

        if (!$datos_sesion == null) {
            date_default_timezone_set("America/Lima");
            $fecha_actual = date("Y-m-d H:i:s");

            $sesion = Sesion::find($datos_sesion->id);
            $sesion->update([
                'conectado' => 1,
                'get_id' => Session::getId(), 
                'datos_sesion' => (new GthController)->datos_registro_real(session('id_agencia')),
                'ultima_accion' => $fecha_actual
            ]);
        }
        return $next($request);
    }
}
