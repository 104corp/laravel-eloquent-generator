<?php

namespace Corp104\Eloquent\Generator\Engines;

use Corp104\Eloquent\Generator\Generators\CommentGenerator;
use Illuminate\Contracts\View\Engine;

class TemplateEngine implements Engine
{
    /**
     * @var CommentGenerator
     */
    private $commentGenerator;

    public function __construct(CommentGenerator $commentGenerator)
    {
        $this->commentGenerator = $commentGenerator;
    }

    public function get($path, array $data = [])
    {
        return $this->compile(file_get_contents($path), $this->prepareData($data));
    }

    private function compile($content, $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{ ${key} }}", $value, $content);
        }

        return $content;
    }

    private function filterData(array $data)
    {
        unset($data['__env'], $data['app']);

        return $data;
    }

    private function prepareData(array $data)
    {
        return $this->filterData($data);
    }
}
