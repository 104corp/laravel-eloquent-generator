<?php

declare(strict_types=1);

namespace Corp104\Eloquent\Generator\Commands\Concerns;

use function basename;
use function dirname;
use function is_file;

trait Environment
{
    /**
     * @param string $envFile
     */
    protected function loadDotEnv(string $envFile): void
    {
        if (is_file($envFile)) {
            $file = basename($envFile);
            $path = dirname($envFile);
            (new \Dotenv\Dotenv($path, $file))->load();
        }
    }
}
