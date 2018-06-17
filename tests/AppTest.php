<?php

namespace Tests;

use Corp104\Eloquent\Generator\App;
use Corp104\Eloquent\Generator\Commands\GenerateCommand;
use Illuminate\Container\Container;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Corp104\Eloquent\Generator\App
 */
class AppTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptyArray()
    {
        $commandWithBasePath = new GenerateCommand();
        $commandWithBasePath->setBasePath($this->root->url());

        $container = $this->createContainer();
        $container->instance(GenerateCommand::class, $commandWithBasePath);

        Container::setInstance($container);

        $output = new BufferedOutput();

        $target = new App();
        $target->setAutoExit(false);
        $target->run(new ArrayInput([]), $output);

        $this->assertSame('', $output->fetch());
    }
}
