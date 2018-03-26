<?php

/**
 * @file
 */

namespace Phloem\Core\Filters;

use Phloem\Core\Expression\Context;
use Phloem\Core\Filter\FilterInterface;
use Psr\Container\ContainerInterface;

/**
 * Class NullFilter
 *
 * @package Phloem\Core\Filters
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
