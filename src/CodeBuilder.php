<?php

namespace Corp104\Eloquent\Generator;

use Corp104\Eloquent\Generator\Generators\CodeGenerator;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CodeGenerator $codeGenerator
     * @param Resolver $resolver
     * @param LoggerInterface $logger
     */
    public function __construct(CodeGenerator $codeGenerator, Resolver $resolver, LoggerInterface $logger)
    {
        $this->codeGenerator = $codeGenerator;
        $this->resolver = $resolver;
        $this->logger = $logger;
    }

    /**
     * @return iterable [filepath => code]
     */
    public function build(): iterable
    {
        foreach (array_keys($this->connections) as $connection) {
            $this->logger->info("Start build connection '$connection' ...");

            yield from $this->transferDatabaseToCode($connection);
        }
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
     * @return iterable
     */
    private function transferDatabaseToCode($connection): iterable
    {
        $schemaGenerator = $this->schemaGenerators[$connection];

        foreach ($schemaGenerator->getTables() as $table) {
            $relativePath = $this->createRelativePath($connection, $table);

            $this->logger->info("Generate model '$relativePath' ...");

            $indexGenerator = $this->resolver->resolveIndexGenerator($connection, $table);

            $code = $this->codeGenerator->generate(
                $schemaGenerator,
                $indexGenerator,
                $this->namespace,
                $connection,
                $table,
                $this->withConnectionNamespace
            );

            yield $relativePath => $code;
        }
    }
}
