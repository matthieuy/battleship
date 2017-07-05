<?php

namespace BonusBundle\Bonus;

/**
 * Class PointBonus
 * @package BonusBundle\Bonus
 */
class PointBonus extends AbstractBonus
{
    // Min and Max point to get
    const MIN_POINTS = 10;
    const MAX_POINTS = 40;

    /**
     * Get the unique name of the bonus
     * @return string
     */
    public function getName()
    {
        return 'bonus.point';
    }

    /**
     * Get the bonus description
     * @return string
     */
    public function getDescription()
    {
        return 'Add points';
    }
}
