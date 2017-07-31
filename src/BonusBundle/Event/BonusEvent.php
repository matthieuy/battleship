<?php

namespace BonusBundle\Event;

use BonusBundle\Bonus\BonusInterface;
use BonusBundle\Entity\Inventory;
use MatchBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BonusEvent
 * @package BonusBundle\Event
 */
class BonusEvent extends Event
{
    private $player;
    private $bonus;
    private $inventory;

    /**
     * BonusEvent constructor
     * @param Player         $player
     * @param BonusInterface $bonus
     * @param Inventory      $inventory
     */
    public function __construct(Player $player, BonusInterface $bonus, Inventory $inventory)
    {
        $this->player = $player;
        $this->bonus = $bonus;
        $this->inventory = $inventory;
    }

    /**
     * Get the player
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get the bonus
     * @return BonusInterface
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * Get the inventory
     * @return Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }
}
