<?php

namespace Tests\Generators;

use Corp104\Eloquent\Generator\Generators\PrimaryKeyGenerator;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Tests\TestCase;
use Xethron\MigrationsGenerator\Generators\IndexGenerator;

/**
 * @covers \Corp104\Eloquent\Generator\Generators\PrimaryKeyGenerator
 */
class PrimaryKeyGeneratorTest extends TestCase
{
    /**
     * @var PrimaryKeyGenerator
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new PrimaryKeyGenerator();
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenSchemaHasNoPrimaryKey()
    {
        $indexGeneratorMock = \Mockery::mock(IndexGenerator::class);
        $indexGeneratorMock->shouldReceive('getIndex')
            ->andReturn('null');

        $actual = $this->target->generate($indexGeneratorMock, $this->createFieldsStub([
            'field_a' => 'integer',
        ]));

        $this->assertSame('null', $actual);
    }

    /**
     * @test
     */
    public function shouldReturnFieldNameWhenSchemaHasOnePrimaryKey()
    {
        $obj = new \stdClass();
        $obj->type = 'primary';

        $indexGeneratorMock = \Mockery::mock(IndexGenerator::class);
        $indexGeneratorMock->shouldReceive('getIndex')
            ->andReturn($obj);

        $actual = $this->target->generate($indexGeneratorMock, $this->createFieldsStub([
            'field_a' => 'integer',
        ]));

        $this->assertSame("'field_a'", $actual);
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenSchemaHasManyPrimaryKey()
    {
        $obj = new \stdClass();
        $obj->type = 'primary';

        $indexGeneratorMock = \Mockery::mock(IndexGenerator::class);
        $indexGeneratorMock->shouldReceive('getIndex')
            ->andReturn($obj);

        $actual = $this->target->generate($indexGeneratorMock, $this->createFieldsStub([
            'field_a' => 'integer',
            'field_b' => 'integer',
        ]));

        $this->assertSame('null', $actual);
    }
}
