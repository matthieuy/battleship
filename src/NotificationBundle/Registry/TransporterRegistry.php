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
    private $transporters;

    /**
     * TransporterRegistry constructor.
     */
    public function __construct()
    {
        $this->transporters = [];
    }

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
}
