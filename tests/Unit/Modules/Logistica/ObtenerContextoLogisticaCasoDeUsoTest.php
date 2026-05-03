<?php

namespace Tests\Unit\Modules\Logistica;

use Modules\Logistica\Application\UseCases\ObtenerContextoLogisticaCasoDeUso;
use Modules\Logistica\Domain\Contracts\RepositorioContextoLogistica;
use Modules\Logistica\Domain\Entities\ContextoLogistica;
use PHPUnit\Framework\TestCase;

class ObtenerContextoLogisticaCasoDeUsoTest extends TestCase
{
    public function test_it_returns_domain_context_without_infrastructure_dependencies(): void
    {
        $repository = new class implements RepositorioContextoLogistica {
            public function obtenerContexto(): ContextoLogistica
            {
                return new ContextoLogistica('Logistica', true, 'logistica-test');
            }
        };

        $casoDeUso = new ObtenerContextoLogisticaCasoDeUso($repository);
        $result = $casoDeUso->ejecutar();

        $this->assertSame('Logistica', $result->nombre());
        $this->assertTrue($result->habilitado());
        $this->assertSame('logistica-test', $result->temaUi());
    }
}

