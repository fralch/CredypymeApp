<?php

namespace Modules\Logistica\Infrastructure\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use Modules\Logistica\Domain\Contracts\RepositorioContextoLogistica;
use Modules\Logistica\Infrastructure\Persistence\RepositorioContextoLogisticaDesdeConfig;

class LogisticaModuleDependencies
{
    public static function register(Container $container): void
    {
        $container->bind(
            RepositorioContextoLogistica::class,
            RepositorioContextoLogisticaDesdeConfig::class,
        );
    }
}
