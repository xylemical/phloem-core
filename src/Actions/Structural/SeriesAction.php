<?php

/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\AbstractAction;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class SeriesAction
 *
 * @package Phloem\Actions
 */
class SeriesAction extends AbstractAction
{
    /**
     * @var \Phloem\Action\ActionInterface[]
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
