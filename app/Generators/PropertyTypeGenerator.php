<?php

namespace App\Generators;

class PropertyTypeGenerator
{
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
    public function generate($type, $nullable = false)
    {
        $property = isset($this->mapping[$type]) ? $this->mapping[$type] : 'mixed';

        if ($nullable) {
            $property = 'null|' . $property;
        }

        return $property;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param string $type
     * @param string $propertyType
     */
    public function setMapping($type, $propertyType)
    {
        $this->mapping[$type] = $propertyType;
    }
}
