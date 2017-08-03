<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class BomberBonus
 * @package BonusBundle\Bonus
 */
class BomberBonus extends AbstractBonus
{
    const MIN_BOX = 5;
    const MAX_BOX = 20;

    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'bomber';
    }

    /**
     * Get the probability to catch this bonus
     * @return integer
     */
    public function getProbabilityToCatch()
    {
        return 5;
    }

    /**
     * Get options to add in inventory
     * @return array
     */
    public function getOptions()
    {
        return [
            'label' => rand(self::MIN_BOX, self::MAX_BOX),
        ];
    }

    /**
     * All players can get it
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
        return (in_array($player->getPosition(), $game->getTour()));
    }

    /**
     * on get boxes : add random box
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory
     * @param ReturnBox $returnBox
     * @param array     $options   shooter and boxes
     *
     * @return array|false Data to push
     */
    public function onGetBoxes(Game &$game, Player &$player, Inventory &$inventory, ReturnBox &$returnBox = null, array &$options = [])
    {
        /** @var Player $shooter */
        $shooter = $options['shooter'];

        if ($player->getPosition() == $shooter->getPosition()) {
            $nbBox = $inventory->getOption('label');
            $grid = $game->getGrid();
            $boxes = [];

            $try = 0;
            do {
                $x = rand(0, $game->getSize()-1);
                $y = rand(0, $game->getSize()-1);

                // Box exist and (already shoot or same team)
                if (isset($grid[$y], $grid[$y][$x]) && (isset($grid[$y][$x]['shoot']) || (isset($grid[$y][$x]['team']) && $grid[$y][$x]['team'] == $shooter->getTeam()))) {
                    $try++;
                    continue;
                } else {
                    $boxes[] = $game->getBox($x, $y);
                    $try = 0;
                }
            } while ($try < 25 && count($boxes) < $nbBox);

            $options['boxes'] = array_merge($options['boxes'], $boxes);
            $this->remove = true;
        }

        return false;
    }
}
