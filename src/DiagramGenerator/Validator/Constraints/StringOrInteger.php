<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class StringOrInteger extends Constraint
{
    public $min;
    public $max;
    public $regex = '^[a-zA-Z\_0-9\-]{1,20}$';
    public $invalidMessage = 'This value is not valid.';
    public $minMessage = 'This value should be {{ limit }} or more.';
    public $maxMessage = 'This value should be {{ limit }} or less.';
}
