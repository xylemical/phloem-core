<?php
/**
 * @file
 */

namespace Phloem\Core\Exception;

use Phloem\Core\Actions\NullAction;
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

