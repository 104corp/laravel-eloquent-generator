<?php

namespace Tests;

use Corp104\Eloquent\Generator\App;
use Corp104\Eloquent\Generator\Commands\GenerateCommand;
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
    public function shouldReturnEmptyStringWhenConfigIsEmptyArray(): void
    {
        $container = $this->createContainer();

        $commandWithBasePath = new GenerateCommand($container);
        $commandWithBasePath->setBasePath($this->root->url());

        $container->instance(GenerateCommand::class, $commandWithBasePath);

        $output = new BufferedOutput();

        $target = new App($container);
        $target->setAutoExit(false);
        $target->run(new ArrayInput(['--version' => null]), $output);

        $this->assertStringContainsString('Laravel Eloquent Generator', $output->fetch());
    }
}
