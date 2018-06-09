<?php

namespace Corp104\Eloquent\Generator\Generators;

use Illuminate\Support\Str;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class ModelGenerator
{
    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    /**
     * @var bool
     */
    private $isMultiDatabase;

    /**
     * @param CodeGenerator $codeGenerator
     */
    public function __construct(CodeGenerator $codeGenerator)
    {
        $this->codeGenerator = $codeGenerator;
    }

    /**
     * @param string $namespace
     * @param array $connections
     * @return array [filepath => code]
     */
    public function generate($namespace, $connections)
    {
        $this->isMultiDatabase = count($connections) > 1;

        return collect($connections)->keys()->flatMap(function ($connection) use ($namespace) {
            return collect($this->createTables($connection))
                ->reduce(function ($carry, $table) use ($namespace, $connection) {
                    if ($this->isMultiDatabase) {
                        $relativePath = '/' . Str::studly($connection) . '/' . Str::studly($table) . '.php';
                    } else {
                        $relativePath = '/' . Str::studly($table) . '.php';
                    }

                    $code = $this->codeGenerator->generate(
                        $namespace,
                        $connection,
                        $table,
                        $this->isMultiDatabase
                    );

                    $carry[$relativePath] = $code;

                    return $carry;
                }, []);
        })->toArray();
    }

    /**
     * @param string $connection
     * @return array
     */
    private function createTables(string $connection): array
    {
        return (new SchemaGenerator($connection, false, false))
            ->getTables();
    }
}
