<?php

namespace Modules\Gth\Application\UseCases;

use Modules\Gth\Domain\Contracts\GthContextRepository;
use Modules\Gth\Domain\Entities\GthContext;

class GetGthContextUseCase
{
    public function __construct(private GthContextRepository $repository)
    {
    }

    public function execute(): GthContext
    {
        return $this->repository->getContext();
    }
}
