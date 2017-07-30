<?php


namespace AppBundle\Manager;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use MatchBundle\Entity\Game;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

/**
 * Class OnlineManager
 * @package AppBundle\Manager
 */
class OnlineManager
{
    private $clientManipulator;
    private $sessionList;

    /**
     * OnlineManager constructor.
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function __construct(ClientManipulatorInterface $clientManipulator)
    {
        $this->clientManipulator = $clientManipulator;
        $this->sessionList = [];
    }

    /**
     * Get all session of a user
     * @param integer $userId User Id
     *
     * @return array Sessions ID
     */
    public function getSessionsByUserId($userId)
    {
        $sessionsId = [];

        foreach ($this->sessionList as $sessionId => $infos) {
            if ($infos['user']->getId() === $userId) {
                $sessionsId[] = $sessionId;
            }
        }

        return $sessionsId;
    }

    /**
     * Get sessions by team
     * @param Game    $game
     * @param integer $team Team number
     *
     * @return array Sessions ID
     */
    public function getSessionsByTeam(Game $game, $team)
    {
        $sessionsId = [];

        foreach ($this->sessionList as $sessionId => $infos) {
            if (isset($infos['team'], $infos['game_id']) && $infos['game_id'] === $game->getId() && $infos['team'] === $team) {
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
        try {
            $user = $this->clientManipulator->getClient($connection);
            $client = $this->clientManipulator->findByUsername($topic, $user->getUsername());
            $sessionId = $client['connection']->WAMP->sessionId;

            if ($addToList) {
                $infos = [
                    'user' => $user,
                    'topic' => $topic->getId(),
                ];
                if ($game && $user) {
                    $player = $game->getPlayerByUser($user);
                    if ($player) {
                        $infos = array_merge($infos, [
                            'player' => $player,
                            'team' => $player->getTeam(),
                            'game_id' => $game->getId(),
                        ]);
                    }
                }

                $this->sessionList[$sessionId] = $infos;
            } else {
                unset($this->sessionList[$sessionId]);
            }
        } catch (\Exception $e) {
        }
    }
}
