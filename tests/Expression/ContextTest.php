<?php
/**
 * @file
 */

namespace Phloem\Core\Expression;

use Phloem\Core\Actions\NullAction;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{

    /**
     * @var \Phloem\Core\Expression\Context
     */
    protected $context;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->context = new Context();
    }

    /**
     * Test basic push/pop mechanism.
     */
    public function testSimplePushPop() {
        $context = $this->context;

        $action = new NullAction();
        $context->push($action, 'test');

        $this->assertEquals([$action], $context->getActions());
        $this->assertEquals(['global', 'test'], $context->getHistory());

        $context->pop();

        $this->assertEquals($context->getHistory(), ['global']);

        // Pop too many times.
        $context->pop();
        $this->assertEquals($context->getHistory(), ['global']);
    }
}
