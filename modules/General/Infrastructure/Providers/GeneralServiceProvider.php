<?php

namespace Modules\General\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\General\Infrastructure\DependencyInjection\GeneralModuleDependencies;

class GeneralServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(base_path('config/modules/general.php'), 'modules.general');
        GeneralModuleDependencies::register($this->app);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../Presentation/Views', 'module-general');
    }
}
