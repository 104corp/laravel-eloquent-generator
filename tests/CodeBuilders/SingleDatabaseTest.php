<?php

namespace Tests\Generators;

use Corp104\Eloquent\Generator\CodeBuilders\SingleDatabase;
use Tests\TestCase;

class SingleDatabaseTest extends TestCase
{
    /**
     * @var SingleDatabase
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = $this->createContainer()->make(SingleDatabase::class);
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnKeyIsOnlyTableNameWithoutConnectionNamespace()
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock([], [
            'SomeTable',
        ]);

        $actual = $this->target->build(
            $schemaGeneratorMock,
            'Whatever',
            'SomeConnection',
            false
        );

        $this->assertArrayHasKey('/SomeTable.php', $actual);
    }

    /**
     * @test
     */
    public function shouldReturnKeyIsOnlyTableNameWithConnectionNamespace()
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock([], [
            'SomeTable',
        ]);

        $actual = $this->target->build(
            $schemaGeneratorMock,
            'Whatever',
            'SomeConnection',
            true
        );

        $this->assertArrayHasKey('/SomeConnection/SomeTable.php', $actual);
    }
}
