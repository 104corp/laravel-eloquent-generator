<?php

namespace Corp104\Eloquent\Generator;

use Illuminate\Database\Capsule\Manager as DatabaseManager;
use Xethron\MigrationsGenerator\Generators\IndexGenerator;
use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class Resolver
{
    /**
     * Resolve SchemaGenerator
     *
     * @param array $connections
     * @return SchemaGenerator[]
     */
    public function resolveSchemaGenerators(array $connections): array
    {
        return collect($connections)->map(
            static function ($config, $connection) {
                return new SchemaGenerator($connection, false, false);
            }
        )->toArray();
    }

    /**
     * Resolve IndexGenerator
     *
     * @param string $connection
     * @param string $table
     * @return IndexGenerator
     */
    public function resolveIndexGenerator($connection, $table): IndexGenerator
    {
        $schema = DatabaseManager::connection($connection)->getDoctrineConnection();

        return new IndexGenerator(
            $table,
            $schema->getSchemaManager(),
            false
        );
    }
}
