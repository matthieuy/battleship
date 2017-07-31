<?php

namespace BonusBundle;

/**
 * Class BonusConstant
 * @package BonusBundle
 */
class BonusConstant
{
    /**
     * Percent of probability to catch a bonus
     */
    const INITIAL_PROBABILITY = 10;

    /**
     * Inventory size
     */
    const INVENTORY_SIZE = 9;

    /**
     * Target selector
     */
    const TARGET_ALL = 'all';
    const TARGET_FRIENDS = 'friends';
    const TARGET_ENEMY = 'enemy';

    /**
     * TRIGGER
     */
    const WHEN_USE = 'use';
    public static $triggerList = [
        self::WHEN_USE => 'onUse',
    ];
}
