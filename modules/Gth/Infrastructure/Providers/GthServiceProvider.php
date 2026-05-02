<?php

namespace Modules\Gth\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Gth\Infrastructure\DependencyInjection\GthModuleDependencies;

class GthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(base_path('config/modules/gth.php'), 'modules.gth');
        GthModuleDependencies::register($this->app);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../Presentation/Views', 'module-gth');
    }
}
