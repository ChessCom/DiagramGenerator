<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Integer extends Constraint
{
    const REGEX = '^(0|(\-){0,1}[1-9]+\d*)$';
    const POSITIVE_REGEX = '^[1-9]{0,1}[1-9]+\d*$';
    const UNSIGNED_REGEX = '^(0|([1-9]{0,1}[1-9]+\d*))$';

    public $invalidPositiveMessage = "The value '{{ value }}' should be a positive integer";
    public $invalidUnsignedMessage = "The value '{{ value }}' should be a valid unsigned integer";
    public $invalidMessage = "The value '{{ value }}' should be a valid integer";
    public $positive = false;
    public $unsigned = false;
}
