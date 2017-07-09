<?php

namespace BonusBundle\RPC;

use BonusBundle\BonusConstant;
use BonusBundle\Manager\BonusRegistry;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use MatchBundle\Entity\Game;
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
    private $bonusRegistry;

    /**
     * BonusRpc constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManager              $entityManager
     * @param BonusRegistry              $bonusRegistry
     */
    public function __construct(ClientManipulatorInterface $clientManipulator, EntityManager $entityManager, BonusRegistry $bonusRegistry)
    {
        $this->clientManipulator = $clientManipulator;
        $this->entityManager = $entityManager;
        $this->bonusRegistry = $bonusRegistry;
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

        // Get and check game
        if (!isset($params['slug']) || null === $game = $this->getGame($params['slug'])) {
            return ['error' => "Bad Request"];
        }

        // Get inventory
        $repo = $this->entityManager->getRepository('BonusBundle:Inventory');
        $inventory = $repo->getInventory($game, $user);

        $list = [];
        foreach ($inventory as $bonus) {
            $infos = $bonus->toArray();
            $b = $this->bonusRegistry->getBonusById($bonus->getName());
            $infos = array_merge($infos, [
                'name' => $b->getName(),
                'description' => $b->getDescription(),
            ]);

            $list[] = $infos;
        }

        return [
            'list' => $list,
            'size' => BonusConstant::INVENTORY_SIZE,
        ];
    }

    /**
     * Get RPC name
     * @return string
     */
    public function getName()
    {
        return 'game.bonus.rpc';
    }

    /**
     * Get the game
     * @param string $slug Slug of game
     *
     * @return Game|null
     */
    private function getGame($slug)
    {
        $repo = $this->entityManager->getRepository('MatchBundle:Game');
        $game = $repo->findOneBy(['slug' => $slug]);
        if (!$game || $game->getStatus() == Game::STATUS_WAIT) {
            return null;
        }

        return $game;
    }
}
