<?php

namespace Tests\Unit\Modules\General;

use Modules\General\Application\UseCases\GetGeneralContextUseCase;
use Modules\General\Domain\Contracts\GeneralContextRepository;
use Modules\General\Domain\Entities\GeneralContext;
use PHPUnit\Framework\TestCase;

class GetGeneralContextUseCaseTest extends TestCase
{
    public function test_it_returns_domain_context_without_infrastructure_dependencies(): void
    {
        $repository = new class implements GeneralContextRepository {
            public function getContext(): GeneralContext
            {
                return new GeneralContext('General', true, 'general-test');
            }
        };

        $useCase = new GetGeneralContextUseCase($repository);
        $result = $useCase->execute();

        $this->assertSame('General', $result->name());
        $this->assertTrue($result->enabled());
        $this->assertSame('general-test', $result->uiTheme());
    }
}
