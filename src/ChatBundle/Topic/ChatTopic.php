<?php

namespace ChatBundle\Topic;

use AppBundle\Manager\OnlineManager;
use ChatBundle\Entity\Message;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use MatchBundle\Entity\Game;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

/**
 * Class ChatTopic
 * @package ChatBundle\Topic
 */
class ChatTopic implements TopicInterface, PushableTopicInterface
{
    private $clientManipulator;
    private $entityManager;
    private $onlineManager;

    /**
     * ChatTopic constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManager              $entityManager
     * @param OnlineManager              $onlineManager
     */
    public function __construct(ClientManipulatorInterface $clientManipulator, EntityManager $entityManager, OnlineManager $onlineManager)
    {
        $this->clientManipulator = $clientManipulator;
        $this->entityManager = $entityManager;
        $this->onlineManager = $onlineManager;
    }

    /**
     * On push
     * @param Topic        $topic
     * @param WampRequest  $request
     * @param string|array $data
     * @param string       $provider
     *
     * @return Topic
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider)
    {
        $message = $data['message'];

        // Public message
        if (!isset($message['channel'])) {
            return $topic->broadcast($data, [], $this->onlineManager->getSessionByGameId($message['game']));
        }

        // Team message
        if ($message['channel'] == Message::CHANNEL_TEAM) {
            return $topic->broadcast($data, [], $this->onlineManager->getSessionsByTeam($message['game'], $message['recipient']));
        }

        // Private message
        return $topic->broadcast($data, [], $this->onlineManager->getSessionsByUserId($message['recipient'], $message['game']));
    }

    /**
     * On subscribe
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        // Get game
        $slug = $request->getAttributes()->get('slug');
        $game = $this->getGame($slug);

        // Update online
        $this->onlineManager->onSubscribe($connection, $topic, $game);
    }

    /**
     * On unsubscribe
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        // Update online
        $this->onlineManager->onUnSubscribe($connection, $topic);
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
        return 'chat';
    }

    /**
     * Get game
     * @param string $slug
     *
     * @return Game|null
     */
    private function getGame($slug)
    {
        if ($slug) {
            $repo = $this->entityManager->getRepository('MatchBundle:Game');
            $game = $repo->findOneBy(['slug' => $slug]);
            if ($game instanceof Game) {
                return $game;
            }
        }

        return null;
    }
}
