<?php


namespace ChatBundle\EventListener;

use BonusBundle\BonusEvents;
use BonusBundle\Event\BonusEvent;
use ChatBundle\Entity\Message;
use Doctrine\ORM\EntityManager;
use MatchBundle\Boats;
use MatchBundle\Entity\Game;
use MatchBundle\Event\GameEvent;
use MatchBundle\Event\PenaltyEvent;
use MatchBundle\Event\TouchEvent;
use MatchBundle\MatchEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ChatListener
 * @package ChatBundle\EventListener
 */
class ChatListener implements EventSubscriberInterface
{
    private $entityManager;
    private $translator;

    /**
     * ChatListener constructor.
     * @param EntityManager       $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Get event listener
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            BonusEvents::CATCH_ONE => 'onBonusCatch',
            BonusEvents::USE_IT => 'onBonusUse',
            MatchEvents::FINISH => 'onFinish',
            MatchEvents::TOUCH => 'onTouch',
            MatchEvents::PENALTY => 'onPenalty',
        ];
    }

    /**
     * When a player catch a bonus
     * @param BonusEvent $event
     */
    public function onBonusCatch(BonusEvent $event)
    {
        $player = $event->getPlayer();
        $game = $player->getGame();
        $context = ['username' => $player->getName()];

        $this->saveMessage($game, 'system.bonus_catch', $context);
    }

    /**
     * When a player use a bonus
     * @param BonusEvent $event
     */
    public function onBonusUse(BonusEvent $event)
    {
        $player = $event->getPlayer();
        $game = $player->getGame();
        $context = ['username' => $player->getName()];

        $this->saveMessage($game, 'system.bonus_use', $context);
    }

    /**
     * When the game is over
     * @param GameEvent $event
     */
    public function onFinish(GameEvent $event)
    {
        $game = $event->getGame();

        // Get winner names
        $list = $game->getPlayersTour();
        $winnersName = [];
        foreach ($list as $player) {
            $winnersName[] = $player->getName();
        }

        $this->saveMessage($game, 'system.finish', ['list' => implode(',', $winnersName)]);
    }

    /**
     * When discovery/touch/sink
     * @param TouchEvent $event
     */
    public function onTouch(TouchEvent $event)
    {
        // unknow shooter
        if (!$event->getShooter()) {
            return;
        }

        $context = [
            'shooter' => $event->getShooter()->getName(),
            'victim' => $event->getVictim()->getName(),
        ];

        switch ($event->getType()) {
            case TouchEvent::DISCOVERY:
                $text = 'system.discovery';
                break;

            case TouchEvent::SINK:
                $boat = $event->getBoat();
                $boatName = Boats::getNameFromLength($boat[1]);
                $context['boat'] = $this->translator->trans($boatName);
                $text = 'system.sink';
                break;

            case TouchEvent::FATAL:
                $text = 'system.fatal';
                break;

            default:
                $text = 'system.touch';
                break;
        }

        $this->saveMessage($event->getGame(), $text, $context);

        return;
    }

    /**
     * When take a penalty
     * @param PenaltyEvent $event
     */
    public function onPenalty(PenaltyEvent $event)
    {
        // Player
        $context = [
            'username' => $event->getPlayer()->getName(),
        ];
        $this->saveMessage($event->getGame(), 'system.penalty', $context);

        // Victim
        if ($event->getPlayer()->getId() !== $event->getVictim()->getId()) {
            $context['victim'] = $event->getVictim()->getName();
            $this->saveMessage($event->getGame(), 'system.penalty_victim', $context);
        }
    }

    /**
     * Save a message
     * @param Game         $game Current game
     * @param string       $text The text (to translate)
     * @param array        $context (translate context)
     * @param integer|null $team (Team or null)
     */
    private function saveMessage(Game $game, $text, $context = [], $team = null)
    {
        // Create message
        $message = new Message();
        $message
            ->setGame($game)
            ->setText($text);
        if (!empty($context)) {
            $message->setContext($context);
        }
        if ($team !== null) {
            $message
                ->setChannel(Message::CHANNEL_TEAM)
                ->setRecipient($team);
        }

        // Save message
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }
}
