<?php

namespace App\Providers;

use App\Auditoria\RegistradorAuditoria;
use App\Finanzas\CoordinadorTransacciones;
use App\MultiAgencia\ResolvedorConexionAgencia;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ResolvedorConexionAgencia::class);
        $this->app->singleton(RegistradorAuditoria::class);
        $this->app->singleton(CoordinadorTransacciones::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
