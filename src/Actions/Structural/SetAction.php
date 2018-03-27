<?php

/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\AbstractAction;
use Phloem\Action\ActionFilterTrait;
use Phloem\Exception\ConfigException;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class SetAction
 *
 * @package Phloem\Actions
 */
class SetAction extends AbstractAction
{
    use ActionFilterTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);
        $this->setupFilters($container);

        // Get the variables to set.
        $this->config = $this->required($config, 'set', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context)
    {
        foreach ($this->config as $name => $value) {
            $value = $this->filter($value, $context);
            $context->setVariable($name, $value);
        }
    }
}
