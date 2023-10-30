<?php

namespace App\ChainCommandBundle\Tests\Unit\DependencyInjection\Compiler;

use App\ChainCommandBundle\Contract\ChainCommandInterface;
use App\ChainCommandBundle\DependencyInjection\Compiler\ChainCommandPass;
use App\ChainCommandBundle\Interfaces\ChainCommandRegistryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ChainCommandPassTest extends TestCase
{
    /**
     * @var CompilerPassInterface
     */
    protected CompilerPassInterface $pass;

    public function setUp(): void
    {
        $this->pass = new ChainCommandPass();
    }

    public function tearDown(): void
    {
        unset($this->pass);
    }

    public function testProcess()
    {
        $definition = $this->getMockBuilder(Definition::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var MockObject|ContainerBuilder $containerBuilder */
        $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $containerBuilder->expects(self::once())
            ->method('has')
            ->with(ChainCommandRegistryInterface::class)
            ->willReturn(true);

        $containerBuilder->expects(self::once())
            ->method('findDefinition')
            ->with(ChainCommandRegistryInterface::class)
            ->willReturn($definition);

        $services = [ChainCommandInterface::class => [['parent' => 'foo:hello']]];

        $containerBuilder->expects(self::once())
            ->method('findTaggedServiceIds')
            ->with(ChainCommandPass::CHAIN_COMMAND_TAG)
            ->willReturn($services);

        $definition->expects(self::once())
            ->method('addMethodCall')
            ->with('addCommandToChain', [new Reference(ChainCommandInterface::class), 'foo:hello']);

        $this->pass->process($containerBuilder);
    }

    public function testShouldThrowExceptionIfRegistryNotRegistered(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('App\ChainCommandBundle\Interfaces\ChainCommandRegistryInterface not registered, check services.yaml file.');

        $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $containerBuilder->expects(self::once())
            ->method('has')
            ->with(ChainCommandRegistryInterface::class)
            ->willReturn(false);

        $this->pass->process($containerBuilder);
    }

    public function testShouldThrowExceptionIfParentAttributeMiss(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parent attribute not registered for App\ChainCommandBundle\Contract\ChainCommandInterface service, check services.yaml');

        $definition = $this->getMockBuilder(Definition::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var MockObject|ContainerBuilder $containerBuilder */
        $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $containerBuilder->expects(self::once())
            ->method('has')
            ->with(ChainCommandRegistryInterface::class)
            ->willReturn(true);

        $containerBuilder->expects(self::once())
            ->method('findDefinition')
            ->with(ChainCommandRegistryInterface::class)
            ->willReturn($definition);

        $services = [ChainCommandInterface::class => [[]]];

        $containerBuilder->expects(self::once())
            ->method('findTaggedServiceIds')
            ->with(ChainCommandPass::CHAIN_COMMAND_TAG)
            ->willReturn($services);

        $definition->expects(self::never())
            ->method('addMethodCall');

        $this->pass->process($containerBuilder);
    }
}
