<?php

namespace Corp104\Eloquent\Generator;

use Corp104\Eloquent\Generator\Generators\CodeGenerator;
use Illuminate\Support\Str;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class CodeBuilder
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
            $schemaGenerator = new SchemaGenerator($connection, false, false);

            return $this->reduceTablesToArray(
                $schemaGenerator->getTables(),
                $namespace,
                $connection,
                $schemaGenerator
            );
        })->toArray();
    }

    /**
     * @param string $connection
     * @param string $table
     * @param bool $isMultiDatabase
     * @return string
     */
    private function createRelativePath(string $connection, string $table, bool $isMultiDatabase): string
    {
        if ($isMultiDatabase) {
            return '/' . Str::studly($connection) . '/' . Str::studly($table) . '.php';
        }

        return '/' . Str::studly($table) . '.php';
    }

    /**
     * @param array $tables
     * @param $namespace
     * @param $connection
     * @param $schemaGenerator
     * @return mixed
     */
    private function reduceTablesToArray(array $tables, $namespace, $connection, $schemaGenerator)
    {
        return collect($tables)
            ->reduce(function ($carry, $table) use ($namespace, $connection, $schemaGenerator) {
                $relativePath = $this->createRelativePath($connection, $table, $this->isMultiDatabase);

                $code = $this->codeGenerator->generate(
                    $schemaGenerator,
                    $namespace,
                    $connection,
                    $table,
                    $this->isMultiDatabase
                );

                $carry[$relativePath] = $code;

                return $carry;
            }, []);
    }
}
