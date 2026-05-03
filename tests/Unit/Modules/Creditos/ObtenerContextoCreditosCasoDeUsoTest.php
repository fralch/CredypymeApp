<?php

namespace Tests\Unit\Modules\Creditos;

use Modules\Creditos\Application\UseCases\ObtenerContextoCreditosCasoDeUso;
use Modules\Creditos\Domain\Contracts\RepositorioContextoCreditos;
use Modules\Creditos\Domain\Entities\ContextoCreditos;
use PHPUnit\Framework\TestCase;

class ObtenerContextoCreditosCasoDeUsoTest extends TestCase
{
    public function test_it_returns_domain_context_without_infrastructure_dependencies(): void
    {
        $repository = new class implements RepositorioContextoCreditos {
            public function obtenerContexto(): ContextoCreditos
            {
                return new ContextoCreditos('Creditos', true, 'creditos-test');
            }
        };

        $casoDeUso = new ObtenerContextoCreditosCasoDeUso($repository);
        $result = $casoDeUso->ejecutar();

        $this->assertSame('Creditos', $result->nombre());
        $this->assertTrue($result->habilitado());
        $this->assertSame('creditos-test', $result->temaUi());
    }
}

