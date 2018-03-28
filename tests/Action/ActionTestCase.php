<?php

/**
 * @file
 */

namespace Phloem\Action;

use Phloem\Expression\Variable;
use Phloem\Log\NullLogger;
use Phloem\Manager;
use Phloem\Phloem;
use PHPUnit\Framework\TestCase;
use Pimple\Psr11\Container;
use Xylemical\Expressions\Evaluator;
use Xylemical\Expressions\ExpressionFactory;
use Xylemical\Expressions\Lexer;
use Xylemical\Expressions\Math\BcMath;
use Xylemical\Expressions\Parser;
use Phloem\Action\Factory as ActionFactory;
use Phloem\Filter\Factory as FilterFactory;
use Phloem\Loader\Factory as LoaderFactory;

/**
 * Class ActionTestCase
 *
 * @package Phloem\Action
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
     * @var \Phloem\Action\AbstractAction
     */
    protected $action;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $this->pimple = new \Pimple\Container();
        $this->container = new Container($this->pimple);
        $this->pimple[Phloem::ACTIONS] = new ActionFactory($this->container);
        $this->pimple[Phloem::EXPRESSIONS] = new ExpressionFactory(new BcMath());
        $this->pimple[Phloem::PARSER] = new Parser(new Lexer($this->pimple[Phloem::EXPRESSIONS]));
        $this->pimple[Phloem::MANAGER] = new Manager();
        $this->pimple[Phloem::EVALUATOR] = new Evaluator();
        $this->pimple[Phloem::FILTERS] = new FilterFactory($this->container);
        $this->pimple[Phloem::LOADER] = new LoaderFactory();
        $this->pimple[Phloem::LOGGER] = new NullLogger();

        // Provide variable behaviour with the filtering.
        $this->pimple[Phloem::EXPRESSIONS]->addOperator(new Variable());
    }
}
