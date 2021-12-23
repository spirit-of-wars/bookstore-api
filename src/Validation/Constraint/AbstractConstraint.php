<?php

namespace App\Validation\Constraint;

use App\Interfaces\ConstraintInterface;
use App\Validation\ConstraintCore;
use Symfony\Component\Validator\Constraint;

/**
 * Class AbstractConstraint
 * @package App\Validation\Constraint
 */
abstract class AbstractConstraint extends Constraint implements ConstraintInterface
{
    /**
     * @return string
     */
    abstract public static function getName();

    public static function register()
    {
        ConstraintCore::registerConstraint(static::getName(), static::class);
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return ConstraintCore::APP_VALIDATOR_NAMESPACE . '\\' . static::getName() . 'Validator';
    }
}
