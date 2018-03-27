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

        // Add action to the factory for use by other actions.
        $this->getFactory()->setAction($this->name, new RunAction($this));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        // Ensure the dependencies are run.
        foreach ($this->dependencies as $dependency) {
            $action = $this->getFactory()->getAction($dependency);
            $this->perform($action, $context, 'dependency (' . $dependency . ')');
        }

        // Execute the action.
        if ($this->action) {
            $this->perform($this->action, $context, $this->getName());
        }
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
}
