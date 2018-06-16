<?php

namespace Corp104\Eloquent\Generator\CodeBuilders;

use Corp104\Eloquent\Generator\ConnectionTransform;

class MultiDatabase
{
    /**
     * @var ConnectionTransform
     */
    private $connectionTransform;

    /**
     * @var SingleDatabase
     */
    private $singleDatabase;

    /**
     * @param SingleDatabase $singleDatabase
     * @param ConnectionTransform $connectionTransform
     */
    public function __construct(SingleDatabase $singleDatabase, ConnectionTransform $connectionTransform)
    {
        $this->singleDatabase = $singleDatabase;
        $this->connectionTransform = $connectionTransform;
    }

    /**
     * @param string $namespace
     * @param array $connections
     * @return array [filepath => code]
     */
    public function build($namespace, $connections)
    {
        $schemaGenerators = $this->connectionTransform->transform($connections);

        return collect($schemaGenerators)->flatMap(function ($schemaGenerator, $connection) use ($namespace) {
            return $this->singleDatabase->build(
                $schemaGenerator,
                $namespace,
                $connection,
                true
            );
        })->toArray();
    }
}
