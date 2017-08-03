<?php

namespace BonusBundle\Bonus;

use BonusBundle\BonusConstant;
use Doctrine\ORM\EntityManager;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Class AbstractBonus
 * @package BonusBundle\Bonus
 */
abstract class AbstractBonus implements BonusInterface
{
    protected $remove = false;
    protected $entityManager;

    /**
     * AbstractBonus constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager = null)
    {
        $this->entityManager = $entityManager;
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
    public function isRemove()
    {
        return $this->remove;
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
     * Get a target player for AI
     * @param Game    $game
     * @param Player  $player
     * @param integer $target
     *
     * @return Player|null
     */
    protected function getTargetForAI(Game $game, Player $player, $target)
    {
        $list = [];
        foreach ($game->getPlayers() as $p) {
            if ($p->getId() == $player->getId() || $p->getLife() <= 0) {
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
}
