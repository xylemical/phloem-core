<?php

/**
 * @file
 */

namespace Phloem\Core\Actions;

use Phloem\Core\Action\AbstractAction;
use Phloem\Core\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class WhileAction
 *
 * @package Phloem\Core\Actions
 */
class WhileAction extends AbstractAction
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

        // Create the condition used by the while loop.
        $this->condition = $this->required($config, 'while', 'string');

        // Create the action performed for the while action.
        $this->action = $this->process(
          $this->required($config, 'do')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        $condition = $this->condition('(' . $this->condition . ') != 0', $context);
        while ($this->evaluate($condition, $context) === '1') {
            $this->perform($this->action, $context, 'while');
        }
    }
}
