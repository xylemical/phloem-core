<?php

/**
 * @file
 */

namespace Phloem\Core\Expression;

use Phloem\Core\Action\ActionInterface;
use Xylemical\Expressions\Context as Ctx;

/**
 * Class Context
 *
 * @package Phloem\Core\Expression
 */
class Context extends Ctx
{

    /**
     * The current stack for actions.
     *
     * @var \Phloem\Core\Action\ActionInterface[]
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
     * Push an action current.
     *
     * @param \Phloem\Core\Action\ActionInterface $action
     * @param string $target
     */
    public function push(ActionInterface $action, $target) {
        // Save the current history change.
        $this->history[] = $this->current;

        // Update the current current.
        $this->current = $target;

        // Add action to the actions.
        $this->actions[] = $action;
    }

    /**
     * Pop an action.
     *
     * @return \Phloem\Core\Action\ActionInterface|NULL
     */
    public function pop() {
        if (count($this->actions)) {
            $this->current = array_pop($this->history);
            return array_pop($this->actions);
        }
        return null;
    }

    /**
     * Get the history of actions.
     *
     * @return \Phloem\Core\Action\ActionInterface[]
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
