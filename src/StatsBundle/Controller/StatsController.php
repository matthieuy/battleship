<?php

namespace StatsBundle\Controller;

use StatsBundle\StatsConstants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\User;

/**
 * Class StatsController
 * @package StatsBundle\Controller
 */
class StatsController extends Controller
{
    /**
     * Get embed render of personal statistics
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function personalStatsAction(User $user)
    {
        // Personal stat
        $repo = $this->get('doctrine.orm.entity_manager')->getRepository('StatsBundle:Stats');
        $personal = $repo->getPersonalStats($user);

        // Views
        return $this->render('@Stats/Stats/user.html.twig', [
            'const' => new StatsConstants(),
            'personal' => $personal,
        ]);
    }
}
