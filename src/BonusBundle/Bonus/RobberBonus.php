<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
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
    const MAX_PERCENTAGE = 25;

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
        $percentage = rand(self::MIN_PERCENTAGE, self::MAX_PERCENTAGE);

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
     * onUse : steal points
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory
     * @param ReturnBox $returnBox
     * @param array     $options
     *
     * @return array|false Data to push to player or false
     */
    public function onUse(Game &$game, Player &$player, Inventory $inventory, ReturnBox $returnBox = null, array &$options = [])
    {
        // Get victim
        if ($player->isAi()) {
            $victim = $this->getTargetForAI($game, $player, $inventory->getOption('select'));
        } else {
            $victimPosition = $inventory->getOption('player');
            $victim = $game->getPlayerByPosition($victimPosition);
        }

        if (!$victim || $victim->getLife() <= 0) {
            return false;
        }
        $returnWS = [];
        $this->remove = true;

        // Calcul point to thief
        $points = floor($victim->getScore() * $inventory->getOption('percent') / 100);

        // Add to player
        $player->addScore($points);
        $this->addScoreToWS($returnWS, $player);

        // Remove to victim
        $victim->removeScore($points);
        $this->addScoreToWS($returnWS, $victim);

        // Chat
        $this->sendMessage($game, $player, $victim, $points);

        return $returnWS;
    }

    /**
     * Update the returnWS array
     * @param array  $returnWS
     * @param Player $player
     */
    private function addScoreToWS(array &$returnWS, Player $player)
    {
        if (!$player->isAi()) {
            $returnWS[$player->getName()] = [
                'score' => [$player->getPosition() => $player->getScore()],
            ];
        }
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
