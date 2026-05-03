<?php

namespace Modules\Logistica\Application\UseCases;

use Modules\Logistica\Domain\Contracts\RepositorioContextoLogistica;
use Modules\Logistica\Domain\Entities\ContextoLogistica;

class ObtenerContextoLogisticaCasoDeUso
{
    public function __construct(private RepositorioContextoLogistica $repositorio)
    {
    }

    public function ejecutar(): ContextoLogistica
    {
        return $this->repositorio->obtenerContexto();
    }
}

