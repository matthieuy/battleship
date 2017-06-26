<?php

namespace BonusBundle\Manager;

use BonusBundle\Weapons\WeaponInterface;

/**
 * Class WeaponRegistry
 * @package BonusBundle\Manager
 */
class WeaponRegistry
{
    /**
     * @var WeaponInterface[]
     */
    protected $weapons;

    /**
     * WeaponRegistry constructor.
     */
    public function __construct()
    {
        $this->weapons = [];
    }

    /**
     * Add a weapon to the registry
     * @param WeaponInterface $weapon
     *
     * @return $this
     */
    public function addWeapon(WeaponInterface $weapon)
    {
        $this->weapons[$weapon->getName()] = $weapon;

        return $this;
    }

    /**
     * Get all weapon
     * @return WeaponInterface[]
     */
    public function getAllWeapons()
    {
        return $this->weapons;
    }

    /**
     * Get a weapon
     * @param string $name Name of the weapon
     *
     * @return WeaponInterface
     * @throws \Exception
     */
    public function getWeapon($name)
    {
        if (!isset($this->weapons[$name])) {
            throw new \Exception("This ammo don't exist !");
        }

        return $this->weapons[$name];
    }
}
