<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
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
        return 'bonus.point';
    }

    /**
     * Get the unique name of the bonus
     * @return string
     */
    public function getName()
    {
        return 'Points';
    }

    /**
     * Get the bonus description
     * @return string
     */
    public function getDescription()
    {
        return 'Add points';
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
        if (!$player) {
            return false;
        }

        return true;
    }

    /**
     * onUse : get points
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory
     * @param array     $options
     *
     * @return array Data to push
     */
    public function onUse(Game &$game, Player &$player, Inventory &$inventory, array &$options = [])
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
