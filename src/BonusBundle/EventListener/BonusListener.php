<?php

namespace BonusBundle\EventListener;

use BonusBundle\BonusEvents;
use BonusBundle\Event\BonusEvent;
use BonusBundle\Manager\BonusRegistry;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BonusListener
 * @package BonusBundle\EventDispatcher
 */
class BonusListener implements EventSubscriberInterface
{
    private $entityManager;
    private $bonusRegistry;
    private $pusher;
    private $logger;

    /**
     * BonusListener constructor.
     * @param EntityManager   $entityManager
     * @param BonusRegistry   $bonusRegistry
     * @param PusherInterface $pusher
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $entityManager, BonusRegistry $bonusRegistry, PusherInterface $pusher, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->bonusRegistry = $bonusRegistry;
        $this->pusher = $pusher;
        $this->logger = $logger;
    }

    /**
     * Get event listener
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            BonusEvents::USE_IT => 'onUse',
            BonusEvents::BEFORE_TOUR => 'dispatch',
            BonusEvents::BEFORE_SCORE => 'dispatch',
            BonusEvents::GET_BOX => 'dispatch',
        ];
    }

    /**
     * Get the method name to call
     * @param string $eventName
     *
     * @return string
     */
    public function getMethodName($eventName)
    {
        $methods = [
            BonusEvents::USE_IT => 'onUse',
            BonusEvents::BEFORE_TOUR => 'onBeforeTour',
            BonusEvents::BEFORE_SCORE => 'onBeforeScore',
            BonusEvents::GET_BOX => 'onGetBoxes',
        ];

        return $methods[$eventName];
    }

    /**
     * On catch bonus : save inventory
     * @param BonusEvent $event
     */
    public function onCatch(BonusEvent $event)
    {
        $this->entityManager->persist($event->getInventory());
        $this->entityManager->flush();
    }

    /**
     * On use : set inUse and trigger
     * @param BonusEvent $event
     * @param string     $eventName
     */
    public function onUse(BonusEvent $event, $eventName)
    {
        $event->getInventory()->setUse();
        $this->triggerEvent($event, $eventName);
        $this->entityManager->flush();
    }

    /**
     * Trigger all others methods after add bonus+inventory in event
     * @param BonusEvent $event
     * @param string     $eventName
     */
    public function dispatch(BonusEvent $event, $eventName)
    {
        $listInventory = $this->entityManager->getRepository('BonusBundle:Inventory')->getActiveBonus($event->getGame());
        foreach ($listInventory as $inventory) {
            $bonus = $this->bonusRegistry->getBonusById($inventory->getName());
            $event
                ->setBonus($bonus)
                ->setInventory($inventory);

            $this->triggerEvent($event, $eventName);
        }
    }

    /**
     * Trigger one method
     * @param BonusEvent $event
     * @param string     $eventName
     *
     * @return bool Trigger or false
     */
    private function triggerEvent(BonusEvent $event, $eventName)
    {
        $methodName = $this->getMethodName($eventName);
        if (method_exists($event->getBonus(), $methodName)) {
            // Call method
            call_user_func_array([$event->getBonus(), $methodName], [$event]);
            $this->logger->info($event->getGame()->getSlug().' - Bonus', [
                'action' => 'trigger',
                'method' => $methodName,
                'bonus' => $event->getBonus()->getName(),
                'player' => $event->getPlayer()->getName(),
                'inventory_id' => $event->getInventory()->getId(),
            ]);

            // WebSocket
            $result = $event->getBonus()->getWSReturn();
            if ($result !== false) {
                $this->pusher->push($result, 'game.bonus.topic', ['slug' => $event->getGame()->getSlug()]);
            }

            // Remove bonus from inventory
            if ($event->getBonus()->isDelete()) {
                $playerInventory = $event->getInventory()->getPlayer();
                $playerInventory->removeBonus($event->getInventory());
                $this->entityManager->remove($event->getInventory());
                $this->logger->info($event->getGame()->getSlug().' - Bonus', [
                    'action' => 'remove',
                    'method' => $methodName,
                    'bonus' => $event->getBonus()->getName(),
                    'inventory_id' => $event->getInventory()->getId(),
                ]);
            }

            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
