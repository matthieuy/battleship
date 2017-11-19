<?php

namespace BonusBundle\Manager;

use BonusBundle\Bonus\BonusInterface;
use BonusBundle\BonusEvents;
use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Player;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class BonusRegistry
 * @package BonusBundle\Manager
 */
class BonusRegistry
{
    /**
     * @var BonusInterface[]
     */
    protected $bonusList;
    private $eventDispatcher;
    private $logger;

    /**
     * BonusRegistry constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->bonusList = [];
    }

    /**
     * Add bonus to the registry
     * @param BonusInterface $bonus
     *
     * @return $this
     */
    public function addBonus(BonusInterface $bonus)
    {
        $this->bonusList[$bonus->getId()] = $bonus;

        return $this;
    }

    /**
     * Get Bonus list
     * @return BonusInterface[]
     */
    public function getAllBonus()
    {
        return $this->bonusList;
    }

    /**
     * Get Bonus by id
     * @param string $id
     *
     * @return BonusInterface
     * @throws \Exception
     */
    public function getBonusById($id)
    {
        if (!isset($this->bonusList[$id])) {
            throw new \Exception("This bonus don't exist ($id) !");
        }

        return $this->bonusList[$id];
    }

    /**
     * Catch a bonus (or increment proba)
     * @param Player    $player
     * @param ReturnBox $returnBox
     *
     * @return boolean Catch or not
     */
    public function catchBonus(Player &$player, ReturnBox &$returnBox)
    {
        // Inventory full
        if ($player->getNbBonus() >= $player->getInventorySize()) {
            return false;
        }

        // Catch or not
        $luck = rand(0, 100);
        if ($luck >= $player->getProbability()) {
            $this->updateProbability($player, $returnBox);

            return false;
        }

        // Get bonus catchable
        $probaPlayer = 100 - $player->getProbability();
        $random = rand(0, 100 - $probaPlayer);
        $listBonus = [];
        foreach ($this->bonusList as $bonus) {
            if ($bonus->getProbabilityToCatch() <= $probaPlayer &&
                $bonus->getProbabilityToCatch() > $random &&
                $bonus->canWeGetIt($player)
            ) {
                $listBonus[] = $bonus;
            }
        }

        // No bonus : increment proba
        if (empty($listBonus)) {
            $this->updateProbability($player, $returnBox);

            return false;
        }

        // Get bonus
        shuffle($listBonus);
        /** @var BonusInterface $bonus */
        $bonus = array_shift($listBonus);

        // Add to inventory
        $inventory = new Inventory();
        $inventory
            ->setName($bonus->getId())
            ->setOptions($bonus->getOptions($player));
        $player->addBonus($inventory);

        // RAZ probability
        $bonus->setProbabilityAfterCatch($player);

        // Event
        $game = $player->getGame();
        $event = new BonusEvent($game, $player);
        $event
            ->setBonus($bonus)
            ->setInventory($inventory);
        $this->eventDispatcher->dispatch(BonusEvents::CATCH_ONE, $event);
        $this->logger->info($game->getSlug().' - Bonus', [
            'action' => 'catch',
            'player' => $player->getName(),
            'bonus' => $bonus->getName(),
        ]);

        // Use it (AI)
        if ($player->isAi() && $bonus->canUseNow($game, $player)) {
            $this->eventDispatcher->dispatch(BonusEvents::USE_IT, $event);
        }

        return true;
    }

    /**
     * Update the probability to catch a bonus
     * @param Player    $player
     * @param ReturnBox $returnBox
     */
    protected function updateProbability(Player &$player, ReturnBox $returnBox)
    {
        // Calculate increment with life
        if ($player->getLife() >= 20) {
            $increment = 3;
        } elseif ($player->getLife() >= 10) {
            $increment = 4;
        } else {
            $increment = 5;
        }

        // Weapon : calculate with weapon price
        if ($returnBox->getWeapon()) {
            $increment += $returnBox->getWeapon()->getPrice() / 10;
        }

        $player->addProbability($increment);
        $this->logger->info($player->getGame()->getSlug().' - Bonus', [
            'action' => 'proba',
            'player' => $player->getName(),
            'incr' => $increment,
            'proba' => $player->getProbability(),
        ]);
    }
}
