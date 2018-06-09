<?php

declare(strict_types=1);

namespace Corp104\Eloquent\Generator\CodeBuilders;

use Corp104\Eloquent\Generator\Generators\CodeGenerator;
use Illuminate\Support\Str;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class MultiDatabase
{
    /**
     * @var SingleDatabase
     */
    private $singleDatabase;

    /**
     * @param SingleDatabase $singleDatabase
     */
    public function __construct(SingleDatabase $singleDatabase)
    {
        $this->singleDatabase = $singleDatabase;
    }

    /**
     * @param string $namespace
     * @param array $connections
     * @return array [filepath => code]
     */
    public function build($namespace, $connections): array
    {
        return collect($connections)->keys()->flatMap(function ($connection) use ($namespace) {
            return $this->singleDatabase->build($namespace, $connection, true);
        })->toArray();
    }
}
