<?php

namespace Tests;

use Corp104\Eloquent\Generator\ConnectionTransformer;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

/**
 * @covers \Corp104\Eloquent\Generator\ConnectionTransformer
 */
class ConnectionTransformerTest extends TestCase
{
    /**
     * @var ConnectionTransformer
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new ConnectionTransformer();
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenUsingSqliteConnection()
    {
        $this->putConfigFileWithVfs([
            'test_sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ]
        ]);

        $this->createContainer();
        $actual = $this->target->transform($this->connections);

        $this->assertArrayHasKey('test_sqlite', $actual);
        $this->assertInstanceOf(SchemaGenerator::class, $actual['test_sqlite']);
    }
}
