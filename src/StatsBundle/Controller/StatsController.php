<?php

namespace StatsBundle\Controller;

use MatchBundle\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\User;

/**
 * Class StatsController
 * @package StatsBundle\Controller
 */
class StatsController extends Controller
{
    /**
     * Statistics game
     * @param Game $game
     * @Route(
     *     name="stats.game",
     *     path="/game/{slug}/stats",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"}
     *     )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gameStatsAction(Game $game)
    {
        $statsManager = $this->get('stats.manager');

        // View
        return $this->render('@Stats/Stats/game.html.twig', [
            'game' => $game,
            'shoots' => $statsManager->getTableShoot($game),
            'penaltyData' => $statsManager->getPenaltyData($game),
            'weaponData' => $statsManager->getWeaponData($game),
            'bonusData' => $statsManager->getBonusData($game),
        ]);
    }

    /**
     * Get embed render of personal statistics
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function personalStatsAction(User $user)
    {
        $statsManager = $this->get('stats.manager');
        $personal = $statsManager->getPersonalStats($user);

        // View
        return $this->render('@Stats/Stats/user.html.twig', [
            'personal' => $personal,
        ]);
    }
}
