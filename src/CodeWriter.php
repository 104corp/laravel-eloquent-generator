<?php

namespace Corp104\Eloquent\Generator;

class CodeWriter
{
    /**
     * @var bool
     */
    private $overwrite = false;

    /**
     * @var int
     */
    private $progress = 0;

    /**
     * @param iterable $buildCode Array or callable which should return array like [filePath => code]
     * @param string $pathPrefix
     * @param null|callable $progressCallback
     */
    public function generate(iterable $buildCode, $pathPrefix, $progressCallback = null): void
    {
        foreach ($buildCode as $filePath => $code) {
            if (null !== $progressCallback) {
                $progressCallback($filePath, $this->progress);
            }

            $this->progress++;

            $this->writeCode($code, $filePath, $pathPrefix);
        }

        $this->progress = 0;
    }

    /**
     * @param bool $overwrite
     * @return static
     */
    public function setOverwrite($overwrite): CodeWriter
    {
        $this->overwrite = (bool)$overwrite;

        return $this;
    }

    /**
     * @param mixed $code
     * @param string $filePath
     * @param string $pathPrefix
     */
    private function writeCode($code, $filePath, $pathPrefix): void
    {
        $fullPath = $pathPrefix . '/' . $filePath;

        if (!$this->overwrite && is_file($fullPath)) {
            return;
        }

        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $code);
    }
}
