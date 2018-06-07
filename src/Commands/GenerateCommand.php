<?php

namespace Corp104\Eloquent\Generator\Commands;

use Corp104\Eloquent\Generator\Writers\CodeWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    use Concerns\DatabaseConnection,
        Concerns\Environment;

    protected function configure()
    {
        parent::configure();

        $this->setName('generate')
            ->setDescription('Generate model')
            ->addOption('--env', null, InputOption::VALUE_REQUIRED, '.env file', '.env')
            ->addOption('--config-file', null, InputOption::VALUE_REQUIRED, 'Config file', 'config/database.php')
            ->addOption('--output-dir', null, InputOption::VALUE_REQUIRED, 'Relative path with getcwd()', 'build')
            ->addOption('--namespace', null, InputOption::VALUE_REQUIRED, 'Namespace prefix', 'App');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getOption('env');
        $configFile = $input->getOption('config-file');
        $outputDir = $input->getOption('output-dir');
        $namespace = $input->getOption('namespace');

        $this->loadDotEnv(
            $this->normalizePath($env)
        );

        $this->prepareConnection(
            $this->normalizePath($configFile)
        );

        $codeWriter = new CodeWriter($this->connections);

        $codeWriter->generate(
            $namespace,
            $this->normalizePath($outputDir)
        );
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalizePath($path): string
    {
        if ($path{0} !== '/') {
            $path = getcwd() . '/' . $path;
        }

        return $path;
    }
}
