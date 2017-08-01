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
     * Get the unique name of the bonus
     * @return string
     */
    public function getName()
    {
        return 'bonus.'.$this->getId();
    }

    /**
     * Get the bonus description
     * @return string
     */
    public function getDescription()
    {
        return $this->getName().'.desc';
    }

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
