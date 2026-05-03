<?php

namespace Modules\Creditos\Application\UseCases;

use Modules\Creditos\Domain\Contracts\RepositorioContextoCreditos;
use Modules\Creditos\Domain\Entities\ContextoCreditos;

class ObtenerContextoCreditosCasoDeUso
{
    public function __construct(private RepositorioContextoCreditos $repositorio)
    {
    }

    public function ejecutar(): ContextoCreditos
    {
        return $this->repositorio->obtenerContexto();
    }
}

