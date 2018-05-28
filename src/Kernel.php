<?php

namespace Corp104\Eloquent\Generator;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\Build::class,
    ];
}
