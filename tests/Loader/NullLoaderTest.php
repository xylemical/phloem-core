<?php
/**
 * @file
 */

namespace Phloem\Loader;

use PHPUnit\Framework\TestCase;

class NullLoaderTest extends TestCase
{
    public function testLoad()
    {
        $loader = new NullLoader();
        $this->assertEmpty($loader->load('filename.path', ''));
    }

}
