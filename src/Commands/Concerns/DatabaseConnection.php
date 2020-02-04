<?php

namespace Corp104\Eloquent\Generator\Commands\Concerns;

use LaravelBridge\Scratch\Application as LaravelBridge;
use Noodlehaus\Config;
use RuntimeException;

trait DatabaseConnection
{
    /**
     * @var array
     */
    protected $connections;

    /**
     * @param LaravelBridge $container
     * @param string $configFile
     */
    protected function prepareConnection(LaravelBridge $container, $configFile): void
    {
        $this->connections = $this->normalizeConnectionConfig($configFile);

        $container->setupDatabase($this->connections);
    }

    /**
     * @param null|string $connection
     */
    protected function filterConnection($connection = null): void
    {
        if (null === $connection) {
            return;
        }

        if (empty($this->connections[$connection])) {
            throw new \RuntimeException("Connection '{$connection}' is not found in config file");
        }

        $this->connections = [
            $connection => $this->connections[$connection],
        ];
    }

    /**
     * @param string $configFile
     * @return array
     */
    protected function normalizeConnectionConfig($configFile): array
    {
        $config = Config::load([
            $configFile,
        ]);

        if (!$config->has('connections')) {
            throw new RuntimeException("The key 'connections' is not set in config file");
        }

        $connections = $config->get('connections');

        if (!is_array($connections)) {
            throw new RuntimeException('Connections config is not an array');
        }

        return $connections;
    }
}
