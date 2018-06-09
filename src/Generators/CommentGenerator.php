<?php

declare(strict_types=1);

namespace Corp104\Eloquent\Generator\Generators;

use function in_array;

class CommentGenerator
{
    /**
     * @var TypeGenerator
     */
    private $typeGenerator;

    /**
     * @param TypeGenerator $typeGenerator
     */
    public function __construct(TypeGenerator $typeGenerator)
    {
        $this->typeGenerator = $typeGenerator;
    }

    /**
     * @param array $fields
     * @return string
     */
    public function generate(array $fields): string
    {
        $comment = '/**' . PHP_EOL;

        foreach ($fields as $field => $property) {
            $propertyType = $this->typeGenerator->generate(
                $property['type'],
                $this->isNullable($property)
            );

            $comment .= " * @property ${propertyType} " . $property['field'] . PHP_EOL;
        }

        $comment .= ' */';

        return $comment;
    }

    /**
     * @param array $property
     * @return bool
     */
    private function isNullable(array $property): bool
    {
        return isset($property['decorators']) && in_array('nullable', $property['decorators'], true);
    }
}
