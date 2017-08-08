<?php

namespace NotificationBundle\Transporter;

use NotificationBundle\Entity\Notification;
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
     * Get form fields (on setting page)
     * @param FormBuilderInterface $builder
     * @param Notification         $notification
     * @param array                $options
     *
     * @return FormBuilderInterface[]
     */
    public function getFormFields(FormBuilderInterface $builder, Notification &$notification, array $options);
}
