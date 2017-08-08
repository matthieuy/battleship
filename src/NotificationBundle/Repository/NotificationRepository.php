<?php

namespace NotificationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MatchBundle\Entity\Game;
use NotificationBundle\Entity\Notification;
use UserBundle\Entity\User;

/**
 * Class NotificationRepository
 * @package NotificationBundle\Repository
 */
class NotificationRepository extends EntityRepository
{
    /**
     * Get notifications
     * @param Game $game
     * @param User $user
     *
     * @return Notification[]
     */
    public function getUserNotification(Game $game, User $user)
    {
        // Create query
        $builder = $this->createQueryBuilder('notification');
        $builder
            ->select('notification')
            ->where('notification.game=:game')
            ->andWhere('notification.user=:user')
            ->setParameters([
                'game' => $game,
                'user' => $user,
            ]);
        $results = $builder->getQuery()->getResult();

        // Organize array
        $notifications = [];
        foreach ($results as $notification) {
            /** @var Notification $notification */
            $notifications[$notification->getName()] = $notification;
        }

        return $notifications;
    }

    /**
     * Get notification for a game
     * @param Game $game
     *
     * @return Notification[]
     */
    public function getNotification(Game $game)
    {
        $builder = $this->createQueryBuilder('notification');
        $builder
            ->select('notification')
            ->where('notification.game=:game')
            ->andWhere('notification.enabled=:enabled')
            ->orderBy('notification.name')
            ->setParameters([
                'game' => $game,
                'enabled' => true,
            ]);

        return $builder->getQuery()->getResult();
    }
}
