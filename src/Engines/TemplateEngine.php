<?php

namespace Corp104\Eloquent\Generator\Engines;

use Illuminate\Contracts\View\Engine;

class TemplateEngine implements Engine
{
    public function get($path, array $data = [])
    {
        return $this->compile(file_get_contents($path), $this->filterData($data));
    }

    private function compile($content, $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{ ${key} }}", $value, $content);
        }

        return $content;
    }

    private function filterData(array $data): array
    {
        unset($data['__env'], $data['app']);

        return $data;
    }
}
