<?php

namespace Corp104\Eloquent\Generator\Writers;

use Corp104\Eloquent\Generator\Generators\CodeGenerator;
use Corp104\Eloquent\Generator\Generators\CommentGenerator;
use Illuminate\Support\Str;
use PHP_CodeSniffer\Reports\Code;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

use function count;

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

    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    public function __construct(CodeGenerator $codeGenerator, $connections)
    {
        $this->connections = $connections;
        $this->isMultiDatabase = count($this->connections) > 1;
        $this->codeGenerator = $codeGenerator;
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

            foreach ($tables as $table) {
                $code = $this->codeGenerator->generate(
                    $namespacePrefix,
                    $connection,
                    $table,
                    $this->isMultiDatabase
                );

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
