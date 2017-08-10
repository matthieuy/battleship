<?php

namespace NotificationBundle\Transporter\Mail;

use NotificationBundle\Type\TypeNotificationInterface;

/**
 * Interface MailTypeInterface
 * @package NotificationBundle\Transporter\Mail
 */
interface MailTypeInterface extends TypeNotificationInterface
{
    /**
     * Get mail subject
     * @return string
     */
    public function getSubject();

    /**
     * Get mail text
     * @return string
     */
    public function getTextMail();
}
