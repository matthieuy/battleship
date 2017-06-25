<?php

namespace MatchBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use UserBundle\Entity\User;

/**
 * Class PlayerRepository
 * @package MatchBundle\Repository
 */
class PlayerRepository extends EntityRepository
{
    /**
     * Join a game
     * @param Game    $game The game
     * @param User    $user The user
     * @param boolean $ai   Is a AI ?
     *
     * @return string|boolean Error or true
     */
    public function joinGame(Game $game, User $user, $ai = false)
    {
        // Game is full
        $playersList = $game->getPlayers();
        if (count($playersList) >= $game->getMaxPlayer()) {
            return "Could not join : game is full!";
        }

        if ($ai) {
            // Get AI already in game
            $listExcludeAI = [];
            foreach ($playersList as $player) {
                if ($player->isAi()) {
                    $listExcludeAI[] = $player->getUser()->getId();
                }
            }

            $repo = $this->getEntityManager()->getRepository('UserBundle:User');
            $user = $repo->getAIavailable($listExcludeAI);
            if (!$user) {
                return "Any AI available";
            }
        } else {
            // Already in the game
            foreach ($playersList as $player) {
                if ($player->getUser()->getId() == $user->getId()) {
                    return 'You are already in the game!';
                }
            }
        }

        // Create player
        $player = new Player();
        $player
            ->setColor($this->randomColor())
            ->setTeam($this->getLastTeam($game)+1)
            ->setUser($user);
        $game->addPlayer($player);

        // Persist
        $em = $this->getEntityManager();
        $em->persist($player);
        $em->flush();

        return true;
    }

    /**
     * Remove player from the game
     * @param Game         $game     The game
     * @param User         $user     The user
     * @param null|integer $playerId The player to delete (null => user)
     *
     * @return bool true
     */
    public function quitGame(Game $game, User $user, $playerId = null)
    {
        $builder = $this->createQueryPlayer($game, $user, $playerId);
        $builder->select('player');
        $player = $builder
            ->select('player')
            ->getQuery()
            ->getOneOrNullResult();

        if (!$player) {
            return true;
        }

        // Delete
        $em = $this->getEntityManager();
        $em->remove($player);
        $em->flush();

        return true;
    }

    /**
     * Change team
     * @param Game         $game     The game
     * @param User         $user     User
     * @param integer      $team     Team number
     * @param integer|null $playerId The player id (null => user)
     *
     * @return integer
     */
    public function changeTeam(Game $game, User $user, $team, $playerId = null)
    {
        $builder = $this->createQueryPlayer($game, $user, $playerId);
        $builder
            ->set('player.team', intval($team))
            ->update();

        return $builder->getQuery()->execute();
    }

    /**
     * Get a player
     * @param Game         $game     Game
     * @param User         $user     The user
     * @param integer|null $playerId Player id (or null => user)
     *
     * @return Player|null
     */
    public function getPlayer(Game $game, User $user, $playerId = null)
    {
        $builder = $this->createQueryPlayer($game, $user, $playerId);
        $builder->select('player');

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * Get the last team number
     * @param Game $game The game
     * @return integer The last team number
     */
    public function getLastTeam(Game $game)
    {
        $builder = $this->createQueryBuilder('player');
        $expr = new Expr();
        $builder
            ->select($expr->max('player.team'))
            ->where('player.game=:game')
            ->setParameter('game', $game)
            ->setMaxResults(1);

        return $builder->getQuery()->getSingleScalarResult();
    }

    /**
     * Create query
     * @param Game         $game     The Game
     * @param User         $user     User
     * @param null|integer $playerId The player id (or null => user)
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQueryPlayer(Game $game, User $user, $playerId = null)
    {
        $builder = $this->createQueryBuilder('player');
        $builder
            ->where('player.game=:game')
            ->setParameter('game', $game);

        if ($playerId) {
            $builder
                ->andWhere('player.id=:playerId')
                ->setParameter('playerId', $playerId);
        } else {
            $builder
                ->andWhere('player.user=:user')
                ->setParameter('user', $user);
        }

        return $builder;
    }

    /**
     * Generate a hexa color
     * @return string
     */
    private function randomColor()
    {
        $string = str_split('0123456789ABCDEF');
        $color = '';
        for ($i=0; $i < 6; $i++) {
            $r = (int) floor(mt_rand(0, 15));
            $color .= $string[$r];
        }

        return $color;
    }
}
