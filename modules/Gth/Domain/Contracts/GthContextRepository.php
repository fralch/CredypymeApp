<?php

namespace Modules\Gth\Domain\Contracts;

use Modules\Gth\Domain\Entities\GthContext;

interface GthContextRepository
{
    public function getContext(): GthContext;
}
