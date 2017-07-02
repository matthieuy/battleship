<?php

namespace AppBundle\EventListener;

/**
 * Class ServerStartListener
 * @package AppBundle\EventListener
 */
class ServerStartListener
{
    private $sessionName;

    /**
     * ServerStartListener constructor (DI)
     * @param string $sessionName Session name
     */
    public function __construct($sessionName)
    {
        $this->sessionName = $sessionName;
    }

    /**
     * On server start : fix session name
     */
    public function onServerStart()
    {
        ini_set('session.name', $this->sessionName);
    }
}
