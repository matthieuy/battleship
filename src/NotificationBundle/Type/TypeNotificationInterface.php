<?php

namespace NotificationBundle\Type;

use MatchBundle\Event\GameEventInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Interface TypeNotificationInterface
 * @package NotificationBundle\Type
 */
interface TypeNotificationInterface
{
    /**
     * TypeNotificationInterface constructor.
     * @param TranslatorInterface $translator
     * @param GameEventInterface  $event
     */
    public function __construct(TranslatorInterface $translator, GameEventInterface $event);

    /**
     * Get type name
     * @return string
     */
    public function getName();

    /**
     * Get type of transporters denied
     * @return array
     */
    public function getDeniedTransporters();
}
