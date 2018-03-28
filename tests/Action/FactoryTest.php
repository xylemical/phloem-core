<?php
/**
 * @file
 */

namespace Phloem\Action;

use Phloem\Actions\NullAction;
use Phloem\Expression\Context;
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
use Phloem\Filter\Factory as FilterFactory;

class FactoryTest extends TestCase
{

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Phloem\Action\Factory
     */
    protected $factory;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = new \Pimple\Container();
        $container = new Container($this->container);
        $this->factory = new Factory($container);
        $this->container[Phloem::ACTIONS] = $this->factory;
        $this->container[Phloem::PARSER] = new Parser(new Lexer(new ExpressionFactory(new BcMath())));
        $this->container[Phloem::MANAGER] = new Manager();
        $this->container[Phloem::EVALUATOR] = new Evaluator();
        $this->container[Phloem::LOGGER] = new NullLogger();
        $this->container[Phloem::FILTERS] = new FilterFactory($container);
    }

    /**
     * Test when no action is defined.
     *
     * @throws \Phloem\Exception\ActionFactoryException
     */
    public function testNoActionDefined() {
        $this->expectException('\\Phloem\\Exception\\ActionFactoryException');

        $this->factory->getAction('unknown');
    }

    /**
     * Test when somebody provides a bad action factory method.
     *
     * @throws \Phloem\Exception\ActionFactoryException
     */
    public function testBadActionDefined() {
        $this->expectException('\\Phloem\\Exception\\ActionFactoryException');

        $this->factory->setAction('bad', true);
        $this->factory->getAction('bad');
    }

    /**
     * Test the lazy-loading of the action.
     *
     * @throws \Phloem\Exception\ActionFactoryException
     */
    public function testStringAction() {
        // Test check works.
        $this->assertFalse($this->factory->hasAction('string'));

        $this->factory->setAction('string', 'Phloem\\Actions\\NullAction');

        // Test check works.
        $this->assertTrue($this->factory->hasAction('string'));

        $action = $this->factory->getAction('string');
        $this->assertEquals(get_class($action), 'Phloem\\Actions\\NullAction');
    }

    /**
     * Test the closure generation of the action.
     *
     * @throws \Phloem\Exception\ActionFactoryException
     */
    public function testClosureAction() {
        $this->factory->setAction('closure', function($factory) {
            return new NullAction();
        });

        $action = $this->factory->getAction('closure');
        $this->assertEquals(get_class($action), 'Phloem\\Actions\\NullAction');
    }

    /**
     * Test using an action for the factory clones the action.
     *
     * @throws \Phloem\Exception\ActionFactoryException
     */
    public function testCloneAction() {
        $original = new NullAction();

        $this->factory->setAction('clone', $original);

        $action = $this->factory->getAction('clone');

        // Change $action to ensure they are two different objects.
        $action->failure = true;

        $this->assertEquals(get_class($action), 'Phloem\\Actions\\NullAction');
        $this->assertNotEquals($action, $original);
    }

    /**
     * Test generating an action purely from a string.
     */
    public function testProcessString()
    {
        $action = $this->factory->process('null');
        $this->assertEquals(get_class($action), 'Phloem\\Actions\\NullAction');
    }

    /**
     * Test failure when neither string nor array.
     */
    public function testProcessConfigFailure()
    {
        $this->expectException('Phloem\\Exception\\ActionFactoryException');

        $this->factory->process(true);
    }

    /**
     * Tests creating a null action from passing an empty array.
     *
     * @throws \Phloem\Exception\ActionFactoryException
     * @throws \Phloem\Exception\ConfigException
     */
    public function testProcessNull()
    {
        $action = $this->factory->process([]);
        $this->assertEquals(get_class($action), 'Phloem\\Actions\\NullAction');
    }

    /**
     * Tests creating a series action.
     */
    public function testProcessSeries()
    {
        $action = $this->factory->process(['null', 'null']);
        $this->assertEquals(get_class($action), 'Phloem\\Actions\\Structural\\SeriesAction');
    }

    /**
     * Tests an undefined action generates an exception.
     *
     * @throws \Phloem\Exception\ActionFactoryException
     * @throws \Phloem\Exception\ConfigException
     */
    public function testProcessInvalidAction()
    {
        $this->expectException('Phloem\\Exception\\ActionFactoryException');

        $this->factory->process([
          'unknown' => true,
        ]);
    }

    /**
     * Test the create of a valid action.
     *
     * @throws \Phloem\Exception\ActionFactoryException
     * @throws \Phloem\Exception\ConfigException
     */
    public function testProcessAction()
    {
        $action = $this->factory->process([
          'series' => [],
        ]);
        $this->assertEquals(get_class($action), 'Phloem\\Actions\\Structural\\SeriesAction');
    }

    /**
     * Test the evaluation routine processes correctly.
     *
     * @throws \Phloem\Exception\ExecutionException
     */
    public function testEvaluateSuccess()
    {
        $action = new NullAction();
        $context = new Context();
        $result = $this->factory->evaluate($action, '1 + 1', $context);
        $this->assertEquals('2', $result);
    }

    /**
     * Test that an invalid or container issue generates the proper exception.
     *
     * @throws \Phloem\Exception\ExecutionException
     */
    public function testEvaluateContainerException()
    {
        $this->expectException('Phloem\\Exception\\ExecutionException');
        $action = new NullAction();
        $context = new Context();
        unset($this->container[Phloem::PARSER]);
        $this->factory->evaluate($action, '1 + 1', $context);
    }

    /**
     * Test that an exception will generate the proper exception.
     */
    public function testEvaluateException()
    {
        $this->expectException('Phloem\\Exception\\ExecutionException');
        $action = new NullAction();
        $context = new Context();
        $this->factory->evaluate($action, '1 + 1 (', $context);
    }
}
