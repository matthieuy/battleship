<?php


namespace ChatBundle;

/**
 * Class ChatEvents
 * @package ChatBundle
 */
final class ChatEvents
{
    /**
     * When a user send a message
     * Instance of ChatBundle\Event\MessageEvent
     */
    const SEND = "chat.send";
}
