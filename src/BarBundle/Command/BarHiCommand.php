<?php

namespace App\BarBundle\Command;

use App\ChainCommandBundle\Contract\ChainCommandInterface;
use App\ChainCommandBundle\Traits\ChainCommandTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BarHiCommand extends Command implements ChainCommandInterface, LoggerAwareInterface
{
    use ChainCommandTrait,
        LoggerAwareTrait;
    protected static $defaultName = 'bar:hi';

    protected static $defaultDescription = 'Outputs greetings from Bar.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hi from Bar<fg=red>!</>');
        $this->logger->info('Hi from Bar!');

        return Command::SUCCESS;
    }
}
