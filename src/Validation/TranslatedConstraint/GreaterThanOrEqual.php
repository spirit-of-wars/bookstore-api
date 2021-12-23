<?php

namespace App\Validation\TranslatedConstraint;

use App\Interfaces\ConstraintInterface;
use App\Validation\ConstraintCore;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqualValidator;

/**
 * Class GreaterThanOrEqual
 * @package App\Validation\TranslatedConstraint
 */
class GreaterThanOrEqual extends \Symfony\Component\Validator\Constraints\GreaterThanOrEqual implements ConstraintInterface
{
    public $message = 'Значение должно быть больше или равно {{ compared_value }}.';

    public static function register()
    {
        ConstraintCore::registerConstraint('Length', static::class);
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return GreaterThanOrEqualValidator::class;
    }

    /**
     * @return string
     */
    public function getDocumentationComment()
    {
        return "Значение должно быть больше или равно {$this->value}.";
    }
}
