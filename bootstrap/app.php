<?php

$app = new Illuminate\Foundation\Application(
    dirname(__DIR__) . '/'
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Corp104\Eloquent\Generator\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Corp104\Eloquent\Generator\Exceptions\Handler::class
);

return $app;
