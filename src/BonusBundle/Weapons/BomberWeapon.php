<?php

namespace BonusBundle\Weapons;

/**
 * Class BomberWeapon
 * @package BonusBundle\Weapons
 */
class BomberWeapon extends AbstractWeapon
{
    /**
     * Get the matrix of weapon shoot
     * @return array
     */
    public function getGridArray()
    {
        return [
            [0, 1, 0],
            [1, 0, 1],
            [0, 1, 0],
        ];
    }

    /**
     * Get the name
     * @return string
     */
    public function getName()
    {
        return 'weapon.bomber';
    }

    /**
     * Get the price
     * @return integer
     */
    public function getPrice()
    {
        return 60;
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
     * Can a AI use this weapon ?
     * @return boolean
     */
    public function canAiUseIt()
    {
        return true;
    }
}
