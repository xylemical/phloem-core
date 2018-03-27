<?php

/**
 * @file
 */

namespace Phloem\Actions\Structural;

use Phloem\Action\AbstractAction;
use Phloem\Action\ActionInterface;
use Phloem\Exception\ConfigException;
use Phloem\Expression\Context;
use Phloem\Phloem;
use Psr\Container\ContainerInterface;

/**
 * Class IfAction
 *
 * @package Phloem\Actions
 */
class IfAction extends AbstractAction
{

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var \Phloem\Action\ActionInterface
     */
    protected $then;

    /**
     * @var \Phloem\Action\ActionInterface
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
