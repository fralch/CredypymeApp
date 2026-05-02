<?php

namespace Modules\Logistica\Infrastructure\Persistence;

use Modules\Logistica\Domain\Contracts\LogisticaContextRepository;
use Modules\Logistica\Domain\Entities\LogisticaContext;

class ConfigLogisticaContextRepository implements LogisticaContextRepository
{
    public function getContext(): LogisticaContext
    {
        return new LogisticaContext(
            name: (string) config('modules.logistica.name', 'Logistica'),
            enabled: (bool) config('modules.logistica.enabled', true),
            uiTheme: (string) config('modules.logistica.ui_theme', 'default'),
        );
    }
}
