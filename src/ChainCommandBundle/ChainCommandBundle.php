<?php

namespace App\ChainCommandBundle;

use App\ChainCommandBundle\DependencyInjection\ChainCommandExtension;
use App\ChainCommandBundle\DependencyInjection\Compiler\ChainCommandPass;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChainCommandBundle extends Bundle
{
    /**
     * @return ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ChainCommandExtension();
    }

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ChainCommandPass());
    }
}
