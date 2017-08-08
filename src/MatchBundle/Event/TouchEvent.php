<?php

namespace MatchBundle\Event;

use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class TouchEvent
 * @package MatchBundle\Event
 */
class TouchEvent extends Event implements GameEventInterface
{
    // Type of touch
    const TOUCH = 0;
    const DISCOVERY = 1;
    const DIRECTION = 2;
    const SINK = 3;
    const FATAL = 4;

    protected $game;
    protected $shooter;
    protected $victim;
    protected $type;
    protected $boat = [];

    /**
     * TouchEvent constructor.
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
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
     * Setter type
     * @param integer $type
     *
     * @return TouchEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get Type
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter shooter
     * @param Player|null $shooter
     *
     * @return TouchEvent
     */
    public function setShooter(Player $shooter = null)
    {
        $this->shooter = $shooter;

        return $this;
    }

    /**
     * Get Shooter
     * @return Player|null
     */
    public function getShooter()
    {
        return $this->shooter;
    }

    /**
     * Setter victim
     * @param Player $victim
     *
     * @return TouchEvent
     */
    public function setVictim(Player $victim)
    {
        $this->victim = $victim;

        return $this;
    }

    /**
     * Get Victim
     * @return Player
     */
    public function getVictim()
    {
        return $this->victim;
    }

    /**
     * Get Boat
     * @return array
     */
    public function getBoat()
    {
        return $this->boat;
    }

    /**
     * Set Boat
     * @param array $boat
     *
     * @return $this
     */
    public function setBoat(array $boat)
    {
        $this->boat = $boat;

        return $this;
    }
}
