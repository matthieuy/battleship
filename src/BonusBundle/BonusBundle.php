<?php

namespace BonusBundle;

use BonusBundle\DependencyInjection\Compiler\WeaponCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class BonusBundle
 * @package BonusBundle
 */
class BonusBundle extends Bundle
{
    /**
     * Build bundle : add compiler pass
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new WeaponCompilerPass());
    }
}
