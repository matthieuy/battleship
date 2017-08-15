<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Event\BonusEvent;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class InventorySizeBonus
 * @package BonusBundle\Bonus
 */
class InventorySizeBonus extends AbstractBonus
{
    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'inventory.size';
    }

    /**
     * Get the probability to catch this bonus
     * @return integer
     */
    public function getProbabilityToCatch()
    {
        return 20;
    }

    /**
     * Get options to add in inventory
     * @return array
     */
    public function getOptions()
    {
        $size = rand(1, 3);

        return [
            'value' => $size,
            'label' => '+'.$size,
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
        return $player->getInventorySize() < BonusConstant::INVENTORY_MAX_SIZE;
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
     * onUse : increment inventory size
     * @param BonusEvent $event
     *
     */
    public function onUse(BonusEvent $event)
    {
        $size = $event->getPlayer()->getInventorySize() + $event->getInventory()->getOption('value');
        $size = min($size, BonusConstant::INVENTORY_MAX_SIZE);

        $event->getPlayer()->setInventorySize($size);
        $this->delete();
    }
}
