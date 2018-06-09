<?php

declare(strict_types=1);

namespace Corp104\Eloquent\Generator\Providers;

use Corp104\Eloquent\Generator\Engines\TemplateEngine;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class EngineProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('view', function ($app) {
            $factory = new Factory(
                new EngineResolver(),
                new FileViewFinder($app['files'], [
                    __DIR__ . '/../templates'
                ]),
                $app['events']
            );

            $factory->setContainer($app);
            $factory->share('app', $app);

            $factory->addExtension('txt', 'text', function () {
                return $this->app->make(TemplateEngine::class);
            });

            return $factory;
        });

        $this->app->bind(\Illuminate\Contracts\View\Factory::class, 'view');
    }
}
