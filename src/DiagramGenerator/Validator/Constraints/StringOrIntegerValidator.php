<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StringOrIntegerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        $isInteger = preg_match('/'.Integer::REGEX.'/', $value);
        if (!$isInteger && !($constraint->regex && preg_match('/'.$constraint->regex.'/', $value))) {
            $this->context->addViolation($constraint->invalidMessage);

            return;
        }

        if ($isInteger) {
            if (null !== $constraint->max && $value > $constraint->max) {
                $this->context->addViolation($constraint->maxMessage, array(
                    '{{ limit }}' => $constraint->max,
                ));

                return;
            }

            if (null !== $constraint->min && $value < $constraint->min) {
                $this->context->addViolation($constraint->minMessage, array(
                    '{{ limit }}' => $constraint->min,
                ));

                return;
            }
        }
    }
}
