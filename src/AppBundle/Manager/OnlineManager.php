<?php

namespace AppBundle\Manager;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use MatchBundle\Entity\Game;
use Predis\Client;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use UserBundle\Entity\User;

/**
 * Class OnlineManager
 * @package AppBundle\Manager
 */
class OnlineManager
{
    private $clientManipulator;
    private $redis;
    private $redisKey = 'ws_list';

    /**
     * OnlineManager constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param Client                     $redis
     */
    public function __construct(ClientManipulatorInterface $clientManipulator, Client $redis)
    {
        $this->clientManipulator = $clientManipulator;
        $this->sessionList = [];
        $this->redis = $redis;
    }

    /**
     * Get session list
     * @return array
     */
    public function getSessionList()
    {
        if ($this->redis->exists($this->redisKey)) {
            return unserialize($this->redis->get($this->redisKey));
        } else {
            return [];
        }
    }

    /**
     * Get all session of a user
     * @param integer      $userId User Id
     * @param integer|null $gameId Filter by game
     *
     * @return array Sessions ID
     */
    public function getSessionsByUserId($userId, $gameId = null)
    {
        $sessionsId = [];
        foreach ($this->getSessionList() as $sessionId => $infos) {
            if ($infos['user'] instanceof User && $infos['user']->getId() == $userId && ($gameId === null || (isset($infos['game_id']) && $infos['game_id'] == $gameId))) {
                $sessionsId[] = $sessionId;
            }
        }

        return $sessionsId;
    }

    /**
     * Get sessions by game id
     * @param integer $gameId
     *
     * @return array Sessions ID
     */
    public function getSessionByGameId($gameId)
    {
        $sessionsId = [];
        foreach ($this->getSessionList() as $sessionId => $infos) {
            if (isset($infos['game_id']) && $infos['game_id'] == $gameId) {
                $sessionsId[] = $sessionId;
            }
        }

        return $sessionsId;
    }

    /**
     * Get Sessions by team
     * @param integer $gameId Game id
     * @param integer $team   Team number
     *
     * @return array Sessions ID
     */
    public function getSessionsByTeam($gameId, $team)
    {
        $sessionsId = [];
        foreach ($this->getSessionList() as $sessionId => $infos) {
            if (isset($infos['team'], $infos['game_id']) && $infos['game_id'] == $gameId && $infos['team'] === $team) {
                $sessionsId[] = $sessionId;
            }
        }

        return $sessionsId;
    }

    /**
     * On subscribe
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param Game|null           $game
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, Game $game = null)
    {
        $this->updateSessionList($connection, $topic, $game);
    }

    /**
     * On unsubscribe
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic)
    {
        $this->updateSessionList($connection, $topic, null, false);
    }

    /**
     * Update session list
     * @param ConnectionInterface $connection WS connection
     * @param Topic               $topic WS Topic
     * @param Game|null           $game
     * @param bool                $addToList Add or remove to list
     */
    private function updateSessionList(ConnectionInterface $connection, Topic $topic, Game $game = null, $addToList = true)
    {
        // Get sessionId
        $user = $this->clientManipulator->getClient($connection);
        if ($user instanceof User) {
            $client = $this->clientManipulator->findByUsername($topic, $user->getUsername());
            $sessionId = $client['connection']->WAMP->sessionId;
        } elseif (is_string($user)) {
            $sessionId = $user;
        } else {
            return;
        }

        $sessionList = $this->getSessionList();
        if ($addToList) {
            // Get infos to add in array
            $infos = [
                'topic' => $topic->getId(),
                'user' => $user,
                'date' => new \DateTime(),
            ];
            if ($game) {
                $infos['game_id'] = $game->getId();
                if ($user instanceof User) {
                    $player = $game->getPlayerByUser($user);
                    $infos['player'] = $player;
                    $infos['team'] = $player->getTeam();
                }
            }

            if (isset($client)) {
                $infos['ip'] = $client['connection']->remoteAddress;
                $infos['user_agent'] = $client['connection']->WebSocket->request->getHeader('user-agent').'';
            }

            // Add into list
            $sessionList[$sessionId] = $infos;
        } else {
            // Remove item
            unset($sessionList[$sessionId]);
        }

        // Update redis
        $this->setSessionList($sessionList);
    }

    /**
     * Set session list
     * @param array $list
     */
    private function setSessionList(array $list)
    {
        $this->redis->set($this->redisKey, serialize($list));
    }
}
