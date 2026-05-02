<?php

namespace Modules\Logistica\Domain\Contracts;

use Modules\Logistica\Domain\Entities\LogisticaContext;

interface LogisticaContextRepository
{
    public function getContext(): LogisticaContext;
}
