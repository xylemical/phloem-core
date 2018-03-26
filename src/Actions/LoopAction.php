<?php

/**
 * @file
 */

namespace Phloem\Core\Actions;

use Phloem\Core\Action\AbstractAction;
use Phloem\Core\Exception\ConfigException;
use Phloem\Core\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class LoopAction
 *
 * @package Phloem\Core\Actions
 */
class LoopAction extends AbstractAction
{

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var \Phloem\Core\Action\ActionInterface
     */
    protected $action;

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        // Get the loop condition.
        $this->condition = $this->required($config, 'until', 'string');

        // Get the loop action.
        $this->action = $this->process(
          $this->required($config, 'loop')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        $condition = $this->condition('(' . $this->condition . ') != 0', $context);
        do {
            $this->perform($this->action, $context, 'loop');
        } while ($this->evaluate($condition, $context) === '0');
    }
}
