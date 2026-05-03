<?php

namespace Modules\General\Infrastructure\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use Modules\General\Domain\Contracts\RepositorioContextoGeneral;
use Modules\General\Infrastructure\Persistence\RepositorioContextoGeneralDesdeConfig;

class GeneralModuleDependencies
{
    public static function register(Container $container): void
    {
        $container->bind(
            RepositorioContextoGeneral::class,
            RepositorioContextoGeneralDesdeConfig::class,
        );
    }
}
