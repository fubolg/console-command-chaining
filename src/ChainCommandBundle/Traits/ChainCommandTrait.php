<?php

namespace App\ChainCommandBundle\Traits;

use Symfony\Component\Console\Input\InputArgument;

trait ChainCommandTrait
{
    protected ?string $parent = null;

    public function setParent(string $commandName): void
    {
        $this->parent = $commandName;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }
}