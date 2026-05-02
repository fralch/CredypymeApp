<?php

namespace Modules\Gth\Infrastructure\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use Modules\Gth\Domain\Contracts\GthContextRepository;
use Modules\Gth\Infrastructure\Persistence\ConfigGthContextRepository;

class GthModuleDependencies
{
    public static function register(Container $container): void
    {
        $container->bind(
            GthContextRepository::class,
            ConfigGthContextRepository::class,
        );
    }
}
