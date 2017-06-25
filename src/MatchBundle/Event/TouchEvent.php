<?php

namespace MatchBundle\Event;

use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class TouchEvent
 * @package MatchBundle\Event
 */
class TouchEvent extends Event
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

    /**
     * TouchEvent constructor.
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
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
}
