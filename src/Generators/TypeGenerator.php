<?php

declare(strict_types=1);

namespace Corp104\Eloquent\Generator\Generators;

class TypeGenerator
{
    /**
     * @var array
     */
    protected $mapping = [
        // boolean fields
        'boolean' => 'bool',

        // int fields
        'smallInteger' => 'int',
        'integer' => 'int',
        'bigInteger' => 'int',

        // float fields
        'decimal' => 'float',
        'float' => 'float',

        // string fields
        'char' => 'string',
        'string' => 'string',
        'text' => 'string',

        // date and time fields
        'dateTime' => '\\Carbon\\Carbon',
        'timestamps' => '\\Carbon\\Carbon',
    ];

    /**
     * @param string $type
     * @param bool $nullable
     * @param string|null $comment
     * @return string
     */
    public function generate(string $type, bool $nullable = false, string $comment = null): string
    {
        $property = $this->mapping[$type] ?? 'mixed';

        if ($nullable) {
            $property = 'null|' . $property;
        }

        if (null !== $comment) {
            $property .= ' ' . $comment;
        }

        return $property;
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    /**
     * @param string $type
     * @param string $propertyType
     */
    public function setMapping(string $type, string $propertyType): void
    {
        $this->mapping[$type] = $propertyType;
    }
}
