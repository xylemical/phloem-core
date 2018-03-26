<?php

/**
 * @file
 */

namespace Phloem\Core\Actions;

use Phloem\Core\Action\ActionInterface;
use Phloem\Core\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class NullAction
 *
 * @package Phloem\Core\Actions
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
