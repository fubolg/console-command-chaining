<?php

namespace App\ChainCommandBundle\Tests\Unit\Service;

use App\ChainCommandBundle\Contract\ChainCommandInterface;
use App\ChainCommandBundle\Service\ChainCommandRegistry;
use App\ChainCommandBundle\Traits\ChainCommandTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class ChainCommandRegistryTest extends TestCase
{
    private ChainCommandRegistry $registry;

    public function setUp(): void
    {
        $this->registry = new ChainCommandRegistry();
    }

    public function testShouldAddCommandToChain(): void
    {
        $command = new class extends Command implements ChainCommandInterface {
            use ChainCommandTrait;
        };
        $command->setName('bar:hi');

        $this->registry->addCommandToChain($command, 'foo:hello');

        $this->assertCount(1, $this->registry->getCommandChain('foo:hello'));
        $this->assertTrue($this->registry->hasChain('foo:hello'));
        $this->assertInstanceOf(ChainCommandInterface::class, $this->registry->getCommandChain('foo:hello')[0]);
    }

    public function testShouldThrowExceptionOnAddingBaseCommand(): void
    {
        $command = new class extends Command {};
        $command->setName('bar:hi');

        $this->expectException(\TypeError::class);

        $this->registry->addCommandToChain($command, 'foo:hello');
    }
}