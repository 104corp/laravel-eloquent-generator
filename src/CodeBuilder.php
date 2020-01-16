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
     * @var array
     */
    private $connections;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var SchemaGenerator[]
     */
    private $schemaGenerators;

    /**
     * @var bool
     */
    private $withConnectionNamespace;

    /**
     * @param CodeGenerator $codeGenerator
     * @param Resolver $resolver
     */
    public function __construct(CodeGenerator $codeGenerator, Resolver $resolver)
    {
        $this->codeGenerator = $codeGenerator;
        $this->resolver = $resolver;
    }

    /**
     * @return array [filepath => code]
     */
    public function build(): array
    {
        $connections = array_keys($this->connections);

        return collect($connections)->flatMap(function ($connection) {
            return $this->transferDatabaseToCode($connection);
        })->toArray();
    }

    /**
     * @param array $connections
     * @return static
     */
    public function setConnections(array $connections): CodeBuilder
    {
        $this->connections = $connections;
        $this->withConnectionNamespace = count($connections) > 1;
        $this->schemaGenerators = $this->resolver->resolveSchemaGenerators($connections);

        return $this;
    }

    /**
     * @param string $namespace
     * @return static
     */
    public function setNamespace($namespace): CodeBuilder
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param string $connection
     * @param string $table
     * @return string
     */
    private function createRelativePath($connection, $table): string
    {
        if ($this->withConnectionNamespace) {
            return '/' . Str::studly($connection) . '/' . Str::studly($table) . '.php';
        }

        return '/' . Str::studly($table) . '.php';
    }

    /**
     * @param string $connection
     * @return mixed
     */
    private function transferDatabaseToCode($connection)
    {
        $schemaGenerator = $this->schemaGenerators[$connection];

        return collect($schemaGenerator->getTables())
            ->reduce(function ($carry, $table) use ($connection, $schemaGenerator) {
                $relativePath = $this->createRelativePath($connection, $table);

                $indexGenerator = $this->resolver->resolveIndexGenerator($connection, $table);

                $code = $this->codeGenerator->generate(
                    $schemaGenerator,
                    $indexGenerator,
                    $this->namespace,
                    $connection,
                    $table,
                    $this->withConnectionNamespace
                );

                $carry[$relativePath] = $code;

                return $carry;
            }, []);
    }
}
