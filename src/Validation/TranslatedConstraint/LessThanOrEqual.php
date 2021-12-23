<?php

namespace App\Validation\TranslatedConstraint;

use App\Interfaces\ConstraintInterface;
use App\Validation\ConstraintCore;
use Symfony\Component\Validator\Constraints\LessThanOrEqualValidator;

/**
 * Class LessThanOrEqual
 * @package App\Validation\TranslatedConstraint
 */
class LessThanOrEqual extends \Symfony\Component\Validator\Constraints\LessThanOrEqual implements ConstraintInterface
{
    public $message = 'Значение должно быть меньше или равно {{ compared_value }}.';

    public static function register()
    {
        ConstraintCore::registerConstraint('Length', static::class);
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return LessThanOrEqualValidator::class;
    }

    /**
     * @return string
     */
    public function getDocumentationComment()
    {
        return "Значение должно быть меньше или равно {$this->value}.";
    }
}
