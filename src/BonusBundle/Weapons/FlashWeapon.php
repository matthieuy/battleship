<?php

namespace BonusBundle\Weapons;

/**
 * Class FlashWeapon
 * @package BonusBundle\Weapons
 */
class FlashWeapon extends AbstractWeapon
{
    /**
     * Get the matrix of weapon shoot
     * @return array
     */
    public function getGridArray()
    {
        return [
            [0, 1],
            [1, 0],
            [0, 1],
            [1, 0],
            [0, 1],
        ];
    }

    /**
     * Get the name
     * @return string
     */
    public function getName()
    {
        return 'weapon.flash';
    }

    /**
     * Get the price
     * @return integer
     */
    public function getPrice()
    {
        return 120;
    }

    /**
     * Can we rotate the weapon ?
     * @return boolean
     */
    public function canBeRotate()
    {
        return true;
    }

    /**
     * Can the shoot random ?
     * @return boolean
     */
    public function canBeShuffle()
    {
        return false;
    }
}
