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

    /**
     * Before change lap
     * Instance of BonusBundle\Event\BonusEvent
     * $options = teamList
     */
    const BEFORE_TOUR = "bonus.before_tour";

    /**
     * A touch : before scoring
     * Instance of BonusBundle\Event\BonusEvent
     * $options = ["points"]
     * $player can be null
     */
    const BEFORE_SCORE = "bonus.before_score";

    /**
     * After get boxes
     * Instance of BonusBundle\Event\BonusEvent
     * $options = boxlist
     */
    const GET_BOX = "bonus.get_box";
}
