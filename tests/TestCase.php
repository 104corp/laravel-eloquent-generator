<?php

namespace Tests;

use Corp104\Eloquent\Generator\Bootstrapper;
use Corp104\Eloquent\Generator\Commands\Concerns\DatabaseConnection;
use Illuminate\Container\Container;
use Mockery;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

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
     * @return SchemaGenerator
     */
    protected function createSchemaGeneratorMock(array $fields = [])
    {
        $mock = Mockery::mock(SchemaGenerator::class);
        $mock->shouldReceive('getFields')
            ->andReturn($this->createFieldsStub($fields));

        return $mock;
    }
}
