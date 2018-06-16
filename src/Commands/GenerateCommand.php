<?php

namespace Corp104\Eloquent\Generator\Commands;

use Corp104\Eloquent\Generator\CodeBuilder;
use Corp104\Eloquent\Generator\CodeWriter;
use Illuminate\Container\Container;
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

        $this->setName('eloquent-generator')
            ->setDescription('Generate Eloquent models')
            ->addOption('--env', null, InputOption::VALUE_REQUIRED, '.env file', '.env')
            ->addOption('--config-file', null, InputOption::VALUE_REQUIRED, 'Config file', 'config/database.php')
            ->addOption('--connection', null, InputOption::VALUE_REQUIRED, 'Connection name will only build', null)
            ->addOption('--output-dir', null, InputOption::VALUE_REQUIRED, 'Relative path with getcwd()', 'build')
            ->addOption('--namespace', null, InputOption::VALUE_REQUIRED, 'Namespace prefix', 'App');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getOption('env');
        $configFile = $input->getOption('config-file');
        $connection = $input->getOption('connection');
        $outputDir = $input->getOption('output-dir');
        $namespace = $input->getOption('namespace');

        $this->loadDotEnv(
            $this->normalizePath($env)
        );

        $container = Container::getInstance();

        $this->prepareConnection(
            $container,
            $this->normalizePath($configFile)
        );

        $codeWriter = $container->make(CodeWriter::class);

        $codeWriter->generate(
            function () use ($container, $namespace, $connection) {
                $connections = $this->connections;

                if (null !== $connection) {
                    $connections = [
                        $connection => $this->connections[$connection],
                    ];
                }

                /** @var CodeBuilder $codeBuilder */
                $codeBuilder = $container->make(CodeBuilder::class);

                return $codeBuilder->setConnections($connections)
                    ->setNamespace($namespace)
                    ->build();
            },
            $this->normalizePath($outputDir)
        );
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalizePath($path)
    {
        if ($path{0} !== '/') {
            $path = $this->basePath() . '/' . $path;
        }

        return $path;
    }
}
