<?php

use Corp104\Eloquent\Generator\App;
use Corp104\Eloquent\Generator\Providers\EngineProvider;
use LaravelBridge\Scratch\Application as LaravelBridge;
use org\bovigo\vfs\vfsStream;

require __DIR__ . '/../vendor/autoload.php';

return (function () {
    $vfs = vfsStream::setup('view');

    $container = (new LaravelBridge())
        ->setupDatabase([])
        ->setupView(dirname(__DIR__) . '/src/templates', $vfs->url())
        ->setupProvider(EngineProvider::class)
        ->bootstrap();

    return new App($container);
})();
