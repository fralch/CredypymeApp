<?php

namespace Modules\General\Application\UseCases;

use Modules\General\Domain\Contracts\RepositorioContextoGeneral;
use Modules\General\Domain\Entities\ContextoGeneral;

class ObtenerContextoGeneralCasoDeUso
{
    public function __construct(private RepositorioContextoGeneral $repositorio)
    {
    }

    public function ejecutar(): ContextoGeneral
    {
        return $this->repositorio->obtenerContexto();
    }
}

