<?php

namespace MatchBundle;

/**
 * Class MatchEvents
 * @package MatchBundle
 */
final class MatchEvents
{
    /**
     * When a new game is create
     * Instance of MatchBundle\Event\GameEvent
     */
    const CREATE = 'match.create';

    /**
     * When a new game is delete
     * Instance of MatchBundle\Event\GameEvent
     */
    const DELETE = 'match.delete';

    /**
     * When the game is launch
     * Instance of MatchBundle\Event\GameEvent
     */
    const LAUNCH = 'match.launch';

    /**
     * When the game is over
     * Instance of MatchBundle\Event\GameEvent
     */
    const FINISH = 'match.finish';

    /**
     * When a player shoot another
     * Instance of MatchBundle\Event\TouchEvent
     */
    const TOUCH = 'match.touch';

    /**
     * When the game do a complete tour of player
     * Instance of MatchBundle\Event\GameEvent
     */
    const NEW_TOUR = 'match.new_tour';

    /**
     * When it is the next team to play
     * Instance of MatchBundle\Event\GameEvent
     */
    const NEXT_TOUR = 'match.next_tour';

    /**
     * When the tour is modify
     * Instance of MatchBundle\Event\GameEvent
     */
    const CHANGE_TOUR = 'match.change_tour';

    /**
     * When a player get a penalty
     * Instance of MatchBundle\Event\PenaltyEvent
     */
    const PENALTY = 'match.penalty';

    /**
     * When a player use a weapon
     * Instance of MatchBundle\Event\WeaponEvent
     */
    const WEAPON = 'match.weapon';

    /**
     * When a player shoot
     * Instance of MatchBundle\Event\PlayerEvent
     */
    const SHOOT = 'match.shoot';
}
