<?php

use Illuminate\Container\Container;

require __DIR__ . '/../vendor/autoload.php';

Container::setInstance(new Container());

$app = new \Corp104\Eloquent\Generator\App();
$app->run();
