<?php

namespace Modules\Creditos\Infrastructure\Persistence;

use Modules\Creditos\Domain\Contracts\CreditosContextRepository;
use Modules\Creditos\Domain\Entities\CreditosContext;

class ConfigCreditosContextRepository implements CreditosContextRepository
{
    public function getContext(): CreditosContext
    {
        return new CreditosContext(
            name: (string) config('modules.creditos.name', 'Creditos'),
            enabled: (bool) config('modules.creditos.enabled', true),
            uiTheme: (string) config('modules.creditos.ui_theme', 'default'),
        );
    }
}
