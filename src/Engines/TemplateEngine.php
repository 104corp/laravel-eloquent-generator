<?php

namespace Corp104\Eloquent\Generator\Engines;

use Illuminate\View\Engines\EngineInterface;

class TemplateEngine implements EngineInterface
{
    public function get($path, array $data = [])
    {
        return $this->compile(file_get_contents($path), $this->filterData($data));
    }

    /**
     * @param string $content
     * @param array $data
     * @return string
     */
    private function compile($content, array $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{ ${key} }}", $value, $content);
        }

        return $content;
    }

    /**
     * @param array $data
     * @return array
     */
    private function filterData(array $data)
    {
        unset($data['__env'], $data['app']);

        return $data;
    }
}
