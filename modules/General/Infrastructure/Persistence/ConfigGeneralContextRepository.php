<?php

namespace Modules\General\Infrastructure\Persistence;

use Modules\General\Domain\Contracts\GeneralContextRepository;
use Modules\General\Domain\Entities\GeneralContext;

class ConfigGeneralContextRepository implements GeneralContextRepository
{
    public function getContext(): GeneralContext
    {
        return new GeneralContext(
            name: (string) config('modules.general.name', 'General'),
            enabled: (bool) config('modules.general.enabled', true),
            uiTheme: (string) config('modules.general.ui_theme', 'default'),
        );
    }
}
