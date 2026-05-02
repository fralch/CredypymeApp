<?php

namespace Modules\Creditos\Infrastructure\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use Modules\Creditos\Domain\Contracts\CreditosContextRepository;
use Modules\Creditos\Infrastructure\Persistence\ConfigCreditosContextRepository;

class CreditosModuleDependencies
{
    public static function register(Container $container): void
    {
        $container->bind(
            CreditosContextRepository::class,
            ConfigCreditosContextRepository::class,
        );
    }
}
