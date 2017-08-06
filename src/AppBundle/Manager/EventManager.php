<?php

namespace AppBundle\Manager;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EventManager
 * @package AppBundle\Manager
 */
class EventManager implements EventDispatcherInterface
{
    private $dispatcher;

    /**
     * EventManager constructor.
     */
    public function __construct()
    {
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * Dispatches an event to all registered listeners.
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     *
     * @param Event  $event     The event to pass to the event handlers/listeners
     *                          If not supplied, an empty Event instance is created.
     *
     * @return Event
     */
    public function dispatch($eventName, Event $event = null)
    {
        return $this->dispatcher->dispatch($eventName, $event);
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events he is
     * interested in and added as a listener for these events.
     *
     * @param EventSubscriberInterface $subscriber The subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * Removes an event listener from the specified events.
     * @param string   $eventName The event to remove a listener from
     * @param callable $listener  The listener to remove
     */
    public function removeListener($eventName, $listener)
    {
        $this->dispatcher->removeListener($eventName, $listener);
    }

    /**
     * Removes an event subscriber.
     * @param EventSubscriberInterface $subscriber The subscriber
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->removeSubscriber($subscriber);
    }

    /**
     * Gets the listeners of a specific event or all listeners sorted by descending priority.
     * @param string $eventName The name of the event
     *
     * @return array The event listeners for the specified event, or all event listeners by event name
     */
    public function getListeners($eventName = null)
    {
        return $this->dispatcher->getListeners($eventName);
    }

    /**
     * Gets the listener priority for a specific event.
     *
     * Returns null if the event or the listener does not exist.
     *
     * @param string   $eventName The name of the event
     * @param callable $listener  The listener
     *
     * @return int|null The event listener priority
     */
    public function getListenerPriority($eventName, $listener)
    {
        return $this->dispatcher->getListenerPriority($eventName, $listener);
    }

    /**
     * Checks whether an event has any registered listeners.
     * @param string $eventName The name of the event
     *
     * @return bool true if the specified event has any listeners, false otherwise
     */
    public function hasListeners($eventName = null)
    {
        return $this->dispatcher->hasListeners($eventName);
    }
}
