<?php

namespace Corp104\Eloquent\Generator\Writers;

use Corp104\Eloquent\Generator\Generators\ModelGenerator;
use function count;

class CodeWriter
{
    /**
     * @var array
     */
    protected $connections;

    /**
     * @var bool
     */
    protected $isMultiDatabase;

    /**
     * @var ModelGenerator
     */
    private $modelGenerator;

    public function __construct(ModelGenerator $modelGenerator, $connections)
    {
        $this->connections = $connections;
        $this->isMultiDatabase = count($this->connections) > 1;
        $this->modelGenerator = $modelGenerator;
    }

    /**
     * @param string $namespacePrefix
     * @param string $pathPrefix
     */
    public function generate($namespacePrefix, $pathPrefix)
    {
        $modelCode = $this->modelGenerator->generate($namespacePrefix, $this->connections);

        collect($modelCode)->each(function ($code, $filePath) use ($pathPrefix) {
            $this->writeCode($code, $filePath, $pathPrefix);
        });
    }

    /**
     * @param string $code
     * @param string $filePath
     * @param string $pathPrefix
     */
    private function writeCode($code, $filePath, $pathPrefix)
    {
        $fullPath = $pathPrefix . '/' . $filePath;

        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $code);
    }
}
