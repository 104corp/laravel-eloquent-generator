<?php

use Corp104\Eloquent\Generator\Providers\EngineProvider;
use Illuminate\Container\Container;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Facade;

require __DIR__ . '/../vendor/autoload.php';

$container = Container::getInstance();
$container->singleton('db', function ($app) {
// Configure database connections
    $connections = [
        'default' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'default',
            'username' => 'root',
            'password' => 'password',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ];

    $capsule = new \Illuminate\Database\Capsule\Manager;

    foreach ($connections as $connectionName => $setting) {
        $capsule->addConnection($setting, $connectionName);
    }

    $capsule->setAsGlobal();

    return $capsule;
});

Facade::setFacadeApplication($container);

AliasLoader::getInstance([
    'DB' => Illuminate\Support\Facades\DB::class,
])->register();

(new \Illuminate\Events\EventServiceProvider($container))->register();
(new \Illuminate\Filesystem\FilesystemServiceProvider($container))->register();
(new EngineProvider($container))->register();

$app = new \Corp104\Eloquent\Generator\App();
$app->run();
