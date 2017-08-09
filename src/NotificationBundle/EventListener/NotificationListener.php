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
        $notifications = $this->entityManager->getRepository('NotificationBundle:Notification')->getNotification($event->getGame());
        $players = $event->getGame()->getPlayersTour();
        $type = new TourTypeNotification($this->translator, $event);

        foreach ($notifications as $notification) {
            // Not allowed
            if (in_array($notification->getName(), $type->getDeniedTransporters())) {
                continue;
            }

            // Get transporter
            $transporter = $this->registry->get($notification->getName());
            if ($transporter->isPersonal()) {
                foreach ($players as $player) {
                    if (!$player->isAi() && $player->isAlive() && $player->getUser()->getId() === $notification->getUser()->getId()) {
                        $transporter->send($notification, $event, $type);
                    }
                }
            } else {
                $transporter->send($notification, $event, $type);
            }
        }
    }
}
