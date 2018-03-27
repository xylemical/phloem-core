<?php

/**
 * @file
 */

namespace Phloem\Loader;

/**
 * Class LoaderInterface
 *
 * @package Phloem\Loader
 */
interface LoaderInterface
{

    /**
     * Loads a path and provides a configuration array.
     *
     * @param $path
     *   A path that matches the glob format.
     *
     * @param $cwd
     *   The current working directory for relative path matching.
     *   This does not change the PHP internal working directory.
     *
     * @return array[]
     *   Indexed by the individual path it contains the contents of each
     *   to be loaded by the factory.
     */
    public function load($path, $cwd);
}
