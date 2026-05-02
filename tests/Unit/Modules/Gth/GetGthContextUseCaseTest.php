<?php

namespace Tests\Unit\Modules\Gth;

use Modules\Gth\Application\UseCases\GetGthContextUseCase;
use Modules\Gth\Domain\Contracts\GthContextRepository;
use Modules\Gth\Domain\Entities\GthContext;
use PHPUnit\Framework\TestCase;

class GetGthContextUseCaseTest extends TestCase
{
    public function test_it_returns_domain_context_without_infrastructure_dependencies(): void
    {
        $repository = new class implements GthContextRepository {
            public function getContext(): GthContext
            {
                return new GthContext('Gth', true, 'gth-test');
            }
        };

        $useCase = new GetGthContextUseCase($repository);
        $result = $useCase->execute();

        $this->assertSame('Gth', $result->name());
        $this->assertTrue($result->enabled());
        $this->assertSame('gth-test', $result->uiTheme());
    }
}
