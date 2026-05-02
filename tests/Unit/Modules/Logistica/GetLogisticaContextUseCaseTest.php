<?php

namespace Tests\Unit\Modules\Logistica;

use Modules\Logistica\Application\UseCases\GetLogisticaContextUseCase;
use Modules\Logistica\Domain\Contracts\LogisticaContextRepository;
use Modules\Logistica\Domain\Entities\LogisticaContext;
use PHPUnit\Framework\TestCase;

class GetLogisticaContextUseCaseTest extends TestCase
{
    public function test_it_returns_domain_context_without_infrastructure_dependencies(): void
    {
        $repository = new class implements LogisticaContextRepository {
            public function getContext(): LogisticaContext
            {
                return new LogisticaContext('Logistica', true, 'logistica-test');
            }
        };

        $useCase = new GetLogisticaContextUseCase($repository);
        $result = $useCase->execute();

        $this->assertSame('Logistica', $result->name());
        $this->assertTrue($result->enabled());
        $this->assertSame('logistica-test', $result->uiTheme());
    }
}
