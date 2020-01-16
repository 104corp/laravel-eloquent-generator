<?php

namespace Corp104\Eloquent\Generator\Commands\Concerns;

use Dotenv\Dotenv;

trait Environment
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @return string
     */
    protected function basePath(): string
    {
        return $this->basePath ?? getcwd();
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * @param string $envFile
     */
    protected function loadDotEnv($envFile): void
    {
        if (is_file($envFile)) {
            $file = basename($envFile);
            $path = dirname($envFile);
            (Dotenv::create($path, $file))->load();
        }
    }
}
