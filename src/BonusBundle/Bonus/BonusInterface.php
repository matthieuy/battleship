<?php

namespace BonusBundle\Bonus;

use MatchBundle\Entity\Player;

/**
 * Interface BonusInterface
 * @package BonusBundle\Bonus
 */
interface BonusInterface
{
    /**
     * Get the unique name of the bonus
     * @return string
     */
    public function getName();

    /**
     * Get the bonus description
     * @return string
     */
    public function getDescription();

    /**
     * Get the probability to catch this bonus
     * @return integer
     */
    public function getProbabilityToCatch();

    /**
     * Get options to add in inventory
     * @return array
     */
    public function getOptions();

    /**
     * Set the new probability after catch the bonus
     * @param Player $player
     */
    public function setProbabilityAfterCatch(Player $player);

    /**
     * Can the player get this bonus ?
     * @param Player $player
     *
     * @return boolean
     */
    public function canWeGetIt(Player $player);
}
