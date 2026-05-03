<?php

namespace Modules\Gth\Infrastructure\Persistence;

use Modules\Gth\Domain\Contracts\RepositorioContextoGth;
use Modules\Gth\Domain\Entities\ContextoGth;

class RepositorioContextoGthDesdeConfig implements RepositorioContextoGth
{
    public function obtenerContexto(): ContextoGth
    {
        return new ContextoGth(
            nombre: (string) config('modules.gth.name', 'Gth'),
            habilitado: (bool) config('modules.gth.enabled', true),
            temaUi: (string) config('modules.gth.ui_theme', 'default'),
        );
    }
}

