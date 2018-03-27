<?php
/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\ActionTestCase;
use Phloem\Expression\Context;

class UnsetActionTest extends ActionTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->action = new UnsetAction();
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

        $config = ['unset' => ''];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is no condition.
     */
    public function testEmptyConfig() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['unset' => []];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test setting the value with a filter.
     */
    public function testFilter() {
        $config = [
          'unset' => ['test']
        ];
        $context = new Context([ 'test' => 2 ]);

        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        $this->assertEquals($context->getVariable('test'), NULL);
    }

}
