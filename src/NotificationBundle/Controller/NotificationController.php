<?php

namespace NotificationBundle\Controller;

use MatchBundle\Entity\Game;
use NotificationBundle\Form\Type\NotificationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NotificationController
 * @package NotificationBundle\Controller
 */
class NotificationController extends Controller
{
    /**
     * Notification system
     * @param Request $request
     * @param Game    $game
     *
     * @Route(
     *     name="notification.config",
     *     path="/{slug}/notifications",
     *     methods={"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Game $game)
    {
        $routeUrl = $this->generateUrl('notification.config', ['slug' => $game->getSlug()]);

        // Form
        $form = $this->createForm(
            NotificationType::class,
            [
                '_game' => $game,
                '_user' => $this->getUser(),
            ],
            [
                'action' => $routeUrl,
            ]
        )->handleRequest($request);

        // Validate form
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('doctrine.orm.entity_manager')->flush();

            return $this->redirect($routeUrl);
        }

        // View
        return $this->render('@Notification/Default/notification.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }
}
