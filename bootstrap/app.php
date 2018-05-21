<?php

$app = new Illuminate\Foundation\Application(
    dirname(__DIR__) . '/'
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
