<?php

namespace Modules\Logistica\Infrastructure\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use Modules\Logistica\Domain\Contracts\LogisticaContextRepository;
use Modules\Logistica\Infrastructure\Persistence\ConfigLogisticaContextRepository;

class LogisticaModuleDependencies
{
    public static function register(Container $container): void
    {
        $container->bind(
            LogisticaContextRepository::class,
            ConfigLogisticaContextRepository::class,
        );
    }
}
