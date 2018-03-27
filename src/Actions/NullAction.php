<?php

/**
 * @file
 */

namespace Phloem\Actions;

use Phloem\Action\ActionInterface;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class NullAction
 *
 * @package Phloem\Actions
 */
class NullAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config) {
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
    }
}
