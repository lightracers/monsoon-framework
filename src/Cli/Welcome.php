<?php

namespace Cli;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class Welcome extends SymfonyCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('greet')
            ->setDescription('Greet a user based on the time of the day.')
            -> setHelp('This command allows you to greet a user based on the time of the day...')
            -> addArgument('username', InputArgument::REQUIRED, 'The username of the user.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello '.$input -> getArgument('username').'!');

    }
}
