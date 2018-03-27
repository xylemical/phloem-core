<?php

/**
 * @file
 */

namespace Phloem\Loader;

/**
 * Class NullLoader
 *
 * @package Phloem\Loader
 */
class NullLoader implements LoaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function load($path, $cwd)
    {
        return [];
    }
}