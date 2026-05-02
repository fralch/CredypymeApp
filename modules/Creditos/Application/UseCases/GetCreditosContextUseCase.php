<?php

namespace Modules\Creditos\Application\UseCases;

use Modules\Creditos\Domain\Contracts\CreditosContextRepository;
use Modules\Creditos\Domain\Entities\CreditosContext;

class GetCreditosContextUseCase
{
    public function __construct(private CreditosContextRepository $repository)
    {
    }

    public function execute(): CreditosContext
    {
        return $this->repository->getContext();
    }
}
