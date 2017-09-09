<?php

namespace MatchBundle\RPC;

use BonusBundle\BonusConstant;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use MatchBundle\Boats;
use MatchBundle\Entity\Game;
use MatchBundle\Event\GameEvent;
use MatchBundle\MatchEvents;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;
use UserBundle\Entity\User;

/**
 * Class LaunchRpc
 * @package MatchBundle\RPC
 */
class LaunchRpc implements RpcInterface
{
    private $clientManipulator;
    private $em;
    private $pusher;
    private $eventDispatcher;
    private $translator;

    /**
     * LaunchRpc constructor (DI)
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManager              $em
     * @param PusherInterface            $pusher
     * @param EventDispatcherInterface   $eventDispatcher
     * @param TranslatorInterface        $translator
     */
    public function __construct(
        ClientManipulatorInterface $clientManipulator,
        EntityManager $em,
        PusherInterface $pusher,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator
    ) {
        $this->clientManipulator = $clientManipulator;
        $this->em = $em;
        $this->pusher = $pusher;
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
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
     * Launch the game
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function run(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get user
        $user = $this->clientManipulator->getClient($connection);
        if (!$user instanceof User || !isset($params['slug'])) {
            return ['error' => "Bad request"];
        }

        // Get game
        $game = $this->getGame($user, $params['slug']);
        if (!$game instanceof Game) {
            return $game;
        }

        // Generate grid
        try {
            $this->generateGrid($game);
            $this->clearGrid($game);
        } catch (\Exception $e) {
            if ($e->getCode() == 10) {
                return ['error' => 'error_generate'];
            } else {
                return ['error' => 'error_generate2'];
            }
        }

        // The first team start
        $tour = [];
        foreach ($game->getPlayers() as $player) {
            if ($player->getTeam() == 1) {
                $tour[] = $player->getPosition();
            }
        }

        // Save
        $now = new \DateTime();
        $game
            ->setLastShoot($now)
            ->setTour($tour)
            ->setStatus(Game::STATUS_RUN)
            ->setRunAt($now);
        $this->em->flush();

        // Event
        $event = new GameEvent($game);
        $this->eventDispatcher->dispatch(MatchEvents::LAUNCH, $event);

        // Push
        $this->pusher->push([
            'reload' => true,
            'console' => $this->translator->trans('game_ready', [], 'console'),
        ], 'game.wait.topic', ['slug' => $game->getSlug()]);

        // Return
        return ['success' => true];
    }

    /**
     * Get the RPC name
     * @return string
     */
    public function getName()
    {
        return 'game.launch.rpc';
    }

    /**
     * Generate the grid
     * @param Game $game
     * @throws \Exception Too many pass
     */
    private function generateGrid(Game $game)
    {
        // Create a empty grid
        $this->translateAndSend($game, 'generate_empty');
        $gridSize = $game->getSize();
        $grid = [];
        for ($y = 0; $y < $gridSize; $y++) {
            $grid[$y] = [];
            for ($x = 0; $x < $gridSize; $x++) {
                $grid[$y][$x] = [];
            }
        }

        $boatList = Boats::getList();
        $players = $game->getPlayers();
        $boatNumber = 1;
        $boatInfo = [];

        // For all boat type (we start with bigger)
        $boatList = array_reverse($boatList);
        foreach ($boatList as $boatType) {
            $lengthBoat = count($boatType['img'][0]);

            // For all boat (of this type)
            for ($iBoat = 0; $iBoat < $boatType['nb']; $iBoat++) {
                // For all players
                foreach ($players as $player) {
                    // Init boat info
                    if (!isset($boatInfo[$player->getPosition()])) {
                        $boatInfo[$player->getPosition()] = [];
                    }

                    $limitX = $gridSize - 1;
                    $limitY = $gridSize - 1;

                    // Random direction
                    $direction = mt_rand(0, 1);
                    $dx = 0;
                    $dy = 0;
                    if ($direction) {
                        $limitY -= $lengthBoat;
                        $dy = 1;
                    } else {
                        $limitX -= $lengthBoat;
                        $dx = 1;
                    }

                    $try = 0;
                    do {
                        $ok = true;

                        // Random position (the first box of the boat)
                        $x = mt_rand(0, $limitX);
                        $y = mt_rand(0, $limitY);
                        $currentX = $x;
                        $currentY = $y;

                        // For all box of the length boat
                        for ($iLength = 0; $iLength < $lengthBoat; $iLength++) {
                            // Check if a boat here
                            if (isset($grid[$currentY][$currentX]['img'])) {
                                $ok = false;
                                break;
                            }

                            // Don't put 2 boats of the same player nearly
                            if ($try < 25) {
                                // Get near boxes
                                $near = [];
                                if ($currentX > 0) {
                                    $near[] = $grid[$currentY][$currentX-1]; // Left box
                                }
                                if ($currentX < $limitX) {
                                    $near[] = $grid[$currentY][$currentX+1]; // Right box
                                }
                                if ($currentY > 0) {
                                    $near[] = $grid[$currentY-1][$currentX]; // Top box
                                }
                                if ($currentY < $limitY) {
                                    $near[] = $grid[$currentY+1][$currentX]; // Bottom box
                                }

                                foreach ($near as $n) {
                                    // try<3 : no player nearly | try<20 : no same team | try>20 : yolo
                                    if (($try < 3 && isset($n['player'])) || ($try < 20 && isset($n['team']) && $n['team'] == $player->getTeam())) {
                                        $ok = false;
                                        break 2;
                                    }
                                }
                            }

                            // Go to the next box and continue check
                            $currentX += $dx;
                            $currentY += $dy;
                        }

                        // Protect against infinite loop
                        if (!$ok && $try > 50) {
                            throw new \Exception('', 10);
                        }
                        $try++;
                    } while (!$ok);

                    // Now we have the coord of the first box of the boat, the direction and
                    // we know the boat have the place here
                    $currentX = $x;
                    $currentY = $y;

                    // Put the boat on the grid
                    for ($iLength = 0; $iLength < $lengthBoat; $iLength++) {
                        $grid[$currentY][$currentX] = [
                            'img' => $boatType['img'][$direction][$iLength],
                            'player' => $player->getPosition(),
                            'team' => $player->getTeam(),
                            'boat' => $boatNumber,
                        ];

                        // Next case of the boat
                        $currentX += $dx;
                        $currentY += $dy;
                    }

                    // Console
                    $this->translateAndSend($game, 'place_boat', [
                        '%boat%' => $this->translator->trans($boatType['name']),
                        '%user%' => $player->getName(),
                        '%i%' => ($iBoat+1),
                        '%nb_boat%' => $boatType['nb'],
                        '%try%' => $try,
                    ]);

                    // Boat info [number, length, touch]
                    $boatInfo[$player->getPosition()][] = [$boatNumber, $lengthBoat, 0];
                    $boatNumber++;
                }
            }
        }

        // Save players
        $life = Boats::getInitialLife();
        foreach ($players as $player) {
            $player
                ->setBoats($boatInfo[$player->getPosition()])
                ->setLife($life)
                ->setProbability(BonusConstant::INITIAL_PROBABILITY)
                ->setScore(0);
        }

        // Save grid
        $game->setGrid($grid);
    }

    /**
     * Clear the grid before save (keep only no empty box)
     * @param Game $game
     */
    private function clearGrid(Game $game)
    {
        $this->translateAndSend($game, 'Clear grid');
        $gridSize = $game->getSize();
        $grid = $game->getGrid();
        $clearGrid = [];

        for ($y = 0; $y < $gridSize; $y++) {
            for ($x = 0; $x < $gridSize; $x++) {
                if (!empty($grid[$y][$x])) {
                    $clearGrid[$y][$x] = $grid[$y][$x];
                }
            }
        }

        $game->setGrid($clearGrid);
    }

    /**
     * Get the game
     * @param User   $user
     * @param string $slugGame
     *
     * @return array|Game The game or RPC array
     */
    private function getGame(User $user, $slugGame)
    {
        $repo = $this->em->getRepository('MatchBundle:Game');
        /** @var Game $game */
        $game = $repo->findOneBy(['slug' => $slugGame]);
        if (!$game || !$game->isCreator($user)) {
            return ['error' => "Bad request"];
        } elseif ($game->getStatus() !== Game::STATUS_WAIT) {
            return ['error' => 'error_already_start'];
        }

        return $game;
    }

    /**
     * Translate and send to console
     * @param Game   $game
     * @param string $string
     * @param array  $parameters
     */
    private function translateAndSend(Game $game, $string, array $parameters = [])
    {
        $msg = $this->translator->trans($string, $parameters, 'console');
        $this->pusher->push(['console' => $msg], 'game.wait.topic', ['slug' => $game->getSlug()]);
    }
}
