<?php

namespace DiagramGenerator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IntegerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if ($constraint->positive && !preg_match('/'.Integer::POSITIVE_REGEX.'/', $value)) {
            $this->context->addViolation($constraint->invalidPositiveMessage, array(
                '{{ value }}' => $value,
            ));

            return;
        }

        if ($constraint->unsigned && !preg_match('/'.Integer::UNSIGNED_REGEX.'/', $value)) {
            $this->context->addViolation($constraint->invalidUnsignedMessage, array(
                '{{ value }}' => $value,
            ));

            return;
        }

        if (!preg_match('/'.Integer::REGEX.'/', $value)) {
            $this->context->addViolation($constraint->invalidMessage, array(
                '{{ value }}' => $value,
            ));

            return;
        }
    }
}
