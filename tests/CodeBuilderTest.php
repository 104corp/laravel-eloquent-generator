<?php

namespace Tests;

use Corp104\Eloquent\Generator\CodeBuilder;
use Corp104\Eloquent\Generator\Resolver;
use Mockery;

class CodeBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnKeyIsOnlyTableNameWithSingleDatabase(): void
    {
        $schemaGeneratorMock = $this->createSchemaGeneratorMock([], [
            'SomeTable',
        ]);

        /** @var CodeBuilder $target */
        $target = $this->createContainerWithResolverMock([
            'whatever' => $schemaGeneratorMock,
        ])->make(CodeBuilder::class);

        $actual = $target
            ->setNamespace('Whatever')
            ->setConnections(['whatever' => []])
            ->build();

        $this->assertArrayHasKey('/SomeTable.php', iterator_to_array($actual));
    }

    /**
     * @test
     */
    public function shouldReturnKeyIsOnlyTableNameWithMultiDatabase(): void
    {
        $schemaGeneratorMock1 = $this->createSchemaGeneratorMock([], [
            'SomeTable1',
        ]);
        $schemaGeneratorMock2 = $this->createSchemaGeneratorMock([], [
            'SomeTable2',
        ]);

        /** @var CodeBuilder $target */
        $target = $this->createContainerWithResolverMock([
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

        $array = iterator_to_array($actual);

        $this->assertArrayHasKey('/SomeConnection1/SomeTable1.php', $array);
        $this->assertArrayHasKey('/SomeConnection2/SomeTable2.php', $array);
    }

    private function createContainerWithResolverMock(array $schemaGenerators)
    {
        $resolverMock = Mockery::mock(Resolver::class);
        $resolverMock->shouldReceive('resolveSchemaGenerators')
            ->andReturn($schemaGenerators);
        $resolverMock->shouldReceive('resolveIndexGenerator')
            ->andReturn($this->createIndexGeneratorMock());

        $container = $this->createContainer();
        $container->instance(Resolver::class, $resolverMock);

        return $container;
    }
}
