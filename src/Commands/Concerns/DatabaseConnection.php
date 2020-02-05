<?php

namespace Corp104\Eloquent\Generator\Commands\Concerns;

use Noodlehaus\Config;
use RuntimeException;

trait DatabaseConnection
{
    /**
     * @param array $connections
     * @param null|string $connection
     * @return array
     */
    protected function filterConnection(array $connections, $connection = null): array
    {
        if (null === $connection) {
            return $connections;
        }

        if (empty($connections[$connection])) {
            throw new \RuntimeException("Connection '{$connection}' is not found in config file");
        }

        return [
            $connection => $connections[$connection],
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
