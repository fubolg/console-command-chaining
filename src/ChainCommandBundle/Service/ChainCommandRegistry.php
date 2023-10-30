<?php

namespace App\ChainCommandBundle\Service;

use App\ChainCommandBundle\Contract\ChainCommandInterface;
use App\ChainCommandBundle\Interfaces\ChainCommandRegistryInterface;

/**
 * Chain Command Registry
 *
 * Registers chains subscribed on parent command
 */
class ChainCommandRegistry implements ChainCommandRegistryInterface
{
    /**
     * @var array
     */
    private array $chains = [];

    /**
     * Adds a command to the chain registry.
     *
     * Registers command as a member of chain
     *
     * @param ChainCommandInterface $command
     * @param string $parent
     *
     * @return void
     */
    public function addCommandToChain(ChainCommandInterface $command, string $parent): void
    {
        $command->setParent($parent);
        if (!$this->hasChain($parent)) {
            $this->chains[$parent] = [];
        }

        $this->chains[$parent]['chain'][] = $command;
    }

    /**
     * Retrieves the command chain from the registry.
     *
     * Returns the list of commands that are registered as members of the chain for the parent command.
     *
     * @param string $commandName
     *
     * @return iterable
     */
    public function getCommandChain(string $commandName): iterable
    {
        return $this->chains[$commandName]['chain'] ?? [];
    }

    /**
     * Сhecks if a command chain exists.
     *
     * Returns true if there are registered chain members for the parent command.
     *
     * @param string $commandName
     *
     * @return bool
     */
    public function hasChain(string $commandName): bool
    {
        return array_key_exists($commandName, $this->chains);
    }
}
