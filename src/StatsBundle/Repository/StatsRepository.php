<?php

namespace StatsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MatchBundle\Entity\Game;
use StatsBundle\Entity\Stats;

/**
 * Class StatsRepository
 * @package StatsBundle\Repository
 */
class StatsRepository extends EntityRepository
{
    /**
     * Increase stat
     * @param integer   $statName
     * @param integer   $userId
     * @param Game|null $game
     * @param mixed     $value2
     * @param int       $increment
     */
    public function increment($statName, $userId, Game $game = null, $value2 = null, $increment = 1)
    {
        $gameId = ($game) ? $game->getId() : null;

        // Get stats
        /** @var Stats $stat */
        $stat = $this->findOneBy([
            'stat' => $statName,
            'userId' => $userId,
            'gameId' => $gameId,
            'value2' => $value2,
        ]);

        // Increment or create
        if ($stat) {
            $stat->increment($increment);
        } else {
            $stat = new Stats();
            $stat
                ->setStat($statName)
                ->setUserId($userId)
                ->setGameId($gameId)
                ->setValue($increment)
                ->setValue2($value2);
            $this->getEntityManager()->persist($stat);
        }

        // Save
        $this->getEntityManager()->flush();
    }

    /**
     * Merge stat on delete game
     * @param integer $gameId
     */
    public function onDeleteGame($gameId)
    {
        // Get stats to move
        $statList = $this->findBy([
            'gameId' => $gameId,
        ]);

        // Move it
        /** @var Stats $stat */
        foreach ($statList as $stat) {
            $this->increment($stat->getStat(), $stat->getUserId(), null, $stat->getValue2(), $stat->getValue());
        }

        // Remove old
        $builder = $this->createQueryBuilder('stats');
        $builder
            ->delete()
            ->where('stats.gameId=:gameId')
            ->setParameters([
                'gameId' => $gameId,
            ]);
        $builder->getQuery()->execute();
    }
}
