<?php

namespace MatchBundle\Topic;

use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use MatchBundle\Entity\Player;
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
        $list = $this->getPlayersList($request);
        if ($list) {
            $topic->broadcast(['scores' => $list]);
        }
    }

    /**
     * On subscribe
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $list = $this->getPlayersList($request);
        if ($list) {
            $connection->event($topic->getId(), ['scores' => $list]);
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
     * @param WampRequest $request
     * @return array|null
     */
    private function getPlayersList(WampRequest $request)
    {
        // Get slug
        $attributes = $request->getAttributes();
        if (!$attributes->has('slug')) {
            return null;
        }

        // Get game
        $game = $this->entityManager->getRepository('MatchBundle:Game')->findOneBy(['slug' => $attributes->get('slug')]);
        $players = $game->getPlayers();
        $tour = $game->getTour();

        $list = [];
        foreach ($players as $player) {
            /** @var Player $player */
            $list[] = array_merge(
                $player->toArray(),
                ['tour' => in_array($player->getPosition(), $tour)]
            );
        }

        return $list;
    }
}
