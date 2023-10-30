<?php

namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Contract\ChainCommandInterface;
use App\ChainCommandBundle\Exceptions\CantBeExecutedException;
use App\ChainCommandBundle\Interfaces\ChainCommandRegistryInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Chain Console Subscriber
 *
 * Implements the functionality of the command chain,
 * runs alternately child commands that must be executed when the parent is started
 */
class ConsoleSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * ConsoleSubscriber constructor.
     *
     * @param ChainCommandRegistryInterface $chainCommandRegistry
     */
    public function __construct(
        private ChainCommandRegistryInterface $chainCommandRegistry
    ) {}

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate'
        ];
    }

    /**
     * Event handler that is executed before the command should be executed.
     *
     * Checks whether the command can be run separately.
     * Child commands cannot be executed separately.
     *
     * @param ConsoleEvent $event
     *
     * @return void
     *
     * @throws CantBeExecutedException
     */
    public function onConsoleCommand(ConsoleEvent $event): void
    {
        $command = $event->getCommand();

        if ($command instanceof ChainCommandInterface) {
            $event->stopPropagation();

            throw new CantBeExecutedException($command->getName(), $command->getParent());
        }

        if ($this->chainCommandRegistry->hasChain($command->getName())) {
            $this->logger->info(
                sprintf(
                    '%s is a master command of a command chain that has registered member commands',
                    $command->getName()
                )
            );

            $commandsChain = $this->chainCommandRegistry->getCommandChain($command->getName());
            foreach ($commandsChain as $chainCommand) {
                $this->logger->info(
                    sprintf(
                        '%s registered as a member of %s command chain',
                        $chainCommand->getName(),
                        $command->getName()
                    )
                );
            }

            $this->logger->info(
                sprintf(
                    'Executing %s command itself first:',
                    $command->getName()
                )
            );
        }
    }

    /**
     * Event handler that is executed after the command has been executed successfully
     *
     * Runs alternately the child commands that are registered members of the chain
     *
     * @param ConsoleEvent $event
     *
     * @return void
     */
    public function onConsoleTerminate(ConsoleEvent $event): void
    {
        $command = $event->getCommand();

        if ($this->chainCommandRegistry->hasChain($command->getName())) {

            $commandsChain = $this->chainCommandRegistry->getCommandChain($command->getName());

            $this->logger->info(
                sprintf(
                    'Executing %s chain members:',
                    $command->getName()
                )
            );
            foreach ($commandsChain as $chainCommand) {
                $chainCommand->run((new ArrayInput([])), $event->getOutput());
            }

            $this->logger->info(
                sprintf(
                    'Execution of %s chain completed.',
                    $command->getName()
                )
            );
        }
    }
}