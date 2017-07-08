<?php

namespace BonusBundle\RPC;

use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use Ratchet\ConnectionInterface;
use UserBundle\Entity\User;

/**
 * Class BonusRpc
 * @package BonusBundle\RPC
 */
class BonusRpc implements RpcInterface
{
    private $clientManipulator;
    private $entityManager;

    /**
     * BonusRpc constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManager              $entityManager
     */
    public function __construct(ClientManipulatorInterface $clientManipulator, EntityManager $entityManager)
    {
        $this->clientManipulator = $clientManipulator;
        $this->entityManager = $entityManager;
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
        return ['error' => "Bad Request ($name)"];
    }

    /**
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function load(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get user
        $user = $this->clientManipulator->getClient($connection);
        if (!$user instanceof User || !isset($params['slug'])) {
            return ['error' => "Bad Request"];
        }
    }

    /**
     * Get RPC name
     * @return string
     */
    public function getName()
    {
        return 'game.bonus.rpc';
    }
}
