<?php

namespace Modules\Gth\Application\UseCases;

use Modules\Gth\Domain\Contracts\RepositorioContextoGth;
use Modules\Gth\Domain\Entities\ContextoGth;

class ObtenerContextoGthCasoDeUso
{
    public function __construct(private RepositorioContextoGth $repositorio)
    {
    }

    public function ejecutar(): ContextoGth
    {
        return $this->repositorio->obtenerContexto();
    }
}

