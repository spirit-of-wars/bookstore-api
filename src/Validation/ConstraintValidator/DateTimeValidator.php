<?php

namespace App\Validation\ConstraintValidator;

use DateTime;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class DateTimeValidator
 * @package App\Validation\ConstraintValidator
 */
class DateTimeValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /* Allowed formats:
         * 2020-07-21T07:37:05.895Z
         * 2020-07-09 06:19:32.000000
         * 2020-07-09 06:19:32
         * 2020-07-09
         */
        $reg = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])([T ]([01]\d|2[0-3]):[0-5]\d:[0-5]\d(\.\d+Z?)?)?$/';
        if (!preg_match($reg, $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        try {
            new DateTime($value);
        } catch (Exception $exception) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
