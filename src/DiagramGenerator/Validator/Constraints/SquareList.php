<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SquareList extends Constraint
{
    public $invalidMessage = 'Invalid string representation of a list of squares';

    public function __construct($options = null)
    {
        parent::__construct($options);
    }
}
