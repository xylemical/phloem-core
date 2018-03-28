<?php

/**
 * @file
 */

namespace Phloem\TextUI\Commands;

use GetOpt\GetOpt;
use GetOpt\Operand;
use Phloem\Phloem;

/**
 * Class BuildCommand
 *
 * @package Phloem\TextUI
 */
class BuildCommand extends \GetOpt\Command {

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('build', [$this, 'handle']);

        $this->addOperands([
          Operand::create('file', Operand::REQUIRED)
                 ->setValidation('is_readable'),
        ]);

    }

    public function handle(Phloem $phloem, GetOpt $getOpt)
    {
        // copy($getOpt->getOperand('file'), $getOpt->getOperand('destination'));
    }

}