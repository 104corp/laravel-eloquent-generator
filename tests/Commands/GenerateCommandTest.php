<?php

namespace Tests\Commands;

use Corp104\Eloquent\Generator\CodeWriter;
use Corp104\Eloquent\Generator\Commands\GenerateCommand;
use Illuminate\Database\Schema\Blueprint;
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

        $this->target = $this->container->make(GenerateCommand::class);
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
    public function shouldReturnWhenConfigHasSqlite()
    {
        $sqliteDb = $this->createSqliteInBuildPath();

        $this->putConfigFileWithVfs([
            'test_sqlite_for_progress_raw' => [
                'driver' => 'sqlite',
                'database' => $sqliteDb,
            ]
        ]);

        $this->createContainer();

        $this->createSchemaBuilder('test_sqlite_for_progress_raw')->create('table_a', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([]), $output);

        $actual = $output->fetch();

        $this->assertContains('Writing', $actual);
        $this->assertContains('TableA.php', $actual);

        // Tear down temp database
        unlink($sqliteDb);
    }

    /**
     * @test
     */
    public function shouldReturnWhenConfigHasSqliteWithProgressArg()
    {
        $sqliteDb = $this->createSqliteInBuildPath();

        $this->putConfigFileWithVfs([
            'test_sqlite_for_progress_bar' => [
                'driver' => 'sqlite',
                'database' => $sqliteDb,
            ]
        ]);

        $this->createContainer();

        $this->createSchemaBuilder('test_sqlite_for_progress_bar')->create('table_a', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([
            '--progress' => null,
        ]), $output);

        $actual = $output->fetch();

        $this->assertContains('1/1', $actual);
        $this->assertContains('100%', $actual);

        // Tear down temp database
        unlink($sqliteDb);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptyArrayWithProgressBar()
    {
        $this->container->make('db');

        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([
            '--progress' => null,
        ]), $output);

        $this->assertSame('', $output->fetch());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptySqlite()
    {
        $this->putConfigFileWithVfs([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ]
        ]);

        $this->container->make('db');

        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([]), $output);

        $this->assertSame('', $output->fetch());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptySqliteAndSpecifyTheSqliteConnection()
    {
        $this->putConfigFileWithVfs([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ]
        ]);

        $this->container->make('db');

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

        $this->putConfigFileWithVfs([
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

        $this->putRawFileWithVfs(
            '<?php return ' . var_export([], true) . ';',
            '/config/database.php'
        );

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
