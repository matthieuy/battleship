<?php

namespace MatchBundle\RPC;

use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use MatchBundle\Entity\Game;
use Ratchet\ConnectionInterface;
use UserBundle\Entity\User;

/**
 * Class WaitingRpc
 * @package MatchBundle\RPC
 */
class WaitingRpc implements RpcInterface
{
    private $clientManipulator;
    private $em;
    private $pusher;

    /**
     * WaitingRpc constructor (DI)
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
        return ['error' => "Bad Request ($name)"];
    }

    /**
     * Join/Leave the game
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array ['success' => bool]
     */
    public function join(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get game and user
        try {
            /**
             * @var Game $game
             * @var User $user
             */
            list($game, $user) = $this->getGameAndUser($connection, $params);
        } catch (\Exception $e) {
            return ['success' => false];
        }

        // Refresh user
        $user = $this->em->getRepository('UserBundle:User')->find($user->getId());

        $repo = $this->em->getRepository('MatchBundle:Player');
        if ($this->getParam($params, 'join', false)) {
            // Join
            $ai = ($game->isCreator($user) && isset($params['ai'])) ? $params['ai'] : false;
            $result = $repo->joinGame($game, $user, $ai);
        } else {
            // Leave
            $playerId = ($game->isCreator($user) && isset($params['id'])) ? $params['id'] : null;
            $result = $repo->quitGame($game, $user, $playerId);
        }

        // Msg Error ?
        if (is_string($result)) {
            return [
                'msg' => $result,
                'success' => false,
            ];
        }

        // Success
        if ($result) {
            $this->pusher->push(['_call' => 'updatePlayers'], 'game.wait.topic', ['slug' => $game->getSlug()]);

            // Update homepage
            $list = $this->em->getRepository('MatchBundle:Game')->getList();
            $this->pusher->push(['games' => $list], 'homepage.topic');
        }

        return ['success' => $result];
    }

    /**
     * Change the grid size or max player
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array ['success' => bool]
     */
    public function size(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        try {
            /** @var Game $game */
            list($game, $user) = $this->getGameAndUser($connection, $params);
        } catch (\Exception $e) {
            return ['success' => false];
        }

        // request error or not Admin
        if (!isset($params['size']) || !$game->isCreator($user)) {
            return ['success' => false];
        }

        // Change size
        if ($this->getParam($params, 'type', 'size') == 'size') {
            $game->setSize($this->getParam($params, 'size', 25));
        } else {
            $game->setMaxPlayer($this->getParam($params, 'size', 4));
            $game->setSizeFromPlayers();
        }
        $this->em->flush();
        $this->pusher->push(['_call' => 'updateGame'], 'game.wait.topic', ['slug' => $game->getSlug()]);

        return ['success' => true];
    }

    /**
     * Change team
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function team(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get game, user and playerId
        try {
            /** @var Game $game */
            list($game, $user, $playerId) = $this->getGameAndUser($connection, $params);
        } catch (\Exception $e) {
            return ['success' => false];
        }

        // Change
        $repo = $this->em->getRepository('MatchBundle:Player');
        $player = $repo->getPlayer($game, $user, $playerId);
        $player->setTeam($this->getParam($params, 'team'));
        $this->em->flush();

        // Push
        $this->pusher->push(['_call' => 'updatePlayers'], 'game.wait.topic', ['slug' => $game->getSlug()]);

        return ['success' => true];
    }

    /**
     * Change color
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function color(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get game, user and playerID
        try {
            /** @var Game $game */
            list($game, $user, $playerId) = $this->getGameAndUser($connection, $params);
        } catch (\Exception $e) {
            return ['success' => false];
        }

        // Get color
        $color = str_replace('#', '', $this->getParam($params, 'color', '000000'));
        if (!preg_match('/^[a-f0-9]{6}/i', $color)) {
            return [
                'success' => false,
                'error' => 'Wrong color',
            ];
        }

        // Change color
        $repo = $this->em->getRepository('MatchBundle:Player');
        $player = $repo->getPlayer($game, $user, $playerId);
        $player->setColor($color);
        $this->em->flush();

        // Push
        $this->pusher->push(['_call' => 'updatePlayers'], 'game.wait.topic', ['slug' => $game->getSlug()]);

        return ['success' => true];
    }

    /**
     * Change position
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function position(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get game, user and playerID
        try {
            /** @var Game $game */
            list($game, $user, $playerId) = $this->getGameAndUser($connection, $params);
        } catch (\Exception $e) {
            return ['success' => false];
        }

        // Admin only
        if (!$game->isCreator($user)) {
            return ['success' => false];
        }

        // Get player
        $position = $this->getParam($params, 'position', 0);
        $repo = $this->em->getRepository('MatchBundle:Player');
        $player = $repo->getPlayer($game, $user, $playerId);

        // Move
        if ($player->getPosition() !== $position) {
            $player->setPosition($position);
            $this->em->flush();
            $this->em->refresh($game);

            // Push
            $this->pusher->push(['_call' => 'updatePlayers'], 'game.wait.topic', ['slug' => $game->getSlug()]);
        }

        return ['success' => true];
    }

    /**
     * Change options
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function options(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get game and user
        try {
            /** @var Game $game */
            list($game, $user) = $this->getGameAndUser($connection, $params);
        } catch (\Exception $e) {
            return ['success' => false];
        }

        // Admin only
        if (!$game->isCreator($user)) {
            return ['success' => false];
        }

        // Get params
        $optionName = $this->getParam($params, 'option');
        $value = $this->getParam($params, 'value');
        if ($optionName === null || $value === null || !in_array($optionName, ['penalty', 'weapon', 'bonus'])) {
            return ['success' => false];
        }

        // Convert for penalty time
        if ($optionName == 'penalty') {
            $value = max(0, min(intval($value), 72));
        }

        // Save
        $game->setOption($optionName, $value);
        $this->em->flush();

        // Push
        $this->pusher->push(['_call' => 'updateGame'], 'game.wait.topic', ['slug' => $game->getSlug()]);

        return ['success' => true];
    }
    /**
     * Get the RPC name
     * @return string
     */
    public function getName()
    {
        return 'game.wait.rpc';
    }

    /**
     * Get param value
     * @param array  $params  Params
     * @param string $name    Key name
     * @param mixed  $default Default value
     *
     * @return mixed
     */
    private function getParam(array $params, $name, $default = null)
    {
        return (key_exists($name, $params)) ? $params[$name] : $default;
    }

    /**
     * Get the game
     * @param string $slug Slug game
     *
     * @return array|Game The game or array for RPC return
     */
    private function getGame($slug)
    {
        $repo = $this->em->getRepository('MatchBundle:Game');
        $game = $repo->findOneBy(['slug' => $slug]);
        if (!$game instanceof Game) {
            return ['error' => 'Bad request'];
        } elseif ($game->getStatus() !== Game::STATUS_WAIT) {
            return ['error' => 'The game has already started'];
        }

        return $game;
    }

    /**
     * Get game, user [and playerID]
     * @param ConnectionInterface $connection
     * @param array $params
     * @return array
     * @throws \Exception
     */
    private function getGameAndUser(ConnectionInterface $connection, array $params)
    {
        // Get game
        $game = $this->getGame($params['slug']);
        if (!$game instanceof Game) {
            throw new \Exception();
        }

        // Get user
        $user = $this->clientManipulator->getClient($connection);
        if (!$user instanceof User || !isset($params['slug'])) {
            throw new \Exception();
        }

        // Get PlayerID
        $playerId = ($game->isCreator($user)) ? $this->getParam($params, 'playerId') : null;

        return [$game, $user, $playerId];
    }
}
