<?php

namespace BonusBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class BonusCompilerPass
 *
 * @package BonusBundle\DependencyInjection\Compiler
 */
class BonusCompilerPass implements CompilerPassInterface
{
    /**
     * Add bonus to the registry
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // Add all services tagged "bonus.type" to the registry
        $definition = $container->getDefinition('bonus.registry');
        $taggedServices = $container->findTaggedServiceIds('bonus.type');

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addBonus', [new Reference($id)]);
        }
    }
}
