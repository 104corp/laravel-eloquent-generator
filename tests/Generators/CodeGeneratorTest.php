<?php

namespace Tests\Generators;

use Corp104\Eloquent\Generator\Generators\CodeGenerator;
use Tests\TestCase;

/**
 * @covers \Corp104\Eloquent\Generator\Generators\CodeGenerator
 */
class CodeGeneratorTest extends TestCase
{
    /**
     * @var CodeGenerator
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = $this->createContainer()->make(CodeGenerator::class);
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnCorrectFieldAndClassAndConnection()
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock([
            'field_a' => 'integer',
            'field_b' => 'text',
            'field_c' => 'decimal',
        ]);

        $actual = $this->target->generate(
            $schemaGeneratorMock,
            'SomeNamespace',
            'someConnection',
            'some_table'
        );

        // Fields name
        $this->assertContains('int field_a', $actual);
        $this->assertContains('string field_b', $actual);
        $this->assertContains('float field_c', $actual);

        // Class name
        $this->assertContains('class SomeTable', $actual);

        // Properties
        $this->assertContains("connection = 'someConnection';", $actual);
        $this->assertContains("table = 'some_table';", $actual);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectContentWithConnectionNamespace()
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock();

        $actual = $this->target->generate(
            $schemaGeneratorMock,
            'SomeNamespace',
            'someConnection',
            'whatever',
            true
        );

        $this->assertContains('namespace SomeNamespace\SomeConnection;', $actual);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectContentWithoutConnectionNamespace()
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock();

        $actual = $this->target->generate(
            $schemaGeneratorMock,
            'SomeNamespace',
            'whatever',
            'whatever',
            false
        );

        $this->assertContains('namespace SomeNamespace;', $actual);
    }
}
