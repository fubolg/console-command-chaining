<?php

namespace App\ChainCommandBundle\Tests\Unit\EventListener;

use App\ChainCommandBundle\Contract\ChainCommandInterface;
use App\ChainCommandBundle\EventListener\ConsoleSubscriber;
use App\ChainCommandBundle\Exceptions\CantBeExecutedException;
use App\ChainCommandBundle\Interfaces\ChainCommandRegistryInterface;
use App\ChainCommandBundle\Traits\ChainCommandTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleEvent;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\ConsoleOutput;
use function PHPUnit\Framework\once;

class ConsoleSubscriberTest extends TestCase
{
    private ConsoleSubscriber $subscriber;

    private MockObject|ConsoleEvent $event;

    private ChainCommandRegistryInterface $registry;

    public function setUp(): void
    {
        $this->event = $this->getMockBuilder(ConsoleEvent::class)->disableOriginalConstructor()->getMock();
        $this->registry = $this->getMockBuilder(ChainCommandRegistryInterface::class)->getMockForAbstractClass();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMockForAbstractClass();

        $this->subscriber = new ConsoleSubscriber($this->registry);
        $this->subscriber->setLogger($this->logger);
    }

    public function testShouldThrowExceptionOnChainCommand(): void
    {
        $command = new class extends Command implements ChainCommandInterface {
            use ChainCommandTrait;
        };
        $command->setName('bar:hi');
        $command->setParent('foo:hello');

        $this->event->expects(self::once())->method('getCommand')->willReturn($command);
        $this->event->expects(self::once())->method('stopPropagation');

        $this->expectException(CantBeExecutedException::class);
        $this->expectExceptionMessage('Error: bar:hi command is a member of foo:hello command chain and cannot be executed on its own.');

        $this->subscriber->onConsoleCommand($this->event);
    }

    public function testOnChainCommand(): void
    {
        $command = new Command();
        $command->setName('foo:hello');

        $chainCommand = new class extends Command implements ChainCommandInterface {
            use ChainCommandTrait;
        };
        $chainCommand->setName('bar:hi');
        $chainCommand->setParent('foo:hello');

        $this->event->expects(self::once())->method('getCommand')->willReturn($command);
        $this->event->expects(self::never())->method('stopPropagation');

        $this->registry->expects(self::once())->method('hasChain')->with('foo:hello')->willReturn(true);
        $this->registry->expects(self::once())->method('getCommandChain')->with('foo:hello')->willReturn([$chainCommand]);

        $this->logger->expects(self::exactly(3))->method('info');

        $this->subscriber->onConsoleCommand($this->event);
    }

    public function testShouldDoNothingOnCommand(): void
    {
        $command = new class extends Command {};
        $command->setName('foo:hello');

        $this->event->expects(self::once())->method('getCommand')->willReturn($command);
        $this->event->expects(self::never())->method('stopPropagation');

        $this->registry->expects(self::once())->method('hasChain')->with('foo:hello')->willReturn(false);
        $this->registry->expects(self::never())->method('getCommandChain');

        $this->logger->expects(self::never())->method('info');

        $this->subscriber->onConsoleCommand($this->event);
    }

    public function testShouldRunChainOnCommandTerminate(): void
    {
        $command = new Command();
        $command->setName('foo:hello');
        $chainCommand = $this->getMockBuilder(Command::class)->disableOriginalConstructor()->getMock();

        $this->event->expects(self::once())->method('getCommand')->willReturn($command);
        $this->event->expects(self::once())->method('getOutput')->willReturn((new ConsoleOutput()));

        $this->registry->expects(self::once())->method('hasChain')->with('foo:hello')->willReturn(true);
        $this->registry->expects(self::once())->method('getCommandChain')->with('foo:hello')->willReturn([$chainCommand]);

        $chainCommand->expects(self::once())->method('run');

        $this->logger->expects(self::exactly(2))->method('info');

        $this->subscriber->onConsoleTerminate($this->event);
    }

    public function testShouldDoNothingOnTerminate(): void
    {
        $command = new Command();
        $command->setName('foo:hello');

        $this->event->expects(self::once())->method('getCommand')->willReturn($command);

        $this->registry->expects(self::once())->method('hasChain')->with('foo:hello')->willReturn(false);
        $this->registry->expects(self::never())->method('getCommandChain');

        $this->logger->expects(self::never())->method('info');

        $this->subscriber->onConsoleCommand($this->event);
    }
}