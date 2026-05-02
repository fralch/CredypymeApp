<?php

namespace Modules\Creditos\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Creditos\Infrastructure\DependencyInjection\CreditosModuleDependencies;

class CreditosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(base_path('config/modules/creditos.php'), 'modules.creditos');
        CreditosModuleDependencies::register($this->app);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../Presentation/Views', 'module-creditos');
    }
}
