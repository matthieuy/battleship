<?php

namespace NotificationBundle\EventListener;

use Doctrine\ORM\EntityManager;
use MatchBundle\Event\GameEvent;
use MatchBundle\Event\GameEventInterface;
use MatchBundle\MatchEvents;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Registry\TransporterRegistry;
use NotificationBundle\Type\TourTypeNotification;
use NotificationBundle\Type\TypeNotificationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class NotificationListener
 * @package NotificationBundle\EventListener
 */
class NotificationListener implements EventSubscriberInterface
{
    private $entityManager;
    private $registry;
    private $translator;

    /**
     * @var Notification[] $notifications
     */
    private $notifications;

    /**
     * NotificationListener constructor.
     * @param EntityManager       $entityManager
     * @param TransporterRegistry $registry
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $entityManager, TransporterRegistry $registry, TranslatorInterface $translator)
    {
        $this->registry = $registry;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            MatchEvents::NEW_TOUR => 'onNewTour',
        ];
    }

    /**
     * On new tour
     * @param GameEvent $event
     */
    public function onNewTour(GameEvent $event)
    {
        $this->notifications = $this->entityManager->getRepository('NotificationBundle:Notification')->getNotification($event->getGame());
        $players = $event->getGame()->getPlayersTour();
        $type = new TourTypeNotification($this->translator, $event);

        foreach ($players as $player) {
            if (!$player->isAi() && $player->isAlive()) {
                $this->sendNotifications($event, $type);
            }
        }
    }

    /**
     * Send notifications
     * @param GameEventInterface        $event
     * @param TypeNotificationInterface $typeNotification
     */
    private function sendNotifications(GameEventInterface $event, TypeNotificationInterface $typeNotification)
    {
        foreach ($this->notifications as $notification) {
            // Not allowed
            if (in_array($notification->getName(), $typeNotification->getDeniedTransporters())) {
                continue;
            }

            $transporter = $this->registry->get($notification->getName());
            $transporter->send($notification, $event, $typeNotification);
        }
    }
}
