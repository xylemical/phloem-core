<?php

/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\AbstractAction;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class WhileAction
 *
 * @package Phloem\Actions
 */
class WhileAction extends AbstractAction
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
