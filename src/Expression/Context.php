<?php

/**
 * @file
 */

namespace Phloem\Expression;

use Phloem\Action\ActionInterface;
use Phloem\Event\EventManager;
use Psr\Container\ContainerInterface;
use Xylemical\Expressions\Context as Ctx;

/**
 * Class Context
 *
 * @package Phloem\Expression
 */
class Context extends Ctx
{

    /**
     * The current stack for actions.
     *
     * @var \Phloem\Action\ActionInterface[]
     */
    protected $actions = [];

    /**
     * The history of scopes.
     *
     * @var string[]
     */
    protected $history = [];

    /**
     * @var string
     */
    protected $current = 'global';

    /**
     * @var \Phloem\Event\EventManager
     */
    protected $events;

    /**
     * Get the events manager.
     *
     * @return \Phloem\Event\EventManager
     */
    public function getEvents()
    {
        if (!$this->events) {
            $this->events = new EventManager();
        }
        return $this->events;
    }

    /**
     * Set the events manager.
     *
     * @param \Phloem\Event\EventManager $manager
     *
     * @return static
     */
    public function setEvents(EventManager $manager)
    {
        $this->events = $manager;
        return $this;
    }

    /**
     * Push an action current.
     *
     * @param \Phloem\Action\ActionInterface $action
     * @param string $target
     */
    public function push(ActionInterface $action, $target) {
        // Save the current history change.
        $this->history[] = $this->current;

        // Update the current current.
        $this->current = $target;

        // Add action to the actions.
        $this->actions[] = $action;

        // Trigger event on context change.
        $this->getEvents()->trigger('context:push', [
          'target' => $target,
          'action' => $action
        ]);
    }

    /**
     * Pop an action.
     *
     * @return \Phloem\Action\ActionInterface|NULL
     */
    public function pop() {
        if (count($this->actions)) {
            // Trigger event on context change.
            $this->getEvents()->trigger('context:pop');

            // Update the contexts.
            $this->current = array_pop($this->history);
            return array_pop($this->actions);
        }
        return null;
    }

    /**
     * Get the history of actions.
     *
     * @return \Phloem\Action\ActionInterface[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get the history of current changes.
     *
     * @return string[]
     */
    public function getHistory()
    {
        return array_merge($this->history, [$this->current]);
    }
}
