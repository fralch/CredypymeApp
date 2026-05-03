<?php

namespace Modules\Logistica\Domain\Contracts;

use Modules\Logistica\Domain\Entities\ContextoLogistica;

interface RepositorioContextoLogistica
{
    public function obtenerContexto(): ContextoLogistica;
}

