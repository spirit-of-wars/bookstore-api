<?php

namespace App\Validation;

use App\Validation\Constraint\DateTime;
use App\Validation\Constraint\Slug;
use App\Validation\Constraint\Enum;
use App\Validation\Constraint\MifName;
use App\Validation\TranslatedConstraint\Email;
use App\Validation\TranslatedConstraint\Length;

/**
 * Class ConstraintCore
 * @package App\Validation
 */
class ConstraintCore
{
    const TRANSLATED_CONSTRAINT_NAMESPACE = 'App\Validation\TranslatedConstraint';
    const APP_CONSTRAINT_NAMESPACE = 'App\Validation\Constraint';
    const APP_VALIDATOR_NAMESPACE = 'App\Validation\ConstraintValidator';

    /** @var array */
    private static $constraintsList = [];

    public static function init()
    {
        Enum::register();
        Length::register();
        Email::register();
        DateTime::register();
        Slug::register();
        MifName::register();
    }

    /**
     * @param string $name
     * @param string $class
     */
    public static function registerConstraint($name, $class)
    {
        if (class_exists($class)) {
            self::$constraintsList[$name] = $class;
        }
    }

    /**
     * @param string $name
     * @return string|false
     */
    public static function getConstraintClass($name)
    {
        if (array_key_exists($name, self::$constraintsList)) {
            return self::$constraintsList[$name];
        }

        $className = self::TRANSLATED_CONSTRAINT_NAMESPACE . '\\' . $name;
        if (class_exists($className)) {
            return $className;
        }

        return false;
    }
}
