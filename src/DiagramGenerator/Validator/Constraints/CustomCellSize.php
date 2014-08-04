<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class CustomCellSize extends Constraint
{
    public $invalidCellSizeMessage = 'The cell size passed should be between {{ minPx }}px and {{ maxPx }}px.';
    public $invalidBoardSizeIndexMessage = 'The size passed should be between {{ min }} and {{ max }}';
    public $invalidMessage = 'Invalid size format! This value should be in the format {number}px or a positive number.';
    public $min;
    public $max;
    public $minPx;
    public $maxPx;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if ( (null === $this->min || null === $this->max) || (null === $this->minPx || null === $this->maxPx) ) {
            throw new MissingOptionsException(
                sprintf('Options "min" and "max" and "minPx" and "maxPx" must be given for constraint %s', __CLASS__),
                array('min', 'max')
            );
        }
    }
}
