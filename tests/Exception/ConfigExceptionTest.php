<?php
/**
 * @file
 */

namespace Phloem\Exception;

use PHPUnit\Framework\TestCase;

class ConfigExceptionTest extends TestCase
{
    public function testConfig()
    {
        $config = ['test'];
        $exception = new ConfigException($config);
        $this->assertEquals(['test'], $exception->getConfig());
    }
}
