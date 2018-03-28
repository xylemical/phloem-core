<?php

/**
 * @file
 */

namespace Phloem\Log;

use Psr\Log\LoggerInterface;

/**
 * Class NullLogger
 *
 * @package Phloem\Log
 */
class NullLogger implements LoggerInterface
{
    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = array()) {
        $this->log('emergency', $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = array()) {
        $this->log('alert', $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = array()) {
        $this->log('critical', $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = array()) {
        $this->log('error', $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = array()) {
        $this->log('warning', $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = array()) {
        $this->log('notice', $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = array()) {
        $this->log('info', $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = array()) {
        $this->log('debug', $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array()) {
    }

}