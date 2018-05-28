<?php

namespace Corp104\Eloquent\Generator\Writers;

use Illuminate\Support\Str;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class CodeWriter
{
    /**
     * @var array
     */
    protected $connections;

    /**
     * @var bool
     */
    protected $isMultiDatabase;

    public function __construct()
    {
        $this->connections = config('database.connections');
        $this->isMultiDatabase = count($this->connections) > 1;
    }

    /**
     * @param string $namespacePrefix
     * @param string $pathPrefix
     */
    public function generate($namespacePrefix, $pathPrefix)
    {
        collect($this->connections)->each(function ($config, $connection) use ($namespacePrefix, $pathPrefix) {
            $schemaGenerator = new SchemaGenerator($connection, false, false);

            $tables = $schemaGenerator->getTables();

            if ($this->isMultiDatabase) {
                $namespacePrefix = $namespacePrefix . '\\' . ucfirst($connection);
            }

            foreach ($tables as $table) {
                $code = view('model', [
                    'connection' => $connection,
                    'fields' => $schemaGenerator->getFields($table),
                    'name' => Str::studly($table),
                    'namespace' => $namespacePrefix,
                    'table' => $table,
                ]);

                $this->writeCode($code, $table, $connection, $pathPrefix);
            }
        });
    }

    /**
     * @param string $code
     * @param string $table
     * @param string $connection
     * @param string $pathPrefix
     */
    private function writeCode($code, $table, $connection, $pathPrefix)
    {
        if ($this->isMultiDatabase) {
            $fullPath = $pathPrefix . '/' . Str::studly($connection) . '/' . Str::studly($table) . '.php';
        } else {
            $fullPath = $pathPrefix . '/' . Str::studly($table) . '.php';
        }

        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $code);
    }
}
