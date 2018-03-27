<?php

/**
 * @file
 */

namespace Phloem\Actions\File;

use Phloem\Action\AbstractAction;
use Phloem\Exception\ConfigException;
use Phloem\Expression\Context;
use Phloem\Phloem;
use Psr\Container\ContainerInterface;

/**
 * Class IncludeAction
 *
 * @package Phloem\Actions
 */
class IncludeAction extends AbstractAction
{

    /**
     * @var \Phloem\Loader\Factory
     */
    protected $loader;

    /**
     * @var \Phloem\Manager
     */
    protected $manager;

    /**
     * @var \Phloem\Action\ActionInterface[]
     */
    protected $actions;

    /**
     * @var string[]
     */
    protected $paths = [];

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        /** @var \Phloem\Loader\Factory loader */
        $this->loader = $container->get(Phloem::LOADER);

        /** @var \Phloem\Manager manager */
        $this->manager = $container->get(Phloem::MANAGER);

        // Ensure the include config is available.
        $paths = $this->required($config, 'include');

        // Set down the actions to be processed.
        $this->actions = [];

        // Process each of the files listed in the config through the loader.
        foreach ((array)$paths as $path) {
            if (!is_string($path)) {
                throw new ConfigException($config, 'One of the paths is not a string.');
            }

            // Store current working directory, and update to the path.
            $current = $this->manager->getPath();
            $this->manager->push(dirname($path));

            // Process the include file based on the relative working directory.
            $contents = $this->loader->getLoader($path)->load($path, $current);
            foreach ($contents as $path => $result) {
                $this->actions[$path] = $this->getFactory()->process($result);
            }

            // Return to original working directory.
            $this->manager->pop();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        foreach ($this->actions as $path => $action) {
            $this->manager->push(dirname($path));
            $this->perform($action, $context, 'include');
            $this->manager->pop();
        }
    }
}
