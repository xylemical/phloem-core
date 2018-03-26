<?php
/**
 * @file
 */

namespace Phloem\Core\Filter;

use Phloem\Core\Expression\Context;
use Phloem\Core\Filters\NullFilter;
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
     * @var \Phloem\Core\Filter\Factory
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
        $this->container[Phloem::ACTIONS] = new \Phloem\Core\Action\Factory($container);
        $this->container[Phloem::FILTERS] = $this->factory;
        $this->container[Phloem::PARSER] = new Parser(new Lexer(new ExpressionFactory(new BcMath())));
        $this->container[Phloem::EVALUATOR] = new Evaluator();
    }

    /**
     * Test when no filter is defined.
     *
     * @throws \Phloem\Core\Exception\FilterFactoryException
     */
    public function testNoFilterDefined() {
        $this->expectException('\\Phloem\\Core\\Exception\\FilterFactoryException');

        $this->factory->getFilter('unknown');
    }

    /**
     * Test when somebody provides a bad filter factory method.
     *
     * @throws \Phloem\Core\Exception\FilterFactoryException
     */
    public function testBadFilterDefined() {
        $this->expectException('\\Phloem\\Core\\Exception\\FilterFactoryException');

        $this->factory->setFilter('bad', true);
        $this->factory->getFilter('bad');
    }

    /**
     * Test the lazy-loading of the filter.
     *
     * @throws \Phloem\Core\Exception\FilterFactoryException
     */
    public function testStringFilter() {
        // Test check works.
        $this->assertFalse($this->factory->hasFilter('string'));

        $this->factory->setFilter('string', 'Phloem\\Core\\Filters\\NullFilter');

        // Test check works.
        $this->assertTrue($this->factory->hasFilter('string'));

        $filter = $this->factory->getFilter('string');
        $this->assertEquals(get_class($filter), 'Phloem\\Core\\Filters\\NullFilter');
    }

    /**
     * Test the closure generation of the filter.
     *
     * @throws \Phloem\Core\Exception\FilterFactoryException
     */
    public function testClosureFilter() {
        $this->factory->setFilter('closure', function($factory) {
            return new NullFilter();
        });

        $filter = $this->factory->getFilter('closure');
        $this->assertEquals(get_class($filter), 'Phloem\\Core\\Filters\\NullFilter');
    }

    /**
     * Test using an filter for the factory clones the filter.
     *
     * @throws \Phloem\Core\Exception\FilterFactoryException
     */
    public function testCloneFilter() {
        $original = new NullFilter();

        $this->factory->setFilter('clone', $original);

        $filter = $this->factory->getFilter('clone');

        // Change $filter to ensure they are two different objects.
        $filter->failure = true;

        $this->assertEquals(get_class($filter), 'Phloem\\Core\\Filters\\NullFilter');
        $this->assertNotEquals($filter, $original);
    }

    /**
     * Test the evaluation routine processes correctly.
     */
    public function testEvaluateSuccess()
    {
        $context = new Context();
        $result = $this->factory->filter('{{ 1 + 1 }}', $context);
        $this->assertEquals('2', $result);
    }

    /**
     * Test that an invalid or container issue generates the proper exception.
     */
    public function testEvaluateContainerException()
    {
        $this->expectException('Phloem\\Core\\Exception\\FilterException');
        $context = new Context();
        unset($this->container[Phloem::PARSER]);
        $this->factory->filter('{{ 1 + 1 }}', $context);
    }

    /**
     * Test that an exception will generate the proper exception.
     */
    public function testEvaluateException()
    {
        $this->expectException('Phloem\\Core\\Exception\\FilterException');
        $context = new Context();
        $this->factory->filter('{{ 1 + 1 ( }}', $context);
    }

    /**
     * Test a normal string returns appropriate value.
     */
    public function testString()
    {
        $context = new Context();
        $result = $this->factory->filter('This is a test string', $context);
        $this->assertEquals('This is a test string', $result);
    }

    /**
     * Test multiple filters in a string.
     */
    public function testStringWithFilters()
    {
        $context = new Context();
        $result = $this->factory->filter('{{ 1 + 1 }}-{{2+2}}', $context);
        $this->assertEquals('2-4', $result);
    }

    /**
     * Test filter with pipes.
     */
    public function testStringWithPipe()
    {
        $context = new Context();
        $result = $this->factory->filter('A: {{ 1 + 1 | null | null }}', $context);
        $this->assertEquals('A: 2', $result);
    }

    /**
     * Test the filter when it is completely empty.
     */
    public function testEmptyFilter()
    {
        $context = new Context();
        $result = $this->factory->filter('{{}}', $context);
        $this->assertEquals('', $result);
    }

}
