<?php

namespace MatchBundle\Event;

use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PenaltyEvent
 * @package MatchBundle\Event
 */
class PenaltyEvent extends Event implements GameEventInterface
{
    private $game;
    private $player;
    private $victim;

    /**
     * PenaltyEvent constructor.
     * @param Game   $game
     * @param Player $player
     */
    public function __construct(Game $game, Player $player)
    {
        $this->game = $game;
        $this->player = $player;
        $this->victim = $player;
    }

    /**
     * Set victim
     * @param Player $player
     *
     * @return $this
     */
    public function setVictim(Player $player)
    {
        $this->victim = $player;

        return $this;
    }

    /**
     * Get the game
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Get the player trigger the penalty
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get the player get the penalty
     * @return Player
     */
    public function getVictim()
    {
        return $this->victim;
    }
}
