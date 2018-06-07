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

    public function testMysql()
    {
        $this->createContainer();

        $schemaGenerator = new SchemaGenerator('default', false, false);
        $fields = $schemaGenerator->getFields('test_basic');

        foreach ($fields as $name => $property) {
            $this->assertPropertyFieldContainsShouldCall($property['field']);

            list($col, $assertMethod) = explode('ShouldCall', $property['field']);

            $assertMethod = lcfirst($assertMethod);

            $this->$assertMethod($this->target->generate($property['type']));
        }
    }

    public function assertPropertyTypeContainsInt($actual)
    {
        $this->assertContains('int', $actual);
    }

    public function assertPropertyTypeContainsString($actual)
    {
        $this->assertContains('string', $actual);
    }

    public function assertPropertyTypeContainsCarbon($actual)
    {
        $this->assertContains('\\Carbon\\Carbon', $actual);
    }

    public function assertPropertyFieldContainsShouldCall($actual)
    {
        $this->assertContains('ShouldCall', $actual);
    }
}
