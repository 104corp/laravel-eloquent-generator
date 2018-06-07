<?php

namespace Corp104\Eloquent\Generator\Commands;

use Corp104\Eloquent\Generator\Writers\CodeWriter;
use Noodlehaus\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    use Concerns\DatabaseConnection;

    protected function configure()
    {
        parent::configure();

        $this->setName('generate')
            ->setDescription('Generate model')
            ->addOption('--config-file', null, InputOption::VALUE_REQUIRED, 'Config file', 'config/database.php')
            ->addOption('--output-dir', null, InputOption::VALUE_REQUIRED, 'Relative path with getcwd()', 'build')
            ->addOption('--namespace', null, InputOption::VALUE_REQUIRED, 'Namespace prefix', 'App');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption('config-file');
        $outputDir = $input->getOption('output-dir');
        $namespace = $input->getOption('namespace');

        $configFile = $this->normalizePath($configFile);

        $this->prepareConnection($configFile);

        $codeWriter = new CodeWriter($this->connections);

        $codeWriter->generate($namespace, $this->normalizePath($outputDir));
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

    /**
     * @param string $alias
     * @param string $file
     */
    public function setFile($alias, $file)
    {
        if ($file{0} !== '/') {
            $file = getcwd() . '/' . $file;
        }

        if (!is_file($file)) {
            throw new FileNotFoundException("$file is not found.");
        }

        $this->files[$alias] = $file;
    }
}
