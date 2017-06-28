<?php

namespace BonusBundle\Weapons;

use MatchBundle\Box\Box;

/**
 * Class AbstractWeapon
 *
 * @package BonusBundle\Weapons
 */
abstract class AbstractWeapon implements WeaponInterface
{
    /**
     * By default : AI don't use weapon
     * @return boolean
     */
    public function canAiUseIt()
    {
        return false;
    }

    /**
     * Rotate a matrix
     * @param array   $matrix Matrix/Grid to rotate
     * @param integer $time   Number of 90 degrees rotates
     *
     * @return array The matrix rotated
     */
    public function rotate(array $matrix, $time = 1)
    {
        if (!$this->canBeRotate()) {
            return $matrix;
        }

        if ($time > 3) {
            $time = $time % 4;
        }
        $result = $matrix;

        for ($t=$time; $t > 0; $t--) {
            $rows = count($matrix);
            $cols = count($matrix[0]);
            $result = [];

            for ($i=0; $i < $cols; $i++) {
                for ($j=0; $j < $rows; $j++) {
                    $result[$rows - $i - 1][$j] = $matrix[$rows - $j - 1][$i];
                }
            }
            $result = array_values($result);
        }

        return $result;
    }

    /**
     * @param integer $x      X position
     * @param integer $y      Y position
     * @param integer $rotate Number of 90degrees rotations
     *
     * @return Box[]
     */
    public function getBoxes($x, $y, $rotate = 0)
    {
        $grid = $this->rotate($this->getGridArray(), $rotate);
        $rows = count($grid);
        $cols = count($grid[0]);
        $centerY = floor($rows / 2);
        $centerX = floor($cols / 2);

        $boxes = [];
        for ($sy=0; $sy < $rows; $sy++) {
            for ($sx=0; $sx < $cols; $sx++) {
                if ($grid[$sy][$sx] == 1) {
                    $boxes[] = new Box($x+$sx-$centerX, $y+$sy-$centerY);
                }
            }
        }

        // Random and shuffle order of boxes
        if ($this->canBeShuffle()) {
            shuffle($boxes);
        } else {
            $random = mt_rand(0, 1);
            if ($random) {
                $boxes = array_reverse($boxes);
            }
        }

        return $boxes;
    }

    /**
     * Convert to array
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'rotate' => $this->canBeRotate(),
            'grid' => $this->getGridArray(),
        ];
    }
}
