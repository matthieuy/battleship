<?php


namespace ChatBundle\RPC;

use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use Ratchet\ConnectionInterface;

/**
 * Class ChatRPC
 * @package ChatBundle\RPC
 */
class ChatRPC implements RpcInterface
{
    private $clientManipulator;
    private $em;
    private $pusher;

    /**
     * ChatRPC constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManager              $em
     * @param PusherInterface            $pusher
     */
    public function __construct(
        ClientManipulatorInterface $clientManipulator,
        EntityManager $em,
        PusherInterface $pusher
    ) {
        $this->clientManipulator = $clientManipulator;
        $this->em = $em;
        $this->pusher = $pusher;
    }

    /**
     * Request on a non exist method
     * @param string $name      Method name
     * @param array  $arguments Arguments
     *
     * @return array Error
     */
    public function __call($name, $arguments)
    {
        return ['error' => "Bad request"];
    }

    /**
     * Send a message
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function send(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'chat.rpc';
    }
}
