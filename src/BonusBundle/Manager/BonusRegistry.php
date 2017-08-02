<?php

namespace BonusBundle\Manager;

use BonusBundle\Bonus\BonusInterface;
use BonusBundle\BonusConstant;
use BonusBundle\BonusEvents;
use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
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
    private $entityManager;
    private $pusher;
    private $eventDispatcher;

    /**
     * BonusRegistry constructor.
     * @param EntityManager            $entityManager
     * @param PusherInterface          $pusher
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, PusherInterface $pusher, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->pusher = $pusher;
        $this->eventDispatcher = $eventDispatcher;
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

        // Event
        $event = new BonusEvent($player, $bonus, $inventory);
        $this->eventDispatcher->dispatch(BonusEvents::CATCH_ONE, $event);


        // Use it (AI)
        if ($player->isAi() && $bonus->canUseNow($player->getGame(), $player)) {
            $game = $player->getGame();
            $this->triggerEvent(BonusConstant::WHEN_USE, $inventory, $bonus, $game, $player);
            if ($bonus->isRemove()) {
                $player->removeBonus($inventory);

                return true;
            }
        }

        // Persist
        $this->entityManager->persist($inventory);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Dispatch event with all current bonus
     * @param string    $event
     * @param Game      $game
     * @param ReturnBox $returnBox
     * @param array     $options
     */
    public function dispatchEvent($event, Game &$game, ReturnBox &$returnBox = null, array &$options = [])
    {
        // Get list of inventory
        $list = $this->entityManager->getRepository('BonusBundle:Inventory')->getActiveBonus($game);

        foreach ($list as $inventory) {
            $bonus = $this->getBonusById($inventory->getName());
            $player = $inventory->getPlayer();
            $this->triggerEvent($event, $inventory, $bonus, $game, $player, $returnBox, $options);
        }
    }

    /**
     * Trigger on event
     * @param string         $event
     * @param Inventory      $inventory
     * @param BonusInterface $bonus
     * @param Game           $game
     * @param Player         $player
     * @param ReturnBox      $returnBox
     * @param array          $options
     */
    public function triggerEvent($event, Inventory &$inventory, BonusInterface &$bonus, Game &$game, Player &$player, ReturnBox &$returnBox = null, array &$options = [])
    {
        // Call methods
        $method = BonusConstant::$triggerList[$event];
        if (method_exists($bonus, $method)) {
            // Call method
            $returnWS = call_user_func_array([$bonus, $method], [&$game, &$player, &$inventory, &$returnBox, &$options]);

            // Use it : dispatch event
            if ($event == BonusConstant::WHEN_USE) {
                $this->eventDispatcher->dispatch(BonusEvents::USE_IT, new BonusEvent($player, $bonus, $inventory));
            }

            // WS push
            if ($returnWS) {
                $this->pusher->push($returnWS, 'game.bonus.topic', ['slug' => $game->getSlug()]);
            }
        }

        // Remove bonus
        if ($bonus->isRemove()) {
            if ($returnBox) {
                $player->removeBonus($inventory);
                $returnBox->setBonus($player);
            }
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

        // Weapon : calculate with weapon price
        if ($returnBox->getWeapon()) {
            $increment += $returnBox->getWeapon()->getPrice() / 10;
        }

        $player->addProbability($increment);
    }
}
