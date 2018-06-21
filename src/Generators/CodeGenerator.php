<?php

namespace Corp104\Eloquent\Generator\Generators;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Str;
use Xethron\MigrationsGenerator\Generators\IndexGenerator;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class CodeGenerator
{
    /**
     * @var CommentGenerator
     */
    private $commentGenerator;

    /**
     * @var PrimaryKeyGenerator
     */
    private $primaryKeyGenerator;

    /**
     * @var ViewFactory
     */
    private $view;

    /**
     * @param CommentGenerator $commentGenerator
     * @param PrimaryKeyGenerator $primaryKeyGenerator
     * @param ViewFactory $view
     */
    public function __construct(
        CommentGenerator $commentGenerator,
        PrimaryKeyGenerator $primaryKeyGenerator,
        ViewFactory $view
    ) {
        $this->commentGenerator = $commentGenerator;
        $this->primaryKeyGenerator = $primaryKeyGenerator;
        $this->view = $view;
    }

    /**
     * @param SchemaGenerator $schemaGenerator
     * @param IndexGenerator $indexGenerator
     * @param string $namespace
     * @param string $connection
     * @param string $table
     * @param bool $withConnectionNamespace
     * @return string
     */
    public function generate(
        SchemaGenerator $schemaGenerator,
        IndexGenerator $indexGenerator,
        $namespace,
        $connection,
        $table,
        $withConnectionNamespace = false
    ) {
        if ($withConnectionNamespace) {
            $namespace = $namespace . '\\' . ucfirst($connection);
        }

        return $this->view->make('model', [
            'comment' => $this->buildCommentOfFields($schemaGenerator, $table),
            'connection' => $connection,
            'name' => Str::studly($table),
            'namespace' => $namespace,
            'pk' => $this->buildPrimaryKeyField($schemaGenerator, $indexGenerator, $table),
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

    /**
     * @param SchemaGenerator $schemaGenerator
     * @param IndexGenerator $indexGenerator
     * @param string $table
     * @return string
     */
    private function buildPrimaryKeyField(SchemaGenerator $schemaGenerator, IndexGenerator $indexGenerator, $table)
    {
        $fields = $schemaGenerator->getFields($table);

        return $this->primaryKeyGenerator->generate($indexGenerator, $fields);
    }
}
