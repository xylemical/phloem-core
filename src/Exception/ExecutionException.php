<?php

/**
 * @file
 */

namespace Phloem\Exception;

use Phloem\Action\ActionInterface;

/**
 * Class ExecutionException
 *
 * @package Phloem\Exception
 */
class ExecutionException extends \Exception
{

    /**
     * @var \Phloem\Action\ActionInterface
     */
    private $action;

    /**
     * ExecutionException constructor.
     *
     * @param \Phloem\Action\ActionInterface $action
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(ActionInterface $action, string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->action = $action;
    }

    /**
     * Get the action for the exception.
     *
     * @return \Phloem\Action\ActionInterface
     */
    public function getAction() {
        return $this->action;
    }
}
