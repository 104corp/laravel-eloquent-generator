<?php

namespace Corp104\Eloquent\Generator\Generators;

class PropertyTypeGenerator
{
    /**
     * @var array
     */
    protected $mapping = [
        'boolean' => 'bool',
        'dateTime' => '\\Carbon\\Carbon',
        'float' => 'float',
        'integer' => 'int',
        'string' => 'string',
        'text' => 'string',
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
