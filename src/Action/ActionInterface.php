<?php

/**
 * @file
 */

namespace Phloem\Action;

use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class ActionInterface
 *
 * @package Phloem
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
     * @throws \Phloem\Exception\ConfigException
     */
    public function setup(ContainerInterface $container, array $config);

    /**
     * Executes the action.
     *
     * @param \Phloem\Expression\Context $context
     *
     * @return void
     *
     * @throws \Phloem\Exception\ExecutionException
     */
    public function execute(Context $context);
}
