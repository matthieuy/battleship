<?php

namespace BonusBundle\Manager;

use BonusBundle\Bonus\BonusInterface;

/**
 * Class BonusRegistry
 * @package BonusBundle\Manager
 */
class BonusRegistry
{
    /**
     * @var BonusInterface[]
     */
    protected $bonusList;

    /**
     * BonusRegistry constructor.
     */
    public function __construct()
    {
        $this->bonusList = [];
    }

    /**
     * Add bonus to the registry
     * @param BonusInterface $bonus
     *
     * @return $this
     */
    public function addBonus(BonusInterface $bonus)
    {
        $this->bonusList[$bonus->getName()] = $bonus;

        return $this;
    }
}
