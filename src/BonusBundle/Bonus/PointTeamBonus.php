<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
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
     * onUse : get points
     * @param BonusEvent $event
     */
    public function onUse(BonusEvent $event)
    {
        $team = $event->getPlayer()->getTeam();
        $points = $event->getInventory()->getOption('label');
        $players = $event->getGame()->getPlayers();

        foreach ($players as $p) {
            if ($p->getTeam() == $team && $p->isAlive()) {
                $p->addScore($points);
                $this->addScoreToWS($p);
            }
        }

        $this->sendMessage($event->getPlayer(), $points);
        $this->delete();
    }

    /**
     * Send chat message
     * @param Player  $player
     * @param integer $points
     */
    private function sendMessage(Player $player, $points)
    {
        $message = new Message();
        $message
            ->setGame($player->getGame())
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
