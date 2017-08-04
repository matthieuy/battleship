<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use ChatBundle\Entity\Message;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class PointTeamBonus
 * @package BonusBundle\Bonus
 */
class PointTeamBonus extends AbstractBonus
{
    // Min and Max point to get
    const MIN_POINTS = 10;
    const MAX_POINTS = 30;

    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'point.team';
    }

    /**
     * Get the probability to catch this bonus
     * @return integer
     */
    public function getProbabilityToCatch()
    {
        return 25;
    }

    /**
     * Get options to add in inventory
     * @return array
     */
    public function getOptions()
    {
        $points = rand(self::MIN_POINTS, self::MAX_POINTS);

        return [
            'label' => $points,
        ];
    }

    /**
     * All player can get it
     * @param Player $player
     *
     * @return boolean
     */
    public function canWeGetIt(Player $player)
    {
        return true;
    }

    /**
     * Can the player use this bonus now ?
     * @param Game   $game
     * @param Player $player
     *
     * @return boolean
     */
    public function canUseNow(Game $game, Player $player = null)
    {
        return ($player !== null);
    }

    /**
     * onUse : get points for all team
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory
     * @param ReturnBox $returnBox
     * @param array     $options
     *
     * @return array Data to push to player
     */
    public function onUse(Game &$game, Player &$player, Inventory $inventory, ReturnBox $returnBox = null, array &$options = [])
    {
        $team = $player->getTeam();
        $points = $inventory->getOption('label');
        $returnWS = [];

        foreach ($game->getPlayers() as $p) {
            if ($p->getTeam() == $team && $p->getLife() > 0) {
                $p->addScore($points);

                if (!$p->isAi()) {
                    $returnWS[$p->getName()] = [
                        'score' => [$p->getPosition() => $p->getScore()],
                    ];
                }
            }
        }

        $this->remove = true;
        $this->sendMessage($game, $player, $points);

        return $returnWS;
    }

    /**
     * Send chat message
     * @param Game    $game
     * @param Player  $player
     * @param integer $points
     */
    private function sendMessage(Game $game, Player $player, $points)
    {
        $message = new Message();
        $message
            ->setGame($game)
            ->setChannel(Message::CHANNEL_TEAM)
            ->setRecipient($player->getTeam())
            ->setText('bonus.point.team.msg')
            ->setContext([
                'user' => $player->getName(),
                'points' => $points,
            ]);

        $this->entityManager->persist($message);
    }
}
