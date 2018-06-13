<?php

namespace Corp104\Eloquent\Generator\Commands\Concerns;

trait Environment
{
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
