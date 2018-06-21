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

        $fields = $this->buildFields($schemaGenerator, $table);

        return $this->view->make('model', [
            'comment' => $this->commentGenerator->generate($fields),
            'connection' => $connection,
            'name' => Str::studly($table),
            'namespace' => $namespace,
            'pk' => $this->primaryKeyGenerator->generate($indexGenerator, $fields),
            'table' => $table,
        ])->render();
    }

    /**
     * @param SchemaGenerator $schemaGenerator
     * @param string $table
     * @return array
     */
    private function buildFields(SchemaGenerator $schemaGenerator, $table)
    {
        return collect($schemaGenerator->getFields($table))->filter(function ($value, $key) {
            return is_string($key);
        })->toArray();
    }
}
