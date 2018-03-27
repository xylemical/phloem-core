<?php

/**
 * @file
 */

namespace Phloem\Action;

use Phloem\Exception\ConfigException;
use Phloem\Expression\Context;
use Phloem\Phloem;
use Psr\Container\ContainerInterface;

/**
 * Class AbstractAction
 *
 * @package Phloem\Action
 */
abstract class AbstractAction implements ActionInterface
{

    /**
     * @var \Phloem\Manager
     */
    private $manager;

    /**
     * @var \Phloem\Action\Factory
     */
    private $factory;

    /**
     * @var \Xylemical\Expressions\Parser
     */
    private $parser;

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        $this->factory = $container->get(Phloem::ACTIONS);
        $this->parser = $container->get(Phloem::PARSER);
        $this->manager = $container->get(Phloem::MANAGER);
    }

    /**
     * {@inheritdoc}
     */
    abstract function execute(Context $context);

    /**
     * Processes configuration into an action.
     *
     * @param string|array $config
     *
     * @return \Phloem\Action\ActionInterface
     *
     * @throws \Phloem\Exception\ActionFactoryException
     * @throws \Phloem\Exception\ConfigException
     */
    protected function process($config)
    {
        return $this->factory->process($config);
    }

    /**
     * Evaluates a condition.
     *
     * @param string|\Xylemical\Expressions\Token[] $string
     * @param \Phloem\Expression\Context $context
     *
     * @return string
     *
     * @throws \Phloem\Exception\ExecutionException
     */
    protected function evaluate($string, Context $context)
    {
        return $this->factory->evaluate($this, $string, $context);
    }

    /**
     * Performs an action.
     *
     * @param \Phloem\Action\ActionInterface $action
     * @param \Phloem\Expression\Context $context
     * @param string $target
     *
     * @throws \Phloem\Exception\ExecutionException
     */
    protected function perform(ActionInterface $action, Context $context, $target)
    {
        $context->push($action, $target);
        $action->execute($context);
        $context->pop();

    }

    /**
     * Get the action factory.
     *
     * @return \Phloem\Action\Factory
     */
    protected function getFactory()
    {
        return $this->factory;
    }

    /**
     * Get the manager.
     *
     * @return \Phloem\Manager
     */
    protected function getManager()
    {
        return $this->manager;
    }

    /**
     * Does a type check of a value.
     *
     * @param string $value
     * @param mixed $type
     *
     * @return bool
     */
    protected function typeOf($value, $type) {
       switch ($type) {
           case 'bool':
               return is_bool($value);
           case 'int':
               return is_int($value);
           case 'array':
               return is_array($value);
           case 'string':
               return is_string($value);
           default:
               return false;
       }
    }

    /**
     * Check config for a required key, with optional $type check.
     *
     * @param array $config
     * @param string $key
     * @param string $type
     * @param null $message
     *
     * @return mixed
     *
     * @throws \Phloem\Exception\ConfigException
     */
    protected function required(array $config, $key, $type = null, $message = null) {
        if (!isset($config[$key])) {
            $message = $message ? $message : "Missing '{$key}'";
            throw new ConfigException($config, $message);
        }

        // Check that it has a value.
        if (!$config[$key] && $config[$key] !== '0') {
            $message = $message ? $message : "'{$key}' requires a value.";
            throw new ConfigException($config, $message);
        }

        // Optional type check.
        if ($type && !$this->typeOf($config[$key], $type)) {
           throw new ConfigException($config, "'{$key}' needs to be a type of {$type}.");
        }

        return $config[$key];
    }

    /**
     * Check config for an optional key, with optional $type check.
     *
     * @param array $config
     * @param string $key
     * @param mixed $default
     * @param string $type
     *
     * @return mixed|null
     *
     * @throws \Phloem\Exception\ConfigException
     */
    protected function optional(array $config, $key, $default = null, $type = null)
    {
        // Provide a default value for an optional config item.
        if (empty($config[$key])) {
            return $default;
        }

        // Optional type check.
        if ($type && !$this->typeOf($config[$key], $type)) {
            throw new ConfigException($config, "'{$key}' needs to be a type of {$type}.");
        }

        return $config[$key];
    }

    /**
     * Converts a string into the token list.
     *
     * @param $string
     *
     * @return \Xylemical\Expressions\Token[]
     *
     * @throws \Phloem\Exception\ConfigException
     */
    protected function condition($string)
    {
        try {
            return $this->parser->parse($string);
        }
        catch (\Exception $e) {
            throw new ConfigException([], $e->getMessage(), $e->getCode(), $e);
        }
    }
}
