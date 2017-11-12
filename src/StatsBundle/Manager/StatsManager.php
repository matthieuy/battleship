<?php

namespace StatsBundle\Manager;

use Doctrine\ORM\EntityManager;
use MatchBundle\Entity\Game;
use StatsBundle\StatsConstants;
use UserBundle\Entity\User;

/**
 * Class StatsManager
 * @package StatsBundle\Manager
 */
class StatsManager
{
    private $entityManager;
    private $userList = [];

    /**
     * StatsManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get personal stats
     * @param User $user
     *
     * @return array
     */
    public function getPersonalStats(User $user)
    {
        $repo = $this->entityManager->getRepository('StatsBundle:Stats');
        $personal = $repo->getPersonalStats($user);

        // Global stats
        $stats = [
            'game' => [
                'create' => (isset($personal[StatsConstants::GAME_CREATE])) ? $personal[StatsConstants::GAME_CREATE] : 0,
                'start' => (isset($personal[StatsConstants::GAME_START])) ? $personal[StatsConstants::GAME_START] : 0,
                'finish' => (isset($personal[StatsConstants::GAME_FINISH])) ? $personal[StatsConstants::GAME_FINISH] : 0,
                'win' => (isset($personal[StatsConstants::GAME_WIN])) ? $personal[StatsConstants::GAME_WIN] : 0,
            ],
            'penalty' => (isset($personal[StatsConstants::PENALTY])) ? $personal[StatsConstants::PENALTY] : 0,
            'laps' => [
                'total' => (isset($personal[StatsConstants::TOUR])) ? $personal[StatsConstants::TOUR] : 0,
            ],
            'shoot' => [
                'total' => (isset($personal[StatsConstants::SHOOT])) ? $personal[StatsConstants::SHOOT] : 0,
                'touch' => (isset($personal[StatsConstants::TOUCH])) ? $personal[StatsConstants::TOUCH] : 0,
                'discovery' => (isset($personal[StatsConstants::DISCOVERY])) ? $personal[StatsConstants::DISCOVERY] : 0,
                'direction' => (isset($personal[StatsConstants::DIRECTION])) ? $personal[StatsConstants::DIRECTION] : 0,
                'sink' => (isset($personal[StatsConstants::SINK])) ? $personal[StatsConstants::SINK] : 0,
                'fatal' => (isset($personal[StatsConstants::FATAL])) ? $personal[StatsConstants::FATAL] : 0,
            ],
            'weapons' => [
                'total' => 0,
                'favorite' => 'none',
                'type' => [],
            ],
            'bonus' => [
                'total' => 0,
                'favorite' => 'none',
                'type' => [],
            ],
        ];

        // Laps avg
        $stats['laps']['avg'] = ($stats['game']['start'] === 0) ? 0 : $stats['laps']['total'] / $stats['game']['start'];

        // Shoot hit sum
        $stats['shoot']['hit'] = $stats['shoot']['touch'] + $stats['shoot']['discovery'] + $stats['shoot']['direction'] + $stats['shoot']['sink'] + $stats['shoot']['fatal'];

        // foreach stat
        foreach ($personal as $name => $value) {
            // Weapon
            if ($name == StatsConstants::WEAPON) {
                $stats['weapons']['total'] += $value['value'];
                if (!isset($stats['weapons']['type'][$value['value2']])) {
                    $stats['weapons']['type'][$value['value2']] = 0;
                }
                $stats['weapons']['type'][$value['value2']] += $value['value'];
            } elseif ($name == StatsConstants::BONUS_CATCH) {
            // Bonus
                $stats['bonus']['total'] += $value['value'];
                if (!isset($stats['bonus']['type'][$value['value2']])) {
                    $stats['bonus']['type'][$value['value2']] = 0;
                }
                $stats['bonus']['type'][$value['value2']] += $value['value'];
            }
        }

        // Favorite
        if (!empty($stats['weapons']['type'])) {
            $stats['weapons']['favorite'] = array_search(max($stats['weapons']['type']), $stats['weapons']['type']);
        }
        if (!empty($stats['bonus']['type'])) {
            $stats['bonus']['favorite'] = array_search(max($stats['bonus']['type']), $stats['bonus']['type']);
        }

        return $stats;
    }

    /**
     * Get penalty's data for graph
     * @param Game $game The game
     *
     * @return string|bool JSON Data or false if any penalty
     */
    public function getPenaltyData(Game $game)
    {
        $repo = $this->entityManager->getRepository('StatsBundle:Stats');
        $gameStats = $repo->getGameStats($game);

        // No penalty : return false
        if (!isset($gameStats[StatsConstants::PENALTY])) {
            return false;
        }

        $data = [
            'nb' => [],
            'victim' => [],
        ];

        foreach ($gameStats[StatsConstants::PENALTY] as $userId => $stat) {
            $nb = 0;
            foreach ($stat as $value) {
                // Init values
                if (!isset($data['victim'][$value['value2']]['y'])) {
                    $data['victim'][$value['value2']]['y'] = 0;
                    $data['victim'][$value['value2']]['name'] = $this->getUserName($value['value2']);
                }

                // Increment values
                $nb += $value['value'];
                $data['victim'][$value['value2']]['y'] += $value['value'];
            }
            $data['nb'][] = [
                'name' => $this->getUserName($userId),
                'y' => $nb,
            ];
        }
        $data['victim'] = array_values($data['victim']);

        return json_encode($data, 4);
    }

    /**
     * Get username from userId
     * @param integer $userId
     *
     * @return string
     */
    private function getUserName($userId)
    {
        if (!isset($this->userList[$userId])) {
            $repoUser = $this->entityManager->getRepository('UserBundle:User');
            $user = $repoUser->find($userId);
            if ($user instanceof User) {
                $this->userList[$userId] = $user->getUsername();
            } else {
                return '';
            }
        }

        return $this->userList[$userId];
    }
}
