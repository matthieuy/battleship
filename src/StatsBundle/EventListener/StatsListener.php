<?php

namespace StatsBundle\EventListener;

use BonusBundle\BonusEvents;
use BonusBundle\Event\BonusEvent;
use Doctrine\ORM\EntityManager;
use MatchBundle\Event\GameEvent;
use MatchBundle\Event\PenaltyEvent;
use MatchBundle\Event\PlayerEvent;
use MatchBundle\Event\TouchEvent;
use MatchBundle\Event\WeaponEvent;
use MatchBundle\MatchEvents;
use StatsBundle\StatsConstants;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class StatsListener
 * @package StatsBundle\EventListener
 */
class StatsListener implements EventSubscriberInterface
{
    private $repo;

    /**
     * StatsListener constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->repo = $entityManager->getRepository('StatsBundle:Stats');
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            MatchEvents::CREATE => 'onCreate',
            MatchEvents::LAUNCH => 'onLaunch',
            MatchEvents::FINISH => 'onFinish',
            MatchEvents::SHOOT => 'onShoot',
            MatchEvents::PENALTY => 'onPenalty',
            MatchEvents::NEW_TOUR => 'onNewTour',
            MatchEvents::TOUCH => 'onTouch',
            MatchEvents::WEAPON => 'onWeapon',
            BonusEvents::CATCH_ONE => 'onCatchBonus',
        ];
    }

    /**
     * On create game : increment create author
     * @param GameEvent $event
     */
    public function onCreate(GameEvent $event)
    {
        $game = $event->getGame();
        $this->repo->increment(StatsConstants::GAME_CREATE, $game->getCreator()->getId());
    }

    /**
     * On finish game
     * @param GameEvent $event
     * @param string    $eventName
     */
    public function onFinish(GameEvent $event, $eventName)
    {
        $game = $event->getGame();
        $players = $game->getPlayersTour();
        foreach ($players as $player) {
            if ($player->isAlive()) {
                $this->repo->increment(StatsConstants::GAME_WIN, $player->getUser()->getId());
            }
        }

        $this->onLaunch($event, $eventName);
    }

    /**
     * On Launch/Finish game
     * @param GameEvent $event
     * @param string    $eventName
     *
     * @return bool
     */
    public function onLaunch(GameEvent $event, $eventName)
    {
        // Get stat name
        $statNames = [
            MatchEvents::LAUNCH => StatsConstants::GAME_START,
            MatchEvents::FINISH => StatsConstants::GAME_FINISH,
        ];
        if (!isset($statNames[$eventName])) {
            return false;
        }
        $statName = $statNames[$eventName];

        // Increment all players
        $game = $event->getGame();
        foreach ($game->getPlayers() as $player) {
            $this->repo->increment($statName, $player->getUser()->getId());
        }
    }

    /**
     * On penalty
     * @param PenaltyEvent $event
     */
    public function onPenalty(PenaltyEvent $event)
    {
        $userId = $event->getPlayer()->getUser()->getId();
        $victimId = $event->getVictim()->getUser()->getId();

        $this->repo->increment(StatsConstants::PENALTY, $userId, $event->getGame(), $victimId);
    }

    /**
     * On new tour
     * @param GameEvent $event
     */
    public function onNewTour(GameEvent $event)
    {
        $game = $event->getGame();
        $players = $game->getPlayers();

        foreach ($players as $player) {
            if ($player->isAlive()) {
                $this->repo->increment(StatsConstants::TOUR, $player->getUser()->getId(), $game);
            }
        }
    }

    /**
     * On touch
     * @param TouchEvent $event
     *
     * @return bool
     */
    public function onTouch(TouchEvent $event)
    {
        // Penalty or no shooter
        if (!$event->getShooter()) {
            return false;
        }

        // Type of touch
        switch ($event->getType()) {
            case TouchEvent::TOUCH:
                $stat = StatsConstants::TOUCH;
                break;
            case TouchEvent::DISCOVERY:
                $stat = StatsConstants::DISCOVERY;
                break;
            case TouchEvent::DIRECTION:
                $stat = StatsConstants::DIRECTION;
                break;
            case TouchEvent::SINK:
                $stat = StatsConstants::SINK;
                break;
            case TouchEvent::FATAL:
                $stat = StatsConstants::FATAL;
                break;
            default:
                $stat = null;
        }
        if (!$stat) {
            return false;
        }

        $shooterId = $event->getShooter()->getUser()->getId();
        $victimId = $event->getVictim()->getUser()->getId();

        $this->repo->increment($stat, $shooterId, $event->getGame(), $victimId);
    }

    /**
     * On catch bonus
     * @param BonusEvent $event
     */
    public function onCatchBonus(BonusEvent $event)
    {
        $userId = $event->getPlayer()->getUser()->getId();
        $bonusId = $event->getBonus()->getId();

        $this->repo->increment(StatsConstants::BONUS_CATCH, $userId, $event->getGame(), $bonusId);
    }

    /**
     * On weapon use
     * @param WeaponEvent $event
     */
    public function onWeapon(WeaponEvent $event)
    {
        $userId = $event->getPlayer()->getUser()->getId();
        $weaponName = $event->getWeapon()->getName();

        $this->repo->increment(StatsConstants::WEAPON, $userId, $event->getGame(), $weaponName);
    }

    /**
     * On shoot
     * @param PlayerEvent $event
     */
    public function onShoot(PlayerEvent $event)
    {
        $userId = $event->getPlayer()->getUser()->getId();
        $this->repo->increment(StatsConstants::SHOOT, $userId, $event->getGame());
    }
}
