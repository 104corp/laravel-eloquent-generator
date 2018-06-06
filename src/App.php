<?php

namespace Corp104\Eloquent\Generator;

use Symfony\Component\Console\Application;

class App extends Application
{
    public function __construct()
    {
        parent::__construct('Laravel Eloquent Generator', 'dev-master');

        $this->addCommands([
            new Commands\GenerateCommand(),
        ]);
    }
}
