<?php

declare(strict_types=1);

namespace Corp104\Eloquent\Generator;

use function dirname;
use function file_put_contents;
use function mkdir;

class CodeWriter
{
    /**
     * @param callable $callable Should return array like [filePath => code]
     * @param string $pathPrefix
     */
    public function generate(callable $callable, $pathPrefix): void
    {
        $modelCode = $callable();

        collect($modelCode)->each(function ($code, $filePath) use ($pathPrefix) {
            $this->writeCode($code, $filePath, $pathPrefix);
        });
    }

    /**
     * @param mixed $code
     * @param string $filePath
     * @param string $pathPrefix
     */
    private function writeCode($code, string $filePath, string $pathPrefix): void
    {
        $fullPath = $pathPrefix . '/' . $filePath;

        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $code);
    }
}
