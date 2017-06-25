<?php

namespace MatchBundle;

/**
 * Class Boats
 * @package MatchBundle
 */
class Boats
{
    private static $initLife;

    /**
     * Get the initial life
     * @return int
     */
    public static function getInitialLife()
    {
        if (!self::$initLife) {
            $life = 0;
            $boats = self::getList();
            foreach ($boats as $boat) {
                $life += $boat['nb'] * count($boat['img'][0]);
            }

            self::$initLife = $life;
        }

        return self::$initLife;
    }

    /**
     * Get the boats list
     * @return array
     */
    public static function getList()
    {
        return [
            [
                'name' => 'Torpedo',
                'nb' => 4,
                'img' => [[5, 6], [41, 49]],
                'dead' => [[48, 56], [4, 12]],
            ],
            [
                'name' => 'Destroyer',
                'nb' => 3,
                'img' => [[13, 14, 15], [34, 42, 50]],
                'dead' => [[53, 54, 55], [3, 11, 19]],
            ],
            [
                'name' => 'Cruiser',
                'nb' => 2,
                'img' => [[21, 22, 23, 24], [27, 35, 43, 51]],
                'dead' => [[16, 45, 46, 47], [2, 10, 18, 26]],
            ],
            [
                'name' => 'Aircraft',
                'nb' => 1,
                'img' => [[29, 30, 31, 32, 40], [20, 28, 36, 44, 52]],
                'dead' => [[7, 8, 37, 38, 39], [1, 9, 17, 25, 33]],
            ],
        ];
    }

    /**
     * Get the dead image number
     * @param int $number The alive image number
     *
     * @return int
     * @throws \Exception
     */
    public static function getDeadImg($number)
    {
        $boats = self::getList();
        foreach ($boats as $boat) {
            for ($orientation=0; $orientation <= 1; $orientation++) {
                foreach ($boat['img'][$orientation] as $k => $v) {
                    if ($number == $v) {
                        return $boat['dead'][$orientation][$k];
                    }
                }
            }
        }

        throw new \Exception("Don't find the dead img");
    }
}
