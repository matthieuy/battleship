<?php

namespace UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class UserBundle
 *
 * @package UserBundle
 */
class UserBundle extends Bundle
{
    /**
     * Get the bundle parent name
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
