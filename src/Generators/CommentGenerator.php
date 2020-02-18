<?php

namespace Corp104\Eloquent\Generator\Generators;

use Illuminate\Support\Facades\Log;

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
        if (empty($fields)) {
            return '';
        }

        $comment = '/**' . PHP_EOL;

        foreach ($fields as $property) {
            $modelProperty = $this->filterComment($this->typeGenerator->generate($property));

            $comment .= " * @property {$modelProperty}" . PHP_EOL;
        }

        $comment .= ' */';

        return $comment;
    }

    private function filterComment(string $generate): string
    {
        $generate = str_replace(["\r\n", "\r"], "\n", $generate);

        $generate = str_replace("\n", "\n *             ", $generate);

        return $generate;
    }
}
