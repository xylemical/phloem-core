<?php
/**
 * @file
 */

namespace Phloem\Actions\Command;

use Phloem\Action\ActionTestCase;
use Phloem\Expression\Context;

class CommandActionTest extends ActionTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->action = new CommandAction();
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

        $config = ['command' => ''];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test when there is Bool condition.
     */
    public function testBoolCondition() {
        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = ['command' => false];

        $this->action->setup($this->container, $config);
    }

    /**
     * Test the basic execution of the command.
     */
    public function testExecution() {
        $context = new Context();

        $config = [
          'command' => 'php test.php',
          'options' => [
            '-c'
          ],
          'arguments' => [
            'compiler' => 'aggregate',
            'heavenly' => true,
          ],
          'path' => __DIR__ . '/fixtures',
          'register' => 'results',
          'ignore-errors' => true,
        ];

        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        // Check the results are recorded.
        $results = $context->getVariable('results');
        $this->assertNotNull($results);

        // Check all the details are there.
        $this->assertArraySubset(['command', 'code', 'stdout', 'stderr'], array_keys($results));
        $this->assertEquals("php test.php -c --compiler=aggregate --heavenly", $results['command']);
        $this->assertEquals(1, $results['code']);
        $this->assertEquals("test.php\n-c\n--compiler=aggregate\n--heavenly\n", $results['stdout']);
        $this->assertEquals("This\nis\nstderr", $results['stderr']);
    }

    /**
     * Test the basic execution of the command.
     */
    public function testExecutionWithoutRegister() {
        $context = new Context();

        $config = [
          'command' => 'php test.php',
          'options' => [
            '-c'
          ],
          'arguments' => [
            'compiler' => 'aggregate',
            'heavenly' => true,
          ],
          'path' => __DIR__ . '/fixtures',
          'ignore-errors' => true,
        ];

        $this->action->setup($this->container, $config);
        $this->action->execute($context);

        // Check the results are recorded.
        $results = $context->getVariable('results');
        $this->assertNull($results);
    }

    /**
     * Test execution generates an error with ignore errors.
     */
    public function testExecutionException() {
        $context = new Context();

        $this->expectException('Phloem\\Exception\\ExecutionException');

        $config = [
          'command' => 'php test.php',
          'path' => __DIR__ . '/fixtures',
          'register' => 'results',
        ];

        $this->action->setup($this->container, $config);
        $this->action->execute($context);
    }

    /**
     * Test invalid option
     */
    public function testInvalidOption() {
        $context = new Context();

        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = [
          'command' => 'php test.php',
          'options' => [
             ['-c']
          ],
          'path' => __DIR__ . '/fixtures',
          'register' => 'results',
          'ignore-errors' => true,
        ];

        $this->action->setup($this->container, $config);
        $this->action->execute($context);
    }

    /**
     * Test invalid argument
     */
    public function testInvalidArgument() {
        $context = new Context();

        $this->expectException('Phloem\\Exception\\ConfigException');

        $config = [
          'command' => 'php test.php',
          'arguments' => [
            ['-c']
          ],
          'path' => __DIR__ . '/fixtures',
          'register' => 'results',
          'ignore-errors' => true,
        ];

        $this->action->setup($this->container, $config);
        $this->action->execute($context);
    }
}

