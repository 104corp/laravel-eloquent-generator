<?php

declare(strict_types=1);

namespace Corp104\Eloquent\Generator;

use Corp104\Eloquent\Generator\Providers\EngineProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Facade;

class Bootstrapper
{
    public function bootstrap(Container $container): void
    {
        Facade::setFacadeApplication($container);

        AliasLoader::getInstance([
            'DB' => \Illuminate\Support\Facades\DB::class,
        ])->register();

        (new \Illuminate\Events\EventServiceProvider($container))->register();
        (new \Illuminate\Filesystem\FilesystemServiceProvider($container))->register();
        (new EngineProvider($container))->register();
    }
}
