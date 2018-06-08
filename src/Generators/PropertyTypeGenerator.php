<?php

namespace Corp104\Eloquent\Generator\Generators;

class PropertyTypeGenerator
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
     * @return string
     */
    public function generate($type, $nullable = false): string
    {
        $property = $this->mapping[$type] ?? 'mixed';

        if ($nullable) {
            $property = 'null|' . $property;
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
    public function setMapping($type, $propertyType): void
    {
        $this->mapping[$type] = $propertyType;
    }
}
