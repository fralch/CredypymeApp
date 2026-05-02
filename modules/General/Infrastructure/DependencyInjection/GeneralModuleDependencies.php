<?php

namespace Modules\General\Infrastructure\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use Modules\General\Domain\Contracts\GeneralContextRepository;
use Modules\General\Infrastructure\Persistence\ConfigGeneralContextRepository;

class GeneralModuleDependencies
{
    public static function register(Container $container): void
    {
        $container->bind(
            GeneralContextRepository::class,
            ConfigGeneralContextRepository::class,
        );
    }
}
