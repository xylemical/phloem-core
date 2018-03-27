<?php
/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\ActionTestCase;
use Phloem\Expression\Context;

class LoopActionTest extends ActionTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->action = new LoopAction();
    }

    /**
     * Test when there is no condition.
     */
    public function testNoConfig()
    {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = [];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is no condition.
     */
    public function testNoCondition()
    {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['until' => ''];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is Bool condition.
     */
    public function testBoolCondition()
    {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['until' => false];

        $this->action->setup($this->container, $config);
    }

    /**
     * Tests the execution paths for if.
     */
    public function testExecution()
    {
        $context = new Context();

        $context->setVariable('test', 10);
        $context->setVariable('count', 0);

        $config = [
          'loop' => [
            [
              'set' => [
                'test' => '{{ $test - 1 }}',
              ],
            ],
            [
              'set' => [
                'count' => '{{ $count + 1 }}',
              ],
            ],
          ],
          'until' => '$test <= 0',
        ];
        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        $this->assertEquals('10', $context->getVariable('count'));
    }

}
