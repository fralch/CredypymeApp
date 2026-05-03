<?php

namespace Modules\Creditos\Domain\Contracts;

use Modules\Creditos\Domain\Entities\ContextoCreditos;

interface RepositorioContextoCreditos
{
    public function obtenerContexto(): ContextoCreditos;
}

