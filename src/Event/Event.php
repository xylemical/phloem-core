<?php

/**
 * @file
 */

namespace Phloem\Core\Event;

/**
 * Class Event
 *
 * @package Phloem\Core\Event
 */
class Event
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $params;

    /**
     * Event constructor.
     *
     * @param $name
     * @param array $params
     */
    public function __construct($name, array $params = []) {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * Get the name used for the event.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the parameters for the event.
     *
     * @return array
     */
    public function getParams() {
        return $this->params;
    }
}
