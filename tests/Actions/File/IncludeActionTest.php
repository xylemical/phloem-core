<?php
/**
 * @file
 */

namespace Phloem\Actions\File;

use Phloem\Action\ActionTestCase;
use Phloem\Actions\File\IncludeAction;
use Phloem\Expression\Context;
use Phloem\Phloem;

class IncludeActionTest extends ActionTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->action = new IncludeAction();
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

        $config = ['include' => ''];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is Bool condition.
     */
    public function testBoolCondition() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['include' => false];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test invalid include file config.
     */
    public function testInvalidConfig() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['include' => [['test.yml']]];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test loading an individual file.
     */
    public function testSingleFile()
    {
        $context = new Context();

        $config = [
          'include' => __DIR__ . '/fixtures/task1.yml',
        ];

        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        // This ensures the task was actually executed.
        $this->assertEquals('task1', $context->getVariable('task1'));
        $this->assertEquals('task2', $context->getVariable('task2'));
        $this->assertEquals(null, $context->getVariable('task3'));
    }

    /**
     * Tests the glob loading behaviour.
     */
    public function testGlobLoad()
    {
        $context = new Context();

        $config = [
          'include' => [
                __DIR__ . '/fixtures/*.yml',
                __DIR__ . '/fixtures/**/*.yml',
          ]
        ];

        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        // This ensures the task was actually executed.
        $this->assertEquals('task1', $context->getVariable('task1'));
        $this->assertEquals('task2', $context->getVariable('task2'));
        $this->assertEquals('task3', $context->getVariable('task3'));
        $this->assertEquals('task4', $context->getVariable('task4'));

        // Ensure the task loaded from include is available.
        $this->assertTrue($this->pimple[Phloem::ACTIONS]->hasAction('dummy-task'));
    }

}
