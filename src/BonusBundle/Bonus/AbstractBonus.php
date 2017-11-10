<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use BonusBundle\Event\BonusEvent;
use Doctrine\ORM\EntityManager;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractBonus
 * @package BonusBundle\Bonus
 */
abstract class AbstractBonus implements BonusInterface
{
    protected $WSreturn = [];
    protected $delete = false;
    protected $entityManager;
    protected $logger;

    /**
     * AbstractBonus constructor.
     * @param EntityManager        $entityManager
     * @param LoggerInterface|null $logger
     */
    public function __construct(EntityManager $entityManager = null, LoggerInterface $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Get the unique name of the bonus
     * @return string
     */
    public function getName()
    {
        return 'bonus.'.$this->getId();
    }

    /**
     * Get the bonus description
     * @return string
     */
    public function getDescription()
    {
        return $this->getName().'.desc';
    }

    /**
     * Remove the bonus ?
     * @return boolean
     */
    public function isDelete()
    {
        return $this->delete;
    }

    /**
     * Delete this bonus
     * @return $this
     */
    public function delete()
    {
        $this->delete = true;

        return $this;
    }

    /**
     * Set the new probability after catch the bonus
     * @param Player $player
     */
    public function setProbabilityAfterCatch(Player $player)
    {
        $player->setProbability(BonusConstant::INITIAL_PROBABILITY);
    }

    /**
     * Get WebSocket return
     * @return array|false
     */
    public function getWSReturn()
    {
        if (empty($this->WSreturn)) {
            return false;
        } else {
            $result = $this->WSreturn;
            $this->WSreturn = [];

            return $result;
        }
    }

    /**
     * Get a target player for AI
     * @param BonusEvent $event
     *
     * @return Player|null
     */
    protected function getTargetForAI(BonusEvent $event)
    {
        $players = $event->getGame()->getPlayers();
        $player = $event->getPlayer();
        $target = $event->getInventory()->getOption('select');
        $list = [];

        foreach ($players as $p) {
            if ($p->getId() == $player->getId() || !$p->isAlive()) {
                continue;
            }

            if ($target == BonusConstant::TARGET_ENEMY && $p->getTeam() !== $player->getTeam()) {
                $list[] = $p;
            } elseif ($target == BonusConstant::TARGET_FRIENDS && $p->getTeam() == $player->getTeam()) {
                $list[] = $p;
            } elseif ($target == BonusConstant::TARGET_ALL) {
                $list[] = $p;
            }
        }

        shuffle($list);

        return (isset($list[0])) ? $list[0] : null;
    }

    /**
     * Add score to WebSocket return
     * @param Player $player
     */
    protected function addScoreToWS(Player $player)
    {
        if (!$player->isAi()) {
            $this->WSreturn[$player->getName()] = [
                'score' => [$player->getPosition() => $player->getScore()],
            ];
        }
    }
}
