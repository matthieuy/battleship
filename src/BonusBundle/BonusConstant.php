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
     * TRIGGER
     */
    const WHEN_CATCH = 0;
    const TRIGGER_LIST = [
        self::WHEN_CATCH => 'onCatch',
    ];
}
