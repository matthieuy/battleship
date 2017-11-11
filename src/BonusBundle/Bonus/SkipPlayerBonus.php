<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Event\BonusEvent;
use ChatBundle\Entity\Message;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use MatchBundle\RPC\GameRpc;
use Psr\Log\LoggerInterface;

/**
 * Class SkipPlayerBonus
 * @package BonusBundle\Bonus
 */
class SkipPlayerBonus extends AbstractBonus
{
    private $rpc;

    /**
     * SkipPlayerBonus constructor (DI)
     * @param EntityManager   $entityManager
     * @param LoggerInterface $logger
     * @param GameRpc         $rpc
     */
    public function __construct(EntityManager $entityManager = null, LoggerInterface $logger = null, GameRpc $rpc)
    {
        parent::__construct($entityManager, $logger);
        $this->rpc = $rpc;
    }

    /**
     * Get the unique id of the bonus
     * @return string
     */
    public function getId()
    {
        return 'skip_player';
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
     * @param Player $player
     *
     * @return array
     */
    public function getOptions(Player $player)
    {
        return [
            'select' => BonusConstant::TARGET_ENEMY,
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
     * On use : remove a player in current lap
     * @param BonusEvent $event
     */
    public function onUse(BonusEvent $event)
    {
        if (!$event->getPlayer()->isAi()) {
            $victimPosition = $event->getInventory()->getOption('player');
            $game = $event->getGame();
            $tour = $game->getTour();
            $key = array_search($victimPosition."", $tour);

            if (false !== $key) {
                unset($tour[$key]);
                $tour = array_values($tour);
                $game->setTour($tour);
                $victim = $game->getPlayerByPosition($victimPosition);
                $this->rpc->checkTour($game, $victim);
                $this->sendMessage($game, $event->getPlayer(), $victim);
                $this->delete();
            }
        }
    }

    /**
     * Before tour : remove a player
     * @param BonusEvent $event
     *
     * @return boolean
     */
    public function onBeforeTour(BonusEvent $event)
    {
        // Get target (player) position to remove
        if ($event->getInventory()->getPlayer()->isAi()) {
            $victim = $this->getTargetForAI($event);
            if (!$victim) {
                return false;
            }
        } else {
            $victimPosition = $event->getInventory()->getOption('player');
            $victim = $event->getGame()->getPlayerByPosition($victimPosition);
        }

        // Update teamlist
        $options = $event->getOptions();
        $teamId = $victim->getTeam();
        if (isset($options[$teamId])) {
            $key = array_search($victim->getPosition(), $options[$teamId]);
            if (false !== $key) {
                unset($options[$teamId][$key]);
                $options[$teamId] = array_values($options[$teamId]);
                $this->sendMessage($event->getGame(), $event->getPlayer(), $victim);
                $this->delete();
                $event->setOptions($options);
                $this->logger->info($event->getGame()->getSlug().' - Bonus', [
                    'name' => $this->getName(),
                    'player' => $event->getPlayer()->getName(),
                    'opts' => $options,
                ]);
            }
        }

        return true;
    }

    /**
     * Send chat message on use
     * @param Game   $game
     * @param Player $player
     * @param Player $victim
     */
    private function sendMessage(Game $game, Player $player, Player $victim)
    {
        $context = [
            'victim' => $victim->getName(),
            'user' => $player->getName(),
        ];

        $message = new Message();
        $message
            ->setGame($game)
            ->setText('bonus.skip_player.msg')
            ->setContext($context);
        $this->entityManager->persist($message);
    }
}
