<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
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
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory
     * @param ReturnBox $returnBox
     * @param array     $options
     *
     * @return array|false Data to push to player
     */
    public function onUse(Game &$game, Player &$player, Inventory $inventory, ReturnBox $returnBox = null, array &$options = [])
    {
        // Get victim
        if ($player->isAi()) {
            $victim = $this->getTargetForAI($game, $player, $inventory->getOption('select'));
        } else {
            $victimPosition = $inventory->getOption('player');
            $victim = $game->getPlayerByPosition($victimPosition);
        }
        if (!$victim) {
            return false;
        }

        // Reset probability
        $victim->setProbability(0);
        $this->remove = true;

        return false;
    }
}
