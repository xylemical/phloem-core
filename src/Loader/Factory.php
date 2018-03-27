<?php

/**
 * @file
 */

namespace Phloem\Loader;

use Phloem\Exception\LoaderException;


/**
 * Class Factory
 *
 * @package Phloem\Loader
 */
class Factory
{

    /**
     * @var array
     */
    protected $loaders = [
      'yml' => 'Phloem\\Loader\\YamlLoader',
      'yaml' => 'Phloem\\Loader\\YamlLoader',
    ];

    /**
     * Set the loader for an extension.
     *
     * @param string $extension
     * @param $loader
     *
     * @return $this
     */
    public function setLoader($extension, $loader) {
        $this->loaders[$extension] = $loader;
        return $this;
    }

    /**
     * Get loader from the path, based on the extension.
     *
     * @param string $path
     *
     * @return \Phloem\Loader\LoaderInterface
     *
     * @throws \Phloem\Exception\LoaderException
     */
    public function getLoader($path) {
        // Get the extensions for the path.
        $exts = explode('.', basename($path));
        array_shift($exts);

        // Go through all of the extensions to find a loader.
        while (count($exts) > 0) {
            $ext = implode('.', $exts);
            array_shift($exts);

            // Attempt to match the extension.
            if (!array_key_exists($ext, $this->loaders)) {
                continue;
            }

            // Use lazy-loading for the loaders.
            if (is_string($this->loaders[$ext])) {
                $class = $this->loaders[$ext];
                return new $class();
            }

            // Use lazy-loading via callable.
            if ($this->loaders[$ext] instanceof \Closure) {
                return $this->loaders[$ext]($this);
            }

            // We have the loader already, so return a clone..
            if ($this->loaders[$ext] instanceof LoaderInterface) {
                return clone($this->loaders[$ext]);
            }

            throw new LoaderException($path, "{$ext} does not have a valid creation factory.");
        }

        throw new LoaderException($path, "{$path} does not have any loader available.");
    }

}
