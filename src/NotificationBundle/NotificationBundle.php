<?php

namespace NotificationBundle;

use NotificationBundle\DependencyInjection\Compiler\TransporterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NotificationBundle
 * @package NotificationBundle
 */
class NotificationBundle extends Bundle
{
    /**
     * Build bundle
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TransporterCompilerPass());
    }
}
