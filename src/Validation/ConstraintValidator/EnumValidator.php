<?php

namespace App\Validation\ConstraintValidator;

use App\Helper\NamespaceHelper;
use App\Validation\Constraint\Enum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class EnumValidator
 * @package App\Validation\Validator
 */
class EnumValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Enum) {
            throw new UnexpectedTypeException($constraint, Enum::class);
        }

        $enumFactor = $constraint->enumName ?? $constraint->enumList ?? null;
        if (!$enumFactor) {

            return;
        }

        if (is_string($enumFactor)) {
            $this->validateWithClass($value, $constraint, $enumFactor);
        } elseif (is_array($enumFactor)) {
            $this->validateWithList($value, $constraint, $enumFactor);
        }
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @param string $enumClassName
     */
    private function validateWithClass($value, $constraint, $enumClassName)
    {
        /** @var \App\Enum\Core\Enum $enumClass */
        $enumClass = NamespaceHelper::defineClassName($enumClassName, NamespaceHelper::getEnumDefaultNamespace());

        if (!is_subclass_of($enumClass, \App\Enum\Core\Enum::class)) {
            $this->context->buildViolation("Enum class '$enumClass' is wrong")
                ->addViolation();

            return;
        }

        if (!$enumClass::validateValue($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter('{{ enum }}', implode(', ', $enumClass::getList()))
                ->addViolation();
        }
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @param array $list
     */
    private function validateWithList($value, $constraint, $list)
    {
        if (!in_array($value, $list)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter('{{ enum }}', implode(', ', $list))
                ->addViolation();
        }
    }
}
