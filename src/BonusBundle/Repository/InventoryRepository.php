<?php

namespace BonusBundle\Repository;

use BonusBundle\Entity\Inventory;
use Doctrine\ORM\EntityRepository;
use MatchBundle\Entity\Game;
use UserBundle\Entity\User;

/**
 * Class InventoryRepository
 * @package BonusBundle\Repository
 */
class InventoryRepository extends EntityRepository
{
    /**
     * Get the inventory
     * @param Game $game
     * @param User $user
     *
     * @return Inventory[]
     */
    public function getInventory(Game $game, User $user)
    {
        $builder = $this->createQueryBuilder('inventory');
        $builder
            ->select('inventory')
            ->where('inventory.game=:game')
            ->innerJoin('inventory.player', 'player')
            ->andWhere('player.user=:user')
            ->orderBy('inventory.useIt', 'ASC')
            ->setParameters([
                'game' => $game,
                'user' => $user,
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
        $builder = $this->createQueryBuilder('inventory');
        $builder
            ->select('inventory')
            ->where('inventory.game=:game')
            ->andWhere('inventory.useIt=:use')
            ->setParameters([
                'game' => $game,
                'use' => true,
            ]);

        return $builder->getQuery()->getResult();
    }
}
