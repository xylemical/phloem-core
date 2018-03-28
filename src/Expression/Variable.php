<?php

/**
 * @file
 */

namespace Phloem\Expression;

use Phloem\Expression\Context;
use Xylemical\Expressions\Operator;
use Xylemical\Expressions\Token;
use Xylemical\Expressions\Value;

/**
 * Class Variable
 *
 * @package Phloem\Expression
 */
class Variable extends Value
{
    /**
     * {@inheritdoc}
     */
    public function __construct($priority = 10, $associativity = Operator::NONE_ASSOCIATIVE) {
        parent::__construct('\$[a-zA-Z_][a-zA-Z0-9_\-]*', function(array $operands, Context $context, Token $token) {
            // Specialized behaviour of variables.
            return $context->getVariable(substr($token->getValue(), 1));
        }, $priority, $associativity);
    }
}
