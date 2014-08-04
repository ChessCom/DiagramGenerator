<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Note [lackovic10]: the two currently supported size formats are:
 * 1. a size index - an integer value
 * 2. cell size - {number}px
 *
 * These two size formats are exclusive, which means a value passed can't be valid for both formats
 */
class CustomCellSizeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        $isBoardSizeIndexFormat = $this->isValidBoardSizeIndexFormat($value);
        $boardCellSize = $this->isValidBoardCellSizeFormat($value);
        if (!$isBoardSizeIndexFormat && !$boardCellSize ) {
            $this->context->addViolation($constraint->invalidMessage, array(
                '{{ value }}' => $value
            ));

            return;
        }

        if ($isBoardSizeIndexFormat) {
            if ($value < $constraint->min || $value > $constraint->max) {
                $this->context->addViolation($constraint->invalidBoardSizeIndexMessage, array(
                    '{{ value }}' => $value,
                    '{{ min }}' => $constraint->min,
                    '{{ max }}' => $constraint->max,
                ));

                return;
            }
        }

        if ($boardCellSize) {
            if ($boardCellSize < $constraint->minPx || $boardCellSize > $constraint->maxPx) {
                $this->context->addViolation($constraint->invalidCellSizeMessage, array(
                    '{{ value }}' => $value,
                    '{{ minPx }}' => $constraint->minPx,
                    '{{ maxPx }}' => $constraint->maxPx,
                ));

                return;
            }
        }
    }

    /**
     * Check if the value is in the valid board size index format (an integer)
     */
    protected function isValidBoardSizeIndexFormat($value)
    {
        return is_numeric($value);
    }

    /**
     * Check if the size passed is in the valid board size format - {number}px
     * If valid, return the size
     *
     * @param string $value
     *
     * @return bool|int
     */
    protected function isValidBoardCellSizeFormat($value)
    {
        if (strpos($value, 'px') === false) {
            return false;
        }

        $size = substr($value, 0, -2);

        if (!is_numeric($size)) {
            return false;
        }

        return $size;
    }
}
