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
 * Class LoopAction
 *
 * @package Phloem\Actions
 */
class LoopAction extends AbstractAction
{

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var \Phloem\Action\ActionInterface
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
