<?php
/**
 * @file
 */

namespace Phloem;

use Phloem\Expression\Context;
use PHPUnit\Framework\TestCase;

class PhloemTest extends TestCase
{

    /**
     * @throws \Phloem\Exception\ActionFactoryException
     * @throws \Phloem\Exception\ConfigException
     * @throws \Phloem\Exception\ExecutionException
     * @throws \Phloem\Exception\LoaderException
     */
    public function testUseCase()
    {
        $phloem = new Phloem();

        $actions = $phloem->load(__DIR__ . '/fixtures/task1.yml');

        $this->assertTrue(is_array($actions));
        $this->assertEquals(count($actions), 1);
        $this->assertTrue(isset($actions[0]['set']));

        $context = new Context();

        $phloem->execute($actions, $context);

        $this->assertEquals($context->getVariable('task1'), 'task1');
    }

    /**
     *
     */
    public function testContainerFail()
    {
        $this->expectException("\\Phloem\\Exception\\ExecutionException");

        $phloem = new Phloem();

        $actions = $phloem->load(__DIR__ . '/fixtures/task1.yml');

        // Remove the container.
        unset($phloem->getContainer()[Phloem::EVENTS]);

        // Check that the container no longer exists.
        $this->assertFalse($phloem->getPsrContainer()->has(Phloem::EVENTS));

        // Expect it to throw an exception.

        $context = new Context();

        $phloem->execute($actions, $context);
    }
}

