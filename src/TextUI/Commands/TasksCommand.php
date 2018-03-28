<?php

/**
 * @file
 */

namespace Phloem\TextUI\Commands;

use GetOpt\GetOpt;

/**
 * Class TasksCommand
 *
 * @package Phloem\TextUI
 */
class TasksCommand extends AbstractCommand {

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('tasks', [$this, 'handle']);
    }

    /**
     * Handle the command.
     *
     * @param \GetOpt\GetOpt $getOpt
     *
     * @throws \Phloem\Exception\ActionFactoryException
     * @throws \Phloem\Exception\ConfigException
     * @throws \Phloem\Exception\LoaderException
     */
    public function handle(GetOpt $getOpt)
    {
        $phloem = $this->getPhloem();

        // Get the filename used for parsing.
        $filename = $this->getFile($getOpt->getOption('file'));

        // Process the file for defined tasks.
        $actions = $phloem->load($filename);

        // Process the loaded actions.
        $phloem->getActionFactory()->process($actions);

        // Get the list of tasks.
        $tasks = $phloem->getManager()->getTasks();

        // Now output the tasks
        $this->log('info', 'Tasks:');
        if (count($tasks)) {
            foreach ($tasks as $task) {
                $this->log('info', "{$task}");
            }
        }
        else {
            $this->log('warning', "No tasks defined.");
        }
    }

}
