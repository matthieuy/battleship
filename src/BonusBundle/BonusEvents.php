<?php

namespace BonusBundle;

/**
 * Class BonusEvents
 * @package BonusBundle
 */
final class BonusEvents
{
    /**
     * When a player catch a bonus
     * Instance of BonusBundle\Event\BonusEvent
     */
    const CATCH_ONE = "bonus.catch";

    /**
     * When a player use a bonus
     * Instance of BonusBundle\Event\BonusEvent
     */
    const USE_IT = "bonus.use";
}
