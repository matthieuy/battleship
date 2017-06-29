<?php

namespace BonusBundle\Weapons;

/**
 * Class GustWeapon
 * @package BonusBundle\Weapons
 */
class GustWeapon extends AbstractWeapon
{
    /**
     * Get the matrix of weapon shoot
     * @return array
     */
    public function getGridArray()
    {
        return [[1], [1], [1], [1], [1]];
    }

    /**
     * Get the name
     * @return string
     */
    public function getName()
    {
        return 'weapon.gust';
    }

    /**
     * Get the price
     * @return integer
     */
    public function getPrice()
    {
        return 80;
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
