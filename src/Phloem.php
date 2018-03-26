<?php

/**
 * @file
 */

namespace Phloem\Core;

use Phloem\Core\Expression\Context;
use Psr\Container\ContainerInterface;
use Xylemical\Expressions\Token;
use Xylemical\Expressions\Value;


/**
 * Class Phloem
 *
 * @package Phloem\Core\Build
 */
class Phloem
{

    /**
     * Service identifier of the \Phloem\Core\Action\Factory class.
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
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * Phloem constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        /** @var \Xylemical\Expressions\ExpressionFactory $expressions */
        $expressions = $container->get(self::EXPRESSIONS);

        // Add in variable processor for lexing.
        $expressions->addOperator(new Value('\$[a-zA-Z_][a-zA-Z0-9_]*', function(array $operands, Context $context, Token $token) {
            // Specialized behaviour of variables.
            return $context->getVariable(substr($token->getValue(), 1));
        }));
    }

    /**
     * Executes an action or actions using configuration as an array.
     *
     * @param string|array $actions
     * @param \Phloem\Core\Expression\Context $context
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     * @throws \Phloem\Core\Exception\ConfigException
     * @throws \Phloem\Core\Exception\ExecutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     */
    public function execute($actions, Context $context)
    {
        /** @var \Phloem\Core\Action\Factory $factory */
        $factory = $this->container->get(self::ACTIONS);

        // Create the action from the actions factory.
        $action = $factory->process($actions);

        // Execute the action.
        $action->execute($context);
    }
}
