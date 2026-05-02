<?php

namespace Tests\Unit\Modules\Creditos;

use Modules\Creditos\Application\UseCases\GetCreditosContextUseCase;
use Modules\Creditos\Domain\Contracts\CreditosContextRepository;
use Modules\Creditos\Domain\Entities\CreditosContext;
use PHPUnit\Framework\TestCase;

class GetCreditosContextUseCaseTest extends TestCase
{
    public function test_it_returns_domain_context_without_infrastructure_dependencies(): void
    {
        $repository = new class implements CreditosContextRepository {
            public function getContext(): CreditosContext
            {
                return new CreditosContext('Creditos', true, 'creditos-test');
            }
        };

        $useCase = new GetCreditosContextUseCase($repository);
        $result = $useCase->execute();

        $this->assertSame('Creditos', $result->name());
        $this->assertTrue($result->enabled());
        $this->assertSame('creditos-test', $result->uiTheme());
    }
}
