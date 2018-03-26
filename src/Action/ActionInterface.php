<?php

/**
 * @file
 */

namespace Phloem\Core\Action;

use Phloem\Core\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class ActionInterface
 *
 * @package Phloem\Core\Build
 */
interface ActionInterface
{

    /**
     * Performs the setup for the action.
     *
     * @param \Psr\Container\ContainerInterface $container
     *   The factory used for generating actions.
     *
     * @param array $config
     *   The configuration given for the action.
     *
     * @return void
     *
     * @throws \Phloem\Core\Exception\ConfigException
     */
    public function setup(ContainerInterface $container, array $config);

    /**
     * Executes the action.
     *
     * @param \Phloem\Core\Expression\Context $context
     *
     * @return void
     *
     * @throws \Phloem\Core\Exception\ExecutionException
     */
    public function execute(Context $context);
}
