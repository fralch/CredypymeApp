<?php

namespace Modules\Gth\Infrastructure\Persistence;

use Modules\Gth\Domain\Contracts\GthContextRepository;
use Modules\Gth\Domain\Entities\GthContext;

class ConfigGthContextRepository implements GthContextRepository
{
    public function getContext(): GthContext
    {
        return new GthContext(
            name: (string) config('modules.gth.name', 'Gth'),
            enabled: (bool) config('modules.gth.enabled', true),
            uiTheme: (string) config('modules.gth.ui_theme', 'default'),
        );
    }
}
