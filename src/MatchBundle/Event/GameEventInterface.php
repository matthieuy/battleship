<?php

namespace MatchBundle\Event;

use MatchBundle\Entity\Game;

/**
 * Interface GameEventInterface
 * @package MatchBundle\Event
 */
interface GameEventInterface
{
    /**
     * Get game
     * @return Game
     */
    public function getGame();
}
