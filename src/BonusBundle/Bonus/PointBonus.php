<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class PointBonus
 * @package BonusBundle\Bonus
 */
class PointBonus extends AbstractBonus
{
    // Min and Max point to get
    const MIN_POINTS = 10;
    const MAX_POINTS = 40;

    /**
     * Get the id
     * @return string
     */
    public function getId()
    {
        return 'point';
    }

    /**
     * Get the probability to catch this bonus
     * @return integer
     */
    public function getProbabilityToCatch()
    {
        return 30;
    }

    /**
     * Get options to add in inventory
     * @return array
     */
    public function getOptions()
    {
        $points = rand(self::MIN_POINTS, self::MAX_POINTS);

        return [
            'label' => $points,
        ];
    }

    /**
     * All players can get this bonus
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
     * onUse : get points
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
        $player->addScore($inventory->getOption('label'));
        $this->remove = true;

        // Send new score to the player
        return [
            $player->getName() => [
                'score' => [$player->getPosition() => $player->getScore()],
            ],
        ];
    }
}
