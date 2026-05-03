<?php

namespace Modules\Gth\Domain\Contracts;

use Modules\Gth\Domain\Entities\ContextoGth;

interface RepositorioContextoGth
{
    public function obtenerContexto(): ContextoGth;
}

