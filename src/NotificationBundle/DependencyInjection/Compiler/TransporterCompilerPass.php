<?php

namespace NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TransporterCompilerPass
 * @package NotificationBundle\DependencyInjection\Compiler
 */
class TransporterCompilerPass implements CompilerPassInterface
{
    /**
     * Add transporters to registry
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('notification.transporter.registry');

        $taggedServices = $container->findTaggedServiceIds('notification.transporter');

        foreach ($taggedServices as $id => $attributes) {
            $registry->addMethodCall('addTransporter', [new Reference($id)]);
        }
    }
}
