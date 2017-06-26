<?php

namespace BonusBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class WeaponCompilerPass
 *
 * @package BonusBundle\DependencyInjection\Compiler
 */
class WeaponCompilerPass implements CompilerPassInterface
{
    /**
     * Add weapon to the registry
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // Add all services tagged "weapon.type" to the registry
        $definition = $container->getDefinition('weapon.registry');
        $taggedServices = $container->findTaggedServiceIds('weapon.type');

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addWeapon', [new Reference($id)]);
        }
    }
}
