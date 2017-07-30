<?php


namespace AppBundle\Manager;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use MatchBundle\Entity\Game;
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

        if ($addToList) {
            // Get infos to add in array
            $infos = [
                'topic' => $topic->getId(),
                'user' => $user,
            ];
            if ($game) {
                $infos['game_id'] = $game->getId();
            }
            if ($user instanceof User) {
                $player = $game->getPlayerByUser($user);
                $infos['player'] = $player;
                $infos['team'] = $player->getTeam();
            }

            // Add into list
            $this->sessionList[$sessionId] = $infos;
        } else {
            // Remove item
            unset($this->sessionList[$sessionId]);
        }
    }
}
