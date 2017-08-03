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
    const WHEN_USE = 'use'; // When use the bonus
    const WHEN_BEFORE_TOUR = 'new_tour'; // On new tour
    const WHEN_BEFORE_SCORE = 'score'; // Before add score to player
    public static $triggerList = [
        self::WHEN_USE => 'onUse',
        self::WHEN_BEFORE_TOUR => 'onBeforeTour',
        self::WHEN_BEFORE_SCORE => 'onBeforeScore',
    ];
}
