<?php

/**
 * @file
 */

namespace Phloem\Exception;

/**
 * Class LoaderException
 *
 * @package Phloem\Exception
 */
class LoaderException extends \Exception
{
    /**
     * @var string
     */
    private $path;

    /**
     * PathException constructor.
     *
     * @param string $path
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($path, string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->path = $path;
    }

    /**
     * Get the path for the exception.
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

}
