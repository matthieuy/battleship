<?php

namespace MatchBundle\Controller;

use MatchBundle\Entity\Game;
use MatchBundle\Event\GameEvent;
use MatchBundle\Form\Type\GameType;
use MatchBundle\MatchEvents;
use MatchBundle\Repository\PlayerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MatchController
 * @package MatchBundle\Controller
 */
class MatchController extends Controller
{
    /**
     * Create a game
     * @param Request $request
     *
     * @Route(name="match.create", path="/create-game", methods={"GET", "POST"})
     * @return Response
     */
    public function createAction(Request $request)
    {
        // Create a new game
        $game = new Game();

        // Create form
        $form = $this->createForm(GameType::class, $game, [
            'action' => $this->generateUrl('match.create'),
        ])->handleRequest($request);

        // Form request
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($game);
            $em->flush();

            // Join the game
            /** @var PlayerRepository $repo */
            $repo = $em->getRepository('MatchBundle:Player');
            $repo->joinGame($game, $this->getUser());

            // Event dispatcher
            $event = new GameEvent($game);
            $this->get('event_dispatcher')->dispatch(MatchEvents::CREATE, $event);

            // Redirect
            return $this->redirectToRoute('match.display', ['slug' => $game->getSlug()]);
        }

        // View
        return $this->render('@Match/Match/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display a game
     * @param Game $game
     *
     * @Route(
     *     name="match.display",
     *     path="/game/{slug}",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     * @return Response
     */
    public function displayAction(Game $game)
    {
        if ($game->getStatus() == Game::STATUS_WAIT) {
            return $this->lobbyingPage($game);
        }

        return $this->runningPage($game);
    }

    /**
     * Delete a game
     * @param Game $game
     *
     * @Route(
     *     name="match.delete",
     *     path="/game/{slug}/delete",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Game $game)
    {
        // Check rights
        if (!$this->isGranted('ROLE_ADMIN') && $game->getCreator()->getId() !== $this->getUser()->getId()) {
            $this->addFlash('error', 'Bad request');

            return $this->redirectToRoute('homepage');
        }

        // Remove
        $em = $this->get('doctrine.orm.entity_manager');
        $em->remove($game);
        $em->flush();
        $this->addFlash('success', 'Game delete successful!');

        // Event
        $event = new GameEvent($game);
        $this->get('event_dispatcher')->dispatch(MatchEvents::DELETE, $event);

        // Redirection
        return $this->redirectToRoute('homepage');
    }

    /**
     * Display the waiting page
     * @param Game $game
     * @return Response
     */
    private function lobbyingPage(Game $game)
    {
        return $this->render('@Match/Match/waiting.html.twig', [
            'game' => $game,
            'isCreator' => $game->isCreator($this->getUser()),
        ]);
    }

    /**
     * Display the running game page
     * @param Game $game
     * @return Response
     */
    private function runningPage(Game $game)
    {
        return $this->render('@Match/Match/game.html.twig', [
            'game' => $game,
            'inGame' => true,
        ]);
    }
}
