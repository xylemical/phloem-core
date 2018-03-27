<?php
/**
 * @file
 */

namespace Phloem\Loader;

use Phloem\Loader\Factory;
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

class FactoryTest extends TestCase
{

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Phloem\Loader\Factory
     */
    protected $factory;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = new \Pimple\Container();
        $container = new Container($this->container);
        $this->factory = new Factory();
        $this->container[Phloem::ACTIONS] = new ActionFactory($container);
        $this->container[Phloem::PARSER] = new Parser(new Lexer(new ExpressionFactory(new BcMath())));
        $this->container[Phloem::MANAGER] = new Manager();
        $this->container[Phloem::EVALUATOR] = new Evaluator();
    }

    /**
     * Test when no loader is defined.
     *
     * @throws \Phloem\Exception\LoaderException
     */
    public function testNoLoaderDefined() {
        $this->expectException('\\Phloem\\Exception\\LoaderException');

        $this->factory->getLoader('filename.unknown');
    }

    /**
     * Test when somebody provides a bad loader factory method.
     *
     * @throws \Phloem\Exception\LoaderException
     */
    public function testBadLoaderDefined() {
        $this->expectException('\\Phloem\\Exception\\LoaderException');

        $this->factory->setLoader('bad', true);
        $this->factory->getLoader('filename.bad');
    }

    /**
     * Test the lazy-loading of the loader.
     *
     * @throws \Phloem\Exception\LoaderException
     */
    public function testStringLoader() {
        $this->factory->setLoader('string', 'Phloem\\Loader\\NullLoader');

        $loader = $this->factory->getLoader('filename.extra.string');
        $this->assertEquals(get_class($loader), 'Phloem\\Loader\\NullLoader');
    }

    /**
     * Test the closure generation of the loader.
     *
     * @throws \Phloem\Exception\LoaderException
     */
    public function testClosureLoader() {
        $this->factory->setLoader('closure', function($factory) {
            return new NullLoader();
        });

        $loader = $this->factory->getLoader('filename.closure');
        $this->assertEquals(get_class($loader), 'Phloem\\Loader\\NullLoader');
    }

    /**
     * Test using an loader for the factory clones the loader.
     *
     * @throws \Phloem\Exception\LoaderException
     */
    public function testCloneLoader() {
        $original = new NullLoader();

        $this->factory->setLoader('clone', $original);

        $loader = $this->factory->getLoader('filename.clone');

        // Change $loader to ensure they are two different objects.
        $loader->failure = true;

        $this->assertEquals(get_class($loader), 'Phloem\\Loader\\NullLoader');
        $this->assertNotEquals($loader, $original);
    }
}
