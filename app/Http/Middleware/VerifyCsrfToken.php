<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        '/gth/*',
        '/gth/asi/marcado_asistencia/verificar_usuario_asistencia',
        '/gth/asi/marcado_asistencia/registrar_asistencia',
        '/gth/asi/asistencias/mis_asistencias',
        '/gth/asi/tardanzas/listar',
        '/gth/asi/faltas/listar',
        '/gth/faltas/listar',
        '/gth/asi/asistencias/version',
        '/gth/asi/justificaciones/listar_api',
        '/usuario/validar/api',
    ];
}
