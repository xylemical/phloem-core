<?php
/**
 * @file
 */

namespace Phloem\Core\Actions;

use Phloem\Core\Action\ActionTestCase;
use Phloem\Core\Expression\Context;

class WhileActionTest extends ActionTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->action = new WhileAction();
    }

    /**
     * Test when there is no condition.
     */
    public function testNoConfig()
    {
        $this->expectException('Phloem\\Core\\Exception\\ConfigException');

        $config = [];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is no condition.
     */
    public function testNoCondition()
    {
        $this->expectException('Phloem\\Core\\Exception\\ConfigException');

        $config = ['while' => ''];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is Bool condition.
     */
    public function testBoolCondition()
    {
        $this->expectException('Phloem\\Core\\Exception\\ConfigException');

        $config = ['while' => false];

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
          'while' => '$test > 0',
          'do' => [
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
        ];
        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        $this->assertEquals('10', $context->getVariable('count'));
    }

}
