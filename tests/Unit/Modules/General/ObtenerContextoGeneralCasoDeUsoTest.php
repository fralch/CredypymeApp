<?php

namespace Tests\Unit\Modules\General;

use Modules\General\Application\UseCases\ObtenerContextoGeneralCasoDeUso;
use Modules\General\Domain\Contracts\RepositorioContextoGeneral;
use Modules\General\Domain\Entities\ContextoGeneral;
use PHPUnit\Framework\TestCase;

class ObtenerContextoGeneralCasoDeUsoTest extends TestCase
{
    public function test_it_returns_domain_context_without_infrastructure_dependencies(): void
    {
        $repository = new class implements RepositorioContextoGeneral {
            public function obtenerContexto(): ContextoGeneral
            {
                return new ContextoGeneral('General', true, 'general-test');
            }
        };

        $casoDeUso = new ObtenerContextoGeneralCasoDeUso($repository);
        $result = $casoDeUso->ejecutar();

        $this->assertSame('General', $result->nombre());
        $this->assertTrue($result->habilitado());
        $this->assertSame('general-test', $result->temaUi());
    }
}

