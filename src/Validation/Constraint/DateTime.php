<?php

namespace App\Validation\Constraint;

/**
 * Class DateTime
 * @package App\Validation\Constraint
 */
class DateTime extends AbstractConstraint
{
    const NAME = 'DateTime';

    public $message = 'Значение не является датой.';

    /**
     * @return string
     */
    public static function getName()
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getDocumentationComment()
    {
        return 'DateTime';
    }
}

