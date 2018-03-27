<?php

/**
 * @file
 */

namespace Phloem\Actions\Command;

use Phloem\Action\AbstractAction;
use Phloem\Exception\ConfigException;
use Phloem\Exception\ExecutionException;
use Phloem\Expression\Context;
use Psr\Container\ContainerInterface;

/**
 * Class AbstractCommandAction
 *
 * @package Phloem\Actions\Command
 */
abstract class AbstractCommandAction extends AbstractAction
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var array
     */
    protected $environment = [];

    /**
     * @var string
     */
    protected $workingDirectory;

    /**
     * The variable to register with the information from the command.
     *
     * @var string
     */
    protected $registerVariable = '';

    /**
     * @var string|boolean
     */
    protected $ignoreErrors = '';

    /**
     * {@inheritdoc}
     */
    public function setup(ContainerInterface $container, array $config)
    {
        parent::setup($container, $config);

        // Ensure the working directory is setup.
        $this->workingDirectory = getcwd();

        // Registers a variable name.
        $this->registerVariable = $this->optional($config, 'register', $this->registerVariable, 'string');

        // Get the details for ignoring errors.
        $this->ignoreErrors = $this->optional($config, 'ignore-errors', $this->ignoreErrors, 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Context $context)
    {
        // Evaluate ignoring the errors
        $this->ignoreErrors = $this->ignoreErrors ?
          $this->evaluate("({$this->ignoreErrors}) !== 0", $context) === "1" :
          false;

        // Run the command.
        $this->run($context);
    }

    /**
     * Setup options for the command.
     *
     * @param $config
     *
     * @throws \Phloem\Exception\ConfigException
     */
    protected function options($config)
    {
        // Options are output as is, in the order defined.
        $options = $this->optional($config, 'options', [], 'array');
        foreach ($options as $opt) {
            if (!is_string($opt)) {
                throw new ConfigException($config, 'Options must only be a string.');
            }
            $this->options[] = $opt;
        }
    }

    /**
     * Setup the arguments for the command.
     *
     * @param $config
     *
     * @throws \Phloem\Exception\ConfigException
     */
    protected function arguments($config)
    {
        // Arguments are output in the form --{argument}={value} when value is
        // a string, or --{argument} when the value is a boolean.
        $arguments = $this->optional($config, 'arguments', [], 'array');
        foreach ($arguments as $arg => $value) {
            if (is_string($value) || is_bool($value)) {
                $this->arguments[$arg] = is_string($value) ? $value : $value;
                continue;
            }
            throw new ConfigException($config, 'Arguments require a string or boolean value.');
        }
    }

    /**
     * Setup the command to be run from the command line.
     *
     * @return string
     */
    protected function command()
    {
        // Setup the command path.
        $cmd[] = escapeshellcmd($this->getCommandPath());

        // Use the options.
        foreach ($this->options as $option) {
            $cmd[] = $option;
        }

        // Use the arguments.
        foreach ($this->arguments as $argument => $value) {
            if (is_bool($value)) {
                $cmd[] = "--{$argument}";
            }
            else {
                $cmd[] = "--{$argument}={$value}";
            }
        }

        return implode(' ', $cmd);
    }

    /**
     * Executes the command to provide the details.
     *
     * @param \Phloem\Expression\Context $context
     *
     * @throws \Phloem\Exception\ExecutionException
     */
    protected function run(Context $context)
    {
        $command = $this->command();

        // Setup the descriptors for the
        $descs = [
          0 => ['pipe','r'], // stdin
          1 => ['pipe','w'], // stdout
          2 => ['pipe','w'], // stderr
        ];

        $proc = proc_open($command, $descs,$pipes, $this->workingDirectory, $this->environment);
        if ($this->registerVariable) {
            // Save the standard output.
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            // Save the standard error output.
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // Save the return code from the process.
            $code = proc_close($proc);

            // Create the details to be saved to the registerVariable.
            $details = [
              'command' => $command,
              'code' => $code,
              'stdout' => $stdout,
              'stderr' => $stderr,
            ];

            // Save the registerVariable.
            $context->setVariable($this->registerVariable, $details);

        }
        else {
            $code = proc_close($proc);
        }

        // We throw an execution error due to a failed command.
        if (!$this->ignoreErrors && $code) {
            throw new ExecutionException($this, "Command failed with exit code {$code}");
        }
    }

    /**
     * Gets the command path for the command line program.
     *
     * @return string
     */
    abstract protected function getCommandPath();
}

