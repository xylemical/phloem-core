<?php

/**
 * @file
 */

namespace Phloem\Core\Actions;

use Phloem\Core\Action\ActionInterface;
use Phloem\Core\Exception\ConfigException;
use Phloem\Core\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class RunAction
 *
 * @package Phloem\Core\Actions
 */
class RunAction implements ActionInterface
{
    /**
     * @var \Phloem\Core\Actions\TaskAction
     */
    protected $task;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * RunAction constructor.
     *
     * @param \Phloem\Core\Actions\TaskAction $task
     */
    public function __construct(TaskAction $task)
    {
        $this->task = $task;
    }

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        $name = $this->task->getName();
        if (array_key_exists($name, $config)) {
            $this->config = (array)$config[$name];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        // Change the context to the task.
        $context->push($this->task, 'task');

        // Add the variables to be passed to this context.
        foreach ($this->config as $name => $value) {
            $context->setVariable($name, $value);
        }

        // Run the task itself.
        $this->task->execute($context);

        // Pop the variable context.
        $context->pop('task');
    }
}
