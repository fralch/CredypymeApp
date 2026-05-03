<?php

namespace Modules\General\Infrastructure\Persistence;

use Modules\General\Domain\Contracts\RepositorioContextoGeneral;
use Modules\General\Domain\Entities\ContextoGeneral;

class RepositorioContextoGeneralDesdeConfig implements RepositorioContextoGeneral
{
    public function obtenerContexto(): ContextoGeneral
    {
        return new ContextoGeneral(
            nombre: (string) config('modules.general.name', 'General'),
            habilitado: (bool) config('modules.general.enabled', true),
            temaUi: (string) config('modules.general.ui_theme', 'default'),
        );
    }
}
