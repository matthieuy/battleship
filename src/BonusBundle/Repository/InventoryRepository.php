<?php

namespace BonusBundle\Repository;

use BonusBundle\Entity\Inventory;
use Doctrine\ORM\EntityRepository;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class InventoryRepository
 * @package BonusBundle\Repository
 */
class InventoryRepository extends EntityRepository
{
    /**
     * Get the inventory
     * @param Game   $game
     * @param Player $player
     *
     * @return Inventory[]
     */
    public function getInventory(Game $game, Player $player)
    {
        $builder = $this->createQueryBuilder('inventory');
        $builder
            ->select('inventory')
            ->where('inventory.game=:game')
            ->andWhere('inventory.player=:player')
            ->orderBy('inventory.useIt', 'ASC')
            ->setMaxResults($player->getInventorySize())
            ->setParameters([
                'game' => $game,
                'player' => $player,
            ]);

        $query = $builder->getQuery();
        $result = $query->getResult();

        return $result;
    }

    /**
     * Get bonus in use
     * @param Game $game
     *
     * @return Inventory[]
     */
    public function getActiveBonus(Game $game)
    {
        $builder = $this->queryActiveBonus($game);

        return $builder->getQuery()->getResult();
    }

    /**
     * Get number of current bonus for user
     * @param Game   $game
     * @param Player $player
     *
     * @return integer
     */
    public function nbrOfCurrentBonus(Game $game, Player $player)
    {
        $builder = $this->queryActiveBonus($game);
        $builder
            ->select('COUNT(inventory)')
            ->andWhere('inventory.player=:player')
            ->setParameter('player', $player);

        return $builder->getQuery()->getSingleScalarResult();
    }


    /**
     * Get query with active bonus
     * @param Game $game
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryActiveBonus(Game $game)
    {
        $builder = $this->createQueryBuilder('inventory');
        $builder
            ->select('inventory')
            ->where('inventory.game=:game')
            ->andWhere('inventory.useIt=:use')
            ->setParameters([
                'game' => $game,
                'use' => true,
            ]);

        return $builder;
    }
}
