<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;

use App\Http\Controllers\General\PermisosController;

use App\Models\General\Agencia;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function servicios()
    {
        if (session('usuario_dni') == null) {
            return view('/');
        } else {
            $band = (new PermisosController)->verificarPermiso(
                session('usuario_dni'),
                'SERVICIOS',
                'GENERAL_MANTENIMIENTO'
            );
            if ($band == 1) {
                return Inertia::render(
                    'General/Mantenimiento/servicios'
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro', [
                    'mensajeTitulo' => $mensajeTitulo,
                    'mensajeContenido' => $mensajeContenido
                ]);
                die();
            }
        }
    }

    public function listar()
    {
        $agencia_servicios = Agencia::select(
            'id_agencia as id',
            'nombre as agencia',
            'servicio_whatsapp',
            'servicio_facturacion',
            'servicio_sms',
        )->orderBy('agencia', 'asc')->get();

        return ['agencia_servicios' => $agencia_servicios];
    }

    public function guardar(Request $request)
    {
        $nombre_servicio = $request->nombre_servicio;
        $servicios = json_decode($request->servicios);

        // Resetear campos
        Agencia::where($nombre_servicio, 1)->update([$nombre_servicio => 0]);

        // Actualizar campos
        foreach ($servicios as $value) {
            Agencia::where('id_agencia', $value->agencia_activada)
                ->update([$nombre_servicio => 1]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Servicios actualizados con éxito.',
        ], 201);
    }
}
