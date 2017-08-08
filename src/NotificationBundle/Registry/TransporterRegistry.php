<?php

namespace NotificationBundle\Registry;

use NotificationBundle\Transporter\TransporterInterface;

/**
 * Class TransporterRegistry
 * @package NotificationBundle\Registry
 */
class TransporterRegistry
{
    /**
     * @var TransporterInterface[]
     */
    private $transporters = [];

    /**
     * Add a transporter
     * @param TransporterInterface $transporter
     *
     * @return $this
     */
    public function addTransporter(TransporterInterface $transporter)
    {
        $this->transporters[$transporter->getName()] = $transporter;

        return $this;
    }

    /**
     * Get all transporters
     * @return TransporterInterface[]
     */
    public function getAll()
    {
        return $this->transporters;
    }

    /**
     * Get a transporter
     * @param string $name
     *
     * @return TransporterInterface
     */
    public function get($name)
    {
        return $this->transporters[$name];
    }
}
