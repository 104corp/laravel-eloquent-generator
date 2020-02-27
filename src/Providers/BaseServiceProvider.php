<?php

namespace Corp104\Eloquent\Generator\Providers;

use Corp104\Eloquent\Generator\Listeners\BootstrapLogger;
use Corp104\Eloquent\Generator\Listeners\BootstrapVersion;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(ArtisanStarting::class, BootstrapVersion::class);

        Event::listen(CommandStarting::class, BootstrapLogger::class);
    }
}
