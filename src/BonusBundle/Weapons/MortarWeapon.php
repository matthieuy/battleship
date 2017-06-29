<?php

namespace BonusBundle\Weapons;

/**
 * Class MortarWeapon
 * @package BonusBundle\Weapons
 */
class MortarWeapon extends AbstractWeapon
{
    /**
     * Get the matrix of weapon shoot
     * @return array
     */
    public function getGridArray()
    {
        return [
            [1, 0, 1],
            [0, 1, 0],
            [1, 0, 1],
        ];
    }

    /**
     * Get the name
     * @return string
     */
    public function getName()
    {
        return 'weapon.mortar';
    }

    /**
     * Get the price
     * @return integer
     */
    public function getPrice()
    {
        return 100;
    }

    /**
     * Can we rotate the weapon ?
     * @return boolean
     */
    public function canBeRotate()
    {
        return false;
    }

    /**
     * Can the shoot random ?
     * @return boolean
     */
    public function canBeShuffle()
    {
        return true;
    }

    /**
     * AI can use this weapon
     * @return boolean
     */
    public function canAiUseIt()
    {
        return true;
    }
}
