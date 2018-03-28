<?php

/**
 * @file
 */

namespace Phloem;

use Phloem\Actions\NullAction;
use Phloem\Expression\Variable;
use Phloem\Exception\ExecutionException;
use Phloem\Log\NullLogger;
use Pimple\Container;
use Phloem\Event\EventManager;
use Phloem\Expression\Context;
use Phloem\Action\Factory as ActionFactory;
use Phloem\Filter\Factory as FilterFactory;
use Phloem\Loader\Factory as LoaderFactory;
use Psr\Log\LoggerInterface;
use Xylemical\Expressions\Evaluator;
use Xylemical\Expressions\ExpressionFactory;
use Xylemical\Expressions\Lexer;
use Xylemical\Expressions\Math\BcMath;
use Xylemical\Expressions\Parser;


/**
 * Class Phloem
 *
 * @package Phloem
 */
class Phloem
{

    /**
     * Service identifier of the \Phloem\Action\Factory class.
     */
    const ACTIONS = 'phloem:actions';

    /**
     * Service identifier of the \Xylemical\Expressions\Evaluator class.
     */
    const EVALUATOR = 'phloem:evaluator';

    /**
     * Service identifier of the \Xylemical\Expressions\ExpressionFactory class.
     */
    const EXPRESSIONS = 'phloem:expressions';

    /**
     * Service identifier of the \Xylemical\Expressions\Parser class.
     */
    const PARSER = 'phloem:parser';

    /**
     * Service identifier of the \Phloem\Event\EventManager class.
     */
    const EVENTS = 'phloem:events';

    /**
     * Service identifier of the \Phloem\Filter\Factory class.
     */
    const FILTERS = 'phloem:filters';

    /**
     * Service identifier for loading the configuration.
     */
    const LOADER = 'phloem:loader';

    /**
     * Service identifier for managing the path locations.
     */
    const MANAGER = 'phloem:manager';

    /**
     * Service identifier for managing the logging behaviour.
     */
    const LOGGER = 'phloem:logger';

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $psrContainer;

    /**
     * Phloem constructor.
     */
    public function __construct()
    {
        $this->container = new Container();
        $this->psrContainer = new \Pimple\Psr11\Container($this->container);

        // Setup the various services.
        $container = $this->container;
        $container[self::ACTIONS] = new ActionFactory($this->psrContainer);
        $container[self::EXPRESSIONS] = new ExpressionFactory(new BcMath());
        $container[self::EVALUATOR] = new Evaluator();
        $container[self::EVENTS] = new EventManager();
        $container[self::FILTERS] = new FilterFactory($this->psrContainer);
        $container[self::LOADER] = new LoaderFactory();
        $container[self::MANAGER] = new Manager();
        $container[self::PARSER] = new Parser(new Lexer($container[Phloem::EXPRESSIONS]));
        $container[self::LOGGER] = new NullLogger();

        /** @var \Xylemical\Expressions\ExpressionFactory $expressions */
        $expressions = $container[self::EXPRESSIONS];

        // Add in variable processor for lexing purposes.
        $expressions->addOperator(new Variable());
    }

    /**
     * Gets the container used for dependency injection.
     *
     * @return \Pimple\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get the PSR Container Interface.
     *
     * @return \Psr\Container\ContainerInterface
     */
    public function getPsrContainer()
    {
        return $this->psrContainer;
    }

    /**
     * Get the manager.
     *
     * @return \Phloem\Manager
     */
    public function getManager() {
        return $this->container[self::MANAGER];
    }

    /**
     * Get the logger used for the system.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger() {
        return $this->container[self::LOGGER];
    }

    /**
     * Set the logger.
     *
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return static
     */
    public function setLogger(LoggerInterface $logger) {
        $this->container[self::LOGGER] = $logger;
        return $this;
    }

    /**
     * Get the action factory.
     *
     * @return \Phloem\Action\Factory
     */
    public function getActionFactory()
    {
        return $this->container[self::ACTIONS];
    }

    /**
     * Loads the actions defined by a file.
     *
     * @param string $file
     *
     * @return array
     *
     * @throws \Phloem\Exception\LoaderException
     */
    public function load($file)
    {
        /** @var \Phloem\Manager $manager */
        $manager = $this->container[self::MANAGER];

        /** @var \Phloem\Loader\Factory $factory */
        $factory = $this->container[self::LOADER];

        // Push the filename onto the manager.
        $manager->push(dirname($file));

        // Load the filename via the factory.
        $loader = $factory->getLoader($file);
        $contents = $loader->load(basename($file), dirname($file));

        // Remove the filename from the manager.
        $manager->pop();

        // Return the contents of the loaded file.
        return !empty($contents) ? reset($contents) : [];
    }

    /**
     * Executes an action or actions using configuration as an array.
     *
     * @param string|array $actions
     * @param \Phloem\Expression\Context $context
     * @param string $path
     *
     * @throws \Phloem\Exception\ActionFactoryException
     * @throws \Phloem\Exception\ConfigException
     * @throws \Phloem\Exception\ExecutionException
     */
    public function execute($actions, Context $context, $path = null)
    {
        /** @var \Phloem\Manager $manager */
        $manager = $this->container[self::MANAGER];

        /** @var \Phloem\Action\Factory $factory */
        $factory = $this->container[self::ACTIONS];

        try {
            // Set the events manager for the context.
            $context->setEvents($this->container[self::EVENTS]);

            // Setup the path management.
            $manager->push($path ? $path : getcwd());

            // Create the action from the actions factory.
            $action = $factory->process($actions);

            // Execute the action.
            $action->execute($context);

            // Remove the path.
            $manager->pop();
        }
        catch (\Psr\Container\ContainerExceptionInterface $e) {
            throw new ExecutionException(new NullAction(), $e->getMessage(), $e->getCode(), $e);
        }
    }
}
