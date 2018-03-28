<?php

/**
 * @file
 */

namespace Phloem\TextUI\Commands;

use GetOpt\ArgumentException;
use GetOpt\Command;
use GetOpt\GetOpt;
use GetOpt\Option;
use Phloem\Phloem;

/**
 * Class AbstractCommand
 *
 * @package Phloem\TextUI\Commands
 */
class AbstractCommand extends Command
{
    /**
     * @var
     */
    private $phloem;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $handler, $options = null) {
        parent::__construct($name, $handler, $options);

        $this->addOption(
          new Option(null,'file', GetOpt::OPTIONAL_ARGUMENT)
        );
    }

    /**
     * Get the help text for a command.
     */
    public function getHelpText()
    {

    }

    /**
     * Set phloem.
     *
     * @param \Phloem\Phloem $phloem
     *
     * @return static
     */
    public function setPhloem(Phloem $phloem)
    {
        $this->phloem = $phloem;
        return $this;
    }

    /**
     * Get phloem.
     *
     * @return \Phloem\Phloem
     */
    public function getPhloem()
    {
        return $this->phloem;
    }

    /**
     * Get the filename used for processing.
     *
     * @param string|null $filename
     *
     * @return string
     */
    protected function getFile($filename)
    {
        // User passed a filename.
        if ($filename) {
            if (!file_exists($filename)) {
                throw new ArgumentException("{$filename} does not exist.");
            }
            return $filename;
        }

        // Aim to detect 'phloem.yml' in the current path.
        if (!file_exists('phloem.yml')) {
            throw new ArgumentException('Unable to detect phloem.yml in current directory.');
        }

        return 'phloem.yml';
    }

    /**
     * Logs a response.
     *
     * @param $level
     * @param $message
     * @param array $context
     */
    protected function log($level, $message, $context = []) {
        $this->getPhloem()->getLogger()->log($level, $message, $context);
    }
}