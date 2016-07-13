<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SquareListValidator extends ConstraintValidator
{
    protected $squares = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (strlen($value) % 2 != 0) {
            $this->context->addViolation($constraint->invalidMessage, ['{{ value }}' => $value]);

            return;
        }

        for ($i = 0; $i < strlen($value); ++$i) {
            if ($i % 2 == 0) {
                if (!in_array($value[$i], $this->squares)) {
                    $this->context->addViolation($constraint->invalidMessage, ['{{ value }}' => $value]);

                    return;
                }
            } else {
                if (!(is_numeric($value[$i]) && $value[$i] >= 1 && $value[$i] <= 8)) {
                    $this->context->addViolation($constraint->invalidMessage, ['{{ value }}' => $value]);

                    return;
                }
            }
        }
    }
}
