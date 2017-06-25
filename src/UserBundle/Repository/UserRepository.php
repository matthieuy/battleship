<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use UserBundle\Entity\User;

/**
 * Class UserRepository
 *
 * @package UserBundle\Repository
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * Load a user
     * @param string $username
     *
     * @return User
     * @throws UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        // Query
        $build = $this->createQueryBuilder('user');
        $build
            ->select('user')
            ->where('user.username=:username')
            ->orWhere('user.email=:username')
            ->setParameter('username', $username);
        $query = $build->getQuery();

        // Cache
        $query
            ->useQueryCache(true)
            ->useResultCache(true, 10);

        try {
            $user = $query->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found', $username), 0, $e);
        }

        return $user;
    }

    /**
     * Get one AI available
     * @param array $excludeIDs ID's AI to exclude
     *
     * @return User|null The AI or null
     */
    public function getAIavailable(array $excludeIDs = [])
    {
        // Count available
        $builder = $this->createQueryBuilder('user');
        $builder
            ->select('COUNT(user)')
            ->where('user.ai=1');
        if (!empty($excludeIDs)) {
            $builder
                ->andWhere('user.id NOT IN (:exclude)')
                ->setParameter('exclude', $excludeIDs);
        }
        $nbAI = $builder->getQuery()->getSingleScalarResult();

        // Any AI
        if (!$nbAI) {
            return null;
        }

        // Get random AI
        $builder = $this->createQueryBuilder('user');
        $builder
            ->where('user.ai=1')
            ->orderBy('user.username')
            ->setFirstResult(rand(0, $nbAI-1))
            ->setMaxResults(1);
        if (!empty($excludeIDs)) {
            $builder
                ->andWhere('user.id NOT IN (:exclude)')
                ->setParameter('exclude', $excludeIDs);
        }

        return $builder->getQuery()->getOneOrNullResult();
    }
}
