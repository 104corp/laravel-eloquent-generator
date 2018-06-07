<?php

use Corp104\Eloquent\Generator\Providers\EngineProvider;
use Illuminate\Container\Container;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Facade;

require __DIR__ . '/../vendor/autoload.php';

$container = Container::getInstance();

Facade::setFacadeApplication($container);

AliasLoader::getInstance([
    'DB' => Illuminate\Support\Facades\DB::class,
])->register();

(new \Illuminate\Events\EventServiceProvider($container))->register();
(new \Illuminate\Filesystem\FilesystemServiceProvider($container))->register();
(new EngineProvider($container))->register();

$app = new \Corp104\Eloquent\Generator\App();
$app->run();
