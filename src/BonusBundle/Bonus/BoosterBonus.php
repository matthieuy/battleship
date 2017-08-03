<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class BoosterBonus
 * @package BonusBundle\Bonus
 */
class BoosterBonus extends AbstractBonus
{
    const MIN_PROBA = 2;
    const MAX_PROBA = 7;

    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'booster';
    }

    /**
     * Get the probability to catch this bonus
     * @return integer
     */
    public function getProbabilityToCatch()
    {
        return 40;
    }

    /**
     * Get options to add in inventory
     * @return array
     */
    public function getOptions()
    {
        return [
            'value' => rand(self::MIN_PROBA, self::MAX_PROBA),
            'img' => rand(1, 6),
        ];
    }

    /**
     * All players
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
     * Don't modify the probability on catch
     * @param Player $player
     */
    public function setProbabilityAfterCatch(Player $player)
    {
    }

    /**
     * onUse : increment probability
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory
     * @param ReturnBox $returnBox
     * @param array     $options
     *
     * @return array Data to push
     */
    public function onUse(Game &$game, Player &$player, Inventory &$inventory, ReturnBox &$returnBox = null, array &$options = [])
    {
        $probability = $player->getProbability() + $inventory->getOption('value');
        $player->setProbability($probability);
        $this->remove = true;
    }
}
