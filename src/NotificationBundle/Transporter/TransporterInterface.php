<?php

namespace NotificationBundle\Transporter;

use MatchBundle\Event\GameEventInterface;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Type\TypeNotificationInterface;
use Symfony\Component\Form\FormBuilderInterface;

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

    /**
     * Send notification
     * @param Notification              $notification
     * @param GameEventInterface        $event
     * @param TypeNotificationInterface $type
     *
     * @return bool
     */
    public function send(Notification $notification, GameEventInterface $event, TypeNotificationInterface $type);

    /**
     * Get form fields (on setting page)
     * @param FormBuilderInterface $builder
     * @param Notification         $notification
     * @param array                $options
     *
     * @return FormBuilderInterface[]
     */
    public function getFormFields(FormBuilderInterface $builder, Notification &$notification, array $options);
}
