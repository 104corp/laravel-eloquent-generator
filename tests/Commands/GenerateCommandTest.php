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

        $this->target = new GenerateCommand($this->createContainer());
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
    public function shouldReturnEmptyStringWhenConfigIsEmptyArray(): void
    {
        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([]), $output);

        $this->assertSame('', $output->fetch());
    }

    /**
     * @test
     */
    public function shouldReturnWhenConfigHasSqlite(): void
    {
        $sqliteDb = $this->createSqliteInBuildPath();

        $this->putConfigFileWithVfs([
            'test_sqlite_for_progress_raw' => [
                'driver' => 'sqlite',
                'database' => $sqliteDb,
            ],
        ]);

        $this->createSchemaBuilder($this->createContainer(), 'test_sqlite_for_progress_raw')
            ->create('table_a', function (Blueprint $table) {
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
    public function shouldReturnWhenConfigHasSqliteWithProgressArg(): void
    {
        $sqliteDb = $this->createSqliteInBuildPath();

        $this->putConfigFileWithVfs([
            'test_sqlite_for_progress_bar' => [
                'driver' => 'sqlite',
                'database' => $sqliteDb,
            ],
        ]);

        $this->createSchemaBuilder($this->createContainer(), 'test_sqlite_for_progress_bar')
            ->create('table_a', function (Blueprint $table) {
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
    public function shouldReturnEmptyStringWhenConfigIsEmptyArrayWithProgressBar(): void
    {
        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([
            '--progress' => null,
        ]), $output);

        $this->assertSame('', $output->fetch());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptySqlite(): void
    {
        $this->putConfigFileWithVfs([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ],
        ]);

        $output = new BufferedOutput();
        $this->target->run(new ArrayInput([]), $output);

        $this->assertSame('', $output->fetch());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConfigIsEmptySqliteAndSpecifyTheSqliteConnection(): void
    {
        $this->putConfigFileWithVfs([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ],
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
     * @covers \Corp104\Eloquent\Generator\Commands\Concerns\Environment
     */
    public function shouldLoadEnvWhenEnvIsExist(): void
    {
        // Clean env before test
        putenv('TEST_FOR_DOT_ENV');

        $excepted = 'bar';

        (new CodeWriter())->generate([
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
    public function shouldThrowExceptionWhenConnectionConfigIsNotArray(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Connections config is not an array');

        $this->putConfigFileWithVfs([
            'connections' => 'notArray',
        ]);

        $this->target->run(new ArrayInput([]), new BufferedOutput());
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenConnectionConfigHasNoConnectionsKey(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("The key 'connections' is not set in config file");

        $this->putRawFileWithVfs(
            '<?php return ' . var_export([], true) . ';',
            '/config/database.php'
        );

        $this->target->run(new ArrayInput([]), new BufferedOutput());
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenConnectionSpecifiedIsNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('unknown');

        $argWithUnknownConnection = [
            '--connection' => 'unknown',
        ];

        $this->target->run(new ArrayInput($argWithUnknownConnection), new BufferedOutput());
    }
}
