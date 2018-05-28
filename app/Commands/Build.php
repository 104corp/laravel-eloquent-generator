<?php

namespace App\Commands;

use App\Writers\CodeWriter;
use Illuminate\Console\Command;

class Build extends Command
{
    /**
     * @var string
     */
    protected $signature = 'build
                                {--output-dir=build : Relative path with getcwd()}
                                {--namespace=App : Namespace prefix}';

    /**
     * @var string
     */
    protected $description = 'Build';

    public function handle(CodeWriter $generator)
    {
        $outputDir = $this->input->getOption('output-dir');
        $namespace = $this->input->getOption('namespace');

        $pathPrefix = getcwd() . '/' . $outputDir;

        $generator->generate($namespace, $pathPrefix);
    }
}
