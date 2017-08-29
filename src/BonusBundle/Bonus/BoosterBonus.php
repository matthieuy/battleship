<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
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
        return 50;
    }

    /**
     * Get options to add in inventory
     * @param Player $player
     *
     * @return array
     */
    public function getOptions(Player $player)
    {
        return [
            'value' => rand(self::MIN_PROBA, self::MAX_PROBA),
            'img' => rand(1, 8),
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
        $player->addProbability(1 - self::MIN_PROBA);
    }

    /**
     * onUse : increment probability
     * @param BonusEvent $event
     *
     */
    public function onUse(BonusEvent $event)
    {
        $player = $event->getPlayer();
        $player->addProbability($event->getInventory()->getOption('value'));
        $this->delete();
    }
}
