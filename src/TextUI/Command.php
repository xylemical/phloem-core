<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phloem\TextUI;

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use GetOpt\ArgumentException;
use GetOpt\GetOpt;
use GetOpt\Option;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Phloem\Phloem;
use Phloem\TextUI\Commands\RunCommand;
use Phloem\TextUI\Commands\TasksCommand;

/**
 * The command line version of Phloem.
 */
class Command
{

    /**
     * The name of the application.
     *
     * @var string
     */
    protected $name = 'phloem';

    /**
     * The version of the application.
     *
     * @var string
     */
    protected $version;

    /**
     * @var \Phloem\Phloem
     */
    protected $phloem;

    /**
     * @param bool $exit
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public static function main($exit = true)
    {
        $command = new static;

        return $command->run($_SERVER['argv'], $exit);
    }

    /**
     * @param array $argv
     * @param bool  $exit
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     * @throws Exception
     *
     * @return int
     */
    public function run(array $argv, $exit = true): int
    {
        $this->getVersion();

        // Create the core section.
        $this->phloem = $this->createPhloem();

        // Process the arguments.
        $result = (int)$this->handleArguments($argv);
        if ($exit) {
            exit($result);
        }

        return $result;
    }

    /**
     * Get the current version of the application.
     */
    public function getVersion()
    {
        $this->version = 'dev-master';

        // Read the library name from this composer.json
        $composer = json_decode(file_get_contents(__DIR__ . '/../../composer.json'), true);
        if (empty($composer['name'])) {
            return;
        }

        $library = $composer['name'];

        // Read the lock file for the current version of the application.
        $paths = [
          __DIR__ . '/../../../../composer.lock',
          __DIR__ . '/../../../composer.lock',
          __DIR__ . '/../../composer.lock'
        ];
        foreach ($paths as $path) {
            if (!file_exists($path)) {
                continue;
            }

            // Get the contents of the lock file.
            $lock = json_decode(file_get_contents($path), true);
            if (empty($lock['packages'])) {
                return;
            }

            // Cycle through all the lock packages, locate the version used.
            foreach ($lock['packages'] as $package) {
                if ($package['name'] === $library) {
                    $this->version = $package['version'];
                    return;
                }
            }
        }
    }

    /**
     * Create the phloem.
     *
     * @return \Phloem\Phloem
     */
    public function createPhloem(): Phloem
    {
        return new Phloem();
    }

    /**
     * Handle the arguments and execute the command.
     *
     * @param array $argv
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function handleArguments(array $argv)
    {
        $getOpt = new GetOpt();

        // Load all the options from the arguments provided.
        $this->getOpt($getOpt);

        // Create a logger that will be used by phloem.
        $logger = new Logger('phloem');
        $handler = new StreamHandler('php://stdout', Logger::DEBUG);
        $handler->setFormatter(new ColoredLineFormatter());
        $logger->pushHandler($handler);
        $this->phloem->setLogger($logger);

        try {
            try {
                $getOpt->process();
            } catch (ArgumentException\Missing $exception) {
                // catch missing exceptions if help is requested
                if (!$getOpt->getOption('help')) {
                    throw $exception;
                }
            }
        } catch (ArgumentException $exception) {
            file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL);
            echo PHP_EOL . $getOpt->getHelpText();
            exit;
        }

        // Show the version and quit.
        if ($getOpt->getOption('version')) {
            echo sprintf('%s: %s' . PHP_EOL, $this->name, $this->version);
            exit;
        }

        /** @var \Phloem\TextUI\Commands\AbstractCommand $command */
        $command = $getOpt->getCommand();
        if ($getOpt->getOption('help') || !$command) {
            if (!$command) {
                echo $getOpt->getHelpText();
            }
            else {
                echo $command->getHelpText();
            }
            exit;
        }

        // call the requested command
        try {
            $command->setPhloem($this->phloem);

            return call_user_func($command->getHandler(), $getOpt);
        } catch (ArgumentException $exception) {
            file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL);
            echo PHP_EOL . $getOpt->getHelpText();
            exit;
        }
    }

    /**
     * Setup the option and argument handling.
     *
     * @param \GetOpt\GetOpt $getOpt
     */
    protected function getOpt(GetOpt $getOpt)
    {
        $getOpt->addOptions([
          Option::create(null, 'version', GetOpt::NO_ARGUMENT)
                ->setDescription('Show version information and quit'),

          Option::create('?', 'help', GetOpt::NO_ARGUMENT)
                ->setDescription('Show this help and quit'),
        ]);

        $getOpt->addCommands([
          new TasksCommand(),
          new RunCommand(),
        ]);
    }

}
