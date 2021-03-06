<?php

namespace Tests;

use Corp104\Eloquent\Generator\Commands\Concerns\DatabaseConnection;
use Corp104\Eloquent\Generator\Generators\PrimaryKeyGenerator;
use Corp104\Eloquent\Generator\Providers\EngineProvider;
use Illuminate\Console\Application;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use LaravelBridge\Scratch\Application as LaravelBridge;
use MilesChou\Codegener\Writer;
use Mockery;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Psr\Log\NullLogger;
use Xethron\MigrationsGenerator\Generators\IndexGenerator;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use DatabaseConnection;

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    /**
     * @var Application
     */
    protected $app;

    protected function setUp()
    {
        parent::setUp();

        $this->app = $this->createApplication();

        $this->root = vfsStream::setup();

        $this->putConfigFileWithVfs();
    }

    protected function tearDown()
    {
        $this->app = null;

        $this->root = null;

        parent::tearDown();
    }

    protected function createApplication(): Application
    {
        return require dirname(__DIR__) . '/bootstrap/app.php';
    }

    protected function createContainer($config = '/config/database.php'): LaravelBridge
    {
        $container = new LaravelBridge();

        $container->setupDatabase($this->normalizeConnectionConfig($this->root->url() . $config))
            ->setupView(__DIR__ . '/../src/templates', $this->root->url() . '/cache');

        (new EngineProvider($container))->register();

        $container->instance(PrimaryKeyGenerator::class, $this->createPrimaryKeyGeneratorNullStub());

        $container->bootstrap();

        return $container;
    }

    /**
     * @param string $field
     * @param string $type
     * @param array $addition
     * @return array
     */
    protected function createFieldStub($field, $type, array $addition = []): array
    {
        return array_merge([
            'field' => $field,
            'type' => $type,
        ], $addition);
    }

    /**
     * @param array $fields [field_name => $type]
     * @return array
     */
    protected function createFieldsStub(array $fields): array
    {
        return collect($fields)->map(function ($type, $field) {
            return $this->createFieldStub($field, $type);
        })->toArray();
    }

    /**
     * @param bool $returnPrimary
     * @return IndexGenerator
     */
    protected function createIndexGeneratorMock($returnPrimary = false): IndexGenerator
    {
        $obj = new \stdClass();

        if ($returnPrimary) {
            $obj->type = 'primary';
        }

        $mock = Mockery::mock(IndexGenerator::class);
        $mock->shouldReceive('getIndex')
            ->andReturn($obj);

        return $mock;
    }

    /**
     * @param array $fields [field_name => $type]
     * @param array $tables
     * @return SchemaGenerator
     */
    protected function createSchemaGeneratorMock(array $fields = [], array $tables = []): SchemaGenerator
    {
        $mock = Mockery::mock(SchemaGenerator::class);
        $mock->shouldReceive('getFields')
            ->andReturn($this->createFieldsStub($fields));

        $mock->shouldReceive('getTables')
            ->andReturn($tables);

        return $mock;
    }

    /**
     * @return PrimaryKeyGenerator
     */
    protected function createPrimaryKeyGeneratorNullStub(): PrimaryKeyGenerator
    {
        $mock = Mockery::mock(PrimaryKeyGenerator::class);
        $mock->shouldReceive('generate')
            ->andReturn('null');

        return $mock;
    }

    /**
     * @param Container $container
     * @param string $connection
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function createSchemaBuilder(Container $container, $connection): \Illuminate\Database\Schema\Builder
    {
        return $container->make('db')->connection($connection)->getSchemaBuilder();
    }

    /**
     * @return string
     */
    protected function createSqliteInBuildPath(): string
    {
        $filename = getcwd() . '/build/sqlite/test.db';
        $dirname = dirname($filename);

        if (is_file($filename)) {
            unlink($filename);
        }

        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }

        touch($filename);

        return $filename;
    }

    /**
     * @param string $path
     * @param array $config
     */
    protected function putConfigFileWithVfs(array $config = [], $path = 'config/database.php'): void
    {
        if (!array_key_exists('connections', $config)) {
            $config = ['connections' => $config];
        }

        $this->putRawFileWithVfs($config, $path);
    }

    /**
     * @param array $config
     * @param string $path
     */
    protected function putRawFileWithVfs(array $config, $path = 'config/database.php'): void
    {
        $code = '<?php return ' . var_export($config, true) . ';';

        (new Writer(new Filesystem(), new NullLogger()))
            ->setBasePath($this->root->url())
            ->write($path, $code, true);
    }
}
