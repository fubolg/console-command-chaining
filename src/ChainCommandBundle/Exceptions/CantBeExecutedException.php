<?php

namespace App\ChainCommandBundle\Exceptions;

class CantBeExecutedException extends ChainCommandException
{
    /**
     * NotMasterCommandException constructor.
     *
     * @param string $commandName Command name
     */
    public function __construct(string $commandName, string $parentName)
    {
        $message = sprintf(
            'Error: %s command is a member of %s command chain and cannot be executed on its own.',
            $commandName,
            $parentName
        );

        parent::__construct($message);
    }
}