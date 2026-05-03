<?php

namespace Modules\Logistica\Infrastructure\Persistence;

use Modules\Logistica\Domain\Contracts\RepositorioContextoLogistica;
use Modules\Logistica\Domain\Entities\ContextoLogistica;

class RepositorioContextoLogisticaDesdeConfig implements RepositorioContextoLogistica
{
    public function obtenerContexto(): ContextoLogistica
    {
        return new ContextoLogistica(
            nombre: (string) config('modules.logistica.name', 'Logistica'),
            habilitado: (bool) config('modules.logistica.enabled', true),
            temaUi: (string) config('modules.logistica.ui_theme', 'default'),
        );
    }
}

