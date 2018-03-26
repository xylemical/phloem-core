<?php

/**
 * @file
 */

namespace Phloem\Core\Actions;

use Phloem\Core\Action\AbstractAction;
use Phloem\Core\Action\ActionInterface;
use Phloem\Core\Exception\ConfigException;
use Phloem\Core\Expression\Context;
use Phloem\Core\Phloem;
use Psr\Container\ContainerInterface;

/**
 * Class IfAction
 *
 * @package Phloem\Core\Actions
 */
class IfAction extends AbstractAction
{

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var \Phloem\Core\Action\ActionInterface
     */
    protected $then;

    /**
     * @var \Phloem\Core\Action\ActionInterface
     */
    protected $else;

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        // Get the if condition.
        $this->condition = $this->required($config, 'if', 'string');

        // Get the then action.
        $this->then = $this->process(
          $this->optional($config, 'then', [])
        );

        // Get the else action.
        $this->else = $this->process(
          $this->optional($config, 'else', [])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        // Evaluation of the expression.
        if ($this->evaluate('(' . $this->condition . ') != 0', $context) === '1') {
            $this->perform($this->then, $context, 'if-then');
        }
        else {
            $this->perform($this->else, $context, 'if-else');
        }
    }
}
