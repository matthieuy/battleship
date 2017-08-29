<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
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
     * @param Player $player
     *
     * @return array
     */
    public function getOptions(Player $player)
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
     * @param BonusEvent $event
     */
    public function onGetBoxes(BonusEvent $event)
    {
        $shooter = $event->getPlayer();
        $player = $event->getInventory()->getPlayer();

        if (!$shooter || $player->getId() == $shooter->getId()) {
            $nbBox = $event->getInventory()->getOption('label');
            $game = $event->getGame();
            $grid = $game->getGrid();
            $boxes = [];

            $try = 0;
            do {
                $x = rand(0, $game->getSize()-1);
                $y = rand(0, $game->getSize()-1);

                // Box exist and (already shoot or same team)
                if ($this->checkBox($grid, $x, $y, $shooter)) {
                    $try++;
                    continue;
                } else {
                    $boxes[] = $game->getBox($x, $y);
                    $try = 0;
                }
            } while ($try < 25 && count($boxes) < $nbBox);

            $listBox = array_merge($event->getOptions(), $boxes);
            $event->setOptions($listBox);
            $this->delete();
        }
    }

    /**
     * Check box to shoot
     * @param array       $grid
     * @param integer     $x
     * @param integer     $y
     * @param Player|null $shooter
     *
     * @return bool
     */
    protected function checkBox(array $grid, $x, $y, Player $shooter = null)
    {
        return (isset($grid[$y], $grid[$y][$x]) && (isset($grid[$y][$x]['shoot']) || (isset($grid[$y][$x]['team']) && $grid[$y][$x]['team'] == $shooter->getTeam())));
    }
}
