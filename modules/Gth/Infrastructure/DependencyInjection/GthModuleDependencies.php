<?php

namespace Modules\Gth\Infrastructure\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use Modules\Gth\Domain\Contracts\RepositorioContextoGth;
use Modules\Gth\Infrastructure\Persistence\RepositorioContextoGthDesdeConfig;

class GthModuleDependencies
{
    public static function register(Container $container): void
    {
        $container->bind(
            RepositorioContextoGth::class,
            RepositorioContextoGthDesdeConfig::class,
        );
    }
}
