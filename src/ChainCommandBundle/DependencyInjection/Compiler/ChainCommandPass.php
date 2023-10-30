<?php

namespace App\ChainCommandBundle\DependencyInjection\Compiler;


use App\ChainCommandBundle\Interfaces\ChainCommandRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registering command chains.
 *
 * In order to add a command to the chain, mark it with a tag named "chain.command" with required attribute parent,
 * which contains the name of the command that the given command should subscribe to
 *
 * Some\Chain\Command:
 *  tags:
 *      ...
 *      - { name: 'chain.command', parent: 'foo:hello' }
 *
 */
class ChainCommandPass implements CompilerPassInterface
{
    const CHAIN_COMMAND_TAG = 'chain.command';

    /**
     * Adding chain command.
     *
     * Modifies the container and adds registered chain commands to the registry.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ChainCommandRegistryInterface::class)) {
            throw new \InvalidArgumentException(
                sprintf('%s not registered, check services.yaml file. ', ChainCommandRegistryInterface::class)
            );
        }

        $definition     = $container->findDefinition(ChainCommandRegistryInterface::class);
        $taggedServices = $container->findTaggedServiceIds(self::CHAIN_COMMAND_TAG);

        foreach ($taggedServices as $id => $tags) {
            $parent = null;
            foreach ($tags as $attributes) {
                if (array_key_exists('parent', $attributes)) {
                    $parent = $attributes['parent'];
                }
            }

            if (empty($parent)) {
                throw new \InvalidArgumentException(
                    sprintf('Parent attribute not registered for %s service, check services.yaml file.', $id)
                );
            }

            $definition->addMethodCall('addCommandToChain', [new Reference($id), $parent]);
        }
    }

}