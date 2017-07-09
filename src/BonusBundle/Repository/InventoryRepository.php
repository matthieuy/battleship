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
     * Get the inventory (not in use)
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
            ->andWhere('inventory.useIt=false')
            ->setParameters([
                'game' => $game,
                'user' => $user,
            ]);

        $query = $builder->getQuery();
        $result = $query->getResult();

        return $result;
    }
}
