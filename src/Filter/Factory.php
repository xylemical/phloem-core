<?php

/**
 * @file
 */

namespace Phloem\Filter;

use Phloem\Exception\FilterException;
use Phloem\Exception\FilterFactoryException;
use Phloem\Expression\Context;
use Phloem\Phloem;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Class Factory
 *
 * @package Phloem\Filter
 */
class Factory
{

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    protected $filters = [
      'null' => 'Phloem\\Filters\\NullFilter',
    ];

    /**
     * Factory constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Get the container.
     *
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get the filter by $name.
     *
     * @param string $name
     *
     * @return \Phloem\Filter\FilterInterface
     *
     * @throws \Phloem\Exception\FilterFactoryException
     */
    public function getFilter($name) {
        // Default to filters defined by this factory.
        if (array_key_exists($name, $this->filters)) {
            // Use lazy-loading for the filters.
            if (is_string($this->filters[$name])) {
                $class = $this->filters[$name];
                return new $class();
            }

            // Use lazy-loading via callable.
            if ($this->filters[$name] instanceof \Closure) {
                return $this->filters[$name]($this);
            }

            // We have the filter already, so return a clone..
            if ($this->filters[$name] instanceof FilterInterface) {
                return clone($this->filters[$name]);
            }

            throw new FilterFactoryException("{$name} does not have a valid creation factory.");
        }

        throw new FilterFactoryException("{$name} filter is not defined.");
    }

    /**
     * Set an filter by $name.
     *
     * @param string $name
     * @param string|\Closure|\Phloem\Filter\FilterInterface $filter
     *
     * @return static
     */
    public function setFilter($name, $filter)
    {
        $this->filters[$name] = $filter;
        return $this;
    }

    /**
     * Indicate an filter has been defined for $name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasFilter($name) {
        return array_key_exists($name, $this->filters);
    }

    /**
     * Filters a string using pipes.
     *
     * @param string $string
     * @param \Phloem\Expression\Context $context
     *
     * @return string
     *
     * @throws \Phloem\Exception\FilterException
     * @throws \Phloem\Exception\FilterFactoryException
     */
    public function filter($string, Context $context) {
        preg_match_all('/\{\{(.*?)\}\}/s', $string, $matches, PREG_SET_ORDER);
        if (!count($matches)) {
            return $string;
        }

        // Process each of the filtered areas.
        $replacements = [];
        foreach ($matches as $match) {
            $replacements[$match[0]] = $this->process($match[1], $context);
        }

        // Special case where the replace matches the entire string.
        if (count($replacements) === 1 && $match[0] === $string) {
            return $replacements[$match[0]];
        }

        // Otherwise convert all the replacement matches to strings.
        $replacements = array_map('strval', $replacements);

        // And perform replacements.
        return str_replace(array_keys($replacements), array_values($replacements), $string);
    }

    /**
     * Processes the filtered string.
     *
     * @param string $string
     * @param \Phloem\Expression\Context $context
     *
     * @return string
     *
     * @throws \Phloem\Exception\FilterException
     * @throws \Phloem\Exception\FilterFactoryException
     */
    protected function process($string, Context $context)
    {
        // Split by pipes.
        $parts = explode('|', $string);
        if (empty($parts[0])) {
            return '';
        }

        // Process the value through the evaluation system.
        $value = $this->evaluate(array_shift($parts), $context);

        // Now continue with any piped filters.
        foreach ($parts as $part) {
            $value = $this->pipe(trim($part), $context, $value);
        }

        return $value;
    }

    /**
     * Evaluates an expression string via the filter.
     *
     * @param string $string
     * @param \Phloem\Expression\Context $context
     *
     * @return string
     *
     * @throws \Phloem\Exception\FilterException
     */
    protected function evaluate($string, Context $context)
    {
        try {
            $tokens = $this->getContainer()
                           ->get(Phloem::PARSER)
                           ->parse($string);
            return $this->getContainer()
                        ->get(Phloem::EVALUATOR)
                        ->evaluate($tokens, $context);
        }
        catch (ContainerExceptionInterface $e) {
            throw new FilterException($e->getMessage(), $e->getCode(), $e);
        }
        catch (\Exception $e) {
            throw new FilterException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Pipe the value through to the filter.
     *
     * @param $string
     * @param \Phloem\Expression\Context $context
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws \Phloem\Exception\FilterFactoryException
     */
    protected function pipe($string, Context $context, $value)
    {
        // TODO: Add arguments to the filter command.
        // Simple filter name check, no arguments currently supported.
        if (preg_match('/^[a-zA-Z\-]+$/', $string, $match)) {
            $filter = $this->getFilter($match[0]);
            // TODO: Add arguments to the filter setup.
            $filter->setup($this->container, []);
            $value = $filter->apply($value, $context);
        }
        return $value;
    }


}
