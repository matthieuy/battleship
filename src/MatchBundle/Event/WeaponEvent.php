<?php

namespace MatchBundle\Event;

use BonusBundle\Weapons\WeaponInterface;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class WeaponEvent
 * @package MatchBundle\Event
 */
class WeaponEvent extends Event
{
    private $game;
    private $player;
    private $weapon;

    /**
     * WeaponEvent constructor.
     * @param Game            $game
     * @param Player          $player
     * @param WeaponInterface $weapon
     */
    public function __construct(Game $game, Player $player, WeaponInterface $weapon)
    {
        $this->game = $game;
        $this->player = $player;
        $this->weapon = $weapon;
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
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get Weapon
     * @return WeaponInterface
     */
    public function getWeapon()
    {
        return $this->weapon;
    }
}
