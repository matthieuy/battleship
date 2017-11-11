<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class DamageBonus
 * @package BonusBundle\Bonus
 */
class DamageBonus extends AbstractBonus
{
    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'damage';
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
        return [
            'label' => 'x2',
            'select' => BonusConstant::TARGET_FRIENDS,
        ];
    }

    /**
     * Only human
     * @param Player $player
     *
     * @return boolean
     */
    public function canWeGetIt(Player $player)
    {
        // No AI
        if ($player->isAi()) {
            return false;
        }

        // Only if not alone in team
        return (count($player->getGame()->getPlayersByTeam($player->getTeam(), true)) > 1);
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
     * Before add score to player (already add in GameRpc so x2)
     * @param BonusEvent $event
     */
    public function onBeforeScore(BonusEvent $event)
    {
        $player = $event->getPlayer();
        $options = $event->getOptions();

        if ($player->getPosition() == $event->getInventory()->getOption('player')) {
            $player->addScore($options['points']);
            $this->delete();
        }
    }

    /**
     * before tour : remove bonus
     * @param BonusEvent $event
     */
    public function onBeforeTour(BonusEvent $event)
    {
        $this->delete();
    }
}
