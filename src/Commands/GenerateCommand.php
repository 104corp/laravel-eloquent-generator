<?php

namespace Corp104\Eloquent\Generator\Commands;

use Closure;
use Corp104\Eloquent\Generator\CodeBuilder;
use Corp104\Eloquent\Generator\CodeWriter;
use Illuminate\Support\Facades\Log;
use LaravelBridge\Scratch\Application as LaravelBridge;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    use Concerns\DatabaseConnection;
    use Concerns\Environment;

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

        $this->loadDotEnv(
            $this->normalizePath($env)
        );

        // Normalize connection config
        $connections = $this->normalizeConnectionConfig($this->normalizePath($configFile));

        // Filter connection if presented
        $this->container['config']['database.connections'] = $this->filterConnection($connections, $connection);

        /** @var CodeBuilder $codeBuilder */
        $codeBuilder = $this->container->make(CodeBuilder::class);

        $buildCode = $this->buildCode($codeBuilder, $this->container['config']['database.connections'], $namespace);

        /** @var CodeWriter $codeWriter */
        $codeWriter = $this->container->make(CodeWriter::class);

        $codeWriter->setOverwrite($overwrite)
            ->generate($buildCode, $this->normalizePath($outputDir), $this->createProgressCallback());
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

    /**
     * @return Closure
     */
    private function createProgressCallback(): callable
    {
        return static function ($filePath) {
            Log::info("Writing '$filePath' ...");
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
