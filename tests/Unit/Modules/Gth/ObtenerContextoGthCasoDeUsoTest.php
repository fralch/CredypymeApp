<?php

namespace Tests\Unit\Modules\Gth;

use Modules\Gth\Application\UseCases\ObtenerContextoGthCasoDeUso;
use Modules\Gth\Domain\Contracts\RepositorioContextoGth;
use Modules\Gth\Domain\Entities\ContextoGth;
use PHPUnit\Framework\TestCase;

class ObtenerContextoGthCasoDeUsoTest extends TestCase
{
    public function test_it_returns_domain_context_without_infrastructure_dependencies(): void
    {
        $repository = new class implements RepositorioContextoGth {
            public function obtenerContexto(): ContextoGth
            {
                return new ContextoGth('Gth', true, 'gth-test');
            }
        };

        $casoDeUso = new ObtenerContextoGthCasoDeUso($repository);
        $result = $casoDeUso->ejecutar();

        $this->assertSame('Gth', $result->nombre());
        $this->assertTrue($result->habilitado());
        $this->assertSame('gth-test', $result->temaUi());
    }
}

