<?php
/**
 * @file
 */

namespace Phloem\Core\Action;

use Phloem\Core\Actions\NullAction;
use Phloem\Core\Expression\Context;
use Phloem\Core\Phloem;
use PHPUnit\Framework\TestCase;
use Pimple\Psr11\Container;
use Xylemical\Expressions\Evaluator;
use Xylemical\Expressions\ExpressionFactory;
use Xylemical\Expressions\Lexer;
use Xylemical\Expressions\Math\BcMath;
use Xylemical\Expressions\Parser;

class FactoryTest extends TestCase
{

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Phloem\Core\Action\Factory
     */
    protected $factory;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = new \Pimple\Container();
        $this->factory = new Factory(new Container($this->container));
        $this->container[Phloem::ACTIONS] = $this->factory;
        $this->container[Phloem::PARSER] = new Parser(new Lexer(new ExpressionFactory(new BcMath())));
        $this->container[Phloem::EVALUATOR] = new Evaluator();
    }

    /**
     * Test when no action is defined.
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     */
    public function testNoActionDefined() {
        $this->expectException('\\Phloem\\Core\\Exception\\ActionFactoryException');

        $this->factory->getAction('unknown');
    }

    /**
     * Test when somebody provides a bad action factory method.
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     */
    public function testBadActionDefined() {
        $this->expectException('\\Phloem\\Core\\Exception\\ActionFactoryException');

        $this->factory->setAction('bad', true);
        $this->factory->getAction('bad');
    }

    /**
     * Test the lazy-loading of the action.
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     */
    public function testStringAction() {
        // Test check works.
        $this->assertFalse($this->factory->hasAction('string'));

        $this->factory->setAction('string', 'Phloem\\Core\\Actions\\NullAction');

        // Test check works.
        $this->assertTrue($this->factory->hasAction('string'));

        $action = $this->factory->getAction('string');
        $this->assertEquals(get_class($action), 'Phloem\\Core\\Actions\\NullAction');
    }

    /**
     * Test the closure generation of the action.
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     */
    public function testClosureAction() {
        $this->factory->setAction('closure', function($factory) {
            return new NullAction();
        });

        $action = $this->factory->getAction('closure');
        $this->assertEquals(get_class($action), 'Phloem\\Core\\Actions\\NullAction');
    }

    /**
     * Test using an action for the factory clones the action.
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     */
    public function testCloneAction() {
        $original = new NullAction();

        $this->factory->setAction('clone', $original);

        $action = $this->factory->getAction('clone');

        // Change $action to ensure they are two different objects.
        $action->failure = true;

        $this->assertEquals(get_class($action), 'Phloem\\Core\\Actions\\NullAction');
        $this->assertNotEquals($action, $original);
    }

    /**
     * Test generating an action purely from a string.
     */
    public function testProcessString()
    {
        $action = $this->factory->process('null');
        $this->assertEquals(get_class($action), 'Phloem\\Core\\Actions\\NullAction');
    }

    /**
     * Test failure when neither string nor array.
     */
    public function testProcessConfigFailure()
    {
        $this->expectException('Phloem\\Core\\Exception\\ActionFactoryException');

        $this->factory->process(true);
    }

    /**
     * Tests creating a null action from passing an empty array.
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     * @throws \Phloem\Core\Exception\ConfigException
     */
    public function testProcessNull()
    {
        $action = $this->factory->process([]);
        $this->assertEquals(get_class($action), 'Phloem\\Core\\Actions\\NullAction');
    }

    /**
     * Tests creating a series action.
     */
    public function testProcessSeries()
    {
        $action = $this->factory->process(['null', 'null']);
        $this->assertEquals(get_class($action), 'Phloem\\Core\\Actions\\SeriesAction');
    }

    /**
     * Tests an undefined action generates an exception.
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     * @throws \Phloem\Core\Exception\ConfigException
     */
    public function testProcessInvalidAction()
    {
        $this->expectException('Phloem\\Core\\Exception\\ActionFactoryException');

        $this->factory->process([
          'unknown' => true,
        ]);
    }

    /**
     * Test the create of a valid action.
     *
     * @throws \Phloem\Core\Exception\ActionFactoryException
     * @throws \Phloem\Core\Exception\ConfigException
     */
    public function testProcessAction()
    {
        $action = $this->factory->process([
          'series' => [],
        ]);
        $this->assertEquals(get_class($action), 'Phloem\\Core\\Actions\\SeriesAction');
    }

    /**
     * Test the evaluation routine processes correctly.
     *
     * @throws \Phloem\Core\Exception\ExecutionException
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
     * @throws \Phloem\Core\Exception\ExecutionException
     */
    public function testEvaluateContainerException()
    {
        $this->expectException('Phloem\\Core\\Exception\\ExecutionException');
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
        $this->expectException('Phloem\\Core\\Exception\\ExecutionException');
        $action = new NullAction();
        $context = new Context();
        $this->factory->evaluate($action, '1 + 1 (', $context);
    }
}
