<?php

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

    public function generate($field)
    {
        return $this->buildComment($field);
    }

    private function buildComment($fields)
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

    private function isNullable(array $property)
    {
        return isset($property['decorators']) && in_array('nullable', $property['decorators'], true);
    }
}
