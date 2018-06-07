<?php

namespace Corp104\Eloquent\Generator;

use Illuminate\Container\Container;
use Symfony\Component\Console\Application;

class App extends Application
{
    public function __construct()
    {
        parent::__construct('Laravel Eloquent Generator', 'dev-master');

        $this->bootstrap();

        $this->addCommands([
            new Commands\GenerateCommand(),
        ]);
    }

    public function bootstrap(): void
    {
        (new Bootstrapper())->bootstrap(Container::getInstance());
    }
}
