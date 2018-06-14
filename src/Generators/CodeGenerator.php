<?php

namespace Corp104\Eloquent\Generator\Generators;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Str;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class CodeGenerator
{
    /**
     * @var CommentGenerator
     */
    private $commentGenerator;

    /**
     * @var ViewFactory
     */
    private $view;

    /**
     * @param CommentGenerator $commentGenerator
     * @param ViewFactory $view
     */
    public function __construct(CommentGenerator $commentGenerator, ViewFactory $view)
    {
        $this->commentGenerator = $commentGenerator;
        $this->view = $view;
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

        return $this->view->make('model', [
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
