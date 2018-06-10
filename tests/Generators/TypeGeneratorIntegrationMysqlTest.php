<?php

namespace Tests\Generators;

use Corp104\Eloquent\Generator\Generators\TypeGenerator;
use Tests\TestCase;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class TypeGeneratorIntegrationMysqlTest extends TestCase
{
    /**
     * @var TypeGenerator
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new TypeGenerator();
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    public function intFieldsWithMysql()
    {
        $this->createContainer();

        return $this->getDatabaseFields('should_return_int');
    }

    /**
     * @test
     * @dataProvider intFieldsWithMysql
     */
    public function shouldReturnIntWithMysqlTable($field, $type)
    {
        $this->assertContains('int', $this->target->generate($type), "Field '${field}' cannot trans to int");
    }

    public function floatFieldsWithMysql()
    {
        $this->createContainer();

        return $this->getDatabaseFields('should_return_float');
    }

    /**
     * @test
     * @dataProvider floatFieldsWithMysql
     */
    public function shouldReturnFloatWithMysqlTable($field, $type)
    {
        $this->assertContains('float', $this->target->generate($type), "Field '${field}' cannot trans to float");
    }

    public function stringFieldsWithMysql()
    {
        $this->createContainer();

        return $this->getDatabaseFields('should_return_string');
    }

    /**
     * @test
     * @dataProvider stringFieldsWithMysql
     */
    public function shouldReturnStringWithMysqlTable($field, $type)
    {
        $this->assertContains('string', $this->target->generate($type), "Field '${field}' cannot trans to string");
    }

    /**
     * @param string $database
     * @return array
     */
    protected function getDatabaseFields($database)
    {
        $schemaGenerator = new SchemaGenerator('test_mysql', false, false);
        $fields = $schemaGenerator->getFields($database);

        return array_map(function ($property) {
            return [
                $property['field'],
                $property['type'],
            ];
        }, $fields);
    }
}
