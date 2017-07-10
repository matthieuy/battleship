<?php

namespace BonusBundle\Manager;

use BonusBundle\Bonus\BonusInterface;
use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
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
    private $entityManager;
    private $pusher;

    /**
     * BonusRegistry constructor.
     * @param EntityManager   $entityManager
     * @param PusherInterface $pusher
     */
    public function __construct(EntityManager $entityManager, PusherInterface $pusher)
    {
        $this->entityManager = $entityManager;
        $this->pusher = $pusher;
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
     * Get Bonus by id
     * @param string $id
     *
     * @return BonusInterface
     * @throws \Exception
     */
    public function getBonusById($id)
    {
        if (!isset($this->bonusList[$id])) {
            throw new \Exception("This bonus don't exist !");
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
        if ($player->getNbBonus() >= BonusConstant::INVENTORY_SIZE) {
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
     * Trigger on event
     * @param string         $event
     * @param Inventory      $inventory
     * @param BonusInterface $bonus
     * @param Game           $game
     * @param Player         $player
     * @param array          $options
     */
    public function trigger($event, Inventory &$inventory, BonusInterface &$bonus, Game &$game, Player &$player, array $options = [])
    {
        // Call methods
        $method = BonusConstant::TRIGGER_LIST[$event];
        if (method_exists($bonus, $method)) {
            // Call method
            $returnWS = call_user_func_array([$bonus, $method], [&$game, &$player, &$inventory, &$options]);

            // WS push
            if ($returnWS) {
                $this->pusher->push($returnWS, 'game.bonus.topic', ['slug' => $game->getSlug()]);
            }
        }

        // Remove bonus
        if ($bonus->isRemove()) {
            $this->entityManager->remove($inventory);
        }
        $this->entityManager->flush();
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
