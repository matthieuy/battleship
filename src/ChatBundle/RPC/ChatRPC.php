<?php


namespace ChatBundle\RPC;

use ChatBundle\ChatEvents;
use ChatBundle\Entity\Message;
use ChatBundle\Event\MessageEvent;
use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use MatchBundle\Entity\Game;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UserBundle\Entity\User;

/**
 * Class ChatRPC
 * @package ChatBundle\RPC
 */
class ChatRPC implements RpcInterface
{
    private $clientManipulator;
    private $em;
    private $dispatcher;

    /**
     * ChatRPC constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManager              $em
     * @param EventDispatcherInterface   $dispatcher
     */
    public function __construct(
        ClientManipulatorInterface $clientManipulator,
        EntityManager $em,
        EventDispatcherInterface $dispatcher
    ) {
        $this->clientManipulator = $clientManipulator;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
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
     * Send a message
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function send(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        try {
            // Get game
            $game = $this->getGame($params['slug']);
            if (!$game instanceof Game) {
                return $game;
            }

            // Get user (refresh)
            $user = $this->clientManipulator->getClient($connection);
            if (!$user instanceof User) {
                throw new \Exception();
            }
            $user = $this->em->find('UserBundle:User', $user->getId());

            // Get params
            $msg = (isset($params['msg'])) ? $params['msg'] : '';
            if (empty($msg)) {
                throw new \Exception();
            }

            // Create message
            $message = new Message();
            $message
                ->setAuthor($user)
                ->setGame($game)
                ->setText($msg);

            // Set recipient and channel
            $chan = (isset($params['chan'])) ? $params['chan'] : 'general';
            if ($chan == 'team') {
                // Team message
                $player = $game->getPlayerByUser($user);
                $message
                    ->setChannel(Message::CHANNEL_TEAM)
                    ->setRecipient($player->getTeam());
            } elseif (preg_match('/^([0-9]+)$/', $chan)) {
                // Private message
                $message
                    ->setChannel(Message::CHANNEL_PRIVATE)
                    ->setRecipient($chan);
            }

            // Persist
            $this->em->persist($message);
            $this->em->flush();
        } catch (\Exception $e) {
            return ['success' => false];
        }

        // Event
        $this->dispatcher->dispatch(ChatEvents::SEND, new MessageEvent($message));

        return ['success' => true];
    }

    /**
     * Get messages
     * @param ConnectionInterface $connection
     * @param WampRequest         $request
     * @param array               $params
     *
     * @return array
     */
    public function get(ConnectionInterface $connection, WampRequest $request, array $params = [])
    {
        // Get game
        $game = $this->getGame($params['slug']);
        if (!$game instanceof Game || !isset($params['last'])) {
            return $game;
        }

        // Get user
        $user = $this->clientManipulator->getClient($connection);
        if (!$user instanceof User) {
            return [];
        }

        // Get messages
        $list = $this->em->getRepository('ChatBundle:Message')->getMessages($game, intval($params['last']), $user);
        $messages = [];
        foreach ($list as $message) {
            $messages[] = $message->toArray();
        }

        return ['messages' => $messages];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'chat.rpc';
    }

    /**
     * Get the game
     * @param string $slug Slug game
     *
     * @return array|Game The game or array for RPC return
     */
    private function getGame($slug)
    {
        $repo = $this->em->getRepository('MatchBundle:Game');
        $game = $repo->findOneBy(['slug' => $slug]);
        if (!$game instanceof Game) {
            return ['error' => 'Bad request'];
        }

        return $game;
    }
}
