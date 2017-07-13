<?php

namespace MatchBundle\Event;

use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PenaltyEvent
 * @package MatchBundle\Event
 */
class PenaltyEvent extends Event
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
}
