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
            if (null !== $constraint->max && $value > $constraint->max) {
                $this->context->addViolation($constraint->maxIndexMessage, array(
                    '{{ value }}' => $value,
                    '{{ limit }}' => $constraint->max,
                ));

                return;
            }

            if (null !== $constraint->min && $value < $constraint->min) {
                $this->context->addViolation($constraint->minIndexMessage, array(
                    '{{ value }}' => $value,
                    '{{ limit }}' => $constraint->min,
                ));
            }
        }

        if ($boardCellSize) {
            if (null !== $constraint->maxPx && $value > $constraint->maxPx) {
                $this->context->addViolation($constraint->maxMessage, array(
                    '{{ value }}' => $value,
                    '{{ limit }}' => $constraint->maxPx,
                ));

                return;
            }

            if (null !== $constraint->minPx && $value < $constraint->minPx) {
                $this->context->addViolation($constraint->minMessage, array(
                    '{{ value }}' => $value,
                    '{{ limit }}' => $constraint->minPx,
                ));
            }
        }
    }

    /**
     * Check if the value is in the valid board size index format (an integer)
     */
    protected function isValidBoardSizeIndexFormat($value)
    {
        return preg_match('/' . Integer::UNSIGNED_REGEX .'/', $value);
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

        if (!preg_match('/' . Integer::POSITIVE_REGEX . '/', $size)) {
            return false;
        }

        return $size;
    }
}
