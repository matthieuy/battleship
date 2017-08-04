<?php

namespace BonusBundle\Event;

use BonusBundle\Bonus\BonusInterface;
use BonusBundle\Entity\Inventory;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BonusEvent
 * @package BonusBundle\Event
 */
class BonusEvent extends Event
{
    private $game;
    private $player;
    private $returnBox;
    private $options;
    private $bonus;
    private $inventory;

    /**
     * BonusEvent constructor.
     * @param Game           $game
     * @param Player|null    $player
     * @param ReturnBox|null $returnBox
     * @param array          $options
     */
    public function __construct(Game $game, Player $player = null, ReturnBox $returnBox = null, array $options = [])
    {
        $this->game = $game;
        $this->player = $player;
        $this->returnBox = $returnBox;
        $this->options = $options;
    }

    /**
     * Get Bonus
     * @return BonusInterface|null
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * Set Bonus
     * @param BonusInterface $bonus
     *
     * @return $this
     */
    public function setBonus(BonusInterface $bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get Inventory
     * @return Inventory|null
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * Set Inventory
     * @param Inventory $inventory
     *
     * @return $this
     */
    public function setInventory(Inventory $inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get Game
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Get Player
     * @return Player|null
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get ReturnBox
     * @return ReturnBox|null
     */
    public function getReturnBox()
    {
        return $this->returnBox;
    }

    /**
     * Set Options
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get Options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
