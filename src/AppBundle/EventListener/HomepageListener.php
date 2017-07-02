<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use MatchBundle\Event\GameEvent;
use MatchBundle\MatchEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class HomepageListener
 *
 * @package AppBundle\EventListener
 */
class HomepageListener implements EventSubscriberInterface
{
    private $entityManager;
    private $pusher;

    /**
     * HomepageListener constructor.
     * @param EntityManager   $entityManager
     * @param PusherInterface $pusher
     */
    public function __construct(EntityManager $entityManager, PusherInterface $pusher)
    {
        $this->pusher = $pusher;
        $this->entityManager = $entityManager;
    }

    /**
     * Get events to listener
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            MatchEvents::CREATE => 'onUpdate',
            MatchEvents::FINISH => 'onUpdate',
            MatchEvents::LAUNCH => 'onUpdate',
            MatchEvents::CHANGE_TOUR => 'onUpdate',
        ];
    }

    /**
     * When game is modify
     * @param GameEvent $event
     */
    public function onUpdate(GameEvent $event)
    {
        $this->sendGameList();
    }

    /**
     * Send the gamelist to homepage
     */
    private function sendGameList()
    {
        $list = $this->entityManager->getRepository('MatchBundle:Game')->getList();
        $this->pusher->push(['games' => $list], 'homepage.topic');
    }
}
