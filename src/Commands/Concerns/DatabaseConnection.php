<?php

namespace Corp104\Eloquent\Generator\Commands\Concerns;

use Illuminate\Container\Container;
use Noodlehaus\Config;

trait DatabaseConnection
{
    /**
     * @var array
     */
    protected $connections;

    /**
     * @param string $configFile
     */
    protected function prepareConnection($configFile)
    {
        $this->connections = $this->normalizeConnectionConfig($configFile);

        $container = Container::getInstance();
        $container->singleton('db', function () {
            $capsule = new \Illuminate\Database\Capsule\Manager;

            foreach ($this->connections as $connectionName => $setting) {
                $capsule->addConnection($setting, $connectionName);
            }

            $capsule->setAsGlobal();

            return $capsule;
        });
    }

    /**
     * @param string $configFile
     * @return array
     */
    protected function normalizeConnectionConfig(string $configFile): array
    {
        $config = Config::load([
            $configFile,
        ]);

        if (!$config->has('connections')) {
            throw new \RuntimeException("The key 'connections' is not set in config file");
        }

        $connections = $config->get('connections');

        if (!\is_array($connections)) {
            throw new \RuntimeException('Connections config is not an array');
        }

        return $connections;
    }
}
