<?php

require __DIR__.'/../vendor/autoload.php';

// Configure database connections
$connections = [
    'default' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'default',
        'username' => 'root',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
];

$capsule = new \Illuminate\Database\Capsule\Manager;

foreach ($connections as $connectionName => $setting) {
    $capsule->addConnection($setting, $connectionName);
}

$capsule->setAsGlobal();

$app = new \Corp104\Eloquent\Generator\App();
$app->run();
