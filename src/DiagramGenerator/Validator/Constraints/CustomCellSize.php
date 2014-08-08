<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class CustomCellSize extends Constraint
{
    public $invalidMessage = 'This value should be an unsigned integer or in the format {number}px with {number} being a positive number.';
    public $maxIndexMessage = 'This value should be {{ limit }} or less.';
    public $minIndexMessage = 'This value should be {{ limit }} or more.';
    public $maxMessage = 'This value should be {{ limit }}px or less.';
    public $minMessage = 'This value should be {{ limit }}px or more.';
    public $min;
    public $max;
    public $minPx;
    public $maxPx;
}
