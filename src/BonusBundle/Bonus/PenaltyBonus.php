<?php

namespace BonusBundle\Bonus;

use BonusBundle\Event\BonusEvent;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class PenaltyBonus
 * @package BonusBundle\Bonus
 */
class PenaltyBonus extends AbstractBonus
{
    const MIN_PERCENT = 5;
    const MAX_PERCENT = 30;

    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'penalty';
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
     * @param Player $player
     *
     * @return array
     */
    public function getOptions(Player $player)
    {
        $percent = rand(self::MIN_PERCENT, self::MAX_PERCENT);
        $value = round($player->getGame()->getOption('penalty', 0) * $percent / 100);

        return [
            'label' => '-'.$value,
            'value' => $value,
            'img' => rand(1, 4),
        ];
    }

    /**
     * Can the player get this bonus ?
     * @param Player $player
     *
     * @return boolean
     */
    public function canWeGetIt(Player $player)
    {
        return !$player->isAi() && $player->getGame()->getOption('penalty', 0) !== 0;
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
     * onUse : decrease penalty time
     * @param BonusEvent $event
     */
    public function onUse(BonusEvent $event)
    {
        // Get vars
        $game = $event->getGame();
        $lastShoot = $game->getLastShoot();
        $hour = $event->getInventory()->getOption('value');

        // Edit last shoot
        $interval = new \DateInterval('PT'.$hour.'H');
        $lastShoot->sub($interval);
        $game->setLastShoot($lastShoot);
        $event->getGame()->setLastShoot($lastShoot);

        //$this->delete();
    }
}
