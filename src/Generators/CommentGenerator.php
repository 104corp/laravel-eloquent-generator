<?php

namespace Corp104\Eloquent\Generator\Generators;

class CommentGenerator
{
    /**
     * @var PropertyGenerator
     */
    private $typeGenerator;

    /**
     * @param PropertyGenerator $typeGenerator
     */
    public function __construct(PropertyGenerator $typeGenerator)
    {
        $this->typeGenerator = $typeGenerator;
    }

    /**
     * @param array $fields
     * @return string
     */
    public function generate(array $fields)
    {
        $comment = '/**' . PHP_EOL;

        foreach ($fields as $field => $property) {
            $modelProperty = $this->typeGenerator->generate($property);

            $comment .= " * @property {$modelProperty} " . PHP_EOL;
        }

        $comment .= ' */';

        return $comment;
    }

    /**
     * @param array $property
     * @return bool
     */
    private function isNullable(array $property)
    {
        return isset($property['decorators']) && in_array('nullable', $property['decorators'], true);
    }

    /**
     * @param array $property
     * @return null|string
     */
    private function resolveComment(array $property)
    {
        if (empty($property['decorators'])) {
            return null;
        }

        $key = collect($property['decorators'])->search(function ($item) {
            return false !== strpos($item, 'comment');
        });

        if (false === $key) {
            return null;
        }

        preg_match('/comment\(\'(.*)\'\)/', $property['decorators'][$key], $matches);

        return $matches[1];
    }
}
