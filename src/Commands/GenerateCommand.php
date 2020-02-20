<?php

namespace Corp104\Eloquent\Generator\Commands;

use Corp104\Eloquent\Generator\CodeBuilder;
use LaravelBridge\Scratch\Application as LaravelBridge;
use MilesChou\Codegener\Traits\Path;
use MilesChou\Codegener\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    use Concerns\DatabaseConnection;
    use Concerns\Environment;
    use Path;

    /**
     * @var LaravelBridge
     */
    private $container;

    public function __construct(LaravelBridge $container, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->container->setupLogger('laravel-eloquent-generator', new ConsoleLogger($output));
    }

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
            ->addOption('--overwrite', null, InputOption::VALUE_NONE, 'Overwrite the exist file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getOption('env');
        $configFile = $input->getOption('config-file');
        $connection = $input->getOption('connection');
        $outputDir = $input->getOption('output-dir');
        $namespace = $input->getOption('namespace');
        $overwrite = $input->getOption('overwrite');

        $this->loadDotEnv($this->formatPath($env));

        // Normalize connection config
        $connections = $this->normalizeConnectionConfig($this->formatPath($configFile));

        // Filter connection if presented
        $this->container['config']['database.connections'] = $this->filterConnection($connections, $connection);

        /** @var CodeBuilder $codeBuilder */
        $codeBuilder = $this->container->make(CodeBuilder::class);

        $buildCode = $this->buildCode($codeBuilder, $this->container['config']['database.connections'], $namespace);

        /** @var Writer $writer */
        $writer = $this->container->make(Writer::class);
        $writer->setBasePath($this->formatPath($outputDir));

        $writer->writeMass($buildCode, $overwrite);
    }

    /**
     * @param CodeBuilder $codeBuilder
     * @param array $connections
     * @param $namespace
     * @return array
     */
    private function buildCode(CodeBuilder $codeBuilder, array $connections, $namespace): iterable
    {
        return $codeBuilder->setConnections($connections)
            ->setNamespace($namespace)
            ->build();
    }
}
