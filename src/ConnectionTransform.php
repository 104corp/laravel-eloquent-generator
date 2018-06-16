<?php

namespace Corp104\Eloquent\Generator;

use Xethron\MigrationsGenerator\Generators\SchemaGenerator;

class ConnectionTransform
{
    /**
     * Transform connections to SchemaGenerator
     * @param array $connections
     * @return SchemaGenerator[]
     */
    public function transform(array $connections)
    {
        return collect($connections)->map(function ($config, $connection) {
            return new SchemaGenerator($connection, false, false);
        })->toArray();
    }
}
