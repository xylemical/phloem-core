<?php
/**
 * @file
 */

namespace Phloem\Exception;

use Phloem\Actions\NullAction;
use PHPUnit\Framework\TestCase;

class ExecutionExceptionTest extends TestCase
{
    public function testGetAction()
    {
        $action = new NullAction();
        $exception = new ExecutionException($action);
        $this->assertEquals($action, $exception->getAction());
    }
}

