<?php

namespace MatchBundle\Topic;

use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use MatchBundle\Entity\Game;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

/**
 * Class ScoreTopic
 * @package MatchBundle\Topic
 */
class ScoreTopic implements TopicInterface, PushableTopicInterface
{
    private $entityManager;

    /**
     * ScoreTopic constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * On push
     * @param Topic        $topic
     * @param WampRequest  $request
     * @param string|array $data
     * @param string       $provider
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider)
    {
    }

    /**
     * On subscribe
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $attributes = $request->getAttributes();
        if ($attributes->has('slug')) {
            $game = $this->entityManager->getRepository('MatchBundle:Game')->findOneBy(['slug' => $attributes->get('slug')]);
            $connection->event($topic->getId(), ['scores' => $this->getPlayersList($game)]);
        }
    }

    /**
     * On unsubscribe
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
    }

    /**
     * On publish
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
     * Get topic name
     * @return string
     */
    public function getName()
    {
        return 'game.score';
    }

    /**
     * Get players as array
     * @param Game $game
     * @return array
     */
    private function getPlayersList(Game $game)
    {
        $list = [];
        $players = $game->getPlayers();
        foreach ($players as $player) {
            $list[] = $player->toArray();
        }

        return $list;
    }
}
