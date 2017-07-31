<?php


namespace ChatBundle\Event;

use ChatBundle\Entity\Message;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MessageEvent
 * @package ChatBundle\Event
 */
class MessageEvent extends Event
{
    private $message;

    /**
     * MessageEvent constructor.
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
