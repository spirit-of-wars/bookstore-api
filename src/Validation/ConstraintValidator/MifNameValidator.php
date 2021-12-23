<?php

namespace App\Validation\ConstraintValidator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class MifNameValidator
 * @package App\Validation\ConstraintValidator
 */
class MifNameValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $regExp = '/([\w\d_а-яёА-ЯЁ .,:;?!@"#$%^&*\(\)=+\-\/]){3,}/u';

        if (!preg_match($regExp, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }
}
