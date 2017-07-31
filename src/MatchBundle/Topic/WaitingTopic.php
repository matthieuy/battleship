<?php

namespace MatchBundle\Topic;

use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use MatchBundle\Entity\Game;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class WaitingTopic
 * @package MatchBundle\Topic
 */
class WaitingTopic implements TopicInterface, PushableTopicInterface
{
    private $em;
    private $translator;

    /**
     * WaitingTopic constructor (DI)
     * @param EntityManager       $em
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @param Topic        $topic
     * @param WampRequest  $request
     * @param string|array $data
     * @param string       $provider
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider)
    {
        if (isset($data['_call']) && null !== $slug = $request->getAttributes()->get('slug')) {
            $game = $this->getGame($slug);
            if ($data['_call'] == 'updatePlayers') {
                $data = ['players' => $this->getPlayersArray($game)];
            } elseif ($data['_call'] == 'updateGame') {
                $data = ['infos' => $this->getGameArray($game)];
            }
        }
        $topic->broadcast($data);
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        // Send players list on subscribe
        if (null !== $slug = $request->getAttributes()->get('slug')) {
            $game = $this->getGame($slug);

            $connection->event($topic->getId(), [
                'players' => ($game) ? $this->getPlayersArray($game) : [],
                'infos' => ($game) ? $this->getGameArray($game) : [],
                'console' => $this->translator->trans('game_load', [], 'console'),
            ]);
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     * @param string              $event
     * @param array               $exclude
     * @param array               $eligible
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'game.wait';
    }

    /**
     * Get players list
     * @param Game $game
     *
     * @return array
     */
    private function getPlayersArray(Game $game)
    {
        $list = [];
        foreach ($game->getPlayers() as $player) {
            $list[] = $player->toArray();
        }

        return $list;
    }

    /**
     * Get game's infos
     * @param Game $game
     *
     * @return array
     */
    private function getGameArray(Game $game)
    {
        return $game->toArray();
    }

    /**
     * Get game
     * @param string $slug
     *
     * @return Game|null
     */
    private function getGame($slug)
    {
        $repo = $this->em->getRepository('MatchBundle:Game');
        $game = $repo->findOneBy(['slug' => $slug]);
        if ($game instanceof Game) {
            return $game;
        }

        return null;
    }
}
