<?php

/**
 * @file
 */

namespace Phloem\Core\Actions;

use Phloem\Core\Action\AbstractAction;
use Phloem\Core\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class SeriesAction
 *
 * @package Phloem\Core\Actions
 */
class SeriesAction extends AbstractAction
{
    /**
     * @var \Phloem\Core\Action\ActionInterface[]
     */
    protected $actions = [];

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        foreach ($this->optional($config, 'series', [], 'array') as $item) {
            $this->actions[] = $this->process($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        foreach ($this->actions as $action) {
            $this->perform($action, $context, 'block');
        }
    }
}
