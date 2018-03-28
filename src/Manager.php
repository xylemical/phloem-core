<?php

/**
 * @file
 */

namespace Phloem;

/**
 * Class Manager
 *
 * @package Phloem
 */
class Manager
{

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string[]
     */
    protected $history = [];

    /**
     * @var string[]
     */
    protected $tasks = [];

    /**
     * Get the current path.
     *
     * @return string
     */
    public function getPath()
    {
        $max = count($this->history);
        if (!$max) {
            return getcwd();
        }
        return $this->history[$max - 1];
    }

    /**
     * Get the history of files.
     *
     * @return string[]
     */
    public function getHistory() {
        return $this->history;
    }

    /**
     * Push paths from the history.
     *
     * @param string $path
     *
     * @return $this
     */
    public function push($path) {
        $this->history[] = $path;
        return $this;
    }

    /**
     * Pop paths from the history.
     *
     * @return $this
     */
    public function pop() {
        array_pop($this->history);
        return $this;
    }

    /**
     * Add a registered task.
     *
     * @param string $task
     *
     * @return static
     */
    public function addTask($task) {
        $this->tasks[] = $task;
        return $this;
    }

    /**
     * Get the tasks defined.
     *
     * @return string[]
     */
    public function getTasks() {
        return $this->tasks;
    }
}