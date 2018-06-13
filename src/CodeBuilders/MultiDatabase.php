<?php

namespace Corp104\Eloquent\Generator\CodeBuilders;

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
            return $this->singleDatabase->build($namespace, $connection, true);
        })->toArray();
    }
}
