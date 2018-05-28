<?php

namespace App\Engines;

use App\Generators\PropertyTypeGenerator;
use Illuminate\Contracts\View\Engine;

class TemplateEngine implements Engine
{
    /**
     * @var PropertyTypeGenerator
     */
    private $propertyTypeGenerator;

    public function __construct(PropertyTypeGenerator $propertyTypeGenerator)
    {
        $this->propertyTypeGenerator = $propertyTypeGenerator;
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
        $data = $this->filterData($data);

        $data['comment'] = $this->buildComment($data['fields']);
        unset($data['fields']);

        return $data;
    }

    private function buildComment($fields)
    {
        $comment = '';

        $comment .= '/**' . PHP_EOL;

        foreach ($fields as $field => $detail) {
            $nullable = isset($detail['decorators']) && in_array('nullable', $detail['decorators'], true);
            $propertyType = $this->propertyTypeGenerator->generate($detail['type'], $nullable);
            $comment .= " * @property ${propertyType} " . $field . PHP_EOL;
        }

        $comment .= ' */';

        return $comment;
    }
}
