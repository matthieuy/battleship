<?php

namespace MatchBundle\Event;

use MatchBundle\Entity\Game;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GameEvent
 * @package MatchBundle\Event
 */
class GameEvent extends Event
{
    private $game;

    /**
     * GameEvent constructor.
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
}
