<?php

namespace Corp104\Eloquent\Generator;

use Symfony\Component\Console\Output\OutputInterface;

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
     * @param array|callable $code Array or callable which should return array like [filePath => code]
     * @param string $pathPrefix
     * @param null|callable $progressCallback
     */
    public function generate($code, $pathPrefix, $progressCallback = null)
    {
        if (is_callable($code)) {
            $code = $code();
        }

        collect($code)->each(function ($code, $filePath) use ($pathPrefix, $progressCallback) {
            if (null !== $progressCallback) {
                $progressCallback($filePath, $this->progress);
            }

            $this->progress++;

            $this->writeCode($code, $filePath, $pathPrefix);
        });

        $this->progress = 0;
    }

    /**
     * @param bool $overwrite
     * @return static
     */
    public function setOverwrite($overwrite)
    {
        $this->overwrite = (bool)$overwrite;

        return $this;
    }

    /**
     * @param mixed $code
     * @param string $filePath
     * @param string $pathPrefix
     */
    private function writeCode($code, $filePath, $pathPrefix)
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
