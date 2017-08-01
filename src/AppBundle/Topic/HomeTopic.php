<?php

namespace AppBundle\Topic;

use AppBundle\Manager\OnlineManager;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

/**
 * Class HomeTopic
 *
 * @package AppBundle\Topic
 */
class HomeTopic implements TopicInterface, PushableTopicInterface
{
    private $entityManager;
    private $onlineManager;

    /**
     * HomeTopic constructor.
     * @param EntityManager $entityManager
     * @param OnlineManager $onlineManager
     */
    public function __construct(EntityManager $entityManager, OnlineManager $onlineManager)
    {
        $this->entityManager = $entityManager;
        $this->onlineManager = $onlineManager;
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $this->onlineManager->onSubscribe($connection, $topic);

        $repo = $this->entityManager->getRepository('MatchBundle:Game');
        $list = $repo->getList();

        $connection->event($topic->getId(), ['games' => $list]);
    }

    /**
     * @param Topic        $topic
     * @param WampRequest  $request
     * @param string|array $data
     * @param string       $provider
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider)
    {
        $topic->broadcast($data);
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param WampRequest         $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $this->onlineManager->onUnSubscribe($connection, $topic);
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
     * Get the topic name
     * @return string
     */
    public function getName()
    {
        return 'homepage';
    }
}
