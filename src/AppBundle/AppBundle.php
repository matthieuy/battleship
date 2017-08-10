<?php

namespace AppBundle;

use AppBundle\DependencyInjection\Compiler\RegisterSubscriberPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AppBundle
 *
 * @package AppBundle
 */
class AppBundle extends Bundle
{
    /**
     * Build bundle
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterSubscriberPass());
    }
}
