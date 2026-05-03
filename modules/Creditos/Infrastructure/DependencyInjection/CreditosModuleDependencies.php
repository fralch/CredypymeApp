<?php

namespace Modules\Creditos\Infrastructure\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use Modules\Creditos\Domain\Contracts\RepositorioContextoCreditos;
use Modules\Creditos\Infrastructure\Persistence\RepositorioContextoCreditosDesdeConfig;

class CreditosModuleDependencies
{
    public static function register(Container $container): void
    {
        $container->bind(
            RepositorioContextoCreditos::class,
            RepositorioContextoCreditosDesdeConfig::class,
        );
    }
}
