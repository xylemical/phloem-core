<?php
/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\ActionTestCase;
use Phloem\Expression\Context;

class IfActionTest extends ActionTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->action = new IfAction();
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
    public function testNoCondition() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['if' => ''];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is Bool condition.
     */
    public function testBoolCondition() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['if' => false];

        $this->action->setup($this->container, $config);
    }

    /**
     * Tests the execution paths for if.
     */
    public function testExecution()
    {
        $context = new Context();

        $config = [
          'if' => '0',
          'then' => [
            'set' => ['test' => 'then', 'current' => 'global']
          ],
          'else' => [
            'set' => ['test' => 'else', 'current' => 'global']
          ]
        ];
        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        $this->assertEquals($context->getVariable('test'), 'else');

        $config['if'] = '1 + 1';
        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        $this->assertEquals($context->getVariable('test'), 'then');
    }
}
