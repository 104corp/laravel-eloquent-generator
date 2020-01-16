<?php

namespace Tests;

use Corp104\Eloquent\Generator\Bootstrapper;
use Corp104\Eloquent\Generator\CodeWriter;
use Corp104\Eloquent\Generator\Commands\Concerns\DatabaseConnection;
use Corp104\Eloquent\Generator\Generators\PrimaryKeyGenerator;
use Illuminate\Container\Container;
use Mockery;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Xethron\MigrationsGenerator\Generators\IndexGenerator;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use DatabaseConnection;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    protected function setUp()
    {
        parent::setUp();

        $this->root = vfsStream::setup();

        $this->putConfigFileWithVfs();

        $this->container = $this->createContainer();
    }

    protected function tearDown()
    {
        $this->container = null;
        $this->root = null;

        parent::tearDown();
    }

    protected function createContainer($config = '/config/database.php'): Container
    {
        $container = new Container();

        (new Bootstrapper())->bootstrap($container);

        $this->prepareConnection(
            $container,
            $this->root->url() . $config
        );

        $container->instance(PrimaryKeyGenerator::class, $this->createPrimaryKeyGeneratorNullStub());
        Container::setInstance($container);

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
     * @param string $connection
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function createSchemaBuilder($connection): \Illuminate\Database\Schema\Builder
    {
        return $this->container->make('db')->connection($connection)->getSchemaBuilder();
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
    protected function putConfigFileWithVfs(array $config = [], $path = '/config/database.php'): void
    {
        if (!array_key_exists('connections', $config)) {
            $config = ['connections' => $config];
        }

        $code = '<?php return ' . var_export($config, true) . ';';

        $this->putRawFileWithVfs($code, $path);
    }

    /**
     * @param string $code
     * @param string $path
     */
    protected function putRawFileWithVfs($code, $path): void
    {
        (new CodeWriter())
            ->setOverwrite(true)
            ->generate([
                $path => $code,
            ], $this->root->url());
    }
}
