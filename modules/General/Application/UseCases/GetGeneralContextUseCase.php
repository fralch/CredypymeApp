<?php

namespace Modules\General\Application\UseCases;

use Modules\General\Domain\Contracts\GeneralContextRepository;
use Modules\General\Domain\Entities\GeneralContext;

class GetGeneralContextUseCase
{
    public function __construct(private GeneralContextRepository $repository)
    {
    }

    public function execute(): GeneralContext
    {
        return $this->repository->getContext();
    }
}
