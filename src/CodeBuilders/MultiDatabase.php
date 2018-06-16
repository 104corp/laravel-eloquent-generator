<?php

namespace Corp104\Eloquent\Generator\CodeBuilders;

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
    public function build($namespace, $connections)
    {
        return collect($connections)->keys()->flatMap(function ($connection) use ($namespace) {
            $schemaGenerator = new SchemaGenerator($connection, false, false);

            return $this->singleDatabase->build(
                $schemaGenerator,
                $namespace,
                $connection,
                true
            );
        })->toArray();
    }
}
