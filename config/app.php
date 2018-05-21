<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'local'),
    'debug' => env('APP_DEBUG', true),

    'providers' => [
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Database\MigrationServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
    ],

    'aliases' => [
    ],
];
