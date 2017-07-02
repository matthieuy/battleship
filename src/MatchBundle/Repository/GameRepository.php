<?php

namespace MatchBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MatchBundle\Entity\Game;

/**
 * Class GameRepository
 *
 * @package MatchBundle\Repository
 */
class GameRepository extends EntityRepository
{
    /**
     * Get game list (array format)
     * @return array
     */
    public function getList()
    {
        $builder = $this->createQueryBuilder('game');
        $games = $builder->getQuery()->getResult();

        $list = [];
        foreach ($games as $game) {
            /** @var Game $game */
            $list[] = $game->toArray();
        }

        return $list;
    }
}
