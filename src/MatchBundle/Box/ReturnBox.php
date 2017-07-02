<?php

namespace MatchBundle\Box;

use MatchBundle\Boats;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class ReturnBox
 * @package MatchBundle\Box
 */
class ReturnBox
{
    /** @var Box[]  */
    protected $listBox;
    protected $useWeapon = false;

    /**
     * ReturnBox constructor.
     */
    public function __construct()
    {
        $this->listBox = [];
    }

    /**
     * Add box to list and update grid
     * @param Game $game The game
     * @param Box  $box  The box to add
     *
     * @return $this
     */
    public function addBox(Game &$game, Box &$box)
    {
        // Update grid
        $grid = $game->getGrid();
        $grid[$box->getY()][$box->getX()] = $box->getInfoToGrid();
        if ($box->isSink()) {
            $grid = $this->updateSink($grid, $box);
        }
        $game->setGrid($grid);

        // Add box to list
        $this->listBox[] = $box;

        return $this;
    }

    /**
     * Get information to return to user
     * @param Game   $game
     * @param Player $player
     *
     * @return array
     */
    public function getReturnBox(Game $game, Player $player)
    {
        $return = [
            'img' => [],
            'tour' => $game->getTour(),
        ];

        // Game over ?
        if ($game->getStatus() == Game::STATUS_END) {
            $return['finished'] = true;
        }

        foreach ($this->listBox as $box) {
            $return['img'][] = $box->getInfoToReturn($player);
        }

        return $return;
    }

    /**
     * Get useWeapon
     * @return boolean
     */
    public function isUseWeapon()
    {
        return $this->useWeapon;
    }

    /**
     * Set Use Weapon
     * @param boolean $useWeapon
     */
    public function setUseWeapon($useWeapon = true)
    {
        $this->useWeapon = $useWeapon;
    }

    /**
     * Update grid when a boat is sink
     * @param array $grid The grid
     * @param Box   $box The box (by ref)
     *
     * @return array The grid updated
     */
    private function updateSink(array $grid, Box &$box)
    {
        foreach ($grid as $y => $row) {
            foreach ($row as $x => $b) {
                if (isset($b['boat']) && $b['boat'] == $box->getBoat()) {
                    // Update grid
                    $img = Boats::getDeadImg($b['img']);
                    $grid[$y][$x]['img'] = $img;
                    $grid[$y][$x]['dead'] = true;
                    unset($grid[$y][$x]['explose']);

                    // Add infos to box
                    $box->setSinkInfo([
                        'x' => $x,
                        'y' => $y,
                        'img' => $img,
                        'shoot' => $b['shoot'],
                        'player' => $b['player'],
                    ]);
                    $box->setDead(true);
                }
            }
        }

        return $grid;
    }
}
