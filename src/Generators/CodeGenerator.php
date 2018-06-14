<?php

namespace Corp104\Eloquent\Generator\Generators;

use Illuminate\Support\Str;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class CodeGenerator
{
    /**
     * @var CommentGenerator
     */
    private $commentGenerator;

    /**
     * @param CommentGenerator $commentGenerator
     */
    public function __construct(CommentGenerator $commentGenerator)
    {
        $this->commentGenerator = $commentGenerator;
    }

    /**
     * @param SchemaGenerator $schemaGenerator
     * @param string $namespace
     * @param string $connection
     * @param string $table
     * @param bool $withConnectionNamespace
     * @return string
     */
    public function generate(
        SchemaGenerator $schemaGenerator,
        $namespace,
        $connection,
        $table,
        $withConnectionNamespace = false
    ) {
        if ($withConnectionNamespace) {
            $namespace = $namespace . '\\' . ucfirst($connection);
        }

        return view('model', [
            'comment' => $this->buildCommentOfFields($schemaGenerator, $table),
            'connection' => $connection,
            'name' => Str::studly($table),
            'namespace' => $namespace,
            'table' => $table,
        ])->render();
    }

    /**
     * @param SchemaGenerator $schemaGenerator
     * @param string $table
     * @return string
     */
    private function buildCommentOfFields(SchemaGenerator $schemaGenerator, $table)
    {
        $fields = $schemaGenerator->getFields($table);

        return $this->commentGenerator->generate($fields);
    }
}
