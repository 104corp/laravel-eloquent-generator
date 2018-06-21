<?php

namespace Corp104\Eloquent\Generator\Generators;

class PropertyGenerator
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
     * @param array $columnProperties
     * @return string
     */
    public function generate(array $columnProperties)
    {
        $type = isset($this->mapping[$columnProperties['type']]) ?
            $this->mapping[$columnProperties['type']] : 'mixed';
        $field = $columnProperties['field'];

        $modelProperty = "{$type} {$field}";
        $modelProperty = $this->resolveDecorators($columnProperties, $modelProperty);

        // Should remove tail space
        return trim($modelProperty);
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

    /**
     * @param array $decorators
     * @return bool
     */
    private function resolveNullable(array $decorators)
    {
        return in_array('nullable', $decorators, true);
    }

    /**
     * @param array $decorators
     * @return null|string
     */
    private function resolveComment(array $decorators)
    {
        $key = collect($decorators)->search(function ($item) {
            return false !== strpos($item, 'comment');
        });

        if (false === $key) {
            return null;
        }

        if (preg_match('/comment\(\'(.*)\'\)/', $decorators[$key], $matches)) {
            return $matches[1];
        }

        return $decorators[$key];
    }

    /**
     * @param array $property
     * @param string $modelProperty
     * @return string
     */
    private function resolveDecorators(array $property, $modelProperty)
    {
        $decorators = isset($property['decorators']) ? $property['decorators'] : null;

        if (empty($decorators)) {
            return $modelProperty;
        }

        if ($this->resolveNullable($decorators)) {
            $modelProperty = 'null|' . $modelProperty;
        }

        $comment = $this->resolveComment($decorators);

        if (null !== $comment) {
            $modelProperty .= ' ' . $comment;
        }

        return $modelProperty;
    }
}
