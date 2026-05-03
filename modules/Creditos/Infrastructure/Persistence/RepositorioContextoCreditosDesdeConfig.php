<?php

namespace Modules\Creditos\Infrastructure\Persistence;

use Modules\Creditos\Domain\Contracts\RepositorioContextoCreditos;
use Modules\Creditos\Domain\Entities\ContextoCreditos;

class RepositorioContextoCreditosDesdeConfig implements RepositorioContextoCreditos
{
    public function obtenerContexto(): ContextoCreditos
    {
        return new ContextoCreditos(
            nombre: (string) config('modules.creditos.name', 'Creditos'),
            habilitado: (bool) config('modules.creditos.enabled', true),
            temaUi: (string) config('modules.creditos.ui_theme', 'default'),
        );
    }
}

