<?php

namespace BonusBundle\EventListener;

use BonusBundle\Entity\Inventory;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;

/**
 * Class InventoryEntityListener
 * @package BonusBundle\EventListener
 */
class InventoryEntityListener
{
    private $pusher;

    /**
     * InventoryEntityListener constructor.
     * @param PusherInterface $pusher
     */
    public function __construct(PusherInterface $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * After save
     * @param Inventory $inventory
     */
    public function postPersist(Inventory $inventory)
    {
        $this->send($inventory);
    }

    /**
     * After remove
     * @param Inventory $inventory
     */
    public function postRemove(Inventory $inventory)
    {
        $this->send($inventory);
    }

    /**
     * Send
     * @param Inventory $inventory
     */
    private function send(Inventory $inventory)
    {
        // Create data array to send
        $player = $inventory->getPlayer();
        $data = [
            $player->getName() => [
                'bonus' => [$player->getPosition() => $player->getNbBonus()],
            ],
        ];

        // Send
        $this->pusher->push($data, 'game.bonus.topic', ['slug' => $inventory->getGame()->getSlug()]);
    }
}
