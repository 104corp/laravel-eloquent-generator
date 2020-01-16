<?php

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

        $container = Container::getInstance();

        $this->bootstrap($container);

        $this->addCommands([
            $container->make(Commands\GenerateCommand::class),
        ]);

        $this->setDefaultCommand('eloquent-generator', true);
    }

    public function bootstrap(Container $container): void
    {
        (new Bootstrapper())->bootstrap($container);
    }
}
