<?php

namespace App\Validation\Constraint;

/**
 * Class MifName
 * @package App\Validation\Constraint
 */
class MifName extends AbstractConstraint
{
    const NAME = 'MifName';

    public $message = 'Значение должно содержать как минимум три печатных символа. Переданное значение: {{ value }}.';

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
        return 'Значение должно содержать как минимум три печатных символа.';
    }
}
