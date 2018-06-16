<?php

namespace Tests;

use Corp104\Eloquent\Generator\Commands\GenerateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class GenerateCommandTest extends TestCase
{
    /**
     * @var GenerateCommand
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = $this->createContainer()->make(GenerateCommand::class);
        $this->target->setBasePath($this->root->url());
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptyArray()
    {
        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([]), $output);

        $this->assertSame('', $output->fetch());
    }
}
