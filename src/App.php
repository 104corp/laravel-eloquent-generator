<?php

namespace Corp104\Eloquent\Generator;

use LaravelBridge\Scratch\Application as LaravelBridge;
use Symfony\Component\Console\Application;

class App extends Application
{
    public function __construct(LaravelBridge $container)
    {
        $version = 'dev-master';

        if (class_exists(Version::class)) {
            $version = Version::VERSION;
        }

        parent::__construct('Laravel Eloquent Generator', $version);

        $this->addCommands([
            new Commands\GenerateCommand($container),
        ]);

        $this->setDefaultCommand('eloquent-generator', true);
    }
}
