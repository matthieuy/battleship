<?php

namespace NotificationBundle\Type;

use MatchBundle\Event\GameEventInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractTypeNotification
 * @package NotificationBundle\Type
 */
abstract class AbstractTypeNotification implements TypeNotificationInterface
{
    protected $translator;
    protected $event;

    /**
     * AbstractTypeNotification constructor.
     * @param TranslatorInterface $translator
     * @param GameEventInterface  $event
     */
    public function __construct(TranslatorInterface $translator, GameEventInterface $event)
    {
        $this->translator = $translator;
        $this->event = $event;
    }
}
