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
     * @param bool $isMultiDatabase
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generate(
        SchemaGenerator $schemaGenerator,
        $namespace,
        $connection,
        $table,
        $isMultiDatabase = false
    ) {
        if ($isMultiDatabase) {
            $namespace = $namespace . '\\' . ucfirst($connection);
        }

        return view('model', [
            'comment' => $this->buildCommentOfFields($schemaGenerator, $table),
            'connection' => $connection,
            'name' => Str::studly($table),
            'namespace' => $namespace,
            'table' => $table,
        ]);
    }

    /**
     * @param SchemaGenerator $schemaGenerator
     * @param string $table
     * @return string
     */
    private function buildCommentOfFields(SchemaGenerator $schemaGenerator, $table): string
    {
        $fields = $schemaGenerator->getFields($table);

        return $this->commentGenerator->generate($fields);
    }
}
