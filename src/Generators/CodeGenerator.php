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
     * @param string $namespace
     * @param string $connection
     * @param string $table
     * @param bool $isMultiDatabase
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generate($namespace, $connection, $table, $isMultiDatabase = false)
    {
        if ($isMultiDatabase) {
            $namespace = $namespace . '\\' . ucfirst($connection);
        }

        return view('model', [
            'comment' => $this->buildCommentOfFields($connection, $table),
            'connection' => $connection,
            'name' => Str::studly($table),
            'namespace' => $namespace,
            'table' => $table,
        ]);
    }

    /**
     * @param string $connection
     * @param string $table
     * @return string
     */
    private function buildCommentOfFields($connection, $table): string
    {
        $schemaGenerator = new SchemaGenerator($connection, false, false);

        $fields = $schemaGenerator->getFields($table);

        return $this->commentGenerator->generate($fields);
    }
}
