<?php

namespace BonusBundle\Manager;

use BonusBundle\Bonus\BonusInterface;
use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
use Doctrine\ORM\EntityManager;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Player;

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

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * BonusRegistry constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $this->bonusList[$bonus->getName()] = $bonus;

        return $this;
    }

    /**
     * Get Bonus by name
     * @param string $name
     *
     * @return BonusInterface
     * @throws \Exception
     */
    public function getBonusByName($name)
    {
        if (!isset($this->bonusList[$name])) {
            throw new \Exception("This bonus don't exist !");
        }

        return $this->bonusList[$name];
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
        if ($player->getNbBonus() >= BonusConstant::INVENTORY_SIZE) {
            return false;
        }

        // Catch or not
        $luck = rand(0, 100);
        if ($luck >=  $player->getProbability()) {
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
            ->setName($bonus->getName())
            ->setOptions($bonus->getOptions());
        $player->addBonus($inventory);
        $returnBox->setBonus($player);

        // RAZ probability
        $bonus->setProbabilityAfterCatch($player);

        // Persist
        $this->entityManager->persist($inventory);
        $this->entityManager->flush();

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

        if ($returnBox->getWeapon() === null) {
            // No weapon : add the standard increment
            $player->addProbability($increment);
        } else {
            // Weapon : calculate with weapon price
            $proba = $returnBox->getWeapon()->getPrice() / 10 + $increment;
            $player->addProbability($proba);
        }
    }
}
