<?php

/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\AbstractAction;
use Phloem\Exception\ConfigException;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class UnsetAction
 *
 * @package Phloem\Actions
 */
class UnsetAction extends AbstractAction
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var
     */
    protected $scope;

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        // Get the variables to set.
        $this->config = $this->required($config, 'unset', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        foreach ($this->config as $name) {
            $context->setVariable($name, null);
        }
    }
}
