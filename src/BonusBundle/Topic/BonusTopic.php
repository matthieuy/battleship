<?php

namespace BonusBundle\Topic;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

/**
 * Class BonusTopic
 * @package BonusBundle\Topic
 */
class BonusTopic implements TopicInterface, PushableTopicInterface
{
    private $clientManipulator;

    /**
     * BonusTopic constructor.
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function __construct(ClientManipulatorInterface $clientManipulator)
    {
        $this->clientManipulator = $clientManipulator;
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
        foreach ($data as $username => $dataToSend) {
            $user = $this->clientManipulator->findByUsername($topic, $username);
            if ($user !== false) {
                $topic->broadcast($dataToSend, [], [$user['connection']->WAMP->sessionId]);
            }
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
        return 'game.bonus';
    }
}
