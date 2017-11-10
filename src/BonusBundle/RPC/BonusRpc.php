<?php

namespace BonusBundle\RPC;

use BonusBundle\BonusConstant;
use BonusBundle\BonusEvents;
use BonusBundle\Event\BonusEvent;
use BonusBundle\Manager\BonusRegistry;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use MatchBundle\Entity\Game;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    private $eventDispatcher;
    private $logger;

    /**
     * BonusRpc constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManager              $entityManager
     * @param BonusRegistry              $bonusRegistry
     * @param EventDispatcherInterface   $eventDispatcher
     * @param LoggerInterface            $logger
     */
    public function __construct(
        ClientManipulatorInterface $clientManipulator,
        EntityManager $entityManager,
        BonusRegistry $bonusRegistry,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->clientManipulator = $clientManipulator;
        $this->entityManager = $entityManager;
        $this->bonusRegistry = $bonusRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
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
     * Load inventory
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
        if (!$user instanceof User) {
            return ['error' => "Bad Request"];
        }

        // Get and check game
        if (!isset($params['slug']) || null === $game = $this->getGame($params['slug'])) {
            return ['error' => "Bad Request"];
        }

        // Get inventory
        $repo = $this->entityManager->getRepository('BonusBundle:Inventory');
        $player = $game->getPlayerByUser($user);
        $inventory = $repo->getInventory($game, $player);

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
            'size' => ($player) ? $player->getInventorySize() : BonusConstant::INVENTORY_SIZE,
        ];
    }

    /**
     * Use bonus
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function useit(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get user
        $user = $this->clientManipulator->getClient($connection);
        if (!$user instanceof User) {
            return ['error' => "Bad Request"];
        }

        // Get and check game
        if (!isset($params['slug']) || null === $game = $this->getGame($params['slug'])) {
            return ['error' => "Bad Request"];
        }

        // Get bonus
        try {
            $repo = $this->entityManager->getRepository('BonusBundle:Inventory');
            if (!isset($params['id']) || null === $inventory = $repo->find($params['id'])) {
                return ['error' => "Bad Request"];
            }
            $bonus = $this->bonusRegistry->getBonusById($inventory->getName());
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }

        // Add options
        if (isset($params['player'])) {
            $inventory->addOption('player', $params['player']);
        }

        // Check if can use bonus now
        $player = $game->getPlayerByUser($user);
        if (!$player || !$bonus->canUseNow($game, $player)) {
            return ['msg' => "Can't use this bonus now"];
        }

        // Mutli bonus ?
        if ($repo->nbrOfCurrentBonus($game, $player) >= 1) {
            return ['msg' => "Can't use multi bonus"];
        }


        // Event
        $event = new BonusEvent($game, $player);
        $event
            ->setInventory($inventory)
            ->setBonus($bonus);
        $this->eventDispatcher->dispatch(BonusEvents::USE_IT, $event);
        $this->logger->info($game->getSlug().' - Bonus', [
            'action' => 'use_it',
            'player' => $player->getName(),
            'bonus' => $bonus->getName(),
            'inventory_id' => $inventory->getId(),
        ]);

        return ['success' => true];
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
