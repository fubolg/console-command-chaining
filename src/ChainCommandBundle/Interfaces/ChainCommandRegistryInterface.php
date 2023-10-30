<?php

namespace App\ChainCommandBundle\Interfaces;

use App\ChainCommandBundle\Contract\ChainCommandInterface;

interface ChainCommandRegistryInterface
{
    /**
     * Adds a command to the chain registry.
     *
     * @param ChainCommandInterface $command
     * @param string$parent
     *
     * @return void
     */
    public function addCommandToChain(ChainCommandInterface $command, string $parent): void;

    /**
     * Retrieves the command chain from the registry.
     *
     * @param string $commandName
     *
     * @return iterable|ChainCommandInterface[]
     */
    public function getCommandChain(string $commandName): iterable;

    /**
     * Сhecks if a command chain exists.
     *
     * @param string $commandName
     *
     * @return bool
     */
    public function hasChain(string $commandName): bool;
}