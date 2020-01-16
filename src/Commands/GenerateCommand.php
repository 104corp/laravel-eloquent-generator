<?php

namespace Corp104\Eloquent\Generator\Commands;

use Corp104\Eloquent\Generator\CodeBuilder;
use Corp104\Eloquent\Generator\CodeWriter;
use Illuminate\Container\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    use Concerns\DatabaseConnection;
    use Concerns\Environment;

    protected function configure()
    {
        parent::configure();

        $this->setName('eloquent-generator')
            ->setDescription('Generate Eloquent models')
            ->addOption('--env', null, InputOption::VALUE_REQUIRED, '.env file', '.env')
            ->addOption('--config-file', null, InputOption::VALUE_REQUIRED, 'Config file', 'config/database.php')
            ->addOption('--connection', null, InputOption::VALUE_REQUIRED, 'Connection name will only build', null)
            ->addOption('--output-dir', null, InputOption::VALUE_REQUIRED, 'Relative path with getcwd()', 'build')
            ->addOption('--namespace', null, InputOption::VALUE_REQUIRED, 'Namespace prefix', 'App')
            ->addOption('--overwrite', null, InputOption::VALUE_NONE, 'Overwrite the exist file')
            ->addOption('--progress', null, InputOption::VALUE_NONE, 'Use progress bar');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getOption('env');
        $configFile = $input->getOption('config-file');
        $connection = $input->getOption('connection');
        $outputDir = $input->getOption('output-dir');
        $namespace = $input->getOption('namespace');
        $overwrite = $input->getOption('overwrite');

        $this->loadDotEnv(
            $this->normalizePath($env)
        );

        $container = Container::getInstance();

        $this->prepareConnection(
            $container,
            $this->normalizePath($configFile)
        );

        $this->filterConnection($connection);

        /** @var CodeBuilder $codeBuilder */
        $codeBuilder = $container->make(CodeBuilder::class);

        $buildCode = $this->buildCode($codeBuilder, $namespace);

        /** @var CodeWriter $codeWriter */
        $codeWriter = $container->make(CodeWriter::class);

        $codeWriter->setOverwrite($overwrite)
            ->generate(
                $buildCode,
                $this->normalizePath($outputDir),
                $this->createProgressCallback($input, $output, $buildCode)
            );
    }

    private function buildCode(CodeBuilder $codeBuilder, $namespace): array
    {
        return $codeBuilder->setConnections($this->connections)
            ->setNamespace($namespace)
            ->build();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $buildCode
     * @return null
     */
    private function createProgressCallback(InputInterface $input, OutputInterface $output, $buildCode)
    {
        $progress = $input->getOption('progress');

        return $progress
            ? $this->createProgressBarCallback($output, count($buildCode))
            : $this->createProgressRawCallback($output);
    }

    /**
     * @param OutputInterface $output
     * @param int $count
     * @return \Closure
     */
    private function createProgressBarCallback(OutputInterface $output, $count): callable
    {
        $progressBar = new ProgressBar($output, $count);

        return static function () use ($progressBar) {
            $progressBar->advance();
        };
    }

    /**
     * @param OutputInterface $output
     * @return \Closure
     */
    private function createProgressRawCallback(OutputInterface $output): callable
    {
        return static function ($filePath) use ($output) {
            $output->writeln("Writing {$filePath} ...");
        };
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalizePath($path): string
    {
        if ($path[0] !== '/') {
            $path = $this->basePath() . '/' . $path;
        }

        return $path;
    }
}
