<?php

namespace ChatBundle\Repository;

use ChatBundle\Entity\Message;
use Doctrine\ORM\EntityRepository;
use MatchBundle\Entity\Game;
use UserBundle\Entity\User;

/**
 * Class MessageRepository
 * @package ChatBundle\Repository
 */
class MessageRepository extends EntityRepository
{
    /**
     * Get messages
     * @param Game      $game
     * @param integer   $lastId
     * @param User|null $user
     *
     * @return Message[]
     */
    public function getMessages(Game $game, $lastId, User $user = null)
    {
        $builder = $this->createQueryBuilder('message');
        $builder
            ->select('message')
            ->where('message.game=:game')
            ->setParameter('game', $game)
            ->andWhere('message.id > :lastId')
            ->setParameter('lastId', $lastId)
            ->orderBy('message.date', 'ASC');

        if ($user) {
            $orX = $builder->expr()->orX();
            $player = $game->getPlayerByUser($user);

            // Author
            $orX->add($builder->expr()->eq('message.author', $user->getId()));

            // Global chan
            $orX->add($builder->expr()->eq('message.channel', Message::CHANNEL_GLOBAL));

            // Private chan
            $orX->add($builder->expr()->andX('message.recipient=:userId', 'message.channel=:chan_private'));
            $builder
                ->setParameter('chan_private', Message::CHANNEL_PRIVATE)
                ->setParameter('userId', $user->getId());

            // Team chan
            if ($player) {
                $orX->add($builder->expr()->andX('message.channel=:chan_team', 'message.recipient=:team'));
                $builder
                    ->setParameter('chan_team', Message::CHANNEL_TEAM)
                    ->setParameter('team', $player->getTeam());
            }

            $builder->andWhere($orX);
        } else {
            // No user : public only
            $builder
                ->andWhere('message.channel=:chan_public')
                ->setParameter('chan_public', Message::CHANNEL_GLOBAL);
        }

        return $builder->getQuery()->getResult();
    }
}
