<?php

namespace Tests;

use Corp104\Eloquent\Generator\Resolver;
use Xethron\MigrationsGenerator\Generators\IndexGenerator;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

/**
 * @covers \Corp104\Eloquent\Generator\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new Resolver();
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenResolveSchemaGeneratorsWithSqliteConnection(): void
    {
        $this->putConfigFileWithVfs([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ],
        ]);

        $this->createContainer();

        $actual = $this->target->resolveSchemaGenerators(
            $this->normalizeConnectionConfig($this->root->url() . '/config/database.php')
        );

        $this->assertArrayHasKey('test_sqlite', $actual);
        $this->assertInstanceOf(SchemaGenerator::class, $actual['test_sqlite']);
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenResolveIndexGeneratorWithSqliteConnection(): void
    {
        $this->putConfigFileWithVfs([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ],
        ]);

        $this->createContainer();

        $actual = $this->target->resolveIndexGenerator('test_sqlite', 'someTable');

        $this->assertInstanceOf(IndexGenerator::class, $actual);
    }
}
