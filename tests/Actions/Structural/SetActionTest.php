<?php
/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\ActionTestCase;
use Phloem\Expression\Context;

class SetActionTest extends ActionTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->action = new SetAction();
    }

    /**
     * Test when there is no condition.
     */
    public function testNoConfig() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = [];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is no condition.
     */
    public function testBadConfig() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['set' => ''];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is no condition.
     */
    public function testEmptyConfig() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['set' => []];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test setting the value with a filter.
     */
    public function testFilter() {
        $config = [
          'set' => [
            'test' => 'A: {{ 1 + $test }}',
          ]
        ];
        $context = new Context([ 'test' => 2 ]);

        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        $this->assertEquals($context->getVariable('test'), 'A: 3');
    }

}
