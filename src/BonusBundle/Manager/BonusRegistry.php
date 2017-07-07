<?php

namespace BonusBundle\Manager;

use BonusBundle\Bonus\BonusInterface;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Player;

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

    /**
     * Update the probability to catch a bonus
     * @param Player    $player
     * @param ReturnBox $returnBox
     */
    public function updateProbability(Player &$player, ReturnBox $returnBox)
    {
        // Calculate increment with life
        if ($player->getLife() >= 20) {
            $increment = 3;
        } elseif ($player->getLife() >= 10) {
            $increment = 4;
        } else {
            $increment = 5;
        }

        if ($returnBox->getWeapon() == null) {
            // No weapon : add the standard increment
            $player->addProbability($increment);
        } else {
            // Weapon : calculate with weapon price
            $proba = $returnBox->getWeapon()->getPrice() / 10 + $increment;
            $player->addProbability($proba);
        }
    }
}
