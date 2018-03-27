<?php

/**
 * @file
 */

namespace Phloem\Action;

use Phloem\Expression\Context;
use Phloem\Phloem;
use Psr\Container\ContainerInterface;

/**
 * Class ActionFilterTrait
 *
 * @package Phloem\Action
 */
trait ActionFilterTrait
{

    /**
     * @var \Phloem\Filter\Factory
     */
    protected $filters;

    /**
     * Setup the filter factory from the container.
     *
     * @param \Psr\Container\ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function setupFilters(ContainerInterface $container) {
        $this->filters = $container->get(Phloem::FILTERS);
    }

    /**
     * Filter a value.
     *
     * @param string $string
     * @param \Phloem\Expression\Context $context
     *
     * @return string
     *
     * @throws \Phloem\Exception\FilterException
     * @throws \Phloem\Exception\FilterFactoryException
     */
    public function filter($string, Context $context) {
        return $this->filters->filter($string, $context);
    }
}
