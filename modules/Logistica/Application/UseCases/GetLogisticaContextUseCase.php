<?php

namespace Modules\Logistica\Application\UseCases;

use Modules\Logistica\Domain\Contracts\LogisticaContextRepository;
use Modules\Logistica\Domain\Entities\LogisticaContext;

class GetLogisticaContextUseCase
{
    public function __construct(private LogisticaContextRepository $repository)
    {
    }

    public function execute(): LogisticaContext
    {
        return $this->repository->getContext();
    }
}
