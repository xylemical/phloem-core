<?php

/**
 * @file
 */

namespace Phloem\Exception;

/**
 * Class ConfigException
 *
 * @package Phloem
 */
class ConfigException extends \Exception
{

    /**
     * @var array
     */
    private $config;

    /**
     * ConfigException constructor.
     *
     * @param array $config
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(array $config, string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->config = $config;
    }

    /**
     * Get the config for the exception.
     *
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }
}
