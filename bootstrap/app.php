<?php

use Corp104\Eloquent\Generator\Commands\GenerateCommand;
use Corp104\Eloquent\Generator\Providers\BaseServiceProvider;
use Illuminate\Console\Application as IlluminateApplication;
use Corp104\Eloquent\Generator\Providers\EngineProvider;
use LaravelBridge\Scratch\Application as LaravelBridge;
use MilesChou\Codegener\CodegenerServiceProvider;
use org\bovigo\vfs\vfsStream;

require __DIR__ . '/../vendor/autoload.php';

return (function () {
    $vfs = vfsStream::setup('view');

    $container = (new LaravelBridge())
        ->setupDatabase([])
        ->setupView(dirname(__DIR__) . '/src/templates', $vfs->url())
        ->setupProvider(BaseServiceProvider::class)
        ->setupProvider(EngineProvider::class)
        ->setupProvider(CodegenerServiceProvider::class)
        ->bootstrap();

    $app = new IlluminateApplication($container, $container->make('events'), 'dev-master');
    $app->add(new GenerateCommand($container));
    $app->setDefaultCommand('eloquent-generator');

    return $app;
})();
