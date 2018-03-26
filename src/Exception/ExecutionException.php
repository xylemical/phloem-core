<?php

/**
 * @file
 */

namespace Phloem\Core\Exception;

use Phloem\Core\Action\ActionInterface;

/**
 * Class ExecutionException
 *
 * @package Phloem\Core\Build\Exception
 */
class ExecutionException extends \Exception
{

    /**
     * @var \Phloem\Core\Action\ActionInterface
     */
    private $action;

    /**
     * ExecutionException constructor.
     *
     * @param \Phloem\Core\Action\ActionInterface $action
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
     * @return \Phloem\Core\Action\ActionInterface
     */
    public function getAction() {
        return $this->action;
    }
}
