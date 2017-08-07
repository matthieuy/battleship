<?php

namespace NotificationBundle\Transporter;

/**
 * Interface TransporterInterface
 * @package NotificationBundle\Transporter
 */
interface TransporterInterface
{
    /**
     * Get the transporter name
     * @return string
     */
    public function getName();
}
