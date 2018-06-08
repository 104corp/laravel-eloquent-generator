<?php

namespace Tests\Generators;

use Corp104\Eloquent\Generator\Generators\PropertyTypeGenerator;
use Tests\TestCase;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class PropertyTypeGeneratorTest extends TestCase
{
    /**
     * @var PropertyTypeGenerator
     */
    private $target;

    public function intFieldsWithMysql()
    {
        $this->createContainer();

        $schemaGenerator = new SchemaGenerator('test_mysql', false, false);
        $fields = $schemaGenerator->getFields('should_return_int');

        return array_map(function ($property) {
            return [
                $property['field'],
                $property['type'],
            ];
        }, $fields);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->target = new PropertyTypeGenerator();
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider intFieldsWithMysql
     */
    public function shouldReturnIntWithMysqlTable($field, $type)
    {
        $this->assertContains('int', $this->target->generate($type), "Field '${field}' cannot trans to int");
    }
}
