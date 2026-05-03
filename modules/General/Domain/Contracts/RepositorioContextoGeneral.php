<?php

namespace Modules\General\Domain\Contracts;

use Modules\General\Domain\Entities\ContextoGeneral;

interface RepositorioContextoGeneral
{
    public function obtenerContexto(): ContextoGeneral;
}

