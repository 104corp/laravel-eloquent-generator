<?php

namespace Tests;

use Corp104\Eloquent\Generator\CodeBuilder;
use Corp104\Eloquent\Generator\ConnectionTransform;
use Mockery;

class CodeBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnKeyIsOnlyTableNameWithSingleDatabase()
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock([], [
            'SomeTable',
        ]);

        /** @var CodeBuilder $target */
        $target = $this->createContainerWithSchemaGenerators([
            'whatever' => $schemaGeneratorMock,
        ])->make(CodeBuilder::class);

        $actual = $target
            ->setNamespace('Whatever')
            ->setConnections(['whatever' => []])
            ->build();

        $this->assertArrayHasKey('/SomeTable.php', $actual);
    }

    /**
     * @test
     */
    public function shouldReturnKeyIsOnlyTableNameWithMultiDatabase()
    {
        $schemaGeneratorMock1 = $this->createSchemaGeneratorMock([], [
            'SomeTable1',
        ]);
        $schemaGeneratorMock2 = $this->createSchemaGeneratorMock([], [
            'SomeTable2',
        ]);

        /** @var CodeBuilder $target */
        $target = $this->createContainerWithSchemaGenerators([
            'SomeConnection1' => $schemaGeneratorMock1,
            'SomeConnection2' => $schemaGeneratorMock2,
        ])->make(CodeBuilder::class);

        $actual = $target
            ->setNamespace('Whatever')
            ->setConnections([
                'SomeConnection1' => [],
                'SomeConnection2' => [],
            ])
            ->build();

        $this->assertArrayHasKey('/SomeConnection1/SomeTable1.php', $actual);
        $this->assertArrayHasKey('/SomeConnection2/SomeTable2.php', $actual);
    }

    private function createContainerWithSchemaGenerators(array $schemaGenerators)
    {
        $connectionTransformMock = Mockery::mock(ConnectionTransform::class);
        $connectionTransformMock->shouldReceive('transform')
            ->andReturn($schemaGenerators);

        $container = $this->createContainer();
        $container->instance(ConnectionTransform::class, $connectionTransformMock);

        return $container;
    }
}
