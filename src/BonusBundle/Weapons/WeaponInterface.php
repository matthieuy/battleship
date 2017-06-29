<?php

namespace BonusBundle\Weapons;

use MatchBundle\Box\Box;
use MatchBundle\Entity\Game;

/**
 * Interface WeaponInterface
 *
 * @package BonusBundle\Weapons
 */
interface WeaponInterface
{
    /**
     * Get the matrix of weapon shoot
     * @return array
     */
    public function getGridArray();

    /**
     * Get boxes to shoot
     * @param Game    $game   The game
     * @param integer $x      X position
     * @param integer $y      Y position
     * @param integer $rotate Number of 90degrees rotations
     *
     * @return Box[]
     */
    public function getBoxes(Game $game, $x, $y, $rotate = 0);

    /**
     * Get the name
     * @return string
     */
    public function getName();

    /**
     * Get the price
     * @return integer
     */
    public function getPrice();

    /**
     * Can a AI use this weapon ?
     * @return boolean
     */
    public function canAiUseIt();

    /**
     * Can we rotate the weapon ?
     * @return boolean
     */
    public function canBeRotate();

    /**
     * Can the shoot random ?
     * @return boolean
     */
    public function canBeShuffle();

    /**
     * Convert to array
     * @return array
     */
    public function toArray();
}
