<?php

namespace AppBundle\DependencyInjection\Compiler;

use AppBundle\Manager\EventManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterSubscriberPass
 * @package AppBundle\DependencyInjection\Compiler
 */
class RegisterSubscriberPass implements CompilerPassInterface
{
    /**
     * Add subscriber to the
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('event.manager');

        // Tag kernel.event_subscriber
        $taggedServices = $container->findTaggedServiceIds('kernel.event_subscriber');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addSubscriber', [new Reference($id)]);
        }

        $container->register('event_dispatcher', EventManager::class);
    }
}
