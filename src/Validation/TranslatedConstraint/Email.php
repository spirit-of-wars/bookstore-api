<?php

namespace App\Validation\TranslatedConstraint;

use App\Interfaces\ConstraintInterface;
use App\Validation\ConstraintCore;
use Symfony\Component\Validator\Constraints\EmailValidator;

/**
 * Class Email
 * @package App\Validation\TranslatedConstraint
 */
class Email extends \Symfony\Component\Validator\Constraints\Email implements ConstraintInterface
{
    public $message = 'Значение не является корректным адресом электронной почты.';

    public static function register()
    {
        ConstraintCore::registerConstraint('Email', static::class);
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return EmailValidator::class;
    }

    /**
     * @return string
     */
    public function getDocumentationComment()
    {
        return 'Значение доджно являться корректным адресом электронной почты.';
    }
}
