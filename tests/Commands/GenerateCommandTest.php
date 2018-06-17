<?php

namespace Tests\Commands;

use Corp104\Eloquent\Generator\CodeWriter;
use Corp104\Eloquent\Generator\Commands\GenerateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

/**
 * @covers \Corp104\Eloquent\Generator\Commands\Concerns\DatabaseConnection
 * @covers \Corp104\Eloquent\Generator\Commands\Concerns\Environment
 * @covers \Corp104\Eloquent\Generator\Commands\GenerateCommand
 */
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

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptySqlite()
    {
        $this->putConfigFile([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ]
        ]);

        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([]), $output);

        $this->assertSame('', $output->fetch());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptySqliteAndSpecifyTheSqliteConnection()
    {
        $this->putConfigFile([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ]
        ]);

        $argWithSqliteConnection = [
            '--connection' => 'test_sqlite',
        ];

        $output = new BufferedOutput();
        $this->target->run(new ArrayInput($argWithSqliteConnection), $output);

        $this->assertSame('', $output->fetch());
    }

    /**
     * @test
     */
    public function shouldLoadEnvWhenEnvIsExist()
    {
        // Clean env before test
        putenv('TEST_FOR_DOT_ENV');

        $excepted = 'bar';

        (new CodeWriter)->generate([
            '/.env' => 'TEST_FOR_DOT_ENV=bar',
        ], $this->root->url());

        $this->target->run(new ArrayInput([]), new BufferedOutput());

        $this->assertSame($excepted, getenv('TEST_FOR_DOT_ENV'));

        // Tear down env after test
        putenv('TEST_FOR_DOT_ENV');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenConnectionConfigIsNotArray()
    {
        $this->setExpectedException(
            \RuntimeException::class,
            'Connections config is not an array'
        );

        $this->putConfigFile([
            'connections' => 'notArray',
        ]);

        $this->target->run(new ArrayInput([]), new BufferedOutput());
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenConnectionConfigHasNoConnectionsKey()
    {
        $this->setExpectedException(
            \RuntimeException::class,
            "The key 'connections' is not set in config file"
        );

        $code = '<?php return ' . var_export([], true) . ';';

        (new CodeWriter)->generate([
            '/config/database.php' => $code,
        ], $this->root->url());

        $this->target->run(new ArrayInput([]), new BufferedOutput());
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenConnectionSpecifiedIsNotFound()
    {
        $this->setExpectedException(
            \RuntimeException::class,
            'unknown'
        );

        $argWithUnknownConnection = [
            '--connection' => 'unknown',
        ];

        $this->target->run(new ArrayInput($argWithUnknownConnection), new BufferedOutput());
    }
}
