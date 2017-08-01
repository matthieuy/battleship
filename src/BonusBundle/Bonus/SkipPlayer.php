<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class SkipPlayer
 * @package BonusBundle\Bonus
 */
class SkipPlayer extends AbstractBonus
{
    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'skip_player';
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
     * @return array
     */
    public function getOptions()
    {
        return [
            'select' => 'enemy',
        ];
    }

    /**
     * Only human can get it
     * @param Player $player
     *
     * @return boolean
     */
    public function canWeGetIt(Player $player)
    {
        return (!$player->isAi());
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
     * before tour : remove a player
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory (options contain "player" to exclude)
     * @param array     $options   Contain teamList from GameRpc::nextTour()
     *
     * @return boolean|array Data to push
     */
    public function onBeforeTour(Game &$game, Player &$player, Inventory &$inventory, array &$options = [])
    {
        $playerPositionExclude = $inventory->getOption('player');

        // Update teamlist
        foreach ($options as $teamId => $players) {
            foreach ($players as $iPlayer => $position) {
                if ($playerPositionExclude == $position) {
                    unset($options[$teamId][$iPlayer]);
                    if (empty($options[$teamId])) {
                        unset($options[$teamId]);
                    } else {
                        $options[$teamId] = array_values($options);
                    }

                    break 2;
                }
            }
        }

        return false;
    }
}
