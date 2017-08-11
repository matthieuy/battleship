<?php

namespace StatsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MatchBundle\Entity\Game;
use StatsBundle\Entity\Stats;
use UserBundle\Entity\User;

/**
 * Class StatsRepository
 * @package StatsBundle\Repository
 */
class StatsRepository extends EntityRepository
{
    /**
     * Increase stat
     * @param integer   $statName
     * @param User      $user
     * @param Game|null $game
     * @param mixed     $value2
     * @param int       $increment
     */
    public function increment($statName, User $user, Game $game = null, $value2 = null, $increment = 1)
    {
        $gameId = ($game) ? $game->getId() : null;

        // Get stats
        /** @var Stats $stat */
        $stat = $this->findOneBy([
            'stat' => $statName,
            'userId' => $user->getId(),
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
                ->setUserId($user->getId())
                ->setGameId($gameId)
                ->setValue($increment)
                ->setValue2($value2);
            $this->getEntityManager()->persist($stat);
        }

        // Save
        $this->getEntityManager()->flush();
    }
}
