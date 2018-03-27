<?php

/**
 * @file
 */

namespace Phloem\Filters;

use Phloem\Expression\Context;
use Phloem\Filter\FilterInterface;
use Psr\Container\ContainerInterface;

/**
 * Class NullFilter
 *
 * @package Phloem\Filters
 */
class NullFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, $config) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply($value, Context $context) {
        return $value;
    }
}
