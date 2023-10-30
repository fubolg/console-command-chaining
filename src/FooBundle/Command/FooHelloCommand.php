<?php

namespace App\FooBundle\Command;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FooHelloCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    protected static $defaultName = 'foo:hello';
    protected static $defaultDescription = 'Outputs greetings from Foo.';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello from Foo<fg=red>!</>');
        $this->logger->info('Hello from Foo!');

        return Command::SUCCESS;
    }
}
