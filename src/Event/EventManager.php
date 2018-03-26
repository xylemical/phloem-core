<?php

/**
 * @file
 */

namespace Phloem\Core\Event;

/**
 * Class EventManager
 *
 * @package Phloem\Core\Event
 */
class EventManager
{
    /**
     * @var \SplPriorityQueue[]
     */
    private $events = [];

    /**
     * Attach an event listener to an event name.
     *
     * @param string $name
     * @param callable $callback
     * @param int $priority
     */
    public function attach($name, callable $callback, $priority = 0) {
        if (!isset($this->events[$name])) {
            $this->events[$name] = new \SplPriorityQueue();
        }

        // Add listener to priority queue.
        $this->events[$name]->insert($callback, $priority);
    }

    /**
     * Trigger event listeners by $name.
     *
     * @param string $name
     * @param array $params
     */
    public function trigger($name, $params = []) {
        // Skip as there are no listeners.
        if (!isset($this->events[$name])) {
            return;
        }

        // Create the event.
        $event = new Event($name, $params);

        // Notify all the listeners based on priority.
        foreach ($this->events[$name] as $listener) {
            $listener($event);
        }
    }
}
