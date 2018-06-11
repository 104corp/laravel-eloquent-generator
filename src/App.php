<?php

declare(strict_types=1);

namespace Corp104\Eloquent\Generator;

use Illuminate\Container\Container;
use Symfony\Component\Console\Application;

class App extends Application
{
    public function __construct()
    {
        $version = 'dev-master';

        if (class_exists(Version::class)) {
            $version = Version::VERSION;
        }

        parent::__construct('Laravel Eloquent Generator', $version);

        $this->bootstrap();

        $this->addCommands([
            new Commands\GenerateCommand(),
        ]);

        $this->setDefaultCommand('eloquent-generator', true);
    }

    public function bootstrap(): void
    {
        (new Bootstrapper())->bootstrap(Container::getInstance());
    }
}
