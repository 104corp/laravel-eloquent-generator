<?php

namespace Corp104\Eloquent\Generator\CodeBuilders;

use Corp104\Eloquent\Generator\Generators\CodeGenerator;
use Illuminate\Support\Str;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class SingleDatabase
{
    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    /**
     * @param CodeGenerator $codeGenerator
     */
    public function __construct(CodeGenerator $codeGenerator)
    {
        $this->codeGenerator = $codeGenerator;
    }

    /**
     * @param SchemaGenerator $schemaGenerator
     * @param string $namespace
     * @param string $connection
     * @param bool $withConnectionNamespace
     * @return array [filepath => code]
     */
    public function build(
        $schemaGenerator,
        $namespace,
        $connection,
        $withConnectionNamespace = false
    ) {
        return collect($schemaGenerator->getTables())
            ->reduce(function (
                $carry,
                $table
            ) use (
                $namespace,
                $connection,
                $schemaGenerator,
                $withConnectionNamespace
            ) {
                $relativePath = $this->createRelativePath($connection, $table, $withConnectionNamespace);

                $code = $this->codeGenerator->generate(
                    $schemaGenerator,
                    $namespace,
                    $connection,
                    $table,
                    $withConnectionNamespace
                );

                $carry[$relativePath] = $code;

                return $carry;
            }, []);
    }

    /**
     * @param string $connection
     * @param string $table
     * @param bool $withConnectionNamespace
     * @return string
     */
    private function createRelativePath($connection, $table, $withConnectionNamespace)
    {
        if ($withConnectionNamespace) {
            return '/' . Str::studly($connection) . '/' . Str::studly($table) . '.php';
        }

        return '/' . Str::studly($table) . '.php';
    }
}
