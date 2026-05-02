<?php

namespace Modules\Creditos\Domain\Contracts;

use Modules\Creditos\Domain\Entities\CreditosContext;

interface CreditosContextRepository
{
    public function getContext(): CreditosContext;
}
