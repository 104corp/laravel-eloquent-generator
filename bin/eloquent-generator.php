<?php

use Corp104\Eloquent\Generator\App;
use LaravelBridge\Scratch\Application as LaravelBridge;

require __DIR__ . '/../vendor/autoload.php';

$app = new App(LaravelBridge::getInstance());
$app->run();
