<?php

namespace Corp104\Eloquent\Generator\Listeners;

use Corp104\Eloquent\Generator\Version;
use Illuminate\Console\Events\ArtisanStarting;

class BootstrapVersion
{
    public function handle(ArtisanStarting $event): void
    {
        $event->artisan->setName('Laravel Eloquent Generator');

        if (class_exists(Version::class)) {
            $event->artisan->setVersion(Version::VERSION);
        }
    }
}
