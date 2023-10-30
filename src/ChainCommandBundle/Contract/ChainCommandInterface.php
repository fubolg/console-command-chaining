<?php

namespace App\ChainCommandBundle\Contract;

/**
 * ChainCommandInterface.
 *
 * The interface that should be implemented by the Commands that should be part of the Chain
 *
 * The methods of this interface are implemented in \App\ChainCommandBundle\Trait\ChainCommandTrait::class
 */
interface ChainCommandInterface
{
    /**
     * Sets the name of the parent command.
     *
     * @param string $commandName
     * @return void
     */
    public function setParent(string $commandName): void;

    /**
     * Returns the name of the parent command.
     *
     * @return string|null
     */
    public function getParent(): ?string;
}