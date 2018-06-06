<?php

namespace Corp104\Eloquent\Generator\Commands;

use Corp104\Eloquent\Generator\Writers\CodeWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('generate')
            ->setDescription('Generate model')
            ->addOption('--output-dir', null, InputOption::VALUE_REQUIRED, 'Relative path with getcwd()', 'build')
            ->addOption('--namespace', null, InputOption::VALUE_REQUIRED, 'Namespace prefix', 'App');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $codeWriter = new CodeWriter([
            'default' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'default',
                'username' => 'root',
                'password' => 'password',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ]);

        $outputDir = $input->getOption('output-dir');
        $namespace = $input->getOption('namespace');

        $pathPrefix = getcwd() . '/' . $outputDir;

        $codeWriter->generate($namespace, $pathPrefix);
    }

    public function handle(CodeWriter $generator)
    {
        $outputDir = $this->input->getOption('output-dir');
        $namespace = $this->input->getOption('namespace');

        $pathPrefix = getcwd() . '/' . $outputDir;

        $generator->generate($namespace, $pathPrefix);
    }
}
