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

        $this->target = $this->container->make(CodeGenerator::class);
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnCorrectFieldAndClassAndConnection(): void
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock([
            'field_a' => 'integer',
            'field_b' => 'text',
            'field_c' => 'decimal',
        ]);

        $indexGeneratorMock = $this->createIndexGeneratorMock();

        $actual = $this->target->generate(
            $schemaGeneratorMock,
            $indexGeneratorMock,
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
    public function shouldReturnCorrectContentWithConnectionNamespace(): void
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock();
        $indexGeneratorMock = $this->createIndexGeneratorMock();

        $actual = $this->target->generate(
            $schemaGeneratorMock,
            $indexGeneratorMock,
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
    public function shouldReturnCorrectContentWithoutConnectionNamespace(): void
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock();
        $indexGeneratorMock = $this->createIndexGeneratorMock();

        $actual = $this->target->generate(
            $schemaGeneratorMock,
            $indexGeneratorMock,
            'SomeNamespace',
            'whatever',
            'whatever',
            false
        );

        $this->assertContains('namespace SomeNamespace;', $actual);
    }
}
