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
     * Get ShortMessage
     * @return string
     */
    public function getShortMessage();

    /**
     * Get no personal shortMessage
     * @return string
     */
    public function getGlobalShortMessage();

    /**
     * Get LongMessage
     * @return string
     */
    public function getLongMessage();

    /**
     * Get type of transporters denied
     * @return array
     */
    public function getDeniedTransporters();
}
