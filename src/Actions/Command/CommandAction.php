<?php

/**
 * @file
 */

namespace Phloem\Actions\Command;

use Psr\Container\ContainerInterface;

/**
 * Class CommandAction
 *
 * @package Phloem\Actions
 */
class CommandAction extends AbstractCommandAction
{

    /**
     * @var string
     */
    protected $command;

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        // The base command to be run.
        $this->command = $this->required($config, 'command', 'string');

        // Get the working directory for the command.
        $this->workingDirectory = $this->optional($config, 'path', $this->workingDirectory, 'string');

        // Setup the options and arguments.
        $this->options($config);
        $this->arguments($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandPath()
    {
        return $this->command;
    }
}
