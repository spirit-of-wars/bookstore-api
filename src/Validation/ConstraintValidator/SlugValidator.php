<?php

namespace App\Validation\ConstraintValidator;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SlugValidator extends ConstraintValidator
{
    /**
     * @param $value
     * @param Constraint $constraint
     * @throws Exception
     */
    public function validate($value, Constraint $constraint)
    {
        $reg = "/^[^\W]+$/";
        if (!preg_match($reg, $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
