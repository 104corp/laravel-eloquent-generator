<?php

namespace Corp104\Eloquent\Generator\Providers;

use Corp104\Eloquent\Generator\Engines\TemplateEngine;
use Corp104\Eloquent\Generator\Generators\PropertyTypeGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class EngineProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('view', function ($app) {
            $factory = new Factory(
                new EngineResolver(),
                new FileViewFinder($app['files'], $app['config']['view.paths']),
                $app['events']
            );

            $factory->setContainer($app);
            $factory->share('app', $app);

            $factory->addExtension('txt', 'text', function () {
                return new TemplateEngine($this->app->make(PropertyTypeGenerator::class));
            });

            return $factory;
        });
    }
}
