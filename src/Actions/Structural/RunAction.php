<?php

/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\ActionInterface;
use Phloem\Actions\Structural\TaskAction;
use Phloem\Exception\ConfigException;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class RunAction
 *
 * @package Phloem\Actions
 */
class RunAction implements ActionInterface
{
    /**
     * @var \Phloem\Actions\Structural\TaskAction
     */
    protected $task;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * RunAction constructor.
     *
     * @param \Phloem\Actions\Structural\TaskAction $task
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
