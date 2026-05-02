<?php

namespace Modules\General\Domain\Contracts;

use Modules\General\Domain\Entities\GeneralContext;

interface GeneralContextRepository
{
    public function getContext(): GeneralContext;
}
