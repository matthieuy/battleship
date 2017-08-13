<?php

namespace MatchBundle\RPC;

use BonusBundle\BonusConstant;
use BonusBundle\BonusEvents;
use BonusBundle\Event\BonusEvent;
use BonusBundle\Manager\BonusRegistry;
use BonusBundle\Manager\WeaponRegistry;
use BonusBundle\Weapons\WeaponInterface;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use MatchBundle\Box\Box;
use MatchBundle\Box\ReturnBox;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use MatchBundle\Event\GameEvent;
use MatchBundle\Event\PenaltyEvent;
use MatchBundle\Event\PlayerEvent;
use MatchBundle\Event\TouchEvent;
use MatchBundle\Event\WeaponEvent;
use MatchBundle\ImagesConstant;
use MatchBundle\MatchEvents;
use MatchBundle\PointsConstant;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UserBundle\Entity\User;

/**
 * Class GameRpc
 * @package MatchBundle\RPC
 */
class GameRpc implements RpcInterface
{
    private $clientManipulator;
    private $em;
    private $eventDispatcher;
    private $weaponRegistry;
    private $bonusRegistry;
    private $pusher;

    /** @var ReturnBox */
    private $returnBox;

    /**
     * GameRpc constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManager              $em
     * @param PusherInterface            $pusher
     * @param EventDispatcherInterface   $eventDispatcher
     * @param WeaponRegistry             $weaponRegistry
     * @param BonusRegistry              $bonusRegistry
     */
    public function __construct(
        ClientManipulatorInterface $clientManipulator,
        EntityManager $em,
        PusherInterface $pusher,
        EventDispatcherInterface $eventDispatcher,
        WeaponRegistry $weaponRegistry,
        BonusRegistry $bonusRegistry
    ) {
        $this->clientManipulator = $clientManipulator;
        $this->em = $em;
        $this->pusher = $pusher;
        $this->eventDispatcher = $eventDispatcher;
        $this->weaponRegistry = $weaponRegistry;
        $this->bonusRegistry = $bonusRegistry;
    }

    /**
     * Request on a non exist method
     * @param string $name      Method name
     * @param array  $arguments Arguments
     *
     * @return array Error
     */
    public function __call($name, $arguments)
    {
        return ['error' => "Bad request"];
    }

    /**
     * Load the game
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function load(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get user (refresh)
        $user = $this->clientManipulator->getClient($connection);
        if ($user instanceof User) {
            $user = $this->em->find('UserBundle:User', $user->getId());
        } else {
            $user = null;
        }

        // Get game
        if (!isset($params['slug']) || null === $game = $this->getGame($params['slug'])) {
            return ['error' => "Bad request"];
        }

        // Global infos
        $infos = [
            'size' => $game->getSize(),
            'boxSize' => ($user) ? $user->getOption('boxSize', 20) : 20,
            'tour' => $game->getTour(),
            'options' => $game->getOptions(),
            'players' => [],
            'chrono' => $game->getChrono(),
        ];
        if ($game->getStatus() == Game::STATUS_END) {
            $infos['finished'] = true;
        }

        // Players list
        $me = null;
        foreach ($game->getPlayers() as $player) {
            $infoPlayer = [
                'name' => $player->getName(),
                'position' => $player->getPosition(),
                'life' => $player->getLife(),
                'color' => $player->getColor(),
                'ai' => $player->isAi(),
                'team' => $player->getTeam(),
            ];

            // Himself
            if ($user && $player->getUser()->getId() === $user->getId()) {
                $infoPlayer = array_merge($infoPlayer, [
                    'me' => true,
                    'score' => $player->getScore(),
                    'nbBonus' => $player->getNbBonus(),
                ]);
                $me = $player;
            }
            $infos['players'][$player->getPosition()] = $infoPlayer;
        }

        // Get grid
        $infos['grid'] = $this->getGrid($game, $me);

        // Penalty
        if ($this->checkPenalty($game)) {
            $this->doPenalty($game, $user);
        }

        return $infos;
    }

    /**
     * Shoot (RPC call)
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function shoot(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get user
        $user = $this->clientManipulator->getClient($connection);
        if (!$user instanceof User) {
            return ['success' => false];
        }

        // Get and check game
        if (!isset($params['slug']) || null === $game = $this->getGame($params['slug'])) {
            return ['error' => "Bad request"];
        }
        if ($game->getStatus() !== Game::STATUS_RUN) {
            return ['error' => "gameover"];
        }

        // Get and check coord
        $x = (isset($params['x'])) ? intval($params['x']) : null;
        $y = (isset($params['y'])) ? intval($params['y']) : null;
        if ($x === null || $y === null || $x < 0 || $y < 0 || $x >= $game->getSize() || $y >= $game->getSize()) {
            return ['error' => "error_shoot"];
        }

        // Get player
        $player = $this->getPlayer($game, null, $user->getId());
        if (!$player instanceof Player) {
            return $player;
        } elseif (!in_array($player->getPosition(), $game->getTour())) {
            return ['error' => 'error_tour'];
        }

        // Weapons
        $this->returnBox = new ReturnBox();
        try {
            if (isset($params['weapon'])) {
                $weapon = $this->weaponRegistry->getWeapon($params['weapon']);
            } else {
                $weapon = null;
            }
        } catch (\Exception $e) {
            $weapon = null;
        }
        $weaponRotate = ($weapon && isset($params['rotate'])) ? $params['rotate'] : 0;

        // Check box
        $box = $game->getBox($x, $y);
        if (!$box->isEmpty() && $weapon === null) {
            // Already shoot
            if ($box->isAlreadyShoot()) {
                return ['error' => 'already_shoot'];
            }

            // Himself
            if ($box->isOwn($player)) {
                return ['error' => 'own_boat'];
            }

            // Same team
            if ($box->isSameTeam($player)) {
                return ['error' => 'team_shoot'];
            }
        }

        // Get boxes to shoot
        $boxList = $this->getBoxesToShoot($game, $player, $x, $y, $weapon, $weaponRotate);

        // Do fire
        foreach ($boxList as $i => $box) {
            $result = $this->doFire($game, $box, $player, $i == 0);

            // Error ?
            if (is_array($result)) {
                return $result;
            }
        }

        // Bonus
        if ($game->getOption('bonus', false) && !$this->returnBox->isDoTouch()) {
            $this->bonusRegistry->catchBonus($player, $this->returnBox);
        }

        // Last shoot
        $game->setLastShoot();

        // Next tour
        $this->nextTour($game, $player);

        // Return and push
        $return = $this->returnBox->getReturnBox($game, $player);
        $this->pusher->push($return, 'game.run.topic', ['slug' => $game->getSlug()]);
        $this->pusher->push([], 'game.score.topic', ['slug' => $game->getSlug()]);
        $this->eventDispatcher->dispatch(MatchEvents::CHANGE_TOUR, new GameEvent($game));
        $this->eventDispatcher->dispatch(MatchEvents::SHOOT, new PlayerEvent($game, $player));

        // Save
        $this->em->flush();

        return ['success' => true];
    }

    /**
     * Get RPC name
     * @return string
     */
    public function getName()
    {
        return 'game.run.rpc';
    }

    /**
     * Get the game
     * @param string $slug Slug of game
     *
     * @return Game|null
     */
    private function getGame($slug)
    {
        $repo = $this->em->getRepository('MatchBundle:Game');
        $game = $repo->findOneBy(['slug' => $slug]);
        if (!$game || $game->getStatus() == Game::STATUS_WAIT) {
            return null;
        }
        $this->em->refresh($game);

        return $game;
    }

    /**
     * Get the grid to show for a specific player
     * @param Game        $game
     * @param Player|null $player
     *
     * @return array
     */
    private function getGrid(Game $game, Player $player = null)
    {
        $boxList = [];
        $grid = $game->getGrid();

        $finished = ($game->getStatus() == Game::STATUS_END);

        // Show all box
        $debug = false;

        foreach ($grid as $y => $row) {
            foreach ($row as $x => $data) {
                // Get box
                $box = $game->getBox($x, $y);

                // Can we show this box ?
                $showAnyway = ($finished || $debug);
                if ($showAnyway) {
                    $show = !$box->isEmpty();
                } else {
                    $show = false;
                    if ($box->isAlreadyShoot()) {
                        $show = true;
                    } elseif (!$player) {
                        continue;
                    } elseif ($box->isSameTeam($player)) {
                        $show = true;
                    }
                }

                // Add the box to list
                if ($show) {
                    $boxList[] = $box->getInfoToReturn($player, $showAnyway);
                }
            }
        }

        return $boxList;
    }

    /**
     * Get player
     * @param Game         $game The game
     * @param integer|null $position The player position (or null to search byUserId)
     * @param integer|null $userId The userId (or null to search byPosition)
     *
     * @return Player|array The player or error
     */
    private function getPlayer(Game $game, $position = null, $userId = null)
    {
        $player = null;
        foreach ($game->getPlayers() as $p) {
            if (($position !== null && $p->getPosition() === $position) ||
                ($userId !== null && $p->getUser()->getId() === $userId)
            ) {
                $player = $p;
                break;
            }
        }

        if (!$player) {
            return ['error' => 'error_player404'];
        }

        return $player;
    }

    /**
     * Get list of boxes to shoot
     * @param Game            $game The game
     * @param Player          $player The shooter
     * @param integer         $x X coord
     * @param integer         $y Y coord
     * @param WeaponInterface $weapon The weapon to use or null
     * @param integer         $weaponRotate Rotate of weapon
     *
     * @return Box[] List of box
     */
    private function getBoxesToShoot(Game $game, Player $player, $x, $y, WeaponInterface $weapon = null, $weaponRotate = 0)
    {
        $this->returnBox->setDoTouch(false);
        $this->returnBox->setWeapon(null);
        $boxList = [];

        if (!$weapon) {
            // No weapon
            $boxList[] = $game->getBox($x, $y);
        } else {
            // Price
            $price = $weapon->getPrice();
            if ($price > $player->getScore()) {
                $boxList[] = $game->getBox($x, $y);
            } else {
                $player->removeScore($price);
                $this->returnBox->setWeapon($weapon);
                $boxList = $weapon->getBoxes($game, $x, $y, $weaponRotate);

                $event = new WeaponEvent($game, $player, $weapon);
                $this->eventDispatcher->dispatch(MatchEvents::WEAPON, $event);
            }
        }

        // Bonus trigger
        $event = new BonusEvent($game, $player, $this->returnBox, $boxList);
        $this->eventDispatcher->dispatch(BonusEvents::GET_BOX, $event);
        $boxList = $event->getOptions();

        return $boxList;
    }

    /**
     * Do fire on a box
     * @param Game    $game The game
     * @param Box     $box The box to shoot
     * @param Player  $shooter The shooter
     * @param boolean $firstBox The first box of shoot
     *
     * @return bool|array Shoot done or error
     */
    private function doFire(Game &$game, Box &$box, Player $shooter = null, $firstBox = false)
    {
        // Use weapon : add score on first shoot
        if ($firstBox && $this->returnBox->getWeapon()) {
            $box->setScore($shooter);
        }

        // Some check
        if (($shooter && $box->isSameTeam($shooter)) || $box->isAlreadyShoot() || $box->isOffzone($game->getSize())) {
            return false;
        }

        // Empty box
        if ($box->isEmpty()) {
            $box
                ->setImg(ImagesConstant::MISS)
                ->setShooter($shooter);
            $this->returnBox->addBox($game, $box);

            return true;
        }

        // Touch
        return $this->touch($game, $box, $shooter);
    }

    /**
     * Touch a boat
     * @param Game    $game
     * @param Box     $box
     * @param Player  $shooter
     * @param boolean $penalty Penalty (don't scoring)
     *
     * @return array|bool Error or true
     */
    private function touch(Game &$game, Box $box, Player $shooter = null, $penalty = false)
    {
        // Update box
        $box->setShooter($shooter);

        // Victim
        $victim = $this->getPlayer($game, $box->getPlayer());
        if (!$victim instanceof Player) {
            return $victim;
        }

        // Get the boat
        $victimBoats = $victim->getBoats();
        $boatIndex = array_search($box->getBoat(), array_column($victimBoats, 0)); // Index 0 => boat number
        if ($boatIndex === false || !isset($victimBoats[$boatIndex])) {
            return ['error' => 'error_boat404'];
        }
        $boat = $victimBoats[$boatIndex];

        // Remove a life and add a touch to the boat
        $victim->removeLife();
        $boat[2]++;
        $isSink = ($boat[2] >= $boat[1]); // Touch >= length

        // Prepare event
        $eventTouch = new TouchEvent($game);
        $eventTouch->setBoat($boat);

        // Calcul points
        if (!$victim->isAlive()) { // Fatal
            $points = PointsConstant::SCORE_FATAL;
            $eventTouch->setType(TouchEvent::FATAL);
        } elseif ($boat[2] == 1) { // Discovery
            $points = PointsConstant::SCORE_DISCOVERY;
            $eventTouch->setType(TouchEvent::DISCOVERY);
        } elseif ($boat[2] == 2) { // Direction
            $points = PointsConstant::SCORE_DIRECTION;
            $eventTouch->setType(TouchEvent::DIRECTION);
        } elseif ($isSink) { // Sink
            $points = PointsConstant::SCORE_SINK;
            $eventTouch->setType(TouchEvent::SINK);
        } else {
            $points = PointsConstant::SCORE_TOUCH;
            $eventTouch->setType(TouchEvent::TOUCH);
        }

        if (!$penalty && $shooter) {
            // Bonus event
            $options = ['points' => $points];
            $eventBonus = new BonusEvent($game, $shooter, $this->returnBox, $options);
            $this->eventDispatcher->dispatch(BonusEvents::BEFORE_SCORE, $eventBonus);

            // Add score
            $shooter->addScore($points);
            if (!$shooter->isAi()) {
                $box->setScore($shooter);
            }
        }

        // Save
        $victimBoats[$boatIndex] = $boat;
        $box
            ->setSink($isSink)
            ->setLife($victim);
        $victim->setBoats($victimBoats);
        $this->returnBox->addBox($game, $box);
        $this->returnBox->setDoTouch();

        // Dispatch event
        $eventTouch
            ->setShooter($shooter)
            ->setVictim($victim);
        $this->eventDispatcher->dispatch(MatchEvents::TOUCH, $eventTouch);

        return true;
    }

    /**
     * Next tour
     * @param Game   $game The game
     * @param Player $fromPlayer The player just play before
     *
     * @return bool Game is over
     */
    private function nextTour(Game &$game, Player $fromPlayer)
    {
        // Get players and teams alive
        $teamsList = $this->checkFinish($game);
        if ($teamsList === false) {
            return false;
        }

        // Bonus Event
        $event = new BonusEvent($game, $fromPlayer, $this->returnBox, $teamsList);
        $this->eventDispatcher->dispatch(BonusEvents::BEFORE_TOUR, $event);
        $teamsList = $event->getOptions();

        // Next
        $tour = $game->getTour();
        $maxTeam = max(array_keys($teamsList));

        do {
            $okTour = true;

            // Remove player just play before
            if (($key = array_search($fromPlayer->getPosition(), $tour)) !== false) {
                unset($tour[$key]);
                $tour = array_values($tour);
            }

            // If all player's of the team played => next team
            if (count($tour) == 0) {
                $isNewTour = false;
                $team = $fromPlayer->getTeam();
                do {
                    if ($team > $maxTeam) {
                        $team = 0;
                        $isNewTour = true;
                    } else {
                        $team++;
                    }
                    $okTeam = (isset($teamsList[$team]) && !empty($teamsList[$team]));
                } while (!$okTeam);
                $tour = $teamsList[$team];
                $game->setTour($tour);

                // Event
                $event = new GameEvent($game);
                $this->eventDispatcher->dispatch(MatchEvents::NEXT_TOUR, $event);
                if ($isNewTour) {
                    $this->eventDispatcher->dispatch(MatchEvents::NEW_TOUR, $event);
                }
            }

            // AI
            if (isset($tour[0])) {
                $player = $game->getPlayerByPosition($tour[0]);
                if ($player && $player->isAi()) {
                    $okTour = false;
                    $fromPlayer = $player;
                    $this->shootAI($game, $fromPlayer);

                    // Check if AI win
                    if ($this->checkFinish($game) === false) {
                        return false;
                    }
                }
            }
        } while (!$okTour);

        return true;
    }

    /**
     * Simulate shoot
     * @param Game   $game The Game
     * @param Player $ai   The AI player
     */
    private function shootAI(Game &$game, Player $ai)
    {
        // Create empty grid (easier to manipulate)
        $grid = $game->getGrid();
        $gridSize = $game->getSize();
        for ($y=0; $y < $gridSize; $y++) {
            for ($x=0; $x < $gridSize; $x++) {
                if (!isset($grid[$y][$x])) {
                    $grid[$y][$x] = [];
                }
            }
        }

        $selectShoot = false;
        $random = range(1, 4);
        shuffle($random);

        // Before random shoot, search any touch for hit nearly
        for ($pass=0; $pass < 2; $pass++) {
            for ($y=0; $y < $gridSize && !$selectShoot; $y++) {
                for ($x=0; $x < $gridSize && !$selectShoot; $x++) {
                    $box = (isset($grid[$y][$x])) ? $grid[$y][$x] : [];

                    // If there is any touch on this box => go to next box
                    if (empty($box) || !isset($box['explose'])) {
                        continue;
                    }

                    // found a touch but himself or same team
                    if (isset($box['team']) && $box['team'] == $ai->getTeam()) {
                        continue;
                    }

                    // Get all near box (or false)
                    $boxTop = ($y > 0 && isset($grid[$y-1][$x])) ? $grid[$y-1][$x] : false;
                    $boxRight = ($x < $gridSize-1 && isset($grid[$y][$x + 1])) ? $grid[$y][$x+1] : false;
                    $boxBottom = ($y < $gridSize-1 && isset($grid[$y+1][$x])) ? $grid[$y+1][$x] : false;
                    $boxLeft = ($x > 0 && isset($grid[$y][$x-1])) ? $grid[$y][$x-1] : false;

                    // Can we shoot on there box ?
                    $canShootTop = ($boxTop !== false && !isset($boxTop['shoot']) && (!isset($boxTop['team']) || $boxTop['team'] != $ai->getTeam()));
                    $canShootRight = ($boxRight !== false && !isset($boxRight['shoot']) && (!isset($boxRight['team']) || $boxRight['team'] != $ai->getTeam()));
                    $canShootBottom = ($boxBottom !== false && !isset($boxBottom['shoot']) && (!isset($boxBottom['team']) || $boxBottom['team'] != $ai->getTeam()));
                    $canShootLeft = ($boxLeft !== false && !isset($boxLeft['shoot']) && (!isset($boxLeft['team']) || $boxLeft['team'] != $ai->getTeam()));

                    $sx = $x;
                    $sy = $y;

                    // First pass : check 2 align explose
                    if ($pass == 0) {
                        $exploseTop = ($boxTop !== false && isset($boxTop['explose']));
                        $exploseRight = ($boxRight !== false && isset($boxRight['explose']));
                        $exploseBottom = ($boxBottom !== false && isset($boxBottom['explose']));
                        $exploseLeft = ($boxLeft !== false && isset($boxLeft['explose']));
                    } else {
                        // Other pass : bypass explose check
                        $exploseTop = true;
                        $exploseRight = true;
                        $exploseBottom = true;
                        $exploseLeft = true;
                    }

                    // Calculate new coordinate
                    for ($i=0; $i < count($random) && !$selectShoot; $i++) {
                        switch ($random[$i]) {
                            // Shot on top
                            case 1:
                                if ($canShootTop && $exploseBottom) {
                                    $sy = $y-1;
                                    $selectShoot = true;
                                }
                                break;

                            // Shot on right
                            case 2:
                                if ($canShootRight && $exploseLeft) {
                                    $sx = $x+1;
                                    $selectShoot = true;
                                }
                                break;

                            // Shot on bottom
                            case 3:
                                if ($canShootBottom && $exploseTop) {
                                    $sy = $y+1;
                                    $selectShoot = true;
                                }
                                break;

                            // Shot on left
                            case 4:
                                if ($canShootLeft && $exploseRight) {
                                    $sx = $x-1;
                                    $selectShoot = true;
                                }
                                break;
                        }
                    }
                }
            }
        }

        // Any intelligent shot found : random shot
        if (!$selectShoot) {
            $try = 0;

            do {
                // Random position
                $sx = mt_rand(0, $gridSize-1);
                $sy = mt_rand(0, $gridSize-1);
                $box = $grid[$sy][$sx];

                // Check
                $alreadyShoot = (isset($box['shoot']));
                $owner = (isset($box['team']) && $box['team'] == $ai->getTeam());

                // Isolate shoot (limit try against infinite while)
                if ($try < 50) {
                    $canShootTop = ($sy > 0 && isset($grid[$sy-1][$sx]['shoot']));
                    $canShootRight = ($sx < $gridSize-1 && isset($grid[$sy][$sx+1]['shoot']));
                    $canShootBottom = ($sy < $gridSize-1 && isset($grid[$sy+1][$sx]['shoot']));
                    $canShootLeft = ($sx > 0 && isset($grid[$sy][$sx-1]['shoot']));
                    $isolate = ($canShootTop && $canShootRight && $canShootBottom && $canShootLeft);
                } else {
                    $isolate = false;
                }
            } while ($alreadyShoot || $owner || $isolate);

            // Weapon
            $weapon = $this->weaponRegistry->getWeaponForAI($ai->getScore());
        } else {
            $weapon = null;
        }

        // Fire !!!
        $boxes = $this->getBoxesToShoot($game, $ai, $sx, $sy, $weapon);
        foreach ($boxes as $i => $box) {
            $this->doFire($game, $box, $ai, $i == 0);
        }
        $this->eventDispatcher->dispatch(MatchEvents::SHOOT, new PlayerEvent($game, $ai));

        // Bonus
        if ($game->getOption('bonus', false) && !$this->returnBox->isDoTouch()) {
            $this->bonusRegistry->catchBonus($ai, $this->returnBox);
        }
    }

    /**
     * Check if is penalty
     * @param Game $game
     *
     * @return boolean
     */
    private function checkPenalty(Game $game)
    {
        // Penalty only on running game and penalty enabled
        if ($game->getStatus() !== Game::STATUS_RUN || !$game->getOption('penalty', 0)) {
            return false;
        }

        return (time() >= $game->getChrono());
    }

    /**
     * Do a penalty
     * @param Game $game
     * @param User $user
     */
    private function doPenalty(Game &$game, User $user = null)
    {
        $game->setLastShoot();

        // Get player
        $players = $game->getPlayersTour();
        if (!$players || !isset($players[0])) {
            return;
        }
        $player = $players[0];
        $event = new PenaltyEvent($game, $player);

        // Get one player on the same team
        $players = $game->getPlayersByTeam($player->getTeam());
        shuffle($players);
        foreach ($players as $p) {
            if ($p->isAlive() && !$p->isAi()) {
                $player = $p;
                $event->setVictim($player);
                break;
            }
        }

        // Get boats and select a boat without touch (if it is possible)
        $boats = array_reverse($player->getBoats());
        $selectBoat = null;
        foreach ($boats as $boat) {
            if ($boat[2] == 0) {
                $selectBoat = $boat;
                break;
            }
        }

        // Parse the grid
        $grid = $game->getGrid();
        $sizeGrid = $game->getSize();
        for ($x = 0; $x < $sizeGrid; $x++) {
            for ($y = 0; $y < $sizeGrid; $y++) {
                if ($selectBoat && isset($grid[$y][$x]['boat']) && $grid[$y][$x]['boat'] == $selectBoat[0]) {
                    // Selected boat => the same boat number
                    break 2;
                } elseif (!$selectBoat && (isset($grid[$y][$x]['player']) && $grid[$y][$x]['player'] == $player->getPosition()) && !isset($grid[$y][$x]['shoot'])) {
                    // Else the boat player
                    break 2;
                }
            }
        }

        // Get box and reset score
        $this->returnBox = new ReturnBox();
        $box = $game->getBox($x, $y);
        $player->setScore(0)->setProbability(BonusConstant::INITIAL_PROBABILITY);
        $box->setScore($player);

        // Do stuff
        $this->touch($game, $box, $player, true);
        $this->nextTour($game, $player);

        // Event
        $this->eventDispatcher->dispatch(MatchEvents::PENALTY, $event);

        // Return
        $player = $this->getPlayer($game, null, $user->getId());
        $return = $this->returnBox->getReturnBox($game, $player);
        $this->pusher->push($return, 'game.run.topic', ['slug' => $game->getSlug()]);

        // Persist
        $this->em->flush();
    }

    /**
     * Check if game is over and get Teams alive
     * @param Game $game
     *
     * @return array|bool teams list or false (if over)
     */
    private function checkFinish(Game &$game)
    {
        // Get players and teams alive
        $teamsList = [];
        foreach ($game->getPlayers() as $player) {
            if ($player->isAlive()) {
                if (!isset($teamsList[$player->getTeam()])) {
                    $teamsList[$player->getTeam()] = [];
                }
                $teamsList[$player->getTeam()][] = $player->getPosition();
            }
        }

        // Game is over
        if (count($teamsList) == 1) {
            // Update game
            $game
                ->setStatus(Game::STATUS_END)
                ->setLastShoot();

            // Event
            $event = new GameEvent($game);
            $this->eventDispatcher->dispatch(MatchEvents::FINISH, $event);

            return false;
        }

        return $teamsList;
    }
}
