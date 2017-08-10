<?php

namespace NotificationBundle\Transporter\SMS;

/**
 * Interface SMSTypeInterface
 * @package NotificationBundle\Transporter\SMS
 */
interface SMSTypeInterface
{
    /**
     * Get SMS text
     * @return string
     */
    public function getSMSText();
}
