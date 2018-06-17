<?php

namespace Tests;

use Corp104\Eloquent\Generator\Bootstrapper;
use Corp104\Eloquent\Generator\CodeWriter;
use Corp104\Eloquent\Generator\Commands\Concerns\DatabaseConnection;
use Illuminate\Container\Container;
use Mockery;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
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

        $this->putConfigFile();

        $this->container = $this->createContainer();
    }

    protected function tearDown()
    {
        $this->root = null;

        parent::tearDown();
    }

    protected function createContainer($config = '/config/database.php')
    {
        $container = new Container();

        (new Bootstrapper())->bootstrap($container);

        $this->prepareConnection(
            $container,
            $this->root->url() . $config
        );

        Container::setInstance($container);

        return $container;
    }

    /**
     * @param string $field
     * @param string $type
     * @param array $addition
     * @return array
     */
    protected function createFieldStub($field, $type, array $addition = [])
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
    protected function createFieldsStub(array $fields)
    {
        return array_map(function ($field, $type) {
            return $this->createFieldStub($field, $type);
        }, array_keys($fields), array_values($fields));
    }

    /**
     * @param array $fields [field_name => $type]
     * @param array $tables
     * @return SchemaGenerator
     */
    protected function createSchemaGeneratorMock(array $fields = [], array $tables = [])
    {
        $mock = Mockery::mock(SchemaGenerator::class);
        $mock->shouldReceive('getFields')
            ->andReturn($this->createFieldsStub($fields));

        $mock->shouldReceive('getTables')
            ->andReturn($tables);

        return $mock;
    }

    /**
     * @param string $path
     * @param array $config
     */
    protected function putConfigFile(array $config = [], $path = '/config/database.php')
    {
        if (!array_key_exists('connections', $config)) {
            $config = ['connections' => $config];
        }

        $code = '<?php return ' . var_export($config, true) . ';';

        (new CodeWriter)->generate([
            $path => $code,
        ], $this->root->url());
    }
}
