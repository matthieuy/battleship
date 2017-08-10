<?php

namespace ChatBundle\EventListener;

use ChatBundle\Entity\Message;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;

/**
 * Class MessageEntityListener
 * @package ChatBundle\EventListener
 */
class MessageEntityListener
{
    private $pusher;

    /**
     * MessageEntityListener constructor.
     * @param PusherInterface $pusher
     */
    public function __construct(PusherInterface $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * After save a message => send it
     * @param Message $message
     */
    public function postPersist(Message $message)
    {
        $game = $message->getGame();

        // Push
        $this->pusher->push(['message' => $message->toArray()], 'chat.topic', ['slug' => $game->getSlug()]);
    }
}
