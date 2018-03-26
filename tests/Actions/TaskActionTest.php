<?php
/**
 * @file
 */

namespace Phloem\Core\Actions;

use Phloem\Core\Action\ActionTestCase;
use Phloem\Core\Expression\Context;

class TaskActionTest extends ActionTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->action = new TaskAction();
    }

    /**
     * Test when there is no condition.
     */
    public function testNoConfig() {
        $this->expectException('Phloem\\Core\\Exception\\ConfigException');

        $config = [];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is no condition.
     */
    public function testNoCondition() {
        $this->expectException('Phloem\\Core\\Exception\\ConfigException');

        $config = ['task' => ''];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is Bool condition.
     */
    public function testBoolCondition() {
        $this->expectException('Phloem\\Core\\Exception\\ConfigException');

        $config = ['task' => false];

        $this->action->setup($this->container, $config);
    }

    /**
     * Tests that a dependency that is not a string causes an exception.
     */
    public function testBadDependency() {
        $this->expectException('Phloem\\Core\\Exception\\ConfigException');

        $config = [
          'task' => 'dummy',
          'dependencies' => ['null', ['null']]
        ];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test that duplication of the task causes an exception.
     */
    public function testDuplication() {
        $this->expectException('Phloem\\Core\\Exception\\ConfigException');

        $config = [
          'task' => 'dummy'
        ];

        $this->action->setup($this->container, $config);
        $this->action->setup($this->container, $config);
    }

    public function testExecution()
    {
        $config = [
          'task' => 'dummy',
          'dependencies' => ['null'],
          'actions' => [
                'set' => [
                  'test' => 'dummy',
                ]
            ]
        ];

        $context = new Context();

        // Setup the task.
        $this->action->setup($this->container, $config);

        $config = [
          'dummy' => [
            'dummy' => true,
          ],
        ];

        // Run the task.
        $action = new RunAction($this->action);

        $action->setup($this->container, $config);
        $action->execute($context);

        $this->assertEquals($context->getVariable('test'), 'dummy');
        $this->assertEquals($context->getVariable('dummy'), true);
    }


}
