<?php

/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\AbstractAction;
use Phloem\Actions\Structural\RunAction;
use Phloem\Exception\ConfigException;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class TaskAction
 *
 * @package Phloem
 */
class TaskAction extends AbstractAction
{
    /**
     * The name of the task.
     *
     * @var string
     */
    protected $name;

    /**
     * The dependencies of the task.
     *
     * @var string[]
     */
    protected $dependencies;

    /**
     * @var \Phloem\Action\ActionInterface
     */
    protected $action;

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        $this->name = $this->required($config, 'task', 'string');
        $this->dependencies = [];

        $dependencies = $this->optional($config, 'dependencies', [], 'array');
        foreach ($dependencies as $dependency) {
            if (!is_string($dependency)) {
                throw new ConfigException($config, 'Dependency is not valid.');
            }

            $this->dependencies[] = $dependency;
        }

        $this->action = $this->process(
          $this->optional($config, 'actions', [])
        );

        // Check action does not already exist.
        if ($this->getFactory()->hasAction($this->getName())) {
            throw new ConfigException($config, "Redefining an existing action '{$this->name}''.");
        }

        // Register a new task.
        $this->getManager()->addTask($this->name);

        // Add action to the factory for use by other actions.
        $this->getFactory()->setAction($this->name, new RunAction($this));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
    }

    /**
     * Get the name of the task.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the dependencies needing to be run for the task.
     *
     * @return string[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Get the action to be performed as part of the task.
     *
     * @return \Phloem\Action\ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }
}
