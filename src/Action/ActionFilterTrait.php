<?php

/**
 * @file
 */

namespace Phloem\Core\Action;

use Phloem\Core\Expression\Context;
use Phloem\Core\Phloem;
use Psr\Container\ContainerInterface;

/**
 * Class ActionFilterTrait
 *
 * @package Phloem\Core\Action
 */
trait ActionFilterTrait
{

    /**
     * @var \Phloem\Core\Filter\Factory
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
     * @param \Phloem\Core\Expression\Context $context
     *
     * @return string
     *
     * @throws \Phloem\Core\Exception\FilterException
     * @throws \Phloem\Core\Exception\FilterFactoryException
     */
    public function filter($string, Context $context) {
        return $this->filters->filter($string, $context);
    }
}
