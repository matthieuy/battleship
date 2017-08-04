<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
use ChatBundle\Entity\Message;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class RobberBonus
 * @package BonusBundle\Bonus
 */
class RobberBonus extends AbstractBonus
{
    const MIN_PERCENTAGE = 5;
    const MAX_PERCENTAGE = 50;

    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'robber';
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
        $random = rand(self::MIN_PERCENTAGE, self::MAX_PERCENTAGE);
        $percentage = $random - ($random % 5);

        return [
            'select' => BonusConstant::TARGET_ENEMY,
            'label' => $percentage.'%',
            'percent' => $percentage,
        ];
    }

    /**
     * All players can get it
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
     * On use : steal points
     * @param BonusEvent $event
     *
     * @return boolean
     */
    public function onUse(BonusEvent $event)
    {
        // Get victim
        if ($event->getPlayer()->isAi()) {
            $victim = $this->getTargetForAI($event);
        } else {
            $victimPosition = $event->getInventory()->getOption('player');
            $victim = $event->getGame()->getPlayerByPosition($victimPosition);
        }

        if (!$victim || !$victim->isAlive()) {
            return false;
        }

        // Calculate points to steal
        $points = floor($victim->getScore() * $event->getInventory()->getOption('percent') / 100);

        // Add to player
        $event->getPlayer()->addScore($points);
        $this->addScoreToWS($event->getPlayer());

        // Remove to victim
        $victim->removeScore($points);
        $this->addScoreToWS($victim);

        // Send
        $this->sendMessage($event->getGame(), $event->getPlayer(), $victim, $points);
        $this->delete();
    }

    /**
     * Send chat message
     * @param Game   $game
     * @param Player $player
     * @param Player $victim
     * @param int    $points
     */
    private function sendMessage(Game $game, Player $player, Player $victim, $points)
    {
        $message = new Message();
        $message
            ->setGame($game)
            ->setText('bonus.robber.msg')
            ->setContext([
                'user' => $player->getName(),
                'victim' => $victim->getName(),
                'points' => $points,
            ]);

        $this->entityManager->persist($message);
    }
}
