<?php

/**
 * @file
 */

namespace Phloem\Filter;

use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class FilterInterface
 *
 * @package Phloem\Filter
 */
interface FilterInterface
{

    /**
     * Sets the filter configuration details.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param $config
     *
     * @return void
     */
    public function setup(ContainerInterface $container, $config);

    /**
     * Applies the filter to the value.
     *
     * @param mixed $value
     * @param \Phloem\Expression\Context $context
     *
     * @return mixed
     */
    public function apply($value, Context $context);
}
