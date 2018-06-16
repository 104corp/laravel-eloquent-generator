<?php

namespace Corp104\Eloquent\Generator\Commands\Concerns;

trait Environment
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @return string
     */
    protected function basePath()
    {
        return null === $this->basePath ? getcwd() : $this->basePath;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @param string $envFile
     */
    protected function loadDotEnv($envFile)
    {
        if (is_file($envFile)) {
            $file = basename($envFile);
            $path = dirname($envFile);
            (new \Dotenv\Dotenv($path, $file))->load();
        }
    }
}
