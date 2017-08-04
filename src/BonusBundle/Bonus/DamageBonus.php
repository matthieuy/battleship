<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
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
     * @return array
     */
    public function getOptions()
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
        return (count($player->getGame()->getPlayersByTeam($player->getTeam(), true)) > 0);
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
     * before add score to player (already add in GameRpc so x2)
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory (options contain "player" to exclude)
     * @param ReturnBox $returnBox
     * @param array     $options   Contain points and shooter
     *
     * @return boolean|array Data to push
     */
    public function onBeforeScore(Game &$game, Player &$player, Inventory &$inventory, ReturnBox &$returnBox = null, array &$options = [])
    {
        if ($player->getPosition() == $inventory->getOption('player')) {
            $player->addScore($options['points']);
            $this->remove = true;
        }

        return false;
    }

    /**
     * before tour : remove bonus
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory (options contain "player" to exclude)
     * @param ReturnBox $returnBox
     * @param array     $options   Contain teamList from GameRpc::nextTour()
     *
     * @return boolean|array Data to push
     */
    public function onBeforeTour(Game &$game, Player &$player, Inventory &$inventory, ReturnBox &$returnBox = null, array &$options = [])
    {
        $this->remove = true;

        return false;
    }
}
