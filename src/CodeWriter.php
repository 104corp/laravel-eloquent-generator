<?php

namespace Corp104\Eloquent\Generator;

class CodeWriter
{
    /**
     * @param array|callable $modelCode Array or callable which should return array like [filePath => code]
     * @param string $pathPrefix
     */
    public function generate($modelCode, $pathPrefix)
    {
        if (is_callable($modelCode)) {
            $modelCode = $modelCode();
        }

        collect($modelCode)->each(function ($code, $filePath) use ($pathPrefix) {
            $this->writeCode($code, $filePath, $pathPrefix);
        });
    }

    /**
     * @param mixed $code
     * @param string $filePath
     * @param string $pathPrefix
     */
    private function writeCode($code, $filePath, $pathPrefix)
    {
        $fullPath = $pathPrefix . '/' . $filePath;

        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $code);
    }
}
