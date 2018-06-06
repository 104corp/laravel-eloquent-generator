<?php

namespace Corp104\Eloquent\Generator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('generate')
            ->setDescription('Generate model');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello World');
    }
}
