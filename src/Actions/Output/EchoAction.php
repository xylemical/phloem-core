<?php

/**
 * @file
 */

namespace Phloem\Actions\Output;

use Phloem\Action\AbstractAction;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class EchoAction
 *
 * @package Phloem\Actions\Output
 */
class EchoAction extends AbstractAction
{
    /**
     * @var string
     */
    protected $message;

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        // Get the if condition.
        $this->message = $this->optional($config, 'echo', '', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context) {
        echo $this->filter($this->message, $context) . "\n";
    }
}