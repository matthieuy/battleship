<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class ResetProbBonus
 * @package BonusBundle\Bonus
 */
class ResetProbBonus extends AbstractBonus
{
    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'reset';
    }

    /**
     * Get the probability to catch this bonus
     * @return integer
     */
    public function getProbabilityToCatch()
    {
        return 15;
    }

    /**
     * Get options to add in inventory
     * @return array
     */
    public function getOptions()
    {
        return [
            'select' => BonusConstant::TARGET_ENEMY,
        ];
    }

    /**
     * All player
     * @param Player $player
     *
     * @return boolean
     */
    public function canWeGetIt(Player $player)
    {
        return true;
    }

    /**
     * Can the player use this bonus now ?
     * @param Game   $game
     * @param Player $player
     *
     * @return boolean
     */
    public function canUseNow(Game $game, Player $player = null)
    {
        return ($player !== null);
    }

    /**
     * onUse : Reset enemy proba
     * @param BonusEvent $event
     */
    public function onUse(BonusEvent $event)
    {
        // Get victim
        if ($event->getPlayer()->isAi()) {
            $victim = $this->getTargetForAI($event);
        } else {
            $victimPosition = $event->getInventory()->getOption('player');
            $victim = $event->getGame()->getPlayerByPosition($victimPosition);
        }

        // Reset probability
        if ($victim) {
            $victim->setProbability(0);
            $this->delete();
        }
    }
}
