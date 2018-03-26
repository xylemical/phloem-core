<?php

/**
 * @file
 */

namespace Phloem\Core\Action;

use Phloem\Core\Phloem;
use PHPUnit\Framework\TestCase;
use Pimple\Psr11\Container;
use Xylemical\Expressions\Context;
use Xylemical\Expressions\Evaluator;
use Xylemical\Expressions\ExpressionFactory;
use Xylemical\Expressions\Lexer;
use Xylemical\Expressions\Math\BcMath;
use Xylemical\Expressions\Parser;
use Xylemical\Expressions\Token;
use Xylemical\Expressions\Value;

/**
 * Class ActionTestCase
 *
 * @package Phloem\Core\Action
 */
abstract class ActionTestCase extends TestCase
{
    /**
     * @var \Pimple\Container
     */
    protected $pimple;

    /**
     * @var \Pimple\Psr11\Container
     */
    protected $container;

    /**
     * @var \Phloem\Core\Action\AbstractAction
     */
    protected $action;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $this->pimple = new \Pimple\Container();
        $this->container = new Container($this->pimple);
        $this->pimple[Phloem::ACTIONS] = new Factory($this->container);
        $this->pimple[Phloem::EXPRESSIONS] = new ExpressionFactory(new BcMath());
        $this->pimple[Phloem::PARSER] = new Parser(new Lexer($this->pimple[Phloem::EXPRESSIONS]));
        $this->pimple[Phloem::EVALUATOR] = new Evaluator();
        $this->pimple[Phloem::FILTERS] = new \Phloem\Core\Filter\Factory($this->container);

        // Provide variable behaviour with the filtering.
        $this->pimple[Phloem::EXPRESSIONS]->addOperator(
            new Value('\$[a-zA-Z_][a-zA-Z0-9_]*',
                function(array $operands, Context $context, Token $token) {
                    // Specialized behaviour of variables.
                    return $context->getVariable(substr($token->getValue(), 1));
                }
            )
        );
    }
}
