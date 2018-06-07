<?php

namespace Tests;

use Corp104\Eloquent\Generator\Bootstrapper;
use Corp104\Eloquent\Generator\Commands\Concerns\DatabaseConnection;
use Illuminate\Container\Container;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use DatabaseConnection;

    protected function createContainer()
    {
        $container = new Container();

        (new Bootstrapper())->bootstrap($container);

        $this->prepareConnection(
            $container,
            __DIR__ . '/Fixture/database.php'
        );

        return $container;
    }
}
