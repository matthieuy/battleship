<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
use ChatBundle\Entity\Message;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class SpyBonus
 * @package BonusBundle\Bonus
 */
class SpyBonus extends AbstractBonus
{
    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'spy';
    }

    /**
     * Get the probability to catch this bonus
     * @return integer
     */
    public function getProbabilityToCatch()
    {
        return 15;
    }

    /**
     * Get options to add in inventory
     * @return array
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * Only human can get it
     * @param Player $player
     *
     * @return boolean
     */
    public function canWeGetIt(Player $player)
    {
        return (!$player->isAi());
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
     * onUse : show enemy points
     * @param BonusEvent $event
     */
    public function onUse(BonusEvent $event)
    {
        $player = $event->getPlayer();
        $players = $event->getGame()->getPlayers();
        $this->delete();

        $txt = [];
        foreach ($players as $p) {
            if ($p->getTeam() !== $player->getTeam() && $p->isAlive()) {
                $txt[] = sprintf('%s (%d/%d%%)', $p->getName(), $p->getScore(), $p->getProbability());
            }
        }

        $message = new Message();
        $message
            ->setGame($event->getGame())
            ->setChannel(Message::CHANNEL_TEAM)
            ->setRecipient($player->getTeam())
            ->setText('bonus.spy.msg')
            ->setContext([
                'user' => $player->getName(),
                'list' => implode(', ', $txt),
            ]);
        $this->entityManager->persist($message);
    }
}
