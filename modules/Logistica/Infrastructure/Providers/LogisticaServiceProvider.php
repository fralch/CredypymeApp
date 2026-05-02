<?php

namespace Modules\Logistica\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Logistica\Infrastructure\DependencyInjection\LogisticaModuleDependencies;

class LogisticaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(base_path('config/modules/logistica.php'), 'modules.logistica');
        LogisticaModuleDependencies::register($this->app);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../Presentation/Views', 'module-logistica');
    }
}
