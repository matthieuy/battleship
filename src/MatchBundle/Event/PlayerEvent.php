<?php

namespace MatchBundle\Event;

use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PlayerEvent
 * @package MatchBundle\Event
 */
class PlayerEvent extends Event implements GameEventInterface
{
    private $game;
    private $player;

    /**
     * PlayerEvent constructor.
     * @param Game   $game
     * @param Player $player
     */
    public function __construct(Game $game, Player $player)
    {
        $this->game = $game;
        $this->player = $player;
    }

    /**
     * Get game
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Get Player
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }
}
