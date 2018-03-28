<?php

/**
 * @file
 */

namespace Phloem\TextUI\Commands;

use GetOpt\GetOpt;
use GetOpt\Operand;
use Phloem\Expression\Context;
use Phloem\Phloem;

/**
 * Class RunCommand
 *
 * @package Phloem\TextUI
 */
class RunCommand extends AbstractCommand {

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('run', [$this, 'handle']);

        //
        $this->addOperand(
          new Operand('task', Operand::OPTIONAL)
        );
    }

    /**
     * Handle the command.
     *
     * @param \GetOpt\GetOpt $getOpt
     *
     */
    public function handle(GetOpt $getOpt)
    {
        $phloem = $this->getPhloem();

        $context = new Context();
        try {
            // Process the file for defined tasks.
            $actions = $phloem->load($this->getFile($getOpt->getOption('file')));

            // We are targeting a specific task.
            if ($operand = $getOpt->getOperand('task')) {
                $actions = [
                  $actions,
                  [$operand => []],
                ];
            }

            $phloem->execute($actions, $context);
        }
        catch (\Exception $e) {
            $this->log('error', $e->getMessage());
        }
    }

}
