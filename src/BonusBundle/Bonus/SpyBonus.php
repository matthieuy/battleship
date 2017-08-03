<?php

namespace BonusBundle\Bonus;

use BonusBundle\Entity\Inventory;
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
     * @param Game      $game
     * @param Player    $player
     * @param Inventory $inventory
     * @param ReturnBox $returnBox
     * @param array     $options
     *
     * @return array|false Data to push to player
     */
    public function onUse(Game &$game, Player &$player, Inventory $inventory, ReturnBox $returnBox = null, array &$options = [])
    {
        $this->remove = true;
        $txt = [];
        foreach ($game->getPlayers() as $p) {
            if ($p->getTeam() !== $player->getTeam() && $p->getLife() > 0) {
                $txt[] = $p->getName().' ('.$p->getScore().')';
            }
        }

        $message = new Message();
        $message
            ->setGame($game)
            ->setChannel(Message::CHANNEL_TEAM)
            ->setRecipient($player->getTeam())
            ->setText('Points : '.implode(', ', $txt));
        $this->entityManager->persist($message);

        return false;
    }
}
