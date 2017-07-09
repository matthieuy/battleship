<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use MatchBundle\Entity\Player;

/**
 * Class AbstractBonus
 * @package BonusBundle\Bonus
 */
abstract class AbstractBonus implements BonusInterface
{
    protected $remove = false;

    /**
     * Remove the bonus ?
     * @return boolean
     */
    public function isRemove()
    {
        return $this->remove;
    }

    /**
     * Set the new probability after catch the bonus
     * @param Player $player
     */
    public function setProbabilityAfterCatch(Player $player)
    {
        $player->setProbability(BonusConstant::INITIAL_PROBABILITY);
    }
}
