<?php

/**
 * @file
 */

namespace Phloem\Loader;

use Symfony\Component\Yaml\Parser;

/**
 * Class YamlLoader
 *
 * @package Phloem\Loader
 */
class YamlLoader implements LoaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function load($path, $cwd)
    {
        // Create pattern based on whether the path is an absolute path.
        $pattern = preg_match('#^(/|[A-Z]:\\\\)#', $path) ? $path : $cwd . '/' . $path;

        // Get the yaml parser.
        $parser = new Parser();

        // Cycle through each filename, and parse the contents.
        $results = [];
        foreach (glob($pattern) as $filename) {
            $results[$filename] = $parser->parse(file_get_contents($filename));
        }

        return $results;
    }
}
