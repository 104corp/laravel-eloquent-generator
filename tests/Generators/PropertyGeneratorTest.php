<?php

namespace Tests\Generators;

use Corp104\Eloquent\Generator\Generators\PropertyGenerator;
use Tests\TestCase;

/**
 * @covers \Corp104\Eloquent\Generator\Generators\PropertyGenerator
 */
class PropertyGeneratorTest extends TestCase
{
    /**
     * @var PropertyGenerator
     */
    private $target;

    public function defaultFields(): array
    {
        return [
            ['int', $this->createFieldStub('whatever', 'smallInteger')],
            ['int', $this->createFieldStub('whatever', 'integer')],
            ['int', $this->createFieldStub('whatever', 'bigInteger')],
            ['float', $this->createFieldStub('whatever', 'decimal')],
            ['float', $this->createFieldStub('whatever', 'float')],
            ['string', $this->createFieldStub('whatever', 'char')],
            ['string', $this->createFieldStub('whatever', 'string')],
            ['string', $this->createFieldStub('whatever', 'text')],
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->target = new PropertyGenerator();
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider defaultFields
     */
    public function shouldReturnCorrectTypeWithDefaultFields($exceptedType, $property): void
    {
        $excepted = "{$exceptedType} {$property['field']}";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldReturnMixedWhenTypeIsNotFound(): void
    {
        $property = $this->createFieldStub('whatever', 'unknown');

        $excepted = "mixed {$property['field']}";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldReturnNullWordWhenDecoratorOfTypeHasNullable(): void
    {
        $property = $this->createFieldStub('whatever', 'unknown', [
            'decorators' => [
                'nullable',
            ],
        ]);

        $excepted = "null|mixed {$property['field']}";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldReturnCommentWordWhenDecoratorOfTypeHasMysqlComment(): void
    {
        $exceptedComment = 'some-comment';

        $property = $this->createFieldStub('whatever', 'unknown', [
            'decorators' => [
                "comment('{$exceptedComment}')",
            ],
        ]);

        $excepted = "mixed {$property['field']} {$exceptedComment}";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldRemoveTailSpace(): void
    {
        $property = $this->createFieldStub('whatever', 'unknown', [
            'decorators' => [
                "comment('some-comment     ')",
            ],
        ]);

        $excepted = "mixed {$property['field']} some-comment";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldReturnCommentWordWhenDecoratorOfTypeHasAnotherComment(): void
    {
        $exceptedComment = 'comment-something';

        $property = $this->createFieldStub('whatever', 'unknown', [
            'decorators' => [
                $exceptedComment,
            ],
        ]);

        $excepted = "mixed {$property['field']} {$exceptedComment}";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldReturnMappingWithNewPropertyWhenSetNewMapping(): void
    {
        $exceptedType = 'propertyType';

        $this->target->setMapping('newType', $exceptedType);

        $property = $this->createFieldStub('whatever', 'newType');

        $excepted = "{$exceptedType} {$property['field']}";

        $this->assertSame($excepted, $this->target->generate($property));
        $this->assertSame($exceptedType, $this->target->getMapping()['newType']);
    }
}
